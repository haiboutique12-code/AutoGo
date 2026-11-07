<section class="page-title"><span class="eyebrow">QUẢN LÝ TÀI KHOẢN</span><h1>Khách hàng</h1><p>Danh sách tài khoản đã đăng ký trên hệ thống AutoGo.</p></section>
<?php $customerCount=count(array_filter($users,static fn($user)=>$user['role']==='customer')); ?>
<div class="admin-summary">
    <div><span>Tổng tài khoản</span><strong><?=count($users)?></strong></div>
    <div><span>Khách hàng</span><strong><?=$customerCount?></strong></div>
    <div><span>Quản trị viên</span><strong><?=count($users)-$customerCount?></strong></div>
</div>
<div class="admin-list-tools">
    <label class="admin-search"><span class="sr-only">Tìm khách hàng</span><input type="search" data-filter-search placeholder="Tìm theo tên, email hoặc số điện thoại"></label>
    <select data-filter-status aria-label="Lọc theo vai trò"><option value="">Tất cả vai trò</option><option value="customer">Khách hàng</option><option value="admin">Quản trị viên</option></select>
</div>
<?php if($users): ?>
    <div class="table-wrap">
        <table class="admin-manage-table" data-filter-list>
            <thead><tr><th>Khách hàng</th><th>Email</th><th>Điện thoại</th><th>Vai trò</th><th>Ngày tham gia</th></tr></thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr data-filter-row data-search="<?=e($user['name'].' '.$user['email'].' '.$user['phone'])?>" data-status="<?=e($user['role'])?>">
                        <td><strong><?=e($user['name'])?></strong></td>
                        <td><?=e($user['email'])?></td>
                        <td><?=e($user['phone']?:'Chưa cập nhật')?></td>
                        <td><span class="status <?=$user['role']==='admin'?'status-confirmed':''?>"><?=$user['role']==='admin'?'Quản trị viên':'Khách hàng'?></span></td>
                        <td><?=e(date('d/m/Y',strtotime($user['created_at'])))?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="filter-empty" data-filter-empty hidden>Không tìm thấy tài khoản phù hợp.</p>
<?php else: ?>
    <div class="booking-empty panel"><span class="booking-empty-icon" aria-hidden="true">01</span><h2>Chưa có tài khoản khách hàng</h2><p>Tài khoản đăng ký mới sẽ xuất hiện tại đây.</p></div>
<?php endif; ?>
