</main>
<footer class="site-footer">
    <div class="container footer-main">
        <div class="footer-brand">
            <a class="logo footer-logo" href="<?=url('/')?>">Auto<span>Go</span><i>.</i></a>
            <p>Chọn xe phù hợp. Chủ động hành trình.</p>
        </div>
        <div class="footer-nav">
            <span class="footer-label">KHÁM PHÁ</span>
            <a href="<?=url('/cars')?>">Tất cả phương tiện</a>
            <a href="<?=url('/cars?type=car')?>">Ô tô</a>
            <a href="<?=url('/cars?type=motorbike')?>">Xe máy</a>
        </div>
        <div class="footer-nav">
            <span class="footer-label">TÀI KHOẢN</span>
            <?php if(!empty($_SESSION['user'])): ?>
                <a href="<?=url('/my-bookings')?>">Đơn thuê của tôi</a>
            <?php else: ?>
                <a href="<?=url('/login')?>">Đăng nhập</a>
                <a href="<?=url('/register')?>">Tạo tài khoản</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="container footer-bottom"><span>© <?=date('Y')?> AutoGo</span><span>Thuê xe trực tuyến, hành trình chủ động.</span></div>
</footer>
<script src="<?=url('/assets/js/app.js')?>" defer></script>
</body>
</html>
