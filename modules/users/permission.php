<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}


$data = [
    'title' => 'Danh sách phân quyền'
];

layout('header', $data);
layout('sidebar');

$filterGet = filterData('GET');
if (!empty($filterGet['id'])) {
    $idUser = $filterGet['id'];
    $checkId = getOnce("select * from users where id = $idUser");

    if (empty($checkId)) {
        redirect('?module=users&action=list');
    }
} else {
    setSessionFlash('msg', 'Người dùng không tồn tại');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=users&action=list');
}

if (isPost()) {
    $filter = filterData();
    if (!empty($filter['permission'])) {
        $permission = json_encode($filter['permission']);
    } else {
        $permission = '';
    }
    // Update vao bang user
    $dataUpdate = [
        'permission' => $permission,
        'updated_at' => date('Y:m:d H:i:s')
    ]; 

    $condition = "id = " . $idUser;
    $checkUpdate = update('users', $dataUpdate, $condition);

    if ($checkUpdate) {
        setSessionFlash('msg', 'Cập nhật thành công');
        setSessionFlash('msg_type', 'success');
        redirect("?module=users&action=permission&id=$idUser");
    } else {
        setSessionFlash('msg', 'Phân quyền thất bại');
        setSessionFlash('msg_type', 'danger');
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');

if(!empty($checkId['permission'])){
    $permissionOld = json_decode($checkId['permission'],true);

}

?>


<div class="container">
    <?php if (!empty($msg) && !empty($msg_type)) {
                getMsg($msg, $msg_type);
            }  ?>
    <form action="" method="POST">
        <table class="table table-borderd">
            <thead>
                <th>STT</th>
                <th>Khoá học</th>
                <th>Phân quyền</th>
            </thead>
            <tbody>
                <?php
                $getDetailCourse = getAll("select id, name from course");
                $dem = 1;
                foreach ($getDetailCourse as $item):
                ?>
                    <tr>
                        <td><?php echo $dem;
                            $dem++; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><input type="checkbox" name="permission[]" <?php echo (!empty($permissionOld)) && in_array($item['id'], $permissionOld) ? 'checked' : false ?> value="<?php echo $item['id']; ?>"></td>
                    </tr>
                <?php
                endforeach;
                ?>

            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Submit</button>
        <a class="btn btn-success" href="?module=users&action=list">Quay lại</a>
    </form>
</div>


<?php
layout('footer');
?>