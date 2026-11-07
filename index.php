<?php
require_once __DIR__ . '/app/core/Router.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/core/Controller.php';
require_once __DIR__ . '/app/core/Model.php';
require_once __DIR__ . '/app/helpers.php';

spl_autoload_register(function ($class) {
    foreach (['app/controllers/', 'app/models/'] as $dir) {
        $file = __DIR__ . '/' . $dir . $class . '.php';
        if (is_file($file)) require_once $file;
    }
});

session_start();
require_once __DIR__ . '/app/helpers.php';
verify_csrf();
$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['timezone']);

$router = new Router();
$router->get('/', 'HomeController@index');
$router->get('/cars', 'CarController@index');
$router->get('/cars/show', 'CarController@show');
$router->get('/register', 'AuthController@registerForm');
$router->post('/register', 'AuthController@register');
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');
$router->get('/booking/create', 'BookingController@create');
$router->post('/booking/store', 'BookingController@store');
$router->post('/booking/payment/notify', 'BookingController@submitPayment');
$router->get('/my-bookings', 'BookingController@mine');
$router->post('/booking/cancel', 'BookingController@cancel');
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/cars', 'AdminController@cars');
$router->post('/admin/cars/save', 'AdminController@saveCar');
$router->post('/admin/cars/delete', 'AdminController@deleteCar');
$router->get('/admin/bookings', 'AdminController@bookings');
$router->post('/admin/bookings/status', 'AdminController@updateBooking');
$router->post('/admin/bookings/payment', 'AdminController@updatePayment');
$router->post('/admin/bookings/refund', 'AdminController@refundPayment');
$router->post('/admin/bookings/deposit/refund', 'AdminController@refundDeposit');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/settings', 'AdminController@settings');
$router->post('/admin/settings/save', 'AdminController@saveSettings');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$basePath = '/AutoGo';

if (strpos($path, $basePath) === 0) {
    $path = substr($path, strlen($basePath));
}

$path = $path ?: '/';

$router->dispatch($_SERVER['REQUEST_METHOD'], $path);
