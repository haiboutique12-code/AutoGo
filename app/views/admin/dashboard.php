<section class="page-title"><span class="eyebrow">AUTOGO CONTROL CENTER</span><h1>Tổng quan kinh doanh</h1><p>Doanh thu, thanh toán và tình hình hoạt động của cửa hàng.</p></section>
<div class="stats stats-finance">
    <div class="stat"><span>Doanh thu ghi nhận</span><strong><?=money($stats['revenue'])?></strong><small>Đơn đã thanh toán; không tính tiền cọc</small></div>
    <div class="stat"><span>Tổng tiền đã thu</span><strong><?=money($stats['payments_received'])?></strong><small>Gồm các khoản cọc đang giữ</small></div>
    <div class="stat"><span>Đơn chờ thanh toán</span><strong><?=e($stats['unpaid_orders'])?></strong><small>Còn phải thu <?=money($stats['outstanding'])?></small></div>
    <div class="stat"><span>Tiền cọc đang giữ</span><strong><?=money($stats['deposits_held'])?></strong><small>Chưa hoàn trả cho khách</small></div>
    <div class="stat"><span>Tổng đơn thuê</span><strong><?=e($stats['orders'])?></strong></div>
    <div class="stat"><span>Khách hàng</span><strong><?=e($stats['customers'])?></strong></div>
</div>
<section class="panel revenue-panel">
    <div class="revenue-panel-heading"><div><span class="eyebrow">BÁO CÁO TÀI CHÍNH</span><h2>Doanh thu 6 tháng gần nhất</h2><p>Ghi nhận tiền thuê và phí bảo hiểm sau khi admin xác nhận đã nhận tiền; không tính tiền cọc hoàn lại.</p></div><a class="revenue-orders-link" href="<?=url('/admin/bookings')?>">Đối soát đơn <span aria-hidden="true">→</span></a></div>
    <?php $maximumRevenue=max(array_column($stats['monthly_revenues'],'revenue')?:[0]); ?>
    <div class="revenue-chart" aria-label="Biểu đồ doanh thu 6 tháng">
        <?php foreach($stats['monthly_revenues'] as $month): ?>
            <?php $height=$maximumRevenue>0?max(3,(int)round($month['revenue']/$maximumRevenue*100)):3; ?>
            <div class="revenue-month">
                <strong><?=money($month['revenue'])?></strong>
                <div class="revenue-bar-track"><span class="revenue-bar" style="height:<?=$height?>%" title="<?=e($month['label'])?>: <?=e(money($month['revenue']))?>"></span></div>
                <span><?=e($month['label'])?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if($stats['refunded']>0): ?><p class="revenue-refund-note">Đơn hủy/từ chối đã hoàn tiền: <?=money($stats['refunded'])?></p><?php endif; ?>
</section>
<div class="admin-links admin-links-finance">
    <a class="panel" href="<?=url('/admin/cars')?>">Quản lý phương tiện <span>Thêm, sửa và ẩn xe khỏi danh sách →</span></a>
    <a class="panel" href="<?=url('/admin/bookings')?>">Đơn đặt &amp; thanh toán <span>Duyệt đơn, xác nhận chuyển khoản, thu và hoàn cọc →</span></a>
    <a class="panel" href="<?=url('/admin/users')?>">Tài khoản khách hàng <span>Xem danh sách người thuê xe →</span></a>
    <a class="panel" href="<?=url('/admin/settings')?>">Cài đặt cửa hàng <span>Địa chỉ Google Maps và tài khoản VietQR →</span></a>
</div>
