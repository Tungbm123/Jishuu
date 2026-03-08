<?php
// bất kể folder nào khi truy cập vào nếu ko khai báo rõ file truy cập thì nó sẽ đi vào file index.php
// set timezone giờ việt nam
date_default_timezone_set('Asia/Tokyo');

//tạo mới 1 phiên làm việc, tiếp tục 1 phiên làm việc đã tồn tại
session_start();

ob_start(); // tránh trường hợp lỗi khi sử dụng các hàm header, cookie

require_once 'config.php';
// nhúng file connect
require_once './includes/connect.php';

//nhúng file DB
require_once './includes/database.php';

//nhúng file session
require_once './includes/session.php';

require_once './includes/mailer/Exception.php';
//nhúng 3 file liên quan tới email
require_once './includes/mailer/PHPMailer.php';
require_once './includes/mailer/SMTP.php';
require_once './includes/functions.php';

//nhúng file index trong template
require_once './templates/layouts/index.php';

// $rel = sendMail('tung.bui2@ntq-solution.com.vn','Test email','Hello world<br> tôi chào bạn lần thứ 2');

// có 2 hàm mã hóa và verify PW là password_hash() và password_verify()
//PASSWORD_DEFAULT có nghia là PHP sẽ chọn cái thuật toán mạnh nhất ở thời điểm hiện tại để hash pw
// $pass = 123545;
// $rel = password_hash($pass,PASSWORD_DEFAULT);
// echo $rel;

// if(password_verify($pass,$rel)){
//     echo 'giống nhau';
// }

$module = _MODULES;
$action = _ACTION;

if (!empty($_GET['module'])) {
    $module = $_GET['module'];
};

if (!empty($_GET['action'])) {
    $action = $_GET['action'];
};

$path = 'modules/' . $module . '/' . $action . '.php';

if (!empty($path)) {
    if (file_exists($path)) {
        require_once $path;
    } else {
        require_once './modules/errors/404.php';
    }
} else {
    require_once './modules/errors/500.php';
}
