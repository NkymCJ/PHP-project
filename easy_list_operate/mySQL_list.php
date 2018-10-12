<?php
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
$sql = 'select * from users;';
$query = mysqli_query($conn, $sql);
if (!$query) {
    exit('数据库查询失败!');
}
// // 3. 遍历结果集
// while ($item = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
//     // echo $row['id'] . $row['name'] . $row['birthday'] . $row['gender'] . $row['avatar'] . '</br>';
//     $data[] = $item;
// }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <title>管理系统</title>
    <style>
        .rounded{
            height:20px;
            width:20px;
        }
    </style>
</head>

<body>
    <main class="container">
        <h1 class="heading">用户管理<a class="btn btn-link btn-xs" href="mySQL_list_add.php">添加</a>
        </h1>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th scope="col">编号</th>
                    <th scope="col">名称</th>
                    <th scope="col">生日</th>
                    <th scope="col">性别</th>
                    <th scope="col">头像</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = mysqli_fetch_array($query, MYSQLI_ASSOC)) : ?>
                <tr>
                    <th scope="row">
                        <?php echo $item['id']; ?>
                    </th>
                    <td>
                        <?php echo $item['name']; ?>
                    </td>
                    <td>
                        <?php echo $item['birthday']; ?>
                    </td>
                    <td style="color:<?php echo $item['gender'] == 0 ? "red" : "deepskyblue"; ?>">
                        <?php echo $item['gender'] == 0 ? '♂' : '♀'; ?>
                    </td>
                    <td>
                        <img class="rounded" src="<?php echo $item['avatar']; ?>" alt="<?php echo $item['name']; ?>">
                    </td>
                    <td>
                        <a class="btn btn-primary btn-xs" href="mySQL_list_edit.php?id=<?php echo $item['id']; ?>">编辑</a>
                        <a class="btn btn-danger btn-xs" href="mySQL_list_delete.php?id=<?php echo $item['id']; ?>">删除</a>
                    </td>
                </tr>
                <?php endwhile ?>
            </tbody>
        </table>
    </main>

    <script src="/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>