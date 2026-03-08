<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Đăng nhập hệ thống'
];

if (isLogin()) {
    $token = getSession('token_login');
    //Thuc hien thao tac xoa token
    $removeToken = delete('token_login', "token = '$token'");
    if ($removeToken) {
        removeSession('token_login');
        redirect('?module=auth&action=login');
    } else {
        setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau.');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'Lỗi hệ thống, xin vui lòng thử lại sau.');
    setSessionFlash('msg_type', 'danger');
}
