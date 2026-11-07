<?php
class AdminController extends Controller {
    public function dashboard() {
        $this->requireAdmin();
        $stats=(new Booking())->stats();
        $this->view('admin/dashboard',['stats'=>$stats]);
    }
    public function cars() { $this->requireAdmin(); $cars=(new Car())->adminAll(); $this->view('admin/cars',['cars'=>$cars]); }
    public function saveCar() {
        $this->requireAdmin(); verify_csrf();
        $idValue=$_POST['id']??'';
        $name=is_string($_POST['name']??null)?trim($_POST['name']):'';
        $brand=is_string($_POST['brand']??null)?trim($_POST['brand']):'';
        $type=is_string($_POST['type']??null)?$_POST['type']:'';
        $seats=filter_var($_POST['seats']??null,FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>100]]);
        $priceValue=$_POST['price']??null;
        $price=is_string($priceValue)&&is_numeric($priceValue)?(float)$priceValue:0;
        $image=is_string($_POST['image']??null)?trim($_POST['image']):'';
        $description=is_string($_POST['description']??null)?trim($_POST['description']):'';
        $imageUrl=$image===''?false:filter_var($image,FILTER_VALIDATE_URL);
        $imageScheme=$imageUrl?strtolower((string)parse_url($image,PHP_URL_SCHEME)):'';
        if (($idValue!==''&&positive_int($idValue)===0)||mb_strlen($name)<1||mb_strlen($name)>150||
            mb_strlen($brand)>100||!in_array($type,['car','motorbike'],true)||
            $seats===false||!is_finite($price)||$price<=0||$price>9999999999.99||
            ($image!==''&&(!$imageUrl||!in_array($imageScheme,['http','https'],true)))||
            mb_strlen($description)>10000) {
            flash('error','Vui lòng kiểm tra tên, loại xe, số chỗ, giá và đường dẫn ảnh.');
            $this->redirect('/admin/cars');
        }
        $d=['id'=>$idValue===''?0:positive_int($idValue),'name'=>$name,'brand'=>$brand,'type'=>$type,'seats'=>$seats,'price'=>$price,'image'=>$image,'description'=>$description,'active'=>isset($_POST['active'])?1:0];
        (new Car())->save($d); flash('success','Đã lưu thông tin xe.'); $this->redirect('/admin/cars');
    }
    public function deleteCar() {
        $this->requireAdmin(); verify_csrf();
        $deleted=(new Car())->delete(positive_int($_POST['id']??null));
        flash($deleted?'success':'error',$deleted?'Đã xóa xe.':'Không thể xóa xe này vì xe đã có đơn thuê hoặc không còn tồn tại.');
        $this->redirect('/admin/cars');
    }
    public function bookings() { $this->requireAdmin(); $items=(new Booking())->all(); $this->view('admin/bookings',['items'=>$items]); }
    public function settings() {
        $this->requireAdmin();
        $this->view('admin/settings',['settings'=>(new Settings())->all()]);
    }
    public function saveSettings() {
        $this->requireAdmin(); verify_csrf();
        $address=is_string($_POST['store_address']??null)?trim($_POST['store_address']):'';
        $mapsUrl=is_string($_POST['google_maps_url']??null)?trim($_POST['google_maps_url']):'';
        $bankCode=is_string($_POST['bank_code']??null)?strtoupper(trim($_POST['bank_code'])):'';
        $bankAccount=is_string($_POST['bank_account']??null)?preg_replace('/\s+/','',trim($_POST['bank_account'])):'';
        $accountName=is_string($_POST['bank_account_name']??null)?trim($_POST['bank_account_name']):'';
        $validMapsUrl=true;
        if ($mapsUrl!=='') {
            $host=strtolower((string)parse_url($mapsUrl,PHP_URL_HOST));
            $validMapsUrl=filter_var($mapsUrl,FILTER_VALIDATE_URL)!==false&&
                strtolower((string)parse_url($mapsUrl,PHP_URL_SCHEME))==='https'&&
                in_array($host,['google.com','www.google.com','maps.google.com','maps.app.goo.gl','goo.gl'],true);
        }
        $hasBankSettings=$bankCode!==''||$bankAccount!==''||$accountName!=='';
        $validBankSettings=!$hasBankSettings||
            (preg_match('/^[A-Z0-9]{2,20}$/',$bankCode)===1&&
                preg_match('/^[0-9]{6,34}$/',$bankAccount)===1&&
                mb_strlen($accountName)>=2&&mb_strlen($accountName)<=100);
        if (mb_strlen($address)>500||!$validMapsUrl||!$validBankSettings) {
            flash('error','Kiểm tra lại địa chỉ, link Google Maps HTTPS và thông tin tài khoản ngân hàng.');
            $this->redirect('/admin/settings');
        }
        (new Settings())->saveMany([
            'store_address'=>$address,
            'google_maps_url'=>$mapsUrl,
            'bank_code'=>$bankCode,
            'bank_account'=>$bankAccount,
            'bank_account_name'=>$accountName
        ]);
        flash('success','Đã lưu điểm nhận xe và cài đặt thanh toán QR.');
        $this->redirect('/admin/settings');
    }
    public function updatePayment() {
        $this->requireAdmin(); verify_csrf();
        $updated=(new Booking())->markPaymentReceived(positive_int($_POST['id']??null));
        flash(
            $updated?'success':'error',
            $updated?'Đã xác nhận nhận đủ tiền. Tiền cọc được ghi nhận riêng.':'Không thể xác nhận thanh toán đơn này.'
        );
        $this->redirect('/admin/bookings');
    }
    public function refundPayment() {
        $this->requireAdmin(); verify_csrf();
        $refunded=(new Booking())->refundPayment(positive_int($_POST['id']??null));
        flash(
            $refunded?'success':'error',
            $refunded?'Đã ghi nhận hoàn tiền cho đơn đã hủy hoặc bị từ chối.':'Chỉ có thể hoàn tiền cho đơn đã hủy/từ chối và đã thanh toán.'
        );
        $this->redirect('/admin/bookings');
    }
    public function refundDeposit() {
        $this->requireAdmin(); verify_csrf();
        $refunded=(new Booking())->refundDeposit(positive_int($_POST['id']??null));
        flash(
            $refunded?'success':'error',
            $refunded?'Đã ghi nhận hoàn trả tiền cọc.':'Chỉ hoàn cọc cho đơn đã thanh toán và hoàn thành.'
        );
        $this->redirect('/admin/bookings');
    }
    public function updateBooking() {
        $this->requireAdmin(); verify_csrf();
        $updated=(new Booking())->updateStatus(positive_int($_POST['id']??null),is_string($_POST['status']??null)?$_POST['status']:'');
        flash($updated?'success':'error',$updated?'Đã cập nhật trạng thái đơn thuê.':'Trạng thái mới không hợp lệ hoặc đơn đã được cập nhật trước đó.');
        $this->redirect('/admin/bookings');
    }
    public function users() { $this->requireAdmin(); $users=(new User())->all(); $this->view('admin/users',['users'=>$users]); }
}
