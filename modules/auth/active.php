<?php
// kiem tra xem active token o url co giong active_token trong bang users ko
//update truong status trong bang users = 1 (da kich hoat) + xoa active_token di
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}
$data = [
    'title' => 'Active account'
];
layout('header-auth', $data);

$filter = filterData();

//Đường link hợp lệ
if (!empty($filter['token'])):
    $token = $filter['token'];
    $checkToken = getOnce("select * from users where active_token = '$token'");
?>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="<?php echo _HOST_URL_TEMPLATE; ?>/assets/image/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <?php
                if (!empty($checkToken)):
                    // Thuc hien update du lieu 
                    $data = [
                        'status' => 1,
                        'active_token' => null,
                        'updated_at' => date('Y:m:d H:i:s')
                    ];
                    $condition = "id =" . $checkToken['id'];
                    update('users', $data, $condition);
                ?>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <h2 class=" fw-normal mb-3 me-3">Kích hoạt tài khoản thành công</h2>
                        </div>

                        <p class="small fw-bold mt-2 pt-1 mb-0"><a href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger" style="font-size:20px; color:blue !important;">Đăng nhập ngay</a></p>
                    </div>
                <?php
                else:
                ?>
                    <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                        <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                            <h2 class=" fw-normal mb-3 me-3">Kích hoạt tài khoản không thành công. Đường link đã hết hạn hoặc không đúng.</h2>
                        </div>
                    <?php
                endif;
                    ?>

                    </div>
            </div>
    </section>
<?php

//Đường link sai, ko hợp lệ
else:
?>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="<?php echo _HOST_URL_TEMPLATE; ?>/assets/image/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="d-flex flex-row align-items-center justify-content-center justify-content-lg-start">
                        <h2 class=" fw-normal mb-3 me-3">Đường link kích hoạt đã hết hạn hoặc không tồn tại</h2>
                    </div>

                    <p class="small fw-bold mt-2 pt-1 mb-0"><a href="<?php echo _HOST_URL ?>?module=auth&action=login" class="link-danger" style="font-size:20px; color:blue !important;">Quay trở lại</a></p>
                </div>
            </div>
        </div>
    </section>
<?php
endif;

?>



<?php
layout('footer');
?>