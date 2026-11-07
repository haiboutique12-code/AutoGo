<a class="detail-back-link" href="<?=url('/cars')?>">← Quay lại danh sách xe</a>
<section class="detail detail-page">
    <div class="detail-image-wrap">
        <img src="<?=e($car['image'])?>" alt="<?=e($car['name'])?>">
        <span class="car-type-label"><?=$car['type']==='motorbike'?'XE MÁY':'Ô TÔ'?></span>
    </div>
    <div class="detail-copy">
        <span class="eyebrow"><?=e($car['brand'])?></span>
        <h1><?=e($car['name'])?></h1>
        <p class="detail-description"><?=e($car['description']?:'Thông tin chi tiết về phương tiện sẽ được cửa hàng cập nhật.')?></p>
        <div class="specs">
            <span>Loại phương tiện<strong><?=$car['type']==='car'?'Ô tô':'Xe máy'?></strong></span>
            <span>Số chỗ<strong><?=e($car['seats'])?></strong></span>
            <span>Thời gian thuê<strong>Theo ngày</strong></span>
        </div>
        <div class="detail-price-panel">
            <span>Giá thuê tham khảo</span>
            <strong><?=money($car['price_per_day'])?><small> / ngày</small></strong>
            <p>Chi phí cuối cùng được tính theo số ngày thuê và các lựa chọn trong bước đặt xe.</p>
        </div>
        <a class="btn detail-book-button" href="<?=url('/booking/create?car_id='.$car['id'])?>">Chọn xe này <span aria-hidden="true">→</span></a>
        <p class="detail-booking-note">Bạn sẽ chọn ngày thuê và xem chi tiết tiền thuê, bảo hiểm tùy chọn và tiền cọc trước khi gửi yêu cầu.</p>
    </div>
</section>