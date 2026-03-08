<?php
const _TUNGBM = true; // dùng để kiểm tra xem việc truy cập có hợp lệ ko

const _MODULES = 'dashboard';
const _ACTION = 'index';

//cấu hình database
const _HOST = 'localhost';
const _DB = 'course_manager';
const _USER = 'root';
const _PASS = '';
const _DRIVER = 'mysql';

// debug error
const _DEBUG = true; // để true là khi chạy chương trình có lỗi thì sẽ hiển thị lên

// thiết lập host, vì const Vì const không cho phép dùng biến động như $_SERVER
//👉 Còn define() thì cho phép.
define('_HOST_URL', 'http://'. $_SERVER['HTTP_HOST'] . '/manager_course');
define('_HOST_URL_TEMPLATE', _HOST_URL. '/templates');

// thiết lập path
define('_PATH_URL', __DIR__);
define('_PATH_URL_TEMPLATES', _PATH_URL . '/templates');