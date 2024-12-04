<?php
class InformationController
{
    public function paymentPolicy()
    {
        require ABSPATH_SITE . 'view/information/paymentPolicy.php';
    }

    public function returnPolicy()
    {
        require ABSPATH_SITE . 'view/information/returnPolicy.php';
    }

    public function deliveryPolicy()
    {
        require ABSPATH_SITE . 'view/information/deliveryPolicy.php';
    }
}
