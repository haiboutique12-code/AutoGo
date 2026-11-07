<section class="page-title"><span class="eyebrow">CẤU HÌNH HỆ THỐNG</span><h1>Cài đặt cửa hàng</h1><p>Cập nhật điểm nhận xe và tài khoản nhận thanh toán. Thông tin ngân hàng sẽ hiển thị trên QR gửi khách.</p></section>
<div class="settings-grid">
    <form class="panel form store-settings-form" method="post" action="<?=url('/admin/settings/save')?>">
        <span class="eyebrow">ĐIỂM NHẬN XE</span>
        <h2>Địa chỉ cửa hàng</h2>
        <?=csrf_field()?>
        <label for="store-address">Địa chỉ nhận và trả xe</label>
        <textarea id="store-address" name="store_address" rows="3" maxlength="500" placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành"><?=e($settings['store_address']??'')?></textarea>
        <label for="google-maps-url">Liên kết Google Maps</label>
        <input id="google-maps-url" type="url" name="google_maps_url" value="<?=e($settings['google_maps_url']??'')?>" placeholder="https://maps.google.com/...">
        <p class="settings-help">Dùng link chia sẻ vị trí thực tế của cửa hàng. Địa chỉ sẽ được hiển thị trên trang đặt xe và lịch sử thuê.</p>

        <span class="eyebrow settings-section-eyebrow">THANH TOÁN CHUYỂN KHOẢN</span>
        <h2>Tài khoản VietQR</h2>
        <label for="bank-code">Mã ngân hàng / BIN</label>
        <input id="bank-code" name="bank_code" value="<?=e($settings['bank_code']??'')?>" maxlength="20" placeholder="Ví dụ: MB hoặc 970422">
        <label for="bank-account">Số tài khoản</label>
        <input id="bank-account" name="bank_account" inputmode="numeric" value="<?=e($settings['bank_account']??'')?>" maxlength="34" placeholder="Nhập số tài khoản nhận tiền">
        <label for="bank-account-name">Tên chủ tài khoản</label>
        <input id="bank-account-name" name="bank_account_name" value="<?=e($settings['bank_account_name']??'')?>" maxlength="100" placeholder="TÊN CHỦ TÀI KHOẢN">
        <?php if(vietqr_configured($settings)): ?>
            <div class="settings-bank-ready"><strong>QR đã được cấu hình</strong><span>Đơn chuyển khoản sẽ hiển thị QR với đúng số tiền và mã đơn riêng.</span></div>
        <?php else: ?>
            <div class="settings-bank-pending"><strong>Chưa bật QR chuyển khoản</strong><span>Hoàn tất cả ba trường ngân hàng để khách có thể quét QR. Tên tài khoản ngân hàng được hiển thị cho khách.</span></div>
        <?php endif; ?>
        <p class="settings-help">Hệ thống chỉ tạo VietQR và ghi nhận “khách đã báo chuyển khoản”; admin phải kiểm tra tài khoản nhận tiền trước khi xác nhận đã thanh toán. Không nhập mã PIN, mật khẩu ngân hàng hoặc OTP.</p>
        <button class="btn" type="submit">Lưu cài đặt cửa hàng <span aria-hidden="true">→</span></button>
    </form>

    <aside class="panel settings-info">
        <span class="eyebrow">QUY TẮC THANH TOÁN</span>
        <h2>Minh bạch từng khoản thu</h2>
        <div class="settings-info-row"><strong>Tiền cọc xe</strong><span><?=money(AUTOGO_BOOKING_DEPOSIT)?> mỗi đơn; cửa hàng ghi nhận hoàn cọc sau khi đơn hoàn thành và xe được kiểm tra.</span></div>
        <div class="settings-info-row"><strong>Bảo hiểm tùy chọn</strong><span><?=money(AUTOGO_INSURANCE_PER_DAY)?>/ngày; hiện là khoản phí tham khảo, chưa kết nối hãng bảo hiểm.</span></div>
        <div class="settings-info-row"><strong>Thanh toán QR</strong><span>Khách quét QR và báo mã giao dịch. Chỉ đánh dấu đã thanh toán khi tiền đã thực nhận.</span></div>
        <div class="settings-info-row"><strong>Tiền mặt</strong><span>Admin đánh dấu đã thu tiền sau khi nhận đủ tiền mặt tại cửa hàng.</span></div>
        <div class="settings-admin-bootstrap">
            <strong>Chưa có tài khoản admin?</strong>
            <span>Đăng ký tài khoản trước, sau đó chạy lệnh cấp quyền an toàn trong Terminal:</span>
            <code>php scripts/promote_admin.php email-cua-ban</code>
            <span>Lệnh chỉ chạy bằng PHP CLI, yêu cầu nhập email tài khoản đã đăng ký và xác nhận trực tiếp.</span>
        </div>
    </aside>
</div>
