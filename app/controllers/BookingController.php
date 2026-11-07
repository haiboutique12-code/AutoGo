<?php
class BookingController extends Controller {
    private function isValidDate($value) {
        if (!is_string($value)) return false;
        $date=DateTimeImmutable::createFromFormat('!Y-m-d',$value);
        $errors=DateTimeImmutable::getLastErrors();
        return $date!==false&&($errors===false||($errors['warning_count']===0&&$errors['error_count']===0))&&$date->format('Y-m-d')===$value;
    }

    public function create() {
        $this->requireLogin();
        $car=(new Car())->find(positive_int($_GET['car_id']??null));
        if (!$car) { flash('error','Xe không tồn tại.'); $this->redirect('/cars'); }
        $settings=(new Settings())->all();
        $this->view('bookings/create',['car'=>$car,'settings'=>$settings,'qrAvailable'=>vietqr_configured($settings)]);
    }
    public function store() {
        $this->requireLogin(); verify_csrf();
        $car=(new Car())->find(positive_int($_POST['car_id']??null));
        $start=$_POST['start_date']??'';
        $end=$_POST['end_date']??'';
        if (!$car) { flash('error','Xe không còn khả dụng. Vui lòng chọn xe khác.'); $this->redirect('/cars'); }
        $note=is_string($_POST['note']??null)?trim($_POST['note']):'';
        if (mb_strlen($note)>500) {
            flash('error','Ghi chú không được vượt quá 500 ký tự.');
            $this->redirect('/booking/create?car_id='.$car['id']);
        }
        $today=date('Y-m-d');
        if (!$this->isValidDate($start)||!$this->isValidDate($end)||$start<$today||$end<$start) {
            flash('error','Vui lòng chọn ngày nhận xe và ngày trả xe hợp lệ.');
            $this->redirect('/booking/create?car_id='.$car['id']);
        }
        $paymentMethod=is_string($_POST['payment_method']??null)?$_POST['payment_method']:'';
        if (!in_array($paymentMethod,['qr','cash'],true)) {
            flash('error','Vui lòng chọn phương thức thanh toán bằng QR hoặc tiền mặt.');
            $this->redirect('/booking/create?car_id='.$car['id']);
        }
        $settings=(new Settings())->all();
        if ($paymentMethod==='qr'&&!vietqr_configured($settings)) {
            flash('error','Cửa hàng chưa cấu hình tài khoản QR. Vui lòng chọn tiền mặt hoặc liên hệ AutoGo.');
            $this->redirect('/booking/create?car_id='.$car['id']);
        }
        $includeInsurance=($_POST['include_insurance']??'')==='1';
        $booking=new Booking();
        $result=$booking->createIfAvailable(
            $_SESSION['user']['id'],$car['id'],$start,$end,$note,
            $includeInsurance,AUTOGO_BOOKING_DEPOSIT,$paymentMethod
        );
        if ($result['status']==='unavailable') {
            flash('error','Xe không còn khả dụng. Vui lòng chọn xe khác.');
            $this->redirect('/cars');
        }
        if ($result['status']==='conflict') {
            flash('error','Xe đã có đơn thuê trong khoảng thời gian này. Vui lòng chọn ngày khác.');
            $this->redirect('/booking/create?car_id='.$car['id']);
        }
        flash('success','Đặt xe thành công! Đơn đang chờ Admin xác nhận.'); $this->redirect('/my-bookings');
    }
    public function mine() {
        $this->requireLogin();
        $items=(new Booking())->forUser($_SESSION['user']['id']);
        $settings=(new Settings())->all();
        $this->view('bookings/mine',['items'=>$items,'settings'=>$settings]);
    }
    public function submitPayment() {
        $this->requireLogin(); verify_csrf();
        $id=positive_int($_POST['id']??null);
        $reference=is_string($_POST['payment_reference']??null)?trim($_POST['payment_reference']):'';
        if (mb_strlen($reference)>100) {
            flash('error','Mã giao dịch không được vượt quá 100 ký tự.');
            $this->redirect('/my-bookings');
        }
        $settings=(new Settings())->all();
        if (!vietqr_configured($settings)) {
            flash('error','Tài khoản QR của cửa hàng chưa được cấu hình.');
            $this->redirect('/my-bookings');
        }
        $submitted=(new Booking())->submitQrPayment($id,$_SESSION['user']['id'],$reference);
        flash(
            $submitted?'success':'error',
            $submitted
                ?'Đã báo chuyển khoản. Đơn sẽ được cập nhật sau khi Admin xác nhận tiền vào tài khoản.'
                :'Không thể gửi thông tin thanh toán cho đơn này.'
        );
        $this->redirect('/my-bookings');
    }
    public function cancel() {
        $this->requireLogin(); verify_csrf();
        $cancelled=(new Booking())->cancel(positive_int($_POST['id']??null),$_SESSION['user']['id']);
        flash($cancelled?'success':'error',$cancelled?'Đơn thuê của bạn đã được hủy.':'Không thể hủy đơn này. Chỉ chủ đơn mới được hủy đơn đang chờ xác nhận.');
        $this->redirect('/my-bookings');
    }
}
