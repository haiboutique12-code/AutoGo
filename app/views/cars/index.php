<section class="page-title fleet-page-title">
    <span class="eyebrow">DANH SÁCH PHƯƠNG TIỆN</span>
    <h1>Tìm chiếc xe phù hợp</h1>
    <p>Lọc và lựa chọn phương tiện cho chuyến đi của bạn.</p>
</section>
<form class="fleet-filter" method="get" action="<?=url('/cars')?>">
    <label class="fleet-search-field">
        <span>Từ khóa</span>
        <input name="q" type="search" value="<?=e($q)?>" placeholder="Tìm theo tên hoặc hãng xe">
    </label>
    <label class="fleet-type-field">
        <span>Loại phương tiện</span>
        <select name="type">
            <option value="">Tất cả phương tiện</option>
            <option value="car" <?=$type==='car'?'selected':''?>>Ô tô</option>
            <option value="motorbike" <?=$type==='motorbike'?'selected':''?>>Xe máy</option>
        </select>
    </label>
    <button class="btn" type="submit">Tìm kiếm <span aria-hidden="true">→</span></button>
</form>
<div class="fleet-results">
    <p><strong><?=count($cars)?></strong> phương tiện phù hợp</p>
    <?php if((isset($_GET['q'])&&is_string($_GET['q'])&&trim($_GET['q'])!=='')||(isset($_GET['type'])&&is_string($_GET['type'])&&trim($_GET['type'])!=='')): ?><a href="<?=url('/cars')?>">Xóa bộ lọc <span aria-hidden="true">×</span></a><?php endif; ?>
</div>
<?php if($cars): ?>
    <div class="grid fleet-grid">
        <?php foreach($cars as $car): ?>
            <article class="car-card fleet-card">
                <a class="fleet-card-image" href="<?=url('/cars/show?id='.$car['id'])?>" aria-label="Xem chi tiết <?=e($car['name'])?>">
                    <img src="<?=e($car['image'])?>" alt="<?=e($car['name'])?>" loading="lazy">
                    <span class="car-type-label"><?=$car['type']==='motorbike'?'XE MÁY':'Ô TÔ'?></span>
                </a>
                <div class="card-body">
                    <span class="car-brand"><?=e($car['brand'])?></span>
                    <h2><?=e($car['name'])?></h2>
                    <div class="car-card-meta"><?=e($car['seats'])?> chỗ <span class="meta-divider">·</span> Thuê theo ngày</div>
                    <div class="car-card-bottom">
                        <p class="price"><?=money($car['price_per_day'])?><small> / ngày</small></p>
                        <a class="btn btn-light" href="<?=url('/cars/show?id='.$car['id'])?>">Xem chi tiết <span aria-hidden="true">→</span></a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="fleet-empty panel">
        <span class="booking-empty-icon" aria-hidden="true">⌕</span>
        <h2>Chưa tìm thấy xe phù hợp</h2>
        <p>Thử đổi từ khóa hoặc loại phương tiện để xem thêm lựa chọn.</p>
        <a class="btn" href="<?=url('/cars')?>">Xem tất cả phương tiện</a>
    </div>
<?php endif; ?>