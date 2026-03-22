<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}


$data = [
    'title' => 'Danh sách lĩnh vực'
];

layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
    if (!empty($keyword)) {
        $chuoiWhere .= " WHERE a.name LIKE '%$keyword%' ";
    }
}

// Xử lý phân trang
$maxData = getRows("select id from course_category");
$perPage = 3;
$maxPage = ceil($maxData / $perPage);
$offset = 0;
$page = 1;

if (isset($filter['page'])) {
    $page = $filter['page'];
}
if ($page > $maxPage || $page < 0) {
    $page = 1;
}
$offset = ($page - 1) * $perPage;

$getDetailCate = getAll(
    "SELECT * from course_category a $chuoiWhere limit $perPage OFFSET $offset"
);

//xu ly query
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>


<div class="container grid-user">
    <div class="container-fluid">
        <div class="row">
            <div class="col-6">
                <h2>Thêm mới lĩnh vực</h2>
                <?php require_once 'add.php' ?>
            </div>
            <div class="col-6">
                <h2>Danh sách lĩnh vực</h2>
                
                <?php if (!empty($msg) && !empty($msg_type)) {
                    getMsg($msg, $msg_type);
                } ?>
                <form class="mb-3" action="" method="get">
                    <input type="hidden" name="module" value="course_category">
                    <input type="hidden" name="action" value="list">
                    <div class="row">
                        <div class="col-9">
                            <input type="text" class="form-control" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Nhập thông tin tìm kiếm...">
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th scope="col">STT</th>
                            <th scope="col">Tên</th>
                            <th scope="col">Thời gian tạo</th>
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($getDetailCate as $key => $item): ?>
                                <th scope="row"><?php echo $key + 1; ?></th>
                                <td><?php echo $item['name']; ?></td>
                                <td><?php echo $item['created_at']; ?></td>
                                <td><a href="?module=course_category&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                                <td><a href="?module=course_category&action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xoá không?')" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page - 1; ?>">Previous</a></li>
                        <?php endif; ?>

                        <?php
                        $start = $page - 1;
                        if ($start < 1) $start = 1;
                        ?>
                        <?php if ($start > 1): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page - 1; ?>">...</a></li>
                        <?php endif; ?>

                        <?php
                        $end = $page + 1;
                        if ($end > $maxPage) $end = $maxPage;
                        ?>
                        <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : false ?>"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $i; ?>"><?php echo $i ?></a></li>
                        <?php endfor; ?>

                        <?php if ($end < $maxPage): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">...</a></li>
                        <?php endif; ?>
                        <?php if ($page < $maxPage): ?>
                            <li class="page-item"><a class="page-link" href="?<?php echo $queryString; ?>&page=<?php echo $page + 1; ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php
layout('footer');
?>
