<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Thêm mới người dùng'
];

layout('header', $data);
layout('sidebar');

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
    if (empty($errors)) {
        $active_token = sha1(uniqid() . time());
        $dataInsert = [
            'fullname' => $filter['fullname'],
            'email'    => $filter['email'],
            'address' => (!empty($filter['address'])? $filter['address']: null),
            'phone' => $filter['phone'],
            'avatar' => '/templates/uploads/avatar.png',
            'password' => password_hash($filter['password'], PASSWORD_DEFAULT),
            'status' => $filter['status'],
            'group_id' => $filter['group_id'],
            'created_at' => date('Y:m:d H:i:s')
        ];

        $checkInsert = insert('users', $dataInsert);
        setSessionFlash('oldData', $filter);
        if ($checkInsert) {
            setSessionFlash('msg', 'Tạo thành công tài khoản thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Đăng ký không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        };
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

<div class="container add-user">
    <h2>Thêm mới người dùng</h2>
    <hr>
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }  ?>
    <form action="" method="post">
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
                <input id="password" type="password" name="password" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                        echo getOldData($oldData, 'password');
                                                                                    }  ?>" placeholder="Password">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'password');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="address">Địa chỉ</label>
                <input name="address" id="address" class="form-control" placeholder="Địa chỉ">
            </div>
            <div class="col-3 pb-3">
                <label for="group">Phân cấp người dùng</label>
                <select name="group_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("select * from groups");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-3 pb-3">
                <label for="status">Trạng thái TK</label>
                <select name="status" id="status" class="form-select form-control">
                    <option value="0">Chưa kích hoạt</option>
                    <option value="1">Đã kích hoạt</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Xác nhận</button>
    </form>
</div>







<?php
layout('footer');
?>