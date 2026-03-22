<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}


//Phan trang

$data = [
    'title' => 'Danh sách khoá học'
];

layout('header', $data);
layout('sidebar');

$filter = filterData();
$chuoiWhere = '';
$cate = '0';
$keyword = '';

if (isGet()) {
    if (isset($filter['keyword'])) {
        $keyword = $filter['keyword'];
    }
    if (isset($filter['cate'])) {
        $cate = $filter['cate'];
    }
    if (!empty($keyword)) {
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= "a.name LIKE '%$keyword%' AND a.description LIKE '%$keyword%' ";
    }

    if (!empty($cate)) {
        if (strpos($chuoiWhere, 'WHERE') === false) {
            $chuoiWhere .= ' WHERE ';
        } else {
            $chuoiWhere .= ' AND ';
        }
        $chuoiWhere .= " a.category_id = $cate ";
    }
}

// Xử lý phân trang
$maxData = getRows("select id from course");
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



$getDetailCourse = getAll(
    "
SELECT a.id,a.name, a.price, a.created_at, a.thumbnail, b.name as name_cate
FROM course a
INNER JOIN course_category b ON a.category_id = b.id $chuoiWhere 
ORDER BY a.created_at DESC limit $perPage OFFSET $offset"
);

//xu ly query
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = $_SERVER['QUERY_STRING'];
    $queryString = str_replace('&page=' . $page, '', $queryString);
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
?>


<div class="container  grid-user">
    <div class="container-fluid">
        <a href="?module=course&action=add" class="btn btn-success mb-3 mt-3"><i class="fa-solid fa-plus"></i>Thêm mới khoá học</a>
        <form class="mb-3" action="" method="get">
            <input type="hidden" name="module" value="course">
            <input type="hidden" name="action" value="list">
            <?php if (!empty($msg) && !empty($msg_type)) {
                getMsg($msg, $msg_type);
            }  ?>
            <div class="row">  
                <div class="col-3 form-cate">
                    <select name="cate" id="" class="form-select form-control">
                        <option value="">Lĩnh vực</option>
                        <?php
                        $getCate = getAll("select * from course_category");
                        foreach ($getCate as $key => $item):
                        ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo ($cate == $item['id']) ? 'selected' : ''; ?>><?php echo $item['name']  ?></option>
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
                    <th scope="col">Tên khoá học</th>
                    <th scope="col">Thumbnail</th>
                    <th scope="col">Giá</th>
                    <th scope="col">Lĩnh vực</th>
                    <th scope="col">Ngày tạo</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <?php
                    foreach ($getDetailCourse as $key => $item):
                    ?>
                        <th scope="row"><?php echo $key + 1; ?></th>
                        <td><?php echo $item['name']; ?></td>
                        <td style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                          
                            data-bs-toggle="tooltip">
                            <img src="<?php echo $item['thumbnail']; ?>" alt="" style="width: 80px; height: 50px; object-fit: cover;">
                            
                        </td>
                        <td><?php echo $item['price']; ?></td>
                        <td><?php echo $item['name_cate']; ?></td>
                        <td><?php echo $item['created_at']; ?></td>
                        
                        <td><a href="?module=course&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-warning"><i class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="?module=course&action=delete&id=<?php echo $item['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xoá không?')" class="btn btn-danger"><i class="fa-solid fa-trash"></i></a></td>
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


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(function(el) { new bootstrap.Tooltip(el); });
    });
</script>

<?php
layout('footer');
?>