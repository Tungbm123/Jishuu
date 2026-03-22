<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Profile'
];

layout('header', $data);
layout('sidebar');

$getDetailUser = filterData('GET');
// if (!empty($getDetailUser)) {
//     $userId = $getDetailUser['id'];
//     $dataDetailUser = getOnce("select * from users where id = $userId");

//     if (empty($dataDetailUser)) {
//         setSessionFlash('msg', 'Người dùng không tồn tại');
//         setSessionFlash('msg_type', 'danger');
//         redirect('?module=users&action=list');
//     }
// } else {
//     setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại');
//     setSessionFlash('msg_type', 'danger');
//     redirect('?module=users&action=list');
// }

$token = getSession('token_login');
if(!empty($token)){
  $checkTokenLogin = getOnce("select * from token_login where token = '$token'");

  if(!empty($checkTokenLogin)){
    $userId = $checkTokenLogin['user_id'];
    $dataDetailUser = getOnce("select * from users where id = $userId");
  }
}



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


    //validate phone
    if (empty(trim($filter['phone']))) {
        $errors['phone']['required'] = 'phone ko duoc de trong';
    } else {
        if (!validatePhone($filter['phone'])) {
            $errors['phone']['isPhone'] = 'phone ko dung dinh dang';
        }
    }

    //validate PW > 6 ki tu
    if (!empty(trim($filter['password']))) {
        if (strlen(trim($filter['password'])) < 6) {
            $errors['password']['length'] = 'Mat khau phai lon hon 6 ky tu';
        }
    }

    if ($filter['email'] != $dataDetailUser['email']) {

        //validate email
        if (empty(trim($filter['email']))) {
            $errors['email']['required'] = 'email ko duoc rong';
        } else {
            //check xem dung dinh dang mail, da ton tai trong CSDL hay chua
            if (!validateEmail(trim($filter['email']))) {
                $errors['email']['isEmail'] = 'Email ko dung dinh dang';
            } else {
                $email = $filter['email'];
                $id = $dataDetailUser['id'];
                $checkMail = getRows("select * from users where email = '$email' AND id != $id");
                if ($checkMail > 0) {
                    $errors['email']['isDuplicate'] = 'Email trùng với email khác đã đăng ký rồi';
                }
            }
        }
    }

    if (empty($errors)) {
        $dataUpdate = [
            'fullname' => $filter['fullname'],
            'email'    => $filter['email'],
            'address' => (!empty($filter['address']) ? $filter['address'] : null),
            'phone' => $filter['phone'],
            'updated_at' => date('Y:m:d H:i:s')
        ];

        // xử lý upload avatar nếu có chọn file mới
        if (!empty($_FILES['avatar']['size'])) {
            $uploadDir = './templates/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = basename($_FILES['avatar']['name']);
            $fileStoreName = time() . '-' . $fileName;
            $targetFile = $uploadDir . $fileStoreName;
            $checkMove = move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile);
            if ($checkMove) {
                if (!empty($dataDetailUser['avatar'])) {
                    $oldAvatarFs = './' . ltrim($dataDetailUser['avatar'], './');
                    if (file_exists($oldAvatarFs)) unlink($oldAvatarFs);
                }
                $dataUpdate['avatar'] = 'templates/uploads/' . $fileStoreName;
            }
        }

        if (!empty($filter['password'])) {
            $dataUpdate['password'] = password_hash($filter['password'], PASSWORD_DEFAULT);
        }
        $condition = "id=" . $userId;

        $updateStatus = update('users', $dataUpdate, $condition);
        setSessionFlash('oldData', $filter);
        if ($updateStatus) {
            setSessionFlash('msg', 'Thay đổi thông tin tài khoản thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=users&action=profile');
        } else {
            setSessionFlash('msg', 'Thay đổi thông tin không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        };
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
if (!empty($dataDetailUser)) {
    $oldData = $dataDetailUser;
}
$errorsArr = getSessionFlash('errors');
?>

<div class="container add-user">
    <h2>Thông tin tài khoản</h2>
    <hr>
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }  ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="fullname">Họ và tên</label>
                <input id="fullname" name="fullname" class="form-control" placeholder="Họ tên" value="<?php if (!empty($oldData)) {
                                                                                                            echo getOldData($oldData, 'fullname');
                                                                                                        }  ?>">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'fullname');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="email">Email</label>
                <input id="email" name="email" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                echo getOldData($oldData, 'email');
                                                                            }  ?>" placeholder="Email">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'email');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="phone">Số điện thoại</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                            echo getOldData($oldData, 'phone');
                                                                                        }  ?>" placeholder="Số điện thoại">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'phone');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" class="form-control" value="" placeholder="Password">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'password');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="address">Địa chỉ</label>
                <input id="address" name="address" class="form-control" placeholder="Địa chỉ" value="<?php if (!empty($oldData)) {
                                                                                                            echo getOldData($oldData, 'address');
                                                                                                        }  ?>">
            </div>
            <div class="col-6 pb-3">
                <label for="avatar">Ảnh đại diện</label>
                <input name="avatar" id="avatar" type="file" class="form-control">

                <?php if (!empty($dataDetailUser['avatar'])): ?>
                    <p class="mt-1 text-muted current-avatar-name" style="font-size: 13px;">
                        Ảnh hiện tại: <?php echo basename($dataDetailUser['avatar']); ?>
                    </p>
                    <img src="<?php echo $dataDetailUser['avatar']; ?>"
                        class="current-avatar"
                        style="width: 200px; object-fit: cover;" alt="">
                <?php endif; ?>

                <img id="previewImage" class="preview-image" src="" style="display:none;" alt="">
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-add">Xác nhận</button>
    </form>
</div>







<?php
layout('footer');
?>

<script>
    const thumbInput = document.getElementById('avatar');
    const previewImg = document.getElementById('previewImage');
    thumbInput.addEventListener('change', function() {
        const $file = this.files[0];
        if ($file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // ẩn ảnh cũ đi
                const oldImg = document.querySelector('.current-avatar');
                if (oldImg) oldImg.style.display = 'none';

                // cập nhật tên ảnh mới
                const oldName = document.querySelector('.current-avatar-name');
                if (oldName) oldName.textContent = 'Ảnh hiện tại: ' + $file.name;

                previewImg.setAttribute('src', e.target.result);
                previewImg.style.display = 'block';
                previewImg.style.width = '200px';
            }
            reader.readAsDataURL($file);
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>