<?php
// 接收要删除的数据ID
if(empty($_GET['id'])){
    exit('必须传入指定参数!');
}
$id = $_GET['id'];
// 1. 建立连接
$dbhost = 'localhost:3306';
$dbuser = 'root';
$dbpass = '441525';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$conn) {
    exit('数据库连接失败!');
}
$conndb = mysqli_select_db($conn, 'demo');
if (!$conndb) {
    exit('数据库选择失败!');
}
$setcharset = mysqli_set_charset($conn, 'utf8');
if (!$setcharset) {
    exit('数据库字符集设置失败!');
}
// 2. 开始查询
$sql = 'delete from users where id = ' . $id . ' limit 1 ;';
// $sql = 'delete from users where id in (' . $id . ');';
$query = mysqli_query($conn, $sql);
if (!$query) {
    exit('数据库查询失败!');
}
$affected_rows = mysqli_affected_rows($conn);
if ($affected_rows <= 0) {
    exit('数据库删除失败!');
}
header('Location: mySQL_list.php');
