<section class="page-title booking-page-title"><span class="eyebrow">ĐẶT XE TRỰC TUYẾN</span><h1>Lên kế hoạch cho chuyến đi</h1><p>Chọn ngày thuê và gửi yêu cầu. Bạn có thể theo dõi xác nhận trong mục đơn thuê.</p></section>
<?php if(!empty($settings['store_address'])): ?>
    <aside class="pickup-location"><span class="pickup-pin" aria-hidden="true">⌖</span><div><strong>Điểm nhận và trả xe</strong><p><?=e($settings['store_address'])?></p></div><?php if(!empty($settings['google_maps_url'])): ?><a href="<?=e($settings['google_maps_url'])?>" target="_blank" rel="noopener noreferrer">Mở Google Maps <span aria-hidden="true">↗</span></a><?php endif; ?></aside>
<?php else: ?>
    <aside class="pickup-location pickup-location-notice"><span class="pickup-pin" aria-hidden="true">⌖</span><div><strong>Điểm nhận xe</strong><p>Địa điểm nhận xe sẽ được cửa hàng thông báo sau khi xác nhận đơn.</p></div></aside>
<?php endif; ?>
<div class="booking-layout">
    <section class="panel booking-car-panel">
        <img class="booking-img" src="<?=e($car['image'])?>" alt="<?=e($car['name'])?>">
        <span class="eyebrow"><?=e($car['brand'])?> · <?=$car['type']==='motorbike'?'XE MÁY':'Ô TÔ'?></span>
        <h2><?=e($car['name'])?></h2>
        <div class="booking-facts"><span><?=e($car['seats'])?> chỗ ngồi</span><span>Thuê theo ngày</span></div>
        <p class="price"><?=money($car['price_per_day'])?><small> / ngày</small></p>
        <a class="booking-back" href="<?=url('/cars/show?id='.$car['id'])?>">← Xem chi tiết xe</a>
    </section>
    <form class="panel form booking-form" method="post" action="<?=url('/booking/store')?>" data-daily-price="<?=e($car['price_per_day'])?>" data-insurance-price="<?=e(AUTOGO_INSURANCE_PER_DAY)?>" data-deposit-amount="<?=e(AUTOGO_BOOKING_DEPOSIT)?>">
        <span class="eyebrow">THÔNG TIN ĐẶT XE</span>
        <h2>Chọn ngày thuê</h2>
        <?=csrf_field()?>
        <input type="hidden" name="car_id" value="<?=$car['id']?>">
        <label for="booking-start">Ngày nhận xe</label>
        <input id="booking-start" type="date" name="start_date" min="<?=date('Y-m-d')?>" required>
        <label for="booking-end">Ngày trả xe</label>
        <input id="booking-end" type="date" name="end_date" min="<?=date('Y-m-d')?>" required>
        <label for="booking-note">Ghi chú <span class="optional-label">Không bắt buộc</span></label>
        <textarea id="booking-note" name="note" rows="3" maxlength="500" placeholder="Địa điểm nhận xe hoặc yêu cầu thêm"></textarea>
        <label class="insurance-choice">
            <input type="checkbox" name="include_insurance" value="1" id="booking-insurance">
            <span class="insurance-copy"><strong>Thêm phí bảo hiểm xe tùy chọn</strong><small><?=money(AUTOGO_INSURANCE_PER_DAY)?>/ngày · phí dịch vụ hiển thị riêng trong đơn</small></span>
        </label>
        <p class="insurance-disclaimer">Gói bảo hiểm đang ở trạng thái tham khảo, chưa kết nối hãng bảo hiểm. Xác nhận điều kiện và phạm vi áp dụng với cửa hàng trước khi thanh toán.</p>
        <fieldset class="payment-methods">
            <legend>Phương thức thanh toán</legend>
            <label class="payment-option"><input type="radio" name="payment_method" value="cash" checked><span><strong>Tiền mặt</strong><small>Thanh toán trực tiếp khi nhận xe</small></span><span class="payment-option-icon">₫</span></label>
            <?php if($qrAvailable): ?>
                <label class="payment-option"><input type="radio" name="payment_method" value="qr"><span><strong>QR / chuyển khoản</strong><small>Quét VietQR, cửa hàng xác nhận thủ công</small></span><span class="payment-option-icon">QR</span></label>
            <?php else: ?>
                <div class="qr-not-configured">Chuyển khoản QR sẽ khả dụng sau khi cửa hàng thêm tài khoản trong mục Quản trị → Cài đặt cửa hàng.</div>
            <?php endif; ?>
        </fieldset>
        <div class="booking-price-summary" aria-live="polite">
            <span>Chi tiết chi phí dự kiến</span>
            <span>Tiền thuê <strong id="booking-rent">Chọn ngày thuê</strong></span>
            <span>Bảo hiểm tùy chọn <strong id="booking-insurance-cost">0 đ</strong></span>
            <span>Tiền cọc xe <strong><?=money(AUTOGO_BOOKING_DEPOSIT)?></strong></span>
            <span class="booking-due-label">Tổng thanh toán <strong id="booking-total">Chọn ngày thuê</strong></span>
            <small id="booking-duration">Giá thuê <?=money($car['price_per_day'])?> mỗi ngày, tính cả ngày nhận và trả. Tiền cọc sẽ được hoàn sau khi hoàn tất chuyến đi và kiểm tra xe.</small>
        </div>
        <p class="hint">Tổng thanh toán gồm tiền thuê, bảo hiểm đã chọn và cọc xe <?=money(AUTOGO_BOOKING_DEPOSIT)?>. Cọc được hoàn riêng sau khi trả xe và đối soát. Gửi đơn không đồng nghĩa thanh toán.</p>
        <button class="btn auth-submit" type="submit">Gửi yêu cầu đặt xe <span aria-hidden="true">→</span></button>
    </form>
</div>
