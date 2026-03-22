<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}



$data = [
    'title' => 'Danh sách student'
];

layout('header', $data);
layout('sidebar');

$filter = filterData();
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
}

//Lay du lieu users
$permissionArr = [];
$userDetail = getAll("Select fullname, email, permission from users");
if (!empty($userDetail)) {
    foreach ($userDetail as $key => $item) {
        $permissionJson = json_decode($item['permission'], true);
        $permissionArr[$key] = $permissionJson;
    }
}


?>


<div class="container">
    <form action="" method="GET">
        <input type="hidden" name="module" value="students">

        <div class="row text-center-tungbm">
            <div class="col-7">
                <select name="keyword" id="" class="form-select form-control">
                    <option value="0">Chọn khoá học</option>

                    <?php
                    $getCourseDetail = getAll('select id, name from course');
                    foreach ($getCourseDetail as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>" <?php if($keyword == $item['id']){echo 'selected';} ?>><?php echo $item['name']; ?></option>
                    <?php
                    endforeach;
                    ?>
                </select>

            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success">Duyệt</button>
            </div>
        </div>
    </form>

    <div class="row text-center-tungbm">
        <div class="col-9">
            <table class="table table-borderd">
                <thead>
                    <th>STT</th>
                    <th>Tên học viên</th>
                    <th>Email</th>
                </thead>
                <tbody>
                    <?php
                    $stt = 1;
                    foreach ($permissionArr as $key => $item):
                        if (!empty($item)):
                            if (in_array($keyword, $item)):
                    ?>
                                <tr>
                                    <td><?php echo $stt;
                                        $stt++; ?></td>
                                    <td><?php echo $userDetail[$key]['fullname']; ?></td>
                                    <td><?php echo $userDetail[$key]['email']; ?></td>
                                </tr>
                    <?php
                            endif;
                        endif;
                    endforeach;
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
layout('footer');
?>