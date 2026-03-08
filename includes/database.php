<?php
if(!defined('_TUNGBM')){
    die('Truy cập ko hợp lệ');
}

// Viết các hàm insert, update, delete

//Truy vấn nhiều dòng dữ liệu
function getAll($sql){
    global $conn; // set 1 biến global $conn
    $stm = $conn -> prepare($sql);
    $stm -> execute();
    $result = $stm -> fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

//hàm đếm số rows lấy ra từ db
function getRows($sql){
    global $conn;
    $stm = $conn -> prepare($sql);
    $stm -> execute();
    $rel = $stm -> rowCount();
    return $rel;
}

//Truy vấn nhiều 1 dòng dữ liệu
function getOnce($sql){
    global $conn; // set 1 biến global $conn
    $stm = $conn -> prepare($sql);
    $stm -> execute();
    $result = $stm -> fetch(PDO::FETCH_ASSOC); // chuyển thành fetch để chuyển thành 1 dòng dữ liệu
    return $result;
}

//Hàm insert dữ liệu
function insert($table, $data){
    global $conn;
    //lấy ra key của mảng
    $keys = array_keys($data);
    //biến mảng thành chuỗi ngăn cách bởi dấu ','
    $columns = implode(',',$keys);
    //biến mảng thành chuỗi cho placeholder
    $place = ':' . implode(',:',$keys);

    $sql = "insert into $table ($columns) values ($place) ";
    $stm = $conn -> prepare($sql);
    $result = $stm -> execute($data);
   
    return $result;
}

//Hàm update dữ liệu
function update($table, $data, $condition = ''){
    global $conn;
    $update = '';
    foreach($data as $key => $value){
        $update .= $key .'=:' .$key .',';
    }
    $update = trim($update, ',');
  
    $sql ='';
    if(empty($condition)){
        $sql ="update $table set $update";
    }else{
        $sql ="update $table set $update where $condition";
    }
    $tmp = $conn ->prepare($sql);
    $rel=$tmp -> execute($data);
    return $rel;

}

//hàm delete dữ liệu:
function delete($table, $condition){
    global $conn;
    $sql = '';

    if(!empty($condition)){
        $sql = "delete from $table where $condition";
    }else{
        $sql = "delete from $table";
    }

    $stm = $conn -> prepare($sql);
    return $stm ->execute();
}

//hàm get ra ID vừa mới insert gần nhất
function getLastIdInsert(){
    global $conn;
    return $conn -> lastInsertId();
}