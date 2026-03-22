<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

// $data = [
//     'title' => 'Thêm mới lĩnh vực'
// ];

// layout('header', $data);
// layout('sidebar');

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
        $dataInsert = [
            'name' => $filter['name'],
            'slug'    => $filter['slug'],
            'created_at' => date('Y:m:d H:i:s')
        ];

        $checkInsert = insert('course_category', $dataInsert);
        setSessionFlash('oldData', $filter);
        if ($checkInsert) {
            setSessionFlash('msg', 'Thêm mới lĩnh vực thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=course_category&action=list');
        } else {
            setSessionFlash('msg', 'Thêm mới lĩnh vực không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        }
    }
     else {
    setSessionFlash('errors', $errors);
    setSessionFlash('oldData', $filter);
    setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
    setSessionFlash('msg_type', 'danger');
}
}

?>


<form action="" method="post">
    <div class="form-group">
        <label for="name">Tên lĩnh vực</label>
        <input id="name" name="name" type="text" class="form-control" placeholder="Tên lĩnh vực">
    </div>
    <div class="form-group">
        <label for="slug">Slug</label>
        <input id="slug" name="slug" type="text" class="form-control" placeholder="slug">
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