<?php
class Booking extends Model {
    public function hasConflict($carId,$start,$end) {
        $s=$this->db->prepare("SELECT COUNT(*) FROM bookings WHERE car_id=? AND status IN ('pending','confirmed','ongoing') AND NOT (end_date < ? OR start_date > ?)");
        $s->execute([$carId,$start,$end]); return (int)$s->fetchColumn()>0;
    }
    public function create($userId,$carId,$start,$end,$days,$total,$note,$insurance,$deposit,$method) {
        $s=$this->db->prepare(
            "INSERT INTO bookings(user_id,car_id,start_date,end_date,days,total_price,insurance_amount,deposit_amount,payment_method,note,status) ".
            "VALUES(?,?,?,?,?,?,?,?,?,?,'pending')"
        );
        return $s->execute([$userId,$carId,$start,$end,$days,$total,$insurance,$deposit,$method,$note]);
    }
    public function createIfAvailable($userId,$carId,$start,$end,$note,$includeInsurance,$depositAmount,$paymentMethod) {
        $days=(new DateTimeImmutable($start))->diff(new DateTimeImmutable($end))->days+1;
        $this->db->beginTransaction();
        try {
            $carQuery=$this->db->prepare('SELECT id,price_per_day FROM cars WHERE id=? AND active=1 FOR UPDATE');
            $carQuery->execute([$carId]);
            $car=$carQuery->fetch();
            if (!$car) {
                $this->db->rollBack();
                return ['status'=>'unavailable'];
            }
            if ($this->hasConflict($carId,$start,$end)) {
                $this->db->rollBack();
                return ['status'=>'conflict'];
            }
            $rentTotal=round($days*(float)$car['price_per_day'],2);
            $insuranceTotal=$includeInsurance?$days*AUTOGO_INSURANCE_PER_DAY:0;
            $this->create(
                $userId,$carId,$start,$end,$days,$rentTotal,$note,
                $insuranceTotal,$depositAmount,$paymentMethod
            );
            $id=(int)$this->db->lastInsertId();
            $this->db->commit();
            return [
                'status'=>'created','id'=>$id,'days'=>$days,
                'rent'=>$rentTotal,'insurance'=>$insuranceTotal,
                'deposit'=>$depositAmount,'amount_due'=>$rentTotal+$insuranceTotal+$depositAmount
            ];
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $exception;
        }
    }
    public function forUser($id) {
        $s=$this->db->prepare("SELECT b.*,c.name car_name,c.image FROM bookings b JOIN cars c ON c.id=b.car_id WHERE b.user_id=? ORDER BY b.created_at DESC");
        $s->execute([$id]); return $s->fetchAll();
    }
    public function cancel($id,$userId) {
        $s=$this->db->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status='pending' AND payment_status='unpaid'");
        $s->execute([$id,$userId]); return $s->rowCount()>0;
    }
    public function submitQrPayment($id,$userId,$reference) {
        $s=$this->db->prepare(
            "UPDATE bookings SET payment_status='awaiting_confirmation',payment_reference=? ".
            "WHERE id=? AND user_id=? AND payment_method='qr' AND payment_status='unpaid' ".
            "AND status NOT IN ('cancelled','rejected')"
        );
        $s->execute([$reference,$id,$userId]);
        return $s->rowCount()>0;
    }
    public function markPaymentReceived($id) {
        $s=$this->db->prepare(
            "UPDATE bookings SET payment_status='paid',paid_at=NOW(),".
            "deposit_status=IF(deposit_amount>0,'held','not_collected') ".
            "WHERE id=? AND (payment_status='awaiting_confirmation' OR ".
            "(payment_status='unpaid' AND status NOT IN ('cancelled','rejected')))"
        );
        $s->execute([$id]);
        return $s->rowCount()>0;
    }
    public function refundPayment($id) {
        $s=$this->db->prepare(
            "UPDATE bookings SET payment_status='refunded',payment_reference=NULL,".
            "deposit_status=IF(deposit_amount>0,'refunded','not_collected') ".
            "WHERE id=? AND payment_status='paid' AND status IN ('cancelled','rejected')"
        );
        $s->execute([$id]);
        return $s->rowCount()>0;
    }
    public function refundDeposit($id) {
        $s=$this->db->prepare(
            "UPDATE bookings SET deposit_status='refunded' ".
            "WHERE id=? AND payment_status='paid' AND deposit_status='held' AND status='completed'"
        );
        $s->execute([$id]);
        return $s->rowCount()>0;
    }
    public function all() {
        return $this->db->query("SELECT b.*,u.name customer,c.name car_name FROM bookings b JOIN users u ON u.id=b.user_id JOIN cars c ON c.id=b.car_id ORDER BY b.created_at DESC")->fetchAll();
    }
    public function updateStatus($id,$status) {
        $s=$this->db->prepare('SELECT status FROM bookings WHERE id=?');
        $s->execute([$id]);
        $current=$s->fetchColumn();
        if ($current===false||!in_array($status,booking_allowed_transitions($current),true)) return false;
        $s=$this->db->prepare('UPDATE bookings SET status=? WHERE id=? AND status=?');
        $s->execute([$status,$id,$current]);
        return $s->rowCount()>0;
    }
    public function stats() {
        $now=new DateTimeImmutable('first day of this month');
        $from=$now->modify('-5 months')->format('Y-m-d');
        $monthlyQuery=$this->db->prepare(
            "SELECT DATE_FORMAT(paid_at,'%Y-%m') AS revenue_month,".
            "SUM(total_price+insurance_amount) AS revenue ".
            "FROM bookings WHERE payment_status='paid' AND status NOT IN ('cancelled','rejected') AND paid_at>=? ".
            "GROUP BY DATE_FORMAT(paid_at,'%Y-%m') ORDER BY revenue_month"
        );
        $monthlyQuery->execute([$from]);
        $monthlyResults=$monthlyQuery->fetchAll();
        $monthlyRevenues=[];
        foreach ($monthlyResults as $row) {
            $monthlyRevenues[$row['revenue_month']]=(float)$row['revenue'];
        }
        $months=[];
        for ($offset=5;$offset>=0;$offset--) {
            $month=$now->modify('-'.$offset.' months');
            $key=$month->format('Y-m');
            $monthName=class_exists('IntlDateFormatter')
                ? (new IntlDateFormatter('vi_VN',IntlDateFormatter::NONE,IntlDateFormatter::NONE,null,null,'MMM yyyy'))->format($month)
                : $month->format('m/Y');
            $months[]=['key'=>$key,'label'=>$monthName,'revenue'=>$monthlyRevenues[$key]??0];
        }
        $stats=[
            'orders'=>(int)$this->db->query("SELECT COUNT(*) FROM bookings")->fetchColumn(),
            'customers'=>(int)$this->db->query("SELECT COUNT(*) FROM users WHERE role='customer'")->fetchColumn(),
            'cars'=>(int)$this->db->query("SELECT COUNT(*) FROM cars")->fetchColumn(),
            'revenue'=>(float)$this->db->query("SELECT COALESCE(SUM(total_price+insurance_amount),0) FROM bookings WHERE payment_status='paid' AND status NOT IN ('cancelled','rejected')")->fetchColumn(),
            'payments_received'=>(float)$this->db->query("SELECT COALESCE(SUM(total_price+insurance_amount+IF(deposit_status='held',deposit_amount,0)),0) FROM bookings WHERE payment_status='paid'")->fetchColumn(),
            'deposits_held'=>(float)$this->db->query("SELECT COALESCE(SUM(deposit_amount),0) FROM bookings WHERE deposit_status='held'")->fetchColumn(),
            'outstanding'=>(float)$this->db->query("SELECT COALESCE(SUM(total_price+insurance_amount+deposit_amount),0) FROM bookings WHERE payment_status IN ('unpaid','awaiting_confirmation') AND status NOT IN ('cancelled','rejected')")->fetchColumn(),
            'unpaid_orders'=>(int)$this->db->query("SELECT COUNT(*) FROM bookings WHERE payment_status IN ('unpaid','awaiting_confirmation') AND status NOT IN ('cancelled','rejected')")->fetchColumn(),
            'refunded'=>(float)$this->db->query("SELECT COALESCE(SUM(total_price+insurance_amount+deposit_amount),0) FROM bookings WHERE payment_status='refunded'")->fetchColumn(),
            'monthly_revenues'=>$months
        ];
        return $stats;
    }
}
