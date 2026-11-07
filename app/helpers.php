<?php
const AUTOGO_INSURANCE_PER_DAY=50000;
const AUTOGO_BOOKING_DEPOSIT=2000000;

function url($path='') {
    $base = (require __DIR__ . '/../config/config.php')['base_url'];
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function money($n) { return number_format((float)$n, 0, ',', '.') . ' đ'; }
function positive_int($value) {
    if (!is_string($value)&&!is_int($value)) return 0;
    $number=filter_var($value,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
    return $number===false?0:$number;
}
function booking_status_label($status) {
    $labels=[
        'pending'=>'Chờ xác nhận',
        'confirmed'=>'Đã xác nhận',
        'ongoing'=>'Đang thuê',
        'completed'=>'Hoàn thành',
        'cancelled'=>'Đã hủy',
        'rejected'=>'Từ chối'
    ];
    return $labels[$status]??'Không xác định';
}
function booking_allowed_transitions($status) {
    $transitions=[
        'pending'=>['confirmed','cancelled','rejected'],
        'confirmed'=>['ongoing','cancelled'],
        'ongoing'=>['completed','cancelled'],
        'completed'=>[],
        'cancelled'=>[],
        'rejected'=>[]
    ];
    return $transitions[$status]??[];
}
function booking_amount_due($booking) {
    return (float)($booking['total_price']??0)+(float)($booking['insurance_amount']??0)+(float)($booking['deposit_amount']??0);
}
function payment_method_label($method) {
    return $method==='qr'?'QR chuyển khoản':'Tiền mặt';
}
function payment_status_label($status) {
    $labels=[
        'unpaid'=>'Chưa thanh toán',
        'awaiting_confirmation'=>'Chờ xác nhận chuyển khoản',
        'paid'=>'Đã thanh toán',
        'refunded'=>'Đã hoàn tiền'
    ];
    return $labels[$status]??'Không xác định';
}
function deposit_status_label($status) {
    $labels=['not_collected'=>'Chưa thu cọc','held'=>'Đang giữ cọc','refunded'=>'Đã hoàn cọc'];
    return $labels[$status]??'Không xác định';
}
function vietqr_configured($settings) {
    return preg_match('/^[A-Za-z0-9]{2,20}$/',(string)($settings['bank_code']??''))===1&&
        preg_match('/^[0-9]{6,34}$/',(string)($settings['bank_account']??''))===1&&
        trim((string)($settings['bank_account_name']??''))!=='';
}
function vietqr_url($settings,$booking) {
    $bankCode=(string)($settings['bank_code']??'');
    $account=(string)($settings['bank_account']??'');
    $accountName=trim((string)($settings['bank_account_name']??''));
    if (!vietqr_configured($settings)) {
        return null;
    }
    $query=http_build_query([
        'amount'=>number_format(booking_amount_due($booking),0,'.',''),
        'addInfo'=>'AUTOGO-'.$booking['id'],
        'accountName'=>$accountName
    ],'','&',PHP_QUERY_RFC3986);
    return 'https://img.vietqr.io/image/'.rawurlencode(strtoupper($bankCode)).'-'.
        rawurlencode($account).'-qr_only.png?'.$query;
}
function flash($key, $message=null) {
    if ($message !== null) { $_SESSION['flash'][$key] = $message; return; }
    $value = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $value;
}
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf() {
    $submitted=is_string($_POST['csrf']??null)?$_POST['csrf']:'';
    if ($_SERVER['REQUEST_METHOD']==='POST' && !hash_equals($_SESSION['csrf'] ?? '', $submitted)) {
        http_response_code(419); exit('Phiên làm việc hết hạn. Hãy tải lại trang.');
    }
}
