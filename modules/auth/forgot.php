<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}
$data = [
    'title' => 'Forgot password'
];
layout('header-auth', $data);

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'email ko duoc rong';
    } else {
        //check xem dung dinh dang mail, da ton tai trong CSDL hay chua
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email ko dung dinh dang';
        }
    }

    if (empty($errors)) {
        //Xu ly va gui mail
        if (!empty($filter['email'])) {
            $email = $filter['email'];

            $checkEmail = getOnce("select * from users where email = '$email'");
            if (!empty($checkEmail)) {
                // Update forgot_token vao bang users
                $forgetToken = sha1(uniqid() . time());
                $data = [
                    'forget_token' => $forgetToken
                ];
                $user_id = $checkEmail['id'];
                $updateStatus = update('users', $data, "id = '$user_id'");
                if ($updateStatus) {
                    $emailTo = $email;
                    $subject = 'Reset mật khẩu tài khoản hệ thống Tungbm';
                    $content = 'Bạn đang yêu cầu reset lại mật khẩu trên Tungbm. <br>';
                    $content .= 'Để thay đổi password, bạn hãy click vào đường link bên dưới: <br>';
                    $content .= _HOST_URL . '/?module=auth&action=reset&token=' . $forgetToken . '<br>';
                    $content .= 'Cảm ơn các bạn đã ủng hộ Tungbm';

                    // gui email
                    sendMail($emailTo, $subject, $content);

                    setSessionFlash('msg', 'Gửi yêu cầu thành công, vui lòng kiểm tra email.');
                    setSessionFlash('msg_type', 'success');
                } else {
                    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại sau.');
                    setSessionFlash('msg_type', 'danger'); 
                }
            }
        }
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
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
                        <h2 class=" fw-normal mb-3 me-3">Quên mật khẩu</h2>

                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" value="<?php if (!empty($oldData)) {
                                                                    echo getOldData($oldData, 'email');
                                                                }  ?>" id="form3Example3" class="form-control form-control-lg"
                            placeholder="Enter a valid email address" /><?php if (!empty($errorsArr)) {
                                                                            echo formError($errorsArr, 'email');
                                                                        }  ?>

                    </div>
                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Gửi</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</section>

<?php
layout('footer');
?>