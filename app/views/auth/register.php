<section class="auth-page auth-register">
    <aside class="auth-aside">
        <span class="auth-aside-logo">Auto<span>Go</span><i>.</i></span>
        <span class="auth-aside-eyebrow">MỞ LỐI CHO HÀNH TRÌNH MỚI</span>
        <h2>Cứ đi theo<br>cách của bạn.</h2>
        <p>Tạo tài khoản để đặt xe trực tuyến và dễ dàng quản lý các chuyến đi sắp tới.</p>
        <div class="auth-aside-foot"><span>02</span><span>Tìm chiếc xe phù hợp với bạn.</span></div>
    </aside>
    <div class="auth-box">
        <span class="eyebrow">TẠO TÀI KHOẢN MIỄN PHÍ</span>
        <h1>Tham gia AutoGo</h1>
        <p class="auth-description">Đăng ký nhanh chóng để bắt đầu đặt xe.</p>
        <?php if(!empty($errors)): ?><div class="auth-error" role="alert"><?php foreach($errors as $error): ?><p><?=e($error)?></p><?php endforeach; ?></div><?php endif; ?>
        <form method="post" action="<?=url('/register')?>" class="auth-form">
            <?=csrf_field()?>
            <label for="register-name">Họ và tên</label>
            <input id="register-name" name="name" value="<?=e($old['name']??'')?>" placeholder="Nguyễn Văn An" autocomplete="name" maxlength="120" required>
            <label for="register-email">Địa chỉ email</label>
            <input id="register-email" type="email" name="email" value="<?=e($old['email']??'')?>" placeholder="ban@email.com" autocomplete="email" maxlength="160" required>
            <label for="register-phone">Số điện thoại <span class="optional-label">Không bắt buộc</span></label>
            <input id="register-phone" type="tel" name="phone" value="<?=e($old['phone']??'')?>" placeholder="090 123 4567" autocomplete="tel" maxlength="30">
            <label for="register-password">Mật khẩu <span class="optional-label">Ít nhất 8 ký tự</span></label>
            <div class="password-field"><input id="register-password" type="password" name="password" placeholder="Tạo mật khẩu an toàn" autocomplete="new-password" minlength="8" maxlength="72" required><button class="password-toggle" type="button" data-toggle-password="register-password" aria-label="Hiện mật khẩu">Hiện</button></div>
            <button class="btn auth-submit" type="submit">Tạo tài khoản <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-switch">Đã có tài khoản? <a href="<?=url('/login')?>">Đăng nhập</a></p>
        <a class="auth-back" href="<?=url('/cars')?>">← Xem danh sách xe</a>
    </div>
</section>
