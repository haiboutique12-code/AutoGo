<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f7f9fc">
    <meta name="description" content="Tìm và thuê ô tô, xe máy trực tuyến tại AutoGo. Xem thông tin, giá thuê và quản lý đơn thuê dễ dàng.">
    <title>AutoGo – Thuê xe cho hành trình của bạn</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?=url('/assets/css/style.css')?>">
    <link rel="stylesheet" href="<?=url('/assets/css/home.css')?>">
</head>
<body>
<header class="topbar">
    <a class="logo" href="<?=url('/')?>" aria-label="AutoGo – Trang chủ">Auto<span>Go</span><i>.</i></a>
    <button class="menu-toggle" type="button" aria-label="Mở menu" aria-expanded="false" aria-controls="main-nav"><span></span><span></span><span></span></button>
    <nav id="main-nav">
        <a href="<?=url('/')?>">Trang chủ</a>
        <a href="<?=url('/cars')?>">Thuê xe</a>
        <?php if(!empty($_SESSION['user'])): ?>
            <a href="<?=url('/my-bookings')?>">Đơn thuê</a>
            <?php if($_SESSION['user']['role']==='admin'): ?><a href="<?=url('/admin')?>">Quản trị</a><a href="<?=url('/admin/settings')?>">Cài đặt</a><?php endif; ?>
            <span class="welcome">Xin chào, <?=e($_SESSION['user']['name'])?></span>
            <form class="inline" method="post" action="<?=url('/logout')?>"><?=csrf_field()?> <button class="link-btn">Đăng xuất</button></form>
        <?php else: ?>
            <a href="<?=url('/login')?>">Đăng nhập</a>
            <a class="nav-cta" href="<?=url('/register')?>">Tạo tài khoản <span aria-hidden="true">→</span></a>
        <?php endif; ?>
    </nav>
</header>
<?php if(!empty($_SESSION['user'])&&$_SESSION['user']['role']==='admin'): ?>
    <div class="admin-nav-wrap">
        <nav class="admin-nav" aria-label="Điều hướng quản trị">
            <span class="admin-nav-label">QUẢN TRỊ</span>
            <a href="<?=url('/admin')?>">Tổng quan</a>
            <a href="<?=url('/admin/bookings')?>">Đơn thuê</a>
            <a href="<?=url('/admin/cars')?>">Phương tiện</a>
            <a href="<?=url('/admin/users')?>">Khách hàng</a>
            <a href="<?=url('/admin/settings')?>">Cài đặt cửa hàng</a>
        </nav>
    </div>
<?php endif; ?>
<main class="container">
<?php foreach(['success','error'] as $type): if($msg=flash($type)): ?><div class="alert <?=$type?>"><?=e($msg)?></div><?php endif; endforeach; ?>
