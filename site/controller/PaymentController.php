<?php
class PaymentController
{
    public function checkout()
    {
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        if ($cart->getTotalProductNumber() == 0) {
            //giỏ hàng empty
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            header('location: ?c=product');
            exit;
        }
        $email = 'khachvanglai@gmail.com';
        if (!empty($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/payment/checkout.php';
    }

    public function order()
    {
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        if ($cart->getTotalProductNumber() == 0) {
            //giỏ hàng empty
            $_SESSION['error'] = 'Giỏ hàng rỗng';
            header('location: ?c=product');
            exit;
        }
        $email = 'khachvanglai@gmail.com';
        if (!empty($_SESSION['email'])) {
            $email = $_SESSION['email'];
        }

        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        $data = [];
        $data["created_date"] = date('Y-m-d H:i:s');
        $data["order_status_id"] = 1; //đã đặt hàng
        $data["staff_id"] = null;
        $data["customer_id"] = $customer->getId();
        $data["shipping_fullname"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["payment_method"] = $_POST['payment_method'];
        $data["shipping_ward_id"] = $_POST['ward'];
        $data["shipping_housenumber_street"] = $_POST['address'];

        $provinceRepository = new ProvinceRepository();
        $pronvince = $provinceRepository->find($_POST['province']);
        $shippingFee = $pronvince->getShippingFee();
        $data["shipping_fee"] = $shippingFee;
        // 3 ngày sau sẽ giao hàng
        $data["delivered_date"] = date('Y-m-d H:i:s', strtotime('+3 days'));

        $orderRepository = new OrderRepository();
        $order_id = $orderRepository->save($data);
        if (!$order_id) {
            $_SESSION['error'] = $orderRepository->getError();
            header('location: /');
            exit;
        }

        //Lưu order item
        $orderItemRepository = new OrderItemRepository();
        foreach ($cart->getItems() as $item) {
            $dataItem = [];
            $dataItem["product_id"] = $item['product_id'];
            $dataItem["order_id"] = $order_id;
            $dataItem["qty"] = $item['qty'];
            $dataItem["unit_price"] = $item['unit_price'];
            $dataItem["total_price"] = $item['total_price'];
            $orderItemRepository->save($dataItem);
        }
        //xóa giỏ hàng
        $cartStorage->clear();

        $_SESSION['success'] = 'Đơn hàng đã được tạo thành công';
        header('location: /');

    }
}
