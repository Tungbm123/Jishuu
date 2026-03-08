<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

function layout($layoutName, $data = [])
{
    if (file_exists(_PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php')) {
        require_once _PATH_URL_TEMPLATES . '/layouts/' . $layoutName . '.php';
    }
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//hàm gửi mail
function sendMail($emailTo, $subject, $content)
{

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'minh.95.tung@gmail.com';                     //SMTP username
        $mail->Password   = 'szgtlogefwphwvkq';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('minh.95.tung@gmail.com', 'Tungbm');
        $mail->addAddress($emailTo);     //Add a recipient

        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->SMTPOptions = array(
            'ssl' => [
                'verify_peer' => true,
                'verify_depth' => 3,
                'allow_self_signed' => true,

            ],
        );

        return $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} 
//kiểm tra phương thức post
function isPost()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

//Kiểm tra phương thức get
function isGet()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

//lọc dữ liệu trước khi lưu vào database
function filterData($method = '')
{
    $filterArr = [];
    if (empty($method)) {
        if (isGet()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
        if (isPost()) {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    } else {
        if ($method == 'GET') {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_var($_GET[$key], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    }
                }
            }
        }

        if ($method == 'POST') {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                    }
                }
            }
        }
    }

    return $filterArr;
}

//validate email
function validateEmail($email)
{
    if (!empty($email)) {
        $checkMail = filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    return $checkMail;
}

//Validate int
function validateInt($number)
{
    if (!empty($number)) {
        $checkNumber = filter_var($number, FILTER_VALIDATE_INT);
    }

    return $checkNumber;
}

//validate phone 
function validatePhone($phone)
{
    $phoneFist = false;
    if ($phone[0] == '0') {
        $phoneFist = true;
        $phoneStr = substr($phone, 1);
    }

    $checkPhone = false;
    if (validateInt($phoneStr)) {
        $checkPhone = true;
    }
    if ($phoneFist = true && $checkPhone) {
        return true;
    }
    return false;
}

// thong bao loi
function getMsg($msg, $type = 'success')
{
    echo '<div class = "annouce-message alert alert-' . $type . '"> ';
    echo $msg;
    echo  '</div>';
}

// Hien thi loi inline
function formError($errors, $fieldName)
{
    return (!empty($errors[$fieldName])) ? '<div class="error">' . reset($errors[$fieldName]) . '</div>' : false;
}

//Hien thi lai gia tri da nhap trong form
function getOldData($oldData, $keyName)
{
    return (!empty($oldData[$keyName])) ? $oldData[$keyName] : null;
}

//ham chuyen huong
function redirect($path, $pathFull = false)
{
    if ($pathFull) {
        header("Location: $path");
        exit();
    } else {
        $url = _HOST_URL . $path;
        header("Location: $url");
        exit();
    }
}

// ham check login
function isLogin()
{
    $checkLogin = false;
    $tokenLogin = getSession('token_login');
    $checkToken = getOnce("select * from token_login where token = '$tokenLogin'");
    if (!empty($checkToken)) {
        $checkLogin = true;
    } else {
        removeSession('token_login');
    }
    return $checkLogin;
}
