<?php
/**
 * @name 生蚝体育比赛管理系统-Web-成绩分析
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-10-17
 * @update 2018-10-17
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
$gamesKind=$_SESSION['swim_gamesJson']['kind'];
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:#00C853;">成 绩 分 析</h3>

<hr>

<center>
	<a href="byGroup.php" class="btn" style="width:96%;font-weight:bold;font-size:21px;background-color:#C6FF00;color:#03A9F4;"><i class="fa fa-list-alt" aria-hidden="true"></i> 按 项 目 分 类 比 较</a>
	<br><br>
	<a href="byAllGames.php" class="btn" style="width:96%;font-weight:bold;font-size:21px;background-color:#C6FF00;color:#03A9F4;"><i class="fa fa-trophy" aria-hidden="true"></i> 查 询 所 有 比 赛</a>
</center>

<hr>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include '../include/footer.php'; ?>

</body>
</html>
