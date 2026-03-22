<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$filterData = filterData('GET');
$cateId = $filterData['id'] ?? null;

if (!empty($cateId)) {
    $checkCate = getOnce("select * from course_category where id = $cateId");

    if (!empty($checkCate)) {
        $checkDelete = delete('course_category', "id = $cateId");
        if ($checkDelete) {
            setSessionFlash('msg', 'Xoá lĩnh vực thành công');
            setSessionFlash('msg_type', 'success');
        } else {
            setSessionFlash('msg', 'Xoá lĩnh vực không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('msg', 'Lĩnh vực không tồn tại');
        setSessionFlash('msg_type', 'danger');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại');
    setSessionFlash('msg_type', 'danger');
}

redirect('/?module=course_category&action=list');
