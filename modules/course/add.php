<?php
if (!defined('_TUNGBM')) {
    die('Truy cập ko hợp lệ');
}

$data = [
    'title' => 'Thêm mới khoá học'
];

layout('header', $data);
layout('sidebar');

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

        // Xu ly thumbnail upload len
        $uploadDir = './templates/uploads/';
        // kiem tra xem thu muc da ton tai chua, neu chua thi tao moi
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true); // tạo mới thư mục upload nếu chưa có
        }

        $fileName = basename($_FILES['thumbnail']['name']);

        $targetFile = $uploadDir . time() . '-' . $fileName;
        $checkMove = move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetFile);
        $thumb = '';
        if ($checkMove) {
            $thumb = $targetFile;
        }

        $dataInsert = [
            'name' => $filter['name'],
            'slug'    => $filter['slug'],
            'description' => $filter['description'],
            'thumbnail' => $thumb,
            'price' => $filter['price'],
            'category_id' => $filter['category_id'],
            'created_at' => date('Y:m:d H:i:s')
        ];

        $checkInsert = insert('course', $dataInsert);
        setSessionFlash('oldData', $filter);
        if ($checkInsert) {
            setSessionFlash('msg', 'Thêm mới course thành công');
            setSessionFlash('msg_type', 'success');
            redirect('/?module=course&action=list');
        } else {
            setSessionFlash('msg', 'Thêm mới course không thành công, vui lòng thử lại.');
            setSessionFlash('msg_type', 'danger');
        };
    } else {
        setSessionFlash('errors', $errors);
        setSessionFlash('oldData', $filter);
        setSessionFlash('msg', 'Vui lòng kiểm tra dữ liệu nhập vào.');
        setSessionFlash('msg_type', 'danger');
    }
}
$msg = getSessionFlash('msg');
$msg_type = getSessionFlash('msg_type');
$oldData = getSessionFlash('oldData');
$errorsArr = getSessionFlash('errors');



?>

<div class="container add-user">
    <h2>Thêm mới khoá học</h2>
    <hr>
    <?php if (!empty($msg) && !empty($msg_type)) {
        getMsg($msg, $msg_type);
    }  ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-6 pb-3">
                <label for="name">Tên khoá học</label>
                <input id="name" name="name" class="form-control" placeholder="Tên khoá học" value="<?php if (!empty($oldData)) {
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
                                                                                                    }  ?>"
                    placeholder="Mô tả">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'description');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="price">Giá</label>
                <input id="price" type="text" name="price" class="form-control" value="<?php if (!empty($oldData)) {
                                                                                            echo getOldData($oldData, 'price');
                                                                                        }  ?>" placeholder="price">
                <?php if (!empty($errors)) {
                    echo formError($errorsArr, 'price');
                }  ?>
            </div>

            <div class="col-6 pb-3">
                <label for="thumbnail">Thumbnail</label>
                <input name="thumbnail" id="thumbnail" type="file" class="form-control" placeholder="Thumbnail">
                <img width="200px" id="previewImage" class="preview-image p-3" src="" style="display:none;" alt="">
            </div>
            <div class="col-3 pb-3">
                <label for="category">Lĩnh vực</label>
                <select name="category_id" id="category" class="form-select form-control">
                    <?php
                    $getGroup = getAll("select * from course_category");
                    foreach ($getGroup as $item):
                    ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>
        <button type="submit" class="btn btn-success">Xác nhận</button>
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
                previewImg.setAttribute('src', e.target.result);
                previewImg.style.display = 'block';
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