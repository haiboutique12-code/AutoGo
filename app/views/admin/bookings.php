<section class="page-title"><span class="eyebrow">QUẢN LÝ VẬN HÀNH</span><h1>Đơn đặt xe</h1><p>Duyệt lịch thuê, xác nhận thanh toán và quản lý tiền cọc của từng đơn.</p></section>
<?php
$pendingBookings=count(array_filter($items,static fn($booking)=>$booking['status']==='pending'));
$awaitingPayments=count(array_filter($items,static fn($booking)=>in_array($booking['payment_status'],['unpaid','awaiting_confirmation'],true)&&!in_array($booking['status'],['cancelled','rejected'],true)));
$activeBookings=count(array_filter($items,static fn($booking)=>in_array($booking['status'],['confirmed','ongoing'],true)));
?>
<div class="admin-summary">
    <div><span>Tổng đơn thuê</span><strong><?=count($items)?></strong></div>
    <div><span>Chờ xác nhận</span><strong><?=$pendingBookings?></strong></div>
    <div><span>Đang thực hiện</span><strong><?=$activeBookings?></strong></div>
    <div><span>Chờ thanh toán</span><strong><?=$awaitingPayments?></strong></div>
</div>
<div class="admin-list-tools booking-list-tools">
    <label class="admin-search"><span class="sr-only">Tìm đơn thuê</span><input type="search" data-filter-search placeholder="Tìm mã đơn, khách hàng hoặc xe"></label>
    <select data-filter-status aria-label="Lọc đơn theo trạng thái"><option value="">Tất cả trạng thái</option><option value="pending">Chờ xác nhận</option><option value="confirmed">Đã xác nhận</option><option value="ongoing">Đang thuê</option><option value="completed">Hoàn thành</option><option value="cancelled">Đã hủy</option><option value="rejected">Từ chối</option></select>
</div>
<?php if($items): ?>
    <div class="table-wrap">
        <table class="admin-booking-table" data-filter-list>
            <thead><tr><th>Mã đơn</th><th>Khách hàng</th><th>Phương tiện</th><th>Ngày thuê</th><th>Chi phí</th><th>Thanh toán &amp; cọc</th><th>Trạng thái đơn</th></tr></thead>
            <tbody>
                <?php foreach($items as $booking): ?>
                    <tr data-filter-row data-status="<?=e($booking['status'])?>" data-search="<?=e('#'.$booking['id'].' '.$booking['customer'].' '.$booking['car_name'].' '.$booking['payment_reference'])?>">
                        <td><strong>#<?=e($booking['id'])?></strong><small class="admin-order-created"><?=e(date('d/m/Y',strtotime($booking['created_at'])))?></small></td>
                        <td><?=e($booking['customer'])?></td>
                        <td><?=e($booking['car_name'])?></td>
                        <td><?=e($booking['start_date'])?> <span class="meta-divider">→</span> <?=e($booking['end_date'])?></td>
                        <td class="admin-cost-cell">
                            Thuê <?=money($booking['total_price'])?><br>
                            Bảo hiểm <?=money($booking['insurance_amount'])?><br>
                            Cọc <?=money($booking['deposit_amount'])?><br>
                            <strong>Tổng <?=money(booking_amount_due($booking))?></strong>
                        </td>
                        <td class="admin-payment-cell">
                            <strong><?=e(payment_method_label($booking['payment_method']))?></strong>
                            <span class="status payment-status-<?=e($booking['payment_status'])?>"><?=e(payment_status_label($booking['payment_status']))?></span>
                            <span>Cọc: <?=e(deposit_status_label($booking['deposit_status']))?></span>
                            <?php if(!empty($booking['payment_reference'])): ?><small>Mã GD: <?=e($booking['payment_reference'])?></small><?php endif; ?>
                            <?php if(!empty($booking['paid_at'])): ?><small><?=e(date('d/m/Y H:i',strtotime($booking['paid_at'])))?></small><?php endif; ?>
                            <?php if($booking['payment_status']==='awaiting_confirmation'||($booking['payment_status']==='unpaid'&&!in_array($booking['status'],['cancelled','rejected'],true))): ?>
                                <form class="admin-payment-action" method="post" action="<?=url('/admin/bookings/payment')?>" onsubmit="return confirm('Chỉ xác nhận khi cửa hàng đã thực nhận đủ tiền.')">
                                    <?=csrf_field()?><input type="hidden" name="id" value="<?=e($booking['id'])?>">
                                    <button class="text-link" type="submit"><?=$booking['payment_method']==='qr'&&$booking['payment_status']==='awaiting_confirmation'?'Xác nhận tiền vào TK':'Xác nhận đã thu tiền'?></button>
                                </form>
                            <?php elseif($booking['payment_status']==='paid'&&in_array($booking['status'],['cancelled','rejected'],true)): ?>
                                <form class="admin-payment-action" method="post" action="<?=url('/admin/bookings/refund')?>" onsubmit="return confirm('Chỉ ghi nhận hoàn tiền sau khi cửa hàng đã hoàn đủ khoản tiền cho khách.')">
                                    <?=csrf_field()?><input type="hidden" name="id" value="<?=e($booking['id'])?>">
                                    <button class="text-danger" type="submit">Ghi nhận hoàn tiền</button>
                                </form>
                            <?php elseif($booking['payment_status']==='paid'&&$booking['deposit_status']==='held'&&$booking['status']==='completed'): ?>
                                <form class="admin-payment-action" method="post" action="<?=url('/admin/bookings/deposit/refund')?>" onsubmit="return confirm('Chỉ xác nhận hoàn cọc sau khi cửa hàng đã hoàn tiền cọc cho khách.')">
                                    <?=csrf_field()?><input type="hidden" name="id" value="<?=e($booking['id'])?>">
                                    <button class="text-link" type="submit">Ghi nhận hoàn cọc</button>
                                </form>
                            <?php endif; ?>
                        </td>
                        <td class="admin-status-cell">
                            <span class="status status-<?=e($booking['status'])?>"><?=e(booking_status_label($booking['status']))?></span>
                            <?php $transitions=booking_allowed_transitions($booking['status']); ?>
                            <?php if($transitions): ?>
                                <form class="admin-status-form" method="post" action="<?=url('/admin/bookings/status')?>">
                                    <?=csrf_field()?>
                                    <input type="hidden" name="id" value="<?=e($booking['id'])?>">
                                    <select name="status" aria-label="Cập nhật trạng thái đơn #<?=e($booking['id'])?>">
                                        <?php foreach($transitions as $status): ?><option value="<?=e($status)?>"><?=e(booking_status_label($status))?></option><?php endforeach; ?>
                                    </select>
                                    <button class="text-link" type="submit">Cập nhật</button>
                                </form>
                            <?php else: ?>
                                <span class="admin-completed">Đã kết thúc</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <p class="filter-empty" data-filter-empty hidden>Không tìm thấy đơn thuê phù hợp.</p>
<?php else: ?>
    <div class="booking-empty panel"><span class="booking-empty-icon" aria-hidden="true">▦</span><h2>Chưa có đơn đặt xe</h2><p>Đơn đặt mới sẽ xuất hiện tại đây để bạn quản lý.</p></div>
<?php endif; ?>
