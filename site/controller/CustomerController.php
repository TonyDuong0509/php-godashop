<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class CustomerController
{
    // Hiển thị thông tin tài khoản
    public function show()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'view/customer/show.php';
    }

    public function updateInfo()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);

        // update customer
        $customer->setName($_POST['fullname']);
        $customer->setMobile($_POST['mobile']);

        // Kiểm tra người dùng có muốn đổi mật khẩu không
        $current_password = $_POST['current_password'];
        $new_password = $_POST['password'];
        if ($current_password && $new_password) {
            // check mật khẩu hiển tại có trùng với database không
            if (!password_verify($current_password, $customer->getPassword())) {
                $_SESSION['error'] = 'Sai mật khẩu hiện tại';
                header('location: ?c=customer&a=show');
                exit;
            }

            // Đúng password
            // Mã hóa mật khẩu mới
            $hash_new_password = password_hash($new_password, PASSWORD_BCRYPT);

            // cập nhật mói password vào customer
            $customer->setPassword($hash_new_password);
        }

        //update customer xuống database
        if (!$customerRepository->update($customer)) {
            $_SESSION['error'] = $customerRepository->getError();
            header('location: ?c=customer&a=show');
            exit;
        }
        // update session
        $_SESSION['name'] = $customer->getName();

        $_SESSION['success'] = 'Đã cập nhật thông tin tài khoản thành công';
        header('location: ?c=customer&a=show');

    }

    // Hiển thị địa chỉ giao hàng mặc định
    public function shippingDefault()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE . 'layout/variable_address.php';
        require ABSPATH_SITE . 'view/customer/shippingDefault.php';
    }

    public function updateShippingDefault()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer->setShippingName($_POST['fullname']);
        $customer->setShippingMobile($_POST['mobile']);
        $customer->setWardId($_POST['ward']);
        $customer->setHousenumberStreet($_POST['address']);

        //update customer xuống database
        if (!$customerRepository->update($customer)) {
            $_SESSION['error'] = $customerRepository->getError();
            header('location: ?c=customer&a=shippingDefault');
            exit;
        }

        $_SESSION['success'] = 'Đã cập nhật địa chỉ giao hàng mặc định';
        header('location: ?c=customer&a=shippingDefault');

    }

    // Hiển thị danh sách đơn hàng
    public function orders()
    {
        $email = $_SESSION['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $orderRepository = new OrderRepository();
        $orders = $orderRepository->getByCustomerId($customer->getId());
        require ABSPATH_SITE . 'view/customer/orders.php';
    }

    // Hiển thị chi tiét đơn hàng
    public function orderDetail()
    {
        $id = $_GET['id'];
        $orderRepository = new OrderRepository();
        $order = $orderRepository->find($id);
        require ABSPATH_SITE . 'view/customer/orderDetail.php';
    }

    public function notExistingEmail()
    {
        // nếu email đã tồn tại trong hệ thống tthi2echo false;
        // ngược lại echo true
        $email = $_GET['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (!empty($customer)) {
            echo 'false';
            return;
        }
        echo 'true';
    }

    public function register()
    {
        var_dump($_POST);
        // kiểm tra token của google recaptcha từ trình duyệt web gởi lên server
        $secret = GOOGLE_RECAPTCHA_SECRET;
        $recaptcha = new \ReCaptcha\ReCaptcha($secret);
        $hostname = get_host_name();
        $gRecaptchaResponse = $_POST['g-recaptcha-response'];
        $remoteIp = "127.0.0.1";
        $resp = $recaptcha->setExpectedHostname($hostname)
            ->verify($gRecaptchaResponse, $remoteIp);
        if (!$resp->isSuccess()) {
            // Verified!
            // $errors = $resp->getErrorCodes();
            $_SESSION['error'] = 'Xác thực google recaptcha thất bại!';
            header('location: /');
            exit;
        }

        // Tạo tài khoản và lưu xuống database
        $data = [];
        $data["name"] = $_POST['fullname'];
        $data["mobile"] = $_POST['mobile'];
        $data["email"] = $_POST['email'];
        $data["login_by"] = "form";
        $data["shipping_name"] = $_POST['fullname'];
        $data["shipping_mobile"] = $_POST['mobile'];
        $data["ward_id"] = null;
        $data["is_active"] = 0;
        $data["housenumber_street"] = null;

        // Mã hóa mật khẩu trước khi lưu
        $data["password"] = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $customerRepository = new CustomerRepository();
        $customerRepository->save($data);

        // Gởi mail xác thực tài khoản
        $emailService = new EmailService();
        $to = $_POST['email'];
        $subject = 'Godashop - Verify your email';
        $name = $data["name"];
        $website = get_domain();
        // $token chứa thông tin email, nhưng người không cách nào sửa được
        // mã hóa email
        $key = JWT_KEY;
        $payload = ['email' => $to];
        $token = JWT::encode($payload, $key, 'HS256');

        $linkActiveAccount = '<a href="http://godashop.com/site?c=customer&a=active&token=' . $token . '">Active Account</a>';
        $content = "
        Xin chào $name, <br>
        Vui lòng click vào link bên dưới để active account <br>
        $linkActiveAccount <br>
        ----------------------<br>
        Được gởi từ $website
        ";
        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = 'Đã tạo tài khoản thành công. Vui lòng check email để kích hoạt tài khoản';
        header('location: /');
    }

    public function active()
    {
        // giải mã token để lấy email
        $token = $_GET['token'];
        $key = JWT_KEY;
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $email = $decoded->email;
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $customer->setIsActive(1);
        $customerRepository->update($customer);
        $_SESSION['success'] = 'Đã kích hoạt tài khoản thành công';
        header('location: /');
    }

    public function test1()
    {
        // mã hóa email
        $key = 'con gà chuẩn bị luộc';
        $payload = ['email' => 'abc@gmail.com'];
        $jwt = JWT::encode($payload, $key, 'HS256');
        echo $jwt;
    }

    public function test2()
    {
        // giả mã token -> email
        $key = 'con gà chuẩn bị luộc';
        $jwt = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImFiY0BnbWFpbC5jb20ifQ.vF8rUP-pWP7OY0aZbhiu6bjIRsnhnQxZ9ip2J3ubi38';
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        print_r($decoded);
    }

    public function forgotPassword()
    {
        $email = $_POST['email'];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if (empty($customer)) {
            $_SESSION['error'] = 'Email không tồn tại';
            header('location: /');
            exit;
        }

        // Gởi mail reset password
        $emailService = new EmailService();
        $to = $_POST['email'];
        $subject = 'Godashop - Reset password';
        $name = $customer->getName();
        $website = get_domain();
        // $token chứa thông tin email, nhưng người không cách nào sửa được
        // mã hóa email
        $key = JWT_KEY;
        $payload = ['email' => $to];
        $token = JWT::encode($payload, $key, 'HS256');

        $linkResetPassword = '<a href="http://godashop.com/site?c=customer&a=resetPassword&token=' . $token . '">Reset Password</a>';
        $content = "
        Xin chào $name, <br>
        Vui lòng click vào link bên dưới để reset password <br>
        $linkResetPassword <br>
        ----------------------<br>
        Được gởi từ $website
        ";
        $emailService->send($to, $subject, $content);

        $_SESSION['success'] = 'Vui lòng check email để reset password';
        header('location: /');
    }

    public function resetPassword()
    {
        $token = $_GET['token'];
        require ABSPATH_SITE . 'view/customer/resetPassword.php';
    }

    public function updatePassword()
    {
        // giải mã token để lấy email
        $token = $_POST['token'];
        $key = JWT_KEY;
        $decoded = JWT::decode($token, new Key($key, 'HS256'));
        $email = $decoded->email;
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        // Mã hóa mật khẩu trước khi lưu
        $encode_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $customer->setPassword($encode_password);
        $customerRepository->update($customer);
        $_SESSION['success'] = 'Đã reset password thành công';
        header('location: /');
    }

}
