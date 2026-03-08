<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}
$data = [
    'title' => 'Register'
];
layout('header-auth', $data);

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate fullname
    if (empty(trim($filter['fullname']))) {
        $errors['fullname']['required'] = 'Họ tên bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['fullname'])) < 5) {
            $errors['fullname']['length'] = 'Họ tên phải lớn hơn 5 ký tự';
        }
    }

    //validate email
    if (empty(trim($filter['email']))) {
        $errors['email']['required'] = 'email ko duoc rong';
    } else {
        //check xem dung dinh dang mail, da ton tai trong CSDL hay chua
        if (!validateEmail(trim($filter['email']))) {
            $errors['email']['isEmail'] = 'Email ko dung dinh dang';
        } else {
            $email = $filter['email'];
            $checkMail = getRows("select * from users where email = '$email'");
            if ($checkMail > 0) {
                $errors['email']['isDuplicate'] = 'Email da ton tai';
            }
        }
    }

    //validate phone
    if (empty(trim($filter['phone']))) {
        $errors['phone']['required'] = 'phone ko duoc de trong';
    } else {
        if (!validatePhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'phone ko dung dinh dang';
        }
    }

    //validate PW > 6 ki tu
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'PW khong duoc de trong';
    } else {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mat khau phai lon hon 6 ky tu';
        }
    }

    //validate confirm PW > 6 ki tu
    if (empty(trim($filter['confirm_pass']))) {
        $errors['confirm_pass']['required'] = 'Confirm PW khong duoc de trong';
    } else {
        if (trim($filter['confirm_pass']) !== trim($filter['password'])) {
            $errors['confirm_pass']['like'] = 'Confirm password khong giong voi password';
        }
    }

    if (empty($errors)) {
        //khong loi
        $active_token = sha1(uniqid() . time());
        $data = [
            'fullname' => $filter['fullname'],
            'address' => null,
            'email' => $filter['email'],
            'phone' => $filter['phone'],
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'active_token' => $active_token,
            // mac dinh la 1 (student), vi ko cho phep user tu tao duoc tai khoan hoc vien 
            'group_id' => 1,
            'created_at' => date('Y:m:d H:i:s')
        ];

        $checkInsert = insert('users', $data);
        setSessionFlash('oldData', $filter);
        if ($checkInsert) {
            $emailTo = $filter['email'];
            $subject = 'Kích hoạt tài khoản hệ thống Tungbm';
            $content = 'Chúc mừng bạn đã đăng ký thành công tài khoản tại Tungbm. <br>';
            $content .= 'Để kích hoạt tài khoản, bạn hãy click vào đường link bên dưới: <br>';
            $content .= _HOST_URL . '/?module=auth&action=active&token=' . $active_token . '<br>';
            $content .= 'Cảm ơn các bạn đã ủng hộ Tungbm';

            // gui email
            sendMail($emailTo, $subject, $content);

            setSessionFlash('msg', 'Đăng ký thành công, vui lòng kích hoạt tài khoản.');
            setSessionFlash('msg_type', 'success');

            
        } else {
            setSessionFlash('msg', 'Đăng ký không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
    $errorsArr = getSessionFlash('errors');
}



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
                        <h2 class=" fw-normal mb-3 me-3">Đăng ký tài khoản</h2>

                    </div>
                    <!-- Email input name, email, sđt, mật khẩu, nhập lại mk -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="fullname" value="<?php if (!empty($oldData)) {
                                                                        echo getOldData($oldData, 'fullname');
                                                                    }  ?>" class="form-control form-control-lg"
                            placeholder="Họ tên" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'fullname');
                        }  ?>
                    </div>
                    <!-- nhập email -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" name="email" value="<?php if (!empty($oldData)) {
                                                                    echo getOldData($oldData, 'email');
                                                                }  ?>" class="form-control form-control-lg"
                            placeholder="Nhập địa chỉ email" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'email');
                        }  ?>

                    </div>
                    <!-- Nhập phone -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="text" name="phone" value="<?php if (!empty($oldData)) {
                                                                    echo getOldData($oldData, 'phone');
                                                                }  ?>" class="form-control form-control-lg"
                            placeholder="Nhập phone" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'phone');
                        } ?>

                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" value="<?php if (!empty($oldData)) {
                                                            echo getOldData($oldData, 'password');
                                                        }  ?>" name="password" class="form-control form-control-lg"
                            placeholder="Enter password" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'password');
                        }  ?>

                    </div>

                    <!-- Nhập lại mật khẩu -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="password" value="<?php if (!empty($oldData)) {
                                                            echo getOldData($oldData, 'confirm_pass');
                                                        }  ?>" name="confirm_pass" class="form-control form-control-lg"
                            placeholder="Enter confirm password" />
                        <?php if (!empty($errors)) {
                            echo formError($errorsArr, 'confirm_pass');
                        }  ?>

                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Đăng ký</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Bạn đã có tài khoản? <a href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger">Đăng nhập ngay</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>

</section>

<?php
layout('footer');
?>