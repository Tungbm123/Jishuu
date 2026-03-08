<?php
if(!defined('_TUNGBM')){
    die('Truy cập ko hợp lệ');
}
// Thông tin connect và connect tới DB

try {

    if (class_exists('PDO')) {
        $opptions = [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", // hỗ trợ về tiếng việt
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION //Đẩy lỗi vào ngoại lệ
        ];
        
        $dsn = _DRIVER . ':host='._HOST.'; dbname='._DB;
        $conn = new PDO($dsn, _USER, _PASS,$opptions);

    }
} catch (Exception $ex) {
    echo 'lỗi kết nối:' . $ex->getMessage();
}