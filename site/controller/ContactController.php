<?php
class ContactController
{
    public function form()
    {
        require ABSPATH_SITE . 'view/contact/form.php';
    }

    public function sendEmail()
    {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $msg = $_POST['content'];

        $emailService = new EmailService();
        $to = SHOP_OWNER;
        $subject = APP_NAME . " - Liên hệ";
        $domain = get_domain();
        $content = "
        Xin chào chủ cửa hàng,<br>
        Dưới đây là thông tin khách hàng liên hệ: <br>
        Tên: $fullname, <br>
        Email: $email,<br>
        Sdt: $mobile, <br>
        Nội dung: $msg<br>
        --------------------------<br>
        Email này được gởi từ trang web $domain
        ";
        $emailService->send($to, $subject, $content);
    }
}
