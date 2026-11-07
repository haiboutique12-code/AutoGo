<section class="home-hero">
    <div class="home-hero-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> THUÊ XE DỄ DÀNG CÙNG AUTOGO</span>
        <h1>Mỗi hành trình,<br><span>một lựa chọn</span><br>thật phù hợp.</h1>
        <p>Tìm chiếc xe dành riêng cho chuyến đi của bạn. Xem thông tin và giá thuê rõ ràng trước khi đặt.</p>
        <div class="hero-highlights">
            <span><span class="highlight-check">✓</span> Ô tô &amp; xe máy</span>
            <span><span class="highlight-check">✓</span> Đặt xe trực tuyến</span>
        </div>
    </div>
    <div class="home-hero-visual">
        <div class="hero-visual-shape"></div>
        <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&amp;fit=crop&amp;w=1100&amp;q=85" alt="Xe ô tô sẵn sàng cho hành trình cùng AutoGo" fetchpriority="high">
        <div class="hero-image-caption"><span class="caption-icon">↗</span><span><strong>Sẵn sàng lên đường?</strong><small>Chuyến đi của bạn bắt đầu từ đây.</small></span></div>
    </div>
</section>

<form class="home-search" method="get" action="<?=url('/cars')?>">
    <div class="search-intro"><span class="search-icon">⌕</span><span><strong>Tìm chiếc xe của bạn</strong><small>Lựa chọn từ các phương tiện đang có</small></span></div>
    <label class="search-field"><span>Từ khóa</span><input name="q" type="search" placeholder="Tên xe hoặc hãng xe"></label>
    <label class="search-field"><span>Loại phương tiện</span><select name="type"><option value="">Tất cả phương tiện</option><option value="car">Ô tô</option><option value="motorbike">Xe máy</option></select></label>
    <button class="btn search-submit" type="submit">Tìm xe <span aria-hidden="true">→</span></button>
</form>

<section class="home-section fleet-section">
    <div class="section-head">
        <div><span class="eyebrow">BẮT ĐẦU KHÁM PHÁ</span><h2>Chọn xe, mở hành trình</h2><p class="section-subtitle">Tìm phương tiện phù hợp với kế hoạch của bạn.</p></div>
        <a class="section-link" href="<?=url('/cars')?>">Khám phá tất cả <span aria-hidden="true">→</span></a>
    </div>
    <div class="category-list">
        <a class="category-card category-car" href="<?=url('/cars?type=car')?>">
            <span class="category-icon" aria-hidden="true">01</span><span><strong>Ô tô</strong><small>Thoải mái cho mọi cung đường</small></span><span class="category-arrow" aria-hidden="true">↗</span>
        </a>
        <a class="category-card category-bike" href="<?=url('/cars?type=motorbike')?>">
            <span class="category-icon" aria-hidden="true">02</span><span><strong>Xe máy</strong><small>Linh hoạt khám phá phố phường</small></span><span class="category-arrow" aria-hidden="true">↗</span>
        </a>
    </div>

    <div class="section-head featured-heading">
        <div><span class="eyebrow">GỢI Ý DÀNH CHO BẠN</span><h2>Những chiếc xe nổi bật</h2></div>
        <a class="section-link mobile-section-link" href="<?=url('/cars')?>">Xem tất cả <span aria-hidden="true">→</span></a>
    </div>
    <?php if($cars): ?>
    <div class="grid home-car-grid">
        <?php foreach($cars as $car): ?>
        <article class="car-card home-car-card">
            <a class="car-image-link" href="<?=url('/cars/show?id='.$car['id'])?>" aria-label="Xem chi tiết <?=e($car['name'])?>">
                <img src="<?=e($car['image'])?>" alt="<?=e($car['name'])?>" loading="lazy">
                <span class="car-type-label"><?=$car['type']==='motorbike'?'XE MÁY':'Ô TÔ'?></span>
            </a>
            <div class="card-body">
                <span class="car-brand"><?=e($car['brand'])?></span>
                <h3><?=e($car['name'])?></h3>
                <div class="car-card-meta"><?=e($car['seats'])?> chỗ <span class="meta-divider">·</span> Thuê theo ngày</div>
                <div class="car-card-bottom"><p class="price"><?=money($car['price_per_day'])?><small> / ngày</small></p><a class="car-arrow-link" href="<?=url('/cars/show?id='.$car['id'])?>" aria-label="Xem <?=e($car['name'])?>">→</a></div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="empty home-empty">Hiện chưa có xe khả dụng. Bạn có thể quay lại xem xe sau.</div>
    <?php endif; ?>
    <a class="btn all-cars-button" href="<?=url('/cars')?>">Xem toàn bộ xe <span aria-hidden="true">→</span></a>
</section>

<section class="journey-section">
    <div class="journey-heading"><span class="eyebrow">NHANH GỌN, RÕ RÀNG</span><h2>Thuê xe chỉ với<br><span>3 bước đơn giản.</span></h2><p>Từ lúc chọn xe đến khi gửi yêu cầu đặt, mọi thứ đều được thực hiện ngay trên AutoGo.</p><a class="btn journey-button" href="<?=url('/cars')?>">Chọn xe ngay <span aria-hidden="true">→</span></a></div>
    <div class="journey-steps">
        <article class="journey-step"><span class="step-number">01</span><div><h3>Tìm chiếc xe phù hợp</h3><p>Duyệt danh sách, tìm theo hãng hoặc chọn loại phương tiện bạn cần.</p></div><span class="step-symbol" aria-hidden="true">⌕</span></article>
        <article class="journey-step"><span class="step-number">02</span><div><h3>Chọn ngày thuê</h3><p>Xem giá theo ngày và gửi yêu cầu với thời gian nhận, trả xe mong muốn.</p></div><span class="step-symbol" aria-hidden="true">▦</span></article>
        <article class="journey-step"><span class="step-number">03</span><div><h3>Theo dõi đơn thuê</h3><p>Đăng nhập để xem trạng thái hoặc quản lý các đơn thuê của bạn.</p></div><span class="step-symbol" aria-hidden="true">✓</span></article>
    </div>
</section>

<section class="home-cta">
    <div><span class="eyebrow">HÀNH TRÌNH ĐANG CHỜ BẠN</span><h2>Sẵn sàng chọn chiếc xe tiếp theo?</h2><p>Khám phá các lựa chọn và gửi yêu cầu đặt xe trực tuyến.</p></div>
    <a class="btn cta-button" href="<?=url('/cars')?>">Khám phá xe <span aria-hidden="true">→</span></a>
</section>
