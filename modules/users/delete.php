<?php
if(!defined('_TUNGBM')){
    die('Truy cập ko hợp lệ');
}

$getData = filterData('GET');
if(!empty($getData)){
    $user_id = $getData['id'];
    $checkUser =getRows("select * from users where id = $user_id");

    if($checkUser > 0){
        //delete account
        //Check xem user đó có đang đăng nhập hay không
        $checkToken = getRows("select * from token_login where user_id = $user_id"); 
        if($checkToken > 0){
            $con = 'user_id=' .$user_id;
            delete('token_login', $con);
        }

        $checkDelete = delete('users',"id = $user_id");
        if ($checkDelete) {
            setSessionFlash('msg', 'Delete account thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=users&action=list');
        } else {
            setSessionFlash('msg', 'Delete không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        }

    }else{
        setSessionFlash('msg', 'Người dùng không tồn tại');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=users&action=list');
    }
}


