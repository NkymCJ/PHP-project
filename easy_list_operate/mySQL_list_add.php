<?php
function add_user()
{
    if (empty($_POST['name'])) {
        $GLOBALS['error_message'] = '请输入姓名';
        return;
    }
    if (!(isset($_POST['gender']) && $_POST['gender'] != '-1')) {
        $GLOBALS['error_message'] = '请选择性别';
        return;
    }
    if (empty($_POST['birthday'])) {
        $GLOBALS['error_message'] = '请输入出生年月';
        return;
    }
    if (empty($_FILES['avatar'])) {
        $GLOBALS['error_message'] = '请上传头像';
        return;
    }
    $avatar_source = $_FILES['avatar'];
    if ($avatar_source['error'] != UPLOAD_ERR_OK) {
        $GLOBALS['error_message'] = '头像上传失败';
        return;
    }
    if ($avatar_source['size'] > 10 * 1024 * 1024) {
        $GLOBALS['error_message'] = '头像文件过大';
        return;
    }
    if ($avatar_source['size'] < 1) {
        $GLOBALS['error_message'] = '头像文件过小';
        return;
    }
    $allow_avatar_source = array('image/png', 'image/gif', 'image/jpeg');
    if (!in_array($avatar_source['type'], $allow_avatar_source)) {
        $GLOBALS['error_message'] = '头像文件格式错误';
        return;
    }
    $dest = "./upload/" . uniqid() . '.' . pathinfo($avatar_source['name'], PATHINFO_EXTENSION);
    $avatar_source_moved = move_uploaded_file($avatar_source['tmp_name'], $dest);
    if (!$avatar_source_moved) {
        $GLOBALS['error_message'] = '头像文件上传失败(移动失败)';
        return;
    }
    $name = $_POST['name'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $avatar = $dest;
    $dbhost = 'localhost:3306';
    $dbuser = 'root';
    $dbpass = '441525';
    $conn = mysqli_connect($dbhost, $dbuser, $dbpass);
    if (!$conn) {
        $GLOBALS['error_message'] = '数据库连接失败';
        return;
    }
    $conndb = mysqli_select_db($conn, 'demo');
    if (!$conndb) {
        $GLOBALS['error_message'] = '数据库选择失败';
        return;
    }
    $setcharset = mysqli_set_charset($conn, 'utf8');
    if (!$setcharset) {
        $GLOBALS['error_message'] = '数据库字符集设置失败';
        return;
    }
    $sql = "insert into users values (null,'{$name}',{$gender},'{$birthday}','{$avatar}');";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        $GLOBALS['error_message'] = '数据库查询数据失败';
        return;
    }
    $affected_rows = mysqli_affected_rows($conn);
    if ($affected_rows != 1) {
        $GLOBALS['error_message'] = '数据库添加数据失败';
        return;
    }
    header('Location: mySQL_list.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    add_user();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <title>管理系统</title>
</head>

<body>
    <main class="container">
        <h1 class="heading">添加用户</h1>
        <?php if (isset($error_message)): ?>
        <div class="alert alert-warning">
            <?php echo $error_message; ?>
        </div>
        <?php endif?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="form-group">
                <label>头像</label>
                <input type="file" class="form-control" name="avatar" id="avatar" style="height:auto;">
            </div>
            <div class="form-group">
                <label>姓名</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="请输入姓名">
            </div>
            <div class="form-group">
                <label>性别</label>
                <select class="form-control" name="gender" id="gender">
                    <option value="-1">请选择性别</option>
                    <option value="1">男</option>
                    <option value="0">女</option>
                </select>
            </div>
            <div class="form-group">
                <label>生日</label>
                <input type="date" class="form-control" name="birthday" id="birthday" >
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </main>

    <script src="/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>