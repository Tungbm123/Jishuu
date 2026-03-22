<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

// $data = [
//     'title' => 'Thêm mới lĩnh vực'
// ];

// layout('header', $data);
// layout('sidebar');
$filterData = filterData('GET');
$filterPost = filterData('POST');

// Lấy id từ GET (lần đầu vào trang) hoặc POST (khi submit form)
$cateId = $filterData['id'] ?? $filterPost['id'] ?? null;

if (!empty($cateId)) {
    $checkCate = getOnce("select * from course_category where id = $cateId");

    if (empty($checkCate)) {
        redirect('?module=course_category&action=list');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra, xin vui lòng thử lại');
    setSessionFlash('msg_type', 'danger');
}

if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty(trim($filter['name']))) {
        $errors['name']['required'] = 'Tên lĩnh vực bắt buộc phải nhập';
    }

    //Validate slug
    if (empty(trim($filter['slug']))) {
        $errors['slug']['required'] = 'Đưòng dẫn lĩnh vực bắt buộc phải nhập';
    }

    if (empty($errors)) {
        $dataUpdate = [
            'name' => $filter['name'],
            'slug'    => $filter['slug'],
            'updated_at' => date('Y:m:d H:i:s')
        ];
        $condition = 'id = ' . $cateId;
        $checkInsert = update('course_category', $dataUpdate, $condition);
        setSessionFlash('oldData', $filter);
        if ($checkInsert) {
            setSessionFlash('msg', 'Edit lĩnh vực thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=course_category&action=list');
        } else {
            setSessionFlash('msg', 'Edit lĩnh vực không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        }
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
}

$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
if (!empty($checkCate)) {
    $oldData = $checkCate;
}
$errorsArr = getSessionFlash('errors');

?>


<form action="" method="post">
    <input type="hidden" name="id" value="<?php echo $cateId ?? ''; ?>">
    <div class="form-group">
        <label for="name">Tên lĩnh vực</label>
        <input id="name" name="name" type="text" class="form-control" placeholder="Tên lĩnh vực" value="<?php if (!empty($oldData)) {
                                                                                                            echo getOldData($oldData, 'name');
                                                                                                        }  ?>">
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" class="form-control" placeholder="slug" value="<?php if (!empty($oldData)) {
                                                                                                    echo getOldData($oldData, 'slug');
                                                                                                }  ?>">
    </div>

    <button type="submit" class="btn btn-success m-3">Xác nhận</button>
</form>




<script>
    //ham giup chuyen text thanh slug
    function createSlug(strig) {
        return strig.toLowerCase()
            .normalize('NFD') // chuyen ki tu co dau thanh to hop
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
    }

    document.getElementById('name').addEventListener('input', function() {
        const getValue = this.value;
        document.getElementById('slug').value = createSlug(getValue);
    })
</script>