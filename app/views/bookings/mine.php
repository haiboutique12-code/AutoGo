<section class="page-title"><span class="eyebrow">KHU VỰC CỦA BẠN</span><h1>Đơn thuê của tôi</h1><p>Theo dõi lịch thuê, thông tin điểm nhận xe, chi phí và xác nhận thanh toán.</p></section>
<?php
$openBookings=count(array_filter($items,static fn($booking)=>in_array($booking['status'],['pending','confirmed','ongoing'],true)));
$finishedBookings=count(array_filter($items,static fn($booking)=>in_array($booking['status'],['completed','cancelled','rejected'],true)));
?>
<?php if($items): ?>
    <div class="customer-booking-summary">
        <div><span>Tổng đơn thuê</span><strong><?=count($items)?></strong></div>
        <div><span>Đơn đang xử lý</span><strong><?=$openBookings?></strong></div>
        <div><span>Đã kết thúc</span><strong><?=$finishedBookings?></strong></div>
    </div>
    <div class="booking-list-toolbar">
        <strong>Lịch sử thuê xe</strong>
        <label><span class="sr-only">Lọc đơn theo trạng thái</span><select data-filter-status aria-label="Lọc đơn theo trạng thái"><option value="">Tất cả đơn thuê</option><option value="pending">Chờ xác nhận</option><option value="confirmed">Đã xác nhận</option><option value="ongoing">Đang thuê</option><option value="completed">Hoàn thành</option><option value="cancelled">Đã hủy</option><option value="rejected">Từ chối</option></select></label>
    </div>
<?php endif; ?>
<?php if($items): ?>
    <div class="booking-orders" data-filter-list>
        <?php foreach($items as $booking): ?>
            <?php $qrImage=$booking['payment_method']==='qr'?vietqr_url($settings,$booking):null; ?>
            <article class="booking-order booking-order-full" data-filter-row data-status="<?=e($booking['status'])?>">
                <img src="<?=e($booking['image'])?>" alt="<?=e($booking['car_name'])?>" loading="lazy">
                <div class="booking-order-info">
                    <span class="booking-order-number">ĐƠN THUÊ #<?=e($booking['id'])?></span>
                    <h2><?=e($booking['car_name'])?></h2>
                    <p><?=e($booking['start_date'])?> <span aria-hidden="true">→</span> <?=e($booking['end_date'])?> <span class="meta-divider">·</span> <?=e($booking['days'])?> ngày</p>
                </div>
                <div class="booking-order-status">
                    <span class="status status-<?=e($booking['status'])?>"><?=e(booking_status_label($booking['status']))?></span>
                    <strong><?=money(booking_amount_due($booking))?></strong>
                </div>
                <div class="booking-cost-breakdown">
                    <span>Tiền thuê <strong><?=money($booking['total_price'])?></strong></span>
                    <span>Bảo hiểm tùy chọn <strong><?=money($booking['insurance_amount'])?></strong></span>
                    <span>Tiền cọc <strong><?=money($booking['deposit_amount'])?></strong></span>
                    <span class="booking-cost-total">Tổng cần thanh toán <strong><?=money(booking_amount_due($booking))?></strong></span>
                </div>
                <div class="booking-payment-info">
                    <span class="payment-method-label">Thanh toán: <?=e(payment_method_label($booking['payment_method']))?></span>
                    <span class="status payment-status-<?=e($booking['payment_status'])?>"><?=e(payment_status_label($booking['payment_status']))?></span>
                    <span>Tiền cọc: <?=e(deposit_status_label($booking['deposit_status']))?></span>
                    <?php if(!empty($booking['paid_at'])): ?><span>Đã nhận tiền: <?=e(date('d/m/Y H:i',strtotime($booking['paid_at'])))?></span><?php endif; ?>
                </div>
                <?php if(!empty($settings['store_address'])): ?>
                    <div class="booking-map-location"><strong>Điểm nhận/trả:</strong> <?=e($settings['store_address'])?> <?php if(!empty($settings['google_maps_url'])): ?><a href="<?=e($settings['google_maps_url'])?>" target="_blank" rel="noopener noreferrer">Mở Google Maps ↗</a><?php endif; ?></div>
                <?php endif; ?>
                <?php if($booking['payment_status']==='unpaid'&&$booking['payment_method']==='qr'&&!in_array($booking['status'],['cancelled','rejected'],true)): ?>
                    <div class="booking-qr-panel">
                        <?php if($qrImage): ?>
                            <img class="booking-qr-image" src="<?=e($qrImage)?>" alt="VietQR thanh toán đơn AutoGo #<?=e($booking['id'])?>" loading="lazy">
                            <div class="booking-qr-instructions">
                                <strong>Quét QR để thanh toán</strong>
                                <span><?=e($settings['bank_account_name'])?> · <?=e($settings['bank_account'])?></span>
                                <span>Nội dung chuyển khoản: <strong>AUTOGO-<?=e($booking['id'])?></strong></span>
                                <span>Số tiền: <strong><?=money(booking_amount_due($booking))?></strong></span>
                                <form class="qr-payment-confirm" method="post" action="<?=url('/booking/payment/notify')?>">
                                    <?=csrf_field()?>
                                    <input type="hidden" name="id" value="<?=e($booking['id'])?>">
                                    <label for="payment-reference-<?=e($booking['id'])?>">Mã giao dịch <small>(nếu có)</small></label>
                                    <input id="payment-reference-<?=e($booking['id'])?>" type="text" name="payment_reference" maxlength="100" placeholder="Ví dụ: FT123456789">
                                    <button class="btn" type="submit">Báo đã chuyển khoản</button>
                                </form>
                                <small>Đơn chỉ được xác nhận thanh toán sau khi cửa hàng kiểm tra giao dịch thực nhận.</small>
                            </div>
                        <?php else: ?>
                            <div class="qr-payment-confirm"><strong>Cửa hàng chưa cấu hình VietQR.</strong><p>Vui lòng liên hệ cửa hàng để thanh toán trực tiếp.</p></div>
                        <?php endif; ?>
                    </div>
                <?php elseif(in_array($booking['status'],['cancelled','rejected'],true)&&in_array($booking['payment_status'],['unpaid','awaiting_confirmation','paid'],true)): ?>
                    <div class="payment-waiting">Đơn đã đóng. Không thanh toán thêm; nếu đã chuyển hoặc thanh toán, cửa hàng sẽ kiểm tra và xử lý hoàn tiền.</div>
                <?php elseif($booking['payment_status']==='awaiting_confirmation'): ?>
                    <div class="payment-waiting">Đã báo chuyển khoản<?php if(!empty($booking['payment_reference'])): ?> · Mã <?=e($booking['payment_reference'])?><?php endif; ?>. Cửa hàng sẽ kiểm tra giao dịch và cập nhật trạng thái.</div>
                <?php elseif($booking['payment_status']==='unpaid'&&$booking['payment_method']==='cash'&&!in_array($booking['status'],['cancelled','rejected'],true)): ?>
                    <div class="payment-waiting">Vui lòng thanh toán tổng số tiền trên bằng tiền mặt tại cửa hàng khi nhận xe.</div>
                <?php elseif($booking['payment_status']==='paid'&&$booking['deposit_status']==='held'): ?>
                    <div class="payment-waiting">Cửa hàng đang giữ <?=money($booking['deposit_amount'])?> tiền cọc. Cọc được xử lý hoàn trả sau khi hoàn tất chuyến đi và kiểm tra xe.</div>
                <?php elseif($booking['payment_status']==='refunded'): ?>
                    <div class="payment-waiting">Cửa hàng đã ghi nhận hoàn tiền cho đơn đã hủy.</div>
                <?php endif; ?>
                <?php if($booking['status']==='pending'&&$booking['payment_status']==='unpaid'): ?>
                    <form class="booking-cancel-form" method="post" action="<?=url('/booking/cancel')?>" onsubmit="return confirm('Bạn có chắc muốn hủy đơn thuê này?')">
                        <?=csrf_field()?>
                        <input type="hidden" name="id" value="<?=e($booking['id'])?>">
                        <button class="text-danger" type="submit">Hủy đơn</button>
                    </form>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <p class="filter-empty" data-filter-empty hidden>Không có đơn thuê ở trạng thái này.</p>
<?php else: ?>
    <div class="booking-empty panel"><span class="booking-empty-icon" aria-hidden="true">↗</span><h2>Bạn chưa có đơn thuê xe nào</h2><p>Chọn một chiếc xe phù hợp để bắt đầu chuyến đi tiếp theo.</p><a class="btn" href="<?=url('/cars')?>">Khám phá xe <span aria-hidden="true">→</span></a></div>
<?php endif; ?>
