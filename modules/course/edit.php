<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Edit thông tin course'
];

layout('header', $data);
layout('sidebar');

$getDetailCourse = filterData('GET');
if (!empty($getDetailCourse)) {
    $courseId = $getDetailCourse['id'];

    $dataDetailCourse = getOnce("select * from course where id = '$courseId'");


    if (empty($dataDetailCourse)) {
        setSessionFlash('msg', 'Course không tồn tại');
        setSessionFlash('msg_type', 'danger');
        redirect('?module=course&action=list');
    }
} else {
    setSessionFlash('msg', 'Đã có lỗi xảy ra, vui lòng thử lại');
    setSessionFlash('msg_type', 'danger');
    redirect('?module=course&action=list');
}




if (isPost()) {
    $filter = filterData();
    $errors = [];

    //validate name
    if (empty(trim($filter['name']))) {
        $errors['name']['required'] = 'Tên khoá học bắt buộc phải nhập';
    } else {
        if (strlen(trim($filter['name'])) < 5) {
            $errors['name']['length'] = 'Tên khoá học phải lớn hơn 5 ký tự';
        }
    }

    //Validate slug
    if (empty(trim($filter['slug']))) {
        $errors['slug']['required'] = 'Đưòng dẫn học bắt buộc phải nhập';
    }

    //validate price
    if (empty(trim($filter['price']))) {
        $errors['price']['required'] = 'price ko duoc de trong';
    }

    //validate description
    if (empty(trim($filter['description']))) {
        $errors['description']['required'] = 'description ko duoc de trong';
    }

    if (empty($errors)) {


        $dataUpdate = [
            'name' => $filter['name'],
            'slug'    => $filter['slug'],
            'description' => $filter['description'],
            'thumbnail' => '',
            'price' => $filter['price'],
            'category_id' => $filter['category_id'],
            'created_at' => date('Y:m:d H:i:s')
        ];

        if (!empty($_FILES['thumbnail'])) {
            // Xu ly thumbnail upload len
            $uploadDir = './templates/uploads/';
            // kiem tra xem thu muc da ton tai chua, neu chua thi tao moi
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true); // tạo mới thư mục upload nếu chưa có
            }

            $fileName = basename($_FILES['thumbnail']['name']);

            $targetFile = $uploadDir . time() . '-' . $fileName;
            $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
            $thumb = $dataDetailCourse['thumbnail']; // giữ ảnh cũ mặc định
            if ($checkMove) {
                // xóa ảnh cũ nếu tồn tại
                if (!empty($dataDetailCourse['thumbnail']) && file_exists($dataDetailCourse['thumbnail'])) {
                    unlink($dataDetailCourse['thumbnail']);
                }
                $thumb = $targetFile;
            }

            $dataUpdate['thumbnail'] = $thumb;
        }

        $condition = 'id=' . $courseId;
        $checkUpdate = update('course', $dataUpdate, $condition);
        setSessionFlash('oldData', $filter);
        if ($checkUpdate) {
            setSessionFlash('msg', 'Update course thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Update course không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        };
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
    $msg = getSessionFlash('msg');
    $msg_type = getSessionFlash('msg_type');
    $oldData = getSessionFlash('oldData');
    $errorsArr = getSessionFlash('errors');
}


$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
if (!empty($dataDetailCourse)) {
    $oldData = $dataDetailCourse;
}
$errorsArr = getSessionFlash('errors');
?>

<div class="container add-user">
    <h2>Chỉnh sửa thông tin course</h2>
    <hr>
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }  ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="name">Tên khoá học</label>
                <input id="name" name="name" class="form-control" placeholder="Họ tên" value="<?php if (!empty($oldData)) {
                                                                                                    echo getOldData($oldData, 'name');
                                                                                                }  ?>">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'name');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="slug">Đường dẫn</label>
                <input id="slug" name="slug" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                echo getOldData($oldData, 'slug');
                                                                            }  ?>" placeholder="slug">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'slug');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="description">Mô tả</label>
                <input type="text" id="description" name="description" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                                        echo getOldData($oldData, 'description');
                                                                                                    }  ?>" placeholder="Mô tả">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'description');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="price">Giá</label>
                <input id="price" type="text" name="price" class="form-control" placeholder="price" value="<?php if (!empty($oldData)) {
                                                                                                                echo getOldData($oldData, 'price');
                                                                                                            }  ?>">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'price');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="thumbnail">Thumbnail</label>
                <input name="thumbnail" id="thumbnail" type="file" class="form-control">

                <?php if (!empty($dataDetailCourse['thumbnail'])): ?>
                    <p class="mt-1 text-muted current-thumbnail-name" style="font-size: 13px;">
                        Ảnh hiện tại: <?php echo basename($dataDetailCourse['thumbnail']); ?>
                    </p>
                    <img src="<?php echo $dataDetailCourse['thumbnail']; ?>"
                        class="current-thumbnail"
                        style="width: 200px; object-fit: cover;" alt="">
                <?php endif; ?>

                <img id="previewImage" class="preview-image" src="" style="display:none;" alt="">
            </div>

            <div class="col-3 pb-3">
                <label for="category_id">Lĩnh vực</label>
                <select name="category_id" id="group" class="form-select form-control">
                    <?php
                    $getGroup = getAll("select * from course_category");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"
                            <?php echo ($oldData['category_id'] == $item['id'] ? 'selected' : false) ?>>
                            <?php echo $item['name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>
        <button type="submit" class="btn btn-success btn-add">Xác nhận</button>
        <a type="button" href="?module=course&action=list" class="btn btn-primary btn-add">Back to list</a>

    </form>
</div>


<script>
    const thumbInput = document.getElementById('thumbnail');
    const previewImg = document.getElementById('previewImage');
    thumbInput.addEventListener('change', function() {
        const $file = this.files[0];
        if ($file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // ẩn ảnh cũ đi
                const oldImg = document.querySelector('.current-thumbnail');
                if (oldImg) oldImg.style.display = 'none';

                // cập nhật tên ảnh mới
                const oldName = document.querySelector('.current-thumbnail-name');
                if (oldName) oldName.textContent = 'Ảnh hiện tại: ' + $file.name;

                previewImg.setAttribute('src', e.target.result);
                previewImg.style.display = 'block';
                previewImg.style.width = '200px';
            }
            reader.readAsDataURL($file);
        } else {
            previewImg.style.display = 'none';
        }
    });
</script>

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




<?php
layout('footer');
?>