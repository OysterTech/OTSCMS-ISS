<?php
/**
 * @name 生蚝体育比赛管理系统-Web-成绩查询
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-02
 */
	
require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:green;">成 绩 查 询</h3>

<hr>

<center>
	<a href="score/scoreByItem.php" class="btn btn-success" style="width:96%;font-weight:bold;font-size:21px;"><i class="fa fa-list " aria-hidden="true"></i> 按 场 / 项 次 查 询</a>
	<br><br>
	<a href="score/scoreByGroup.php" class="btn btn-success" style="width:96%;font-weight:bold;font-size:21px;"><i class="fa fa-users" aria-hidden="true"></i> 按 项 目 分 类 查 询</a>
	<br><br>
	<a href="score/scoreByAthlete.php" class="btn btn-success" style="width:96%;font-weight:bold;font-size:21px;"><i class="fa fa-user-o" aria-hidden="true"></i> 按 运 动 员 查 询</a>
</center>

<hr>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

</body>
</html>