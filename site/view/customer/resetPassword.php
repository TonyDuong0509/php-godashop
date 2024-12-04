<?php require ABSPATH_SITE . 'layout/header.php'?>
<main id="maincontent" class="page-main">
    <div class="container">
        <div class="row">
            <div class="col-xs-9">
                <ol class="breadcrumb">
                    <li><a href="/" target="_self">Trang chủ</a></li>
                    <li><span>/</span></li>
                    <li class="active"><span>Reset password</span></li>
                </ol>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-9 account">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="home-title">ResetPassword</h4>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <form class="reset-password" action="?c=customer&a=updatePassword" method="POST" role="form">
                            <input type="hidden" name="token" value="<?=$token?>">
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" name="password_confirmation"
                                    placeholder="Nhập lại mật khẩu mới">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right">Lưu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require ABSPATH_SITE . 'layout/footer.php'?>