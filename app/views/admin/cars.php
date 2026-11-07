<?php $activeCars=count(array_filter($cars,static fn($car)=>(int)$car['active']===1)); ?>
<section class="page-title"><span class="eyebrow">QUẢN LÝ ĐỘI XE</span><h1>Phương tiện</h1><p>Thêm xe mới, cập nhật thông tin hoặc tạm ẩn phương tiện khỏi danh sách thuê.</p></section>
<div class="admin-summary">
    <div><span>Tổng phương tiện</span><strong><?=count($cars)?></strong></div>
    <div><span>Đang hiển thị</span><strong><?=$activeCars?></strong></div>
    <div><span>Đang ẩn</span><strong><?=count($cars)-$activeCars?></strong></div>
</div>
<form class="panel form admin-form" method="post" action="<?=url('/admin/cars/save')?>">
    <?=csrf_field()?>
    <div class="admin-form-heading"><div><span class="eyebrow">DANH MỤC PHƯƠNG TIỆN</span><h2>Thêm hoặc cập nhật xe</h2></div><span class="admin-form-hint">Các trường có dấu * là bắt buộc</span></div>
    <input type="hidden" name="id" id="car-id">
    <div class="form-grid">
        <label>Tên xe *<input name="name" required maxlength="150"></label>
        <label>Hãng xe<input name="brand" maxlength="100"></label>
        <label>Loại phương tiện<select name="type"><option value="car">Ô tô</option><option value="motorbike">Xe máy</option></select></label>
        <label>Số chỗ<input type="number" name="seats" value="4" min="1" max="100"></label>
        <label>Giá thuê mỗi ngày *<input type="number" name="price" min="1" required></label>
        <label>URL hình ảnh<input name="image" type="url" placeholder="https://..."></label>
    </div>
    <label>Mô tả<textarea name="description" rows="3" maxlength="10000"></textarea></label>
    <div class="admin-form-actions"><label class="check"><input type="checkbox" name="active" checked> Hiển thị xe cho khách thuê</label><div><button class="btn" type="submit">Lưu phương tiện <span aria-hidden="true">→</span></button><button class="admin-form-reset" type="reset">Làm mới</button></div></div>
</form>
<section class="admin-list-section">
    <div class="admin-list-heading"><div><span class="eyebrow">KHO PHƯƠNG TIỆN</span><h2>Danh sách xe</h2></div><span class="admin-list-count"><?=count($cars)?> xe</span></div>
    <div class="admin-list-tools">
        <label class="admin-search"><span class="sr-only">Tìm xe</span><input type="search" data-filter-search placeholder="Tìm theo tên hoặc hãng xe"></label>
        <select data-filter-status aria-label="Lọc theo trạng thái hiển thị"><option value="">Tất cả trạng thái</option><option value="active">Đang hiển thị</option><option value="inactive">Đang ẩn</option></select>
    </div>
    <div class="table-wrap">
        <table class="admin-manage-table" data-filter-list>
            <thead><tr><th>Phương tiện</th><th>Loại</th><th>Giá thuê</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
            <tbody>
                <?php foreach($cars as $c): ?>
                    <tr data-filter-row data-search="<?=e($c['name'].' '.$c['brand'])?>" data-status="<?=$c['active']?'active':'inactive'?>">
                        <td><strong><?=e($c['name'])?></strong><small><?=e($c['brand']?:'Chưa cập nhật hãng')?></small></td>
                        <td><?=$c['type']==='motorbike'?'Xe máy':'Ô tô'?></td>
                        <td><strong><?=money($c['price_per_day'])?></strong><small>/ ngày</small></td>
                        <td><span class="status <?=$c['active']?'status-active':'status-inactive'?>"><?=$c['active']?'Đang hiển thị':'Đang ẩn'?></span></td>
                        <td class="admin-row-actions">
                            <button class="text-link" type="button" onclick='editCar(<?=json_encode($c,JSON_HEX_APOS|JSON_HEX_QUOT)?>)'>Sửa</button>
                            <form class="inline" method="post" action="<?=url('/admin/cars/delete')?>" onsubmit="return confirm('Xóa xe này? Các xe đã có đơn thuê sẽ không thể xóa.')"><?=csrf_field()?><input type="hidden" name="id" value="<?=$c['id']?>"><button class="text-danger">Xóa</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(!$cars): ?><tr><td colspan="5" class="table-empty-cell">Chưa có phương tiện. Hãy thêm xe bằng biểu mẫu phía trên.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="filter-empty" data-filter-empty hidden>Không có phương tiện nào khớp với bộ lọc.</p>
</section>