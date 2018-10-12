<?php
// 接收要编辑的数据ID
if (empty($_GET['id'])) {
    exit('必须传入指定参数');
}
$id = $_GET['id'];
// 1. 建立连接
$dbhost = 'localhost:3306';
$dbuser = 'root';
$dbpass = '441525';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass);
if (!$conn) {
    exit('数据库连接失败');
}
$conndb = mysqli_select_db($conn, 'demo');
if (!$conndb) {
    exit('数据库选择失败');
}
$setcharset = mysqli_set_charset($conn, 'utf8');
if (!$setcharset) {
    exit('数据库字符集设置失败');
}
// 2. 开始查询
$sql = "select * from users where id = {$id} limit 1;";
$query = mysqli_query($conn, $sql);
if (!$query) {
    exit('数据库查询失败');
}
// $users = mysqli_affected_rows($conn);
$users = mysqli_fetch_assoc($query);
if (!$users) {
    exit('找不到该条数据');
}

function edit()
{
    global $users;
    global $id;
    // 验证输入非空
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
    // 初始化值
    $users['name'] = $_POST['name'];
    $users['gender'] = $_POST['gender'];
    $users['birthday'] = $_POST['birthday'];
    // 验证文件非空
    if (isset($_FILES['avatar'])&&$_FILES['avatar']['error']==UPLOAD_ERR_OK) {
        $avatar_source = $_FILES['avatar'];
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
        $users['avatar'] = $dest;
    }
    
    // var_dump($name, $gender, $birthday, $avatar);
    // 保存
    // 1. 建立连接
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
    // 2. 开始查询
    $sql = "update users set `name` = '{$users['name']}',`gender` = {$users['gender']},`birthday` = '{$users['birthday']}',`avatar` = '{$users['avatar']}' where `id` = {$id} limit 1;";
    $query = mysqli_query($conn, $sql);
    if (!$query) {
        $GLOBALS['error_message'] = '数据库查询数据失败';
        return;
    }
    $affected_rows = mysqli_affected_rows($conn);
    // 这里有个BUG, 即当没有做任何修改的更新时, 影响行数会是0, 报错
    if ($affected_rows != 1) {
        $GLOBALS['error_message'] = '数据库修改数据失败';
        return;
    }
    header('Location: mySQL_list.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    edit();
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
        <h1 class="heading">编辑
            <?php echo $users['name']; ?>
        </h1>
        <?php if (isset($error_message)) : ?>
        <div class="alert alert-warning">
            <?php echo $error_message; ?>
        </div>
        <?php endif ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $users['id']; ?>" method="post" enctype="multipart/form-data" autocomplete="off">
            <img src="<?php echo $users['avatar']; ?>" alt="avatar" style="height:50px;width:50px;">
            <div class="form-group">
                <label>头像</label>
                <input type="file" class="form-control" name="avatar" id="avatar" style="height:auto;">
            </div>
            <div class="form-group">
                <label>姓名</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="请输入姓名" value="<?php echo $users['name']; ?>">
            </div>
            <div class="form-group">
                <label>性别</label>
                <select class="form-control" name="gender" id="gender">
                    <option value="-1">请选择性别</option>
                    <option value="1"<?php echo $users['gender'] === '1' ? ' selected' : ''; ?>>男</option>
                    <option value="0"<?php echo $users['gender'] === '0' ? ' selected' : ''; ?>>女</option>
                </select>
            </div>
            <div class="form-group">
                <label>生日</label>
                <input type="date" class="form-control" name="birthday" id="birthday" value="<?php echo $users['birthday']; ?>">
            </div>
            <button type="submit" class="btn btn-primary">保存</button>
        </form>
    </main>

    <script src="/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>