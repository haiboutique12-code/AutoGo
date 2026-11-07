<section class="auth-page">
    <aside class="auth-aside">
        <span class="auth-aside-logo">Auto<span>Go</span><i>.</i></span>
        <span class="auth-aside-eyebrow">MỖI CHUYẾN ĐI, MỘT CÂU CHUYỆN</span>
        <h2>Chuyến đi tiếp theo<br>bắt đầu từ đây.</h2>
        <p>Đăng nhập để đặt chiếc xe phù hợp và theo dõi hành trình thuê xe của bạn.</p>
        <div class="auth-aside-foot"><span>01</span><span>Chọn xe. Chọn ngày. Sẵn sàng lên đường.</span></div>
    </aside>
    <div class="auth-box">
        <span class="eyebrow">CHÀO MỪNG TRỞ LẠI</span>
        <h1>Đăng nhập</h1>
        <p class="auth-description">Nhập thông tin tài khoản để tiếp tục.</p>
        <?php if(!empty($errors)): ?><div class="auth-error" role="alert"><?php foreach($errors as $error): ?><p><?=e($error)?></p><?php endforeach; ?></div><?php endif; ?>
        <form method="post" action="<?=url('/login')?>" class="auth-form">
            <?=csrf_field()?>
            <label for="login-email">Địa chỉ email</label>
            <input id="login-email" type="email" name="email" value="<?=e($old['email']??'')?>" placeholder="ban@email.com" autocomplete="email" maxlength="160" required>
            <div class="password-label"><label for="login-password">Mật khẩu</label><a href="<?=url('/register')?>">Tạo tài khoản mới</a></div>
            <div class="password-field"><input id="login-password" type="password" name="password" placeholder="Nhập mật khẩu" autocomplete="current-password" required><button class="password-toggle" type="button" data-toggle-password="login-password" aria-label="Hiện mật khẩu">Hiện</button></div>
            <button class="btn auth-submit" type="submit">Đăng nhập <span aria-hidden="true">→</span></button>
        </form>
        <p class="auth-switch">Chưa có tài khoản? <a href="<?=url('/register')?>">Đăng ký miễn phí</a></p>
        <a class="auth-back" href="<?=url('/cars')?>">← Xem danh sách xe</a>
    </div>
</section>
