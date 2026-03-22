<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = filterData('GET');
if (!empty($data)) {
    $courseId = $data['id'];
    $checkCourse = getOnce("select * from course where id = '$courseId'");
    if (!empty($checkCourse)) {
        $deleteStatus = delete('course', "id = $courseId");
        if ($deleteStatus) {
            setSessionFlash('msg', 'Xoá khoá học thành công');
            setSessionFlash('msg_type', 'success');
            redirect('?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Khoá học không tồn tại');
            setSessionFlash('msg_type', 'danger');
        }
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=course&action=list');
}
