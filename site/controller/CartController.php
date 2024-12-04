<?php
class CartController
{
    public $cartStorage;

    public function __construct()
    {
        $this->cartStorage = new CartStorage();
    }

    public function display()
    {
        $cart = $this->cartStorage->fetch();

        // hàm chuyển từ array sang chuỗi json.
        $json = json_encode($cart->convertToArray());

        // $json = '{"total_product_number": 5, "total_price": 400000, "items": []}';
        echo $json;
    }

    public function add()
    {
        $product_id = $_GET['product_id'];
        $qty = $_GET['qty'];
        $cart = $this->cartStorage->fetch();
        $cart->addProduct($product_id, $qty);
        // lưu giỏ hàng lại để mua lần sau
        $this->cartStorage->store($cart);
        // gởi dữ liệu về trình duyệt để cập nhật giao diện giỏ hàng
        $json = json_encode($cart->convertToArray());
        echo $json;
    }

    public function delete()
    {
        $product_id = $_GET['product_id'];
        $cart = $this->cartStorage->fetch();
        $cart->deleteProduct($product_id);
        // lưu giỏ hàng lại để mua lần sau
        $this->cartStorage->store($cart);
        // gởi dữ liệu về trình duyệt để cập nhật giao diện giỏ hàng
        $json = json_encode($cart->convertToArray());
        echo $json;
    }

    public function update()
    {
        $product_id = $_GET['product_id'];
        $qty = $_GET['qty'];
        $cart = $this->cartStorage->fetch();
        $cart->deleteProduct($product_id);
        $cart->addProduct($product_id, $qty);
        // lưu giỏ hàng lại để mua lần sau
        $this->cartStorage->store($cart);
        // gởi dữ liệu về trình duyệt để cập nhật giao diện giỏ hàng
        $json = json_encode($cart->convertToArray());
        echo $json;
    }
}
