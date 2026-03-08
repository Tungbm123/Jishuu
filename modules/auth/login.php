<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Đăng nhập hệ thống'
];
layout('header-auth', $data);

/**
 * Validate du lieu dau vao
 * Check du lieu voi CSDL (Email, pass)
 * Du lieu khop -> token login -> insert vao bang token_login (kiem tra dang nhap)
 * dieu huong den trang dashboard
 */

/**
 * Kiem tra dang nhap
 * - gaan token_login len session
 * - trongh header -> lấy token từ sessison về và so khớp với token trong table token_login 
 * - nếu khớp thì điều hướng trang đích (ko khớp điều hướng về trang login)
- Dang nhap tai khoan o 1 noi tai 1 thoi diem. 
 
 */


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

    //validate pass
    //validate PW > 6 ki tu
    if (empty(trim($filter['password']))) {
        $errors['password']['required'] = 'PW khong duoc de trong';
    } else {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mat khau phai lon hon 6 ky tu';
        }
    }

    if (empty($errors)) {
        //Kiem tra du lieu email trong DB
        $email = $filter['email'];
        $password = $filter['password'];

        //Kiem tra email
        $checkEmail = getOnce("select * from users where email = '$email'");

        if (!empty($checkEmail)) {
            if (!empty($password)) {
                $checkStatus = password_verify($password, $checkEmail['password']); // ham nay co hash luon
                if ($checkStatus) {
                    // Tai khoan chi login 1 noi
                    $user_id = $checkEmail['id'];
                    $checkAlready = getRows("select * from token_login where user_id = '$user_id'");
                    if ($checkAlready > 0) {
                        setSessionFlash('msg', 'Tài khoản đang được đăng nhập ở 1 nơi khác, vui lòng thử lại sau.');
                        setSessionFlash('msg_type', 'danger');
                        redirect('?module=auth&action=login');
                    } else {
                        // tao token va insert vao table token_login
                        $token = sha1(uniqid() . time());

                        // gan token len session
                        setSession ('token_login', $token);

                        $data = [
                            'token' => $token,
                            'created_at' => date('Y:m:d H:i:s'),
                            'user_id' => $checkEmail['id']
                        ];
                        $insertToken = insert('token_login', $data);
                        if ($insertToken) {
                            // setSessionFlash('msg', 'Login success');
                            // setSessionFlash('msg_type', 'success');

                            //Dieu huong toi dashboard
                            redirect('/');
                        } else {
                            setSessionFlash('oldData', $filter);
                            setSessionFlash('msg', 'Đăng nhập không thành công. Vui lòng kiểm tra dữ liệu nhập vào.');
                            setSessionFlash('msg_type', 'danger');
                        }
                    }
                } else {
                    setSessionFlash('oldData', $filter);
                    setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
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
                        <h2 class=" fw-normal mb-3 me-3">Sign in with</h2>

                    </div>
                    <!-- Email input -->
                    <div data-mdb-input-init class="form-outline mb-4">
                        <input type="email" id="form3Example3" name="email"
                            value="<?php if (!empty($oldData)) {
                                        echo getOldData($oldData, 'email');
                                    }  ?>" class="form-control form-control-lg"
                            placeholder="Enter a valid email address" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'email');
                        }  ?>

                    </div>

                    <!-- Password input -->
                    <div data-mdb-input-init class="form-outline mb-3">
                        <input type="password" id="form3Example4" name="password" value="<?php if (!empty($oldData)) {
                                                                                                echo getOldData($oldData, 'password');
                                                                                            }  ?>" class="form-control form-control-lg"
                            placeholder="Enter password" />
                        <?php if (!empty($errorsArr)) {
                            echo formError($errorsArr, 'password');
                        }  ?>

                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Checkbox -->

                        <a href="<?php echo _HOST_URL; ?>?module=auth&action=forgot" class="text-body">Forgot password?</a>
                    </div>

                    <div class="text-center text-lg-start mt-4 pt-2">
                        <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-lg"
                            style="padding-left: 2.5rem; padding-right: 2.5rem;">Login</button>
                        <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="<?php echo _HOST_URL; ?>?module=auth&action=register"
                                class="link-danger">Register</a></p>
                    </div>

                </form>
            </div>
        </div>
    </div>

</section>

<?php
layout('footer');

?>