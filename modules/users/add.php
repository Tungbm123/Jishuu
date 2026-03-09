<?php
if(!defined('_TUNGBM')){
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Thêm mới người dùng'
];

layout('header', $data);
layout('sidebar');


?>

<div class="container add-user">
    <div class="row">
        <div class="col-6 pb-3">
            <label for="fullname">Họ và tên</label>
            <input id="fullname" class="form-control" placeholder="Họ tên">
        </div>
        <div class="col-6 pb-3">
            <label for="email">Email</label>
            <input id="email" class="form-control" placeholder="Email">
        </div>
        <div class="col-6 pb-3">
            <label for="phone">Số điện thoại</label>
            <input id="phone" class="form-control" placeholder="Số điện thoại">
        </div>
        <div class="col-6 pb-3">
            <label for="password">Password</label>
            <input id="password" class="form-control" placeholder="Password">
        </div>
        <div class="col-6 pb-3">
            <label for="address">Địa chỉ</label>
            <input id="address" class="form-control" placeholder="Địa chỉ">
        </div>
        <div class="col-6 pb-3">
            <label for="fullname">Họ và tên</label>
            <input id="fullname" class="form-control" placeholder="Họ tên">
        </div>
        <div class="col-6 pb-3">
            <label for="fullname">Họ và tên</label>
            <input id="fullname" class="form-control" placeholder="Họ tên">
        </div>
        <div class="col-6 pb-3">
            <label for="fullname">Họ và tên</label>
            <input id="fullname" class="form-control" placeholder="Họ tên">
        </div>
        

    </div>
</div>







<?php
layout('footer');
?>