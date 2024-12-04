<?php
session_start();
require 'vendor/autoload.php';

use Cocur\Slugify\Slugify;

$slugify = new Slugify();

$router = new AltoRouter();

// import config vs database
require 'config.php';
require ABSPATH . 'connectDB.php';

// import models
require ABSPATH . 'bootstrap.php';

// import controllers
require ABSPATH_SITE . 'load.php';

// map homepage
// chạy hàm index của HomeController
// home là tên route
$router->map('GET', '/', ['HomeController', 'index'], 'home');

// Trang danh sách sản phẩm
$router->map('GET', '/san-pham.html', ['ProductController', 'index'], 'product');

// Trang chi tiết sản phẩm
// /san-pham/kem-dan-rang-5.html
// slug là kem-danh-rang
// id là số 5

$router->map('GET', '/san-pham/[*:slug]-[i:id].html', function ($slug, $id) {
    $controller = new ProductController();
    call_user_func_array([$controller, 'detail'], [$id]);
}, 'productDetail');

// Trang /chinh-sach-doi-tra.html
$router->map('GET', '/chinh-sach-doi-tra.html', ['InformationController', 'returnPolicy'], 'returnPolicy');

// Trang /chinh-sach-thanh-toan.html
$router->map('GET', '/chinh-sach-thanh-toan.html', ['InformationController', 'paymentPolicy'], 'paymentPolicy');

// Trang /chinh-sach-giao-hang.html
$router->map('GET', '/chinh-sach-giao-hang.html', ['InformationController', 'deliveryPolicy'], 'deliveryPolicy');

// Trang /lien-he.html
$router->map('GET', '/lien-he.html', ['ContactController', 'form'], 'contact');

// Danh mục sản phẩm
//  /danh-muc/kem-chong-nang-3.html
$router->map('GET', '/danh-muc/[*:slug]-[i:categoryId].html', function ($slug, $categoryId) {
    call_user_func_array(['ProductController', 'index'], [$categoryId]);
}, 'category');


// Tìm kiếm
//  /search?search=kem
$router->map('GET', '/search', ['ProductController', 'index'], 'search');

// match current request url
$match = $router->match();
$routeName = $match['name'];
// call closure or throw 404 status
if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {

    $c = $_GET['c'] ?? 'home';
    $a = $_GET['a'] ?? 'index';

    $strController = ucfirst($c) . 'Controller';

    // khởi tạo đối tượng controller
    $controller = new $strController();

    // gọi hàm chạy
    $controller->$a();
}
