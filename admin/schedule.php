<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台日程管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-08-18
 */
	
require_once '../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<center>
<a href="<?=ROOT_PATH;?>admin/schedule/import.php?gamesId=<?=$gamesId;?>" class="btn btn-primary" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-upload" aria-hidden="true"></i> 导 入 日 程</a><br><br>
<a href="<?=ROOT_PATH;?>admin/schedule/edit.php?gamesId=<?=$gamesId;?>" class="btn btn-warning" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-edit" aria-hidden="true"></i> 修 改 日 程</a><br><br>
<a href="<?=ROOT_PATH;?>admin/gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 后 台 比 赛 主 页</a>
</center>

<!--br><br><br-->

<?php include '../include/footer.php'; ?>

</body>
</html>
