<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}
$data = [
    'title' => 'Reset password'
];
layout('header-auth', $data);

//lay ra token
$filterGet = filterData('GET');
if (!empty($filterGet['token'])) {
    $tokenReset = $filterGet['token'];
}

if (!empty($tokenReset)) {
    //check xem token co chinh xac hay khong
    $checkToken = getOnce("select * from users where forget_token = '$tokenReset'");

    if (!empty($checkToken)) {
        if (isPost()) {
            $filter = filterData();
            $errors = [];
            //validate pass
            //validate PW > 6 ki tu
            if (empty(trim($filter['password']))) {
                $errors['password']['required'] = 'PW khong duoc de trong';
            } else {
                if (strlen(trim($filter['password'])) < 6) {
                    $errors['password']['length'] = 'Mat khau phai lon hon 6 ky tu';
                }
            }

            if (empty(trim($filter['confirm_pass']))) {
                $errors['confirm_pass']['required'] = 'Confirm PW khong duoc de trong';
            } else {
                if (trim($filter['confirm_pass']) !== trim($filter['password'])) {
                    $errors['confirm_pass']['like'] = 'Confirm password khong giong voi password';
                }
            }
            // Xu ly trong truong hop khong co loi
            if (empty($errors)) {
                $passwordHash = password_hash($filter['password'], PASSWORD_DEFAULT);
                $data = [
                    'password' => $passwordHash,
                    'forget_token' => null,
                    'updated_at' => date('Y:m:d H:i:s')
                ];
                $condition = "id = '{$checkToken['id']}'";
                $rel = update('users', $data, $condition);

                if ($rel) {
                    //Gui mail thong bao cho nguoi dung
                    $emailTo = $checkToken['email'];
                    $subject = 'Đổi mật khẩu tài khoản hệ thống Tungbm thành công';
                    $content = 'Chúc mừng bạn đã đổi mật khẩu thành công trên hệ thống Tungbm. <br>';
                    $content .= 'Nếu không phải bạn thao tác đổi mật khẩu thì hãy liên hệ ngay với admin <br>';
                    $content .= 'Cảm ơn các bạn đã ủng hộ Tungbm';

                    // gui email
                    sendMail($emailTo, $subject, $content);

                    setSessionFlash('msg', 'Đã thay đổi password thành công. Vui lòng check mail.');
                    setSessionFlash('msg_type', 'success');

                }else{
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại.');
                    setSessionFlash('msg_type', 'danger');
                }
            } else {
                setSessionFlash('errors', $errors);
                setSessionFlash('oldData', $filter);
                setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
                setSessionFlash('msg_type', 'danger');
            }
        }
    } else {
        getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
    }
} else {
    getMsg('Liên kết đã hết hạn hoặc không tồn tại', 'danger');
}


$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');

?>

<section class="vh-100">
    <div class="container-fluid h-custom">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-9 col-lg-6 col-xl-5">
                <img src="<?php echo _HOST_URL_TEMPLATE; ?>/assets/image/draw2.webp"
                    class="img-fluid" alt="Sample image">
            </div>
            <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                <?php if (!empty($msg) && !empty($msg_type)) {
                    getMsg($msg, $msg_type);
                }  ?>
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class=" fw-normal mb-3 me-3">Đặt lại mật khẩu</h2>

                    </div>

                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" name="password" class="form-control form-control-lg"
                            placeholder="Password mới" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'password');
                        } ?>

                    </div>
                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" name="confirm_pass" class="form-control form-control-lg"
                            placeholder="Nhập lại password confirm" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'confirm_pass');
                        } ?>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Submit</button>
                    </div>
                       <p style="margin-top: 15px;"><a href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger">Đăng nhập</a></p>

                </form>
            </div>
        </div>
    </div>

</section>

<?php
layout('footer');
?>