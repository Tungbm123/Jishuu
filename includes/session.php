<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

// set session (gán giá trị vào biến session)
function setSession($key, $value)
{
    if (!empty(session_id())) {
        $_SESSION[$key] = $value;
        return true;
    }

    return false;
}

//lấy dữ liệu từ session
function getSession($key = '')
{
    if (empty($key)) {
        return $_SESSION;
    } else {
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
    }

    return false;
}

//Xóa session
function removeSession($key = '')
{
    if (empty($key)) {
        session_destroy();
        return true;
    } else {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
        return true;
    }

    return false;
}

//tạo session flash
function setSessionFlash($key, $value){
    $keyFlash = $key. 'Flash';
    return setSession($keyFlash,$value);
}

//lấy session flash
function getSessionFlash($key){
    $keyFlash = $key .'Flash';
    $rel = getSession($keyFlash);
    
    //sau khi lấy xong thì xóa đi, ko cần phải mất công gọi tới hàm removeSession bên ngoài nữa
    removeSession($keyFlash);
    return $rel;
}