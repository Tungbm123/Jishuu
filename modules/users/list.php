<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}


//Phan trang

$data = [
    'title' => 'Danh sách người dùng'
];

layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$group = '0';
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
    if (isset($filter['group'])) {
        $group = $filter['group'];
    }
    if (!empty($keyword)) {
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "a.fullname LIKE '%$keyword%' AND a.email LIKE '%$keyword%' ";
    }

    if (!empty($group)) {
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " a.group_id = $group ";
    }
}

// Xử lý phân trang
$maxData = getRows("select id from users");
$perPage = 3; // so dong du lieu 1 trang
$maxPage = ceil($maxData / $perPage); // tinh max page
$offset = 0;
$page = 1;

if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 0) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;



$getDetailUser = getAll(
    "
SELECT a.id,a.fullname, a.email, a.created_at, b.name
FROM users a
INNER JOIN groups b ON a.group_id = b.id $chuoiWhere 
ORDER BY a.created_at DESC limit $perPage OFFSET $offset"
);

$getGroup = getAll("Select * from groups");

//xu ly query
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

if ($group > 0 || !empty($keyword)) {
    $maxData2 = getRows("SELECT a.id
FROM users a
 $chuoiWhere");
    $maxPage = ceil($maxData2 / $perPage);
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>


<div class="container  grid-user">
    <div class="container-fluid">
        <a href="?modules=users&action=add" class="btn btn-success mb-3 mt-3"><i class="fa-solid fa-plus"></i>Thêm mới người dùng</a>
        <form class="mb-3" action="" method="get">
            <input type="hidden" name="module" value="users">
            <input type="hidden" name="action" value="list">
            <?php if (!empty($msg) && !empty($msg_type)) {
                getMsg($msg, $msg_type);
            }  ?>
            <div class="row">
                <div class="col-3 form-group">
                    <select name="group" id="" class="form-select form-control">
                        <option value="">Nhóm người dùng</option>
                        <?php
                        foreach ($getGroup as $key => $item):
                        ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo ($group == $item['id']) ? 'selected' : ''; ?>><?php echo $item['name']  ?></option>
                        <?php
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="col-7 ">
                    <input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Nhập thông tin tìm kiếm...">
                </div>
                <div class="col-2">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                </div>
            </div>

        </form>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Họ và tên</th>
                    <th scope="col">Email</th>
                    <th scope="col">Ngày đăng ký</th>
                    <th scope="col">Nhóm</th>
                    <th scope="col">Phân quyền</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    foreach ($getDetailUser as $key => $item):
                    ?>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo $item['fullname']; ?></td>
                        <td><?php echo $item['email']; ?></td>
                        <td><?php echo $item['created_at']; ?></td>
                        <td><?php echo $item['name']; ?></td>
                        <td><a href="?module=users&action=permission&id=<?php echo $item['id']; ?>" class="btn btn-primary">Phân quyền</a></td>
                        <td><a href="?module=users&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="?module=users&action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xoá không?')" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
            <?php
                    endforeach;
            ?>

            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php
                if ($page > 1):
                ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                <?php endif; ?>

                <!-- Tinh vi tri bat dau 3-2 =1 -->
                <?php
                $start = $page - 1;
                if ($start < 1) {
                    $start = 1;
                }
                ?>
                <?php
                if ($start > 1):

                ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page - 1; ?>">...</a></li>
                <?php endif; ?>
                <?php
                $end = $page + 1;
                if ($end > $maxPage) {
                    $end = $maxPage;
                }
                ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : false ?>"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $i; ?>"><?php echo $i ?></a></li>

                <?php endfor; ?>

                <!-- Tinh vi tri ket thuc -->

                <?php
                if ($end < $maxPage):
                ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">...</a></li>
                <?php endif; ?>
                <?php if ($page < $maxPage): ?>
                    <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>


<?php
layout('footer');
?>