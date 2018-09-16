<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台比赛首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-12
 * @update 2018-08-21
 */
	
require_once '../include/public.func.php';
checkLogin();

$level=$_SESSION['swim_admin_level'];
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
<?php if($level==1){ ?>
<a href="schedule.php?gamesId=<?=$gamesId;?>" class="btn btn-info" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-list-alt" aria-hidden="true"></i> 日 程 管 理</a><br><br>
<a href="order.php?gamesId=<?=$gamesId;?>" class="btn btn-primary" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-table" aria-hidden="true"></i> 分 组 管 理</a><br><br>
<a href="score.php?gamesId=<?=$gamesId;?>" class="btn btn-success" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-trophy" aria-hidden="true"></i> 成 绩 管 理</a><br><br>
<a href="file.php?gamesId=<?=$gamesId;?>" class="btn btn-warning" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-files-o" aria-hidden="true"></i> 资 料 管 理</a><br><br>
<?php } ?>
<a href="call.php?gamesId=<?=$gamesId;?>" class="btn" style="background-color:#ffa6ff;font-weight:bold;font-size:21px;color:white;width:96%"><i class="fa fa-volume-up" aria-hidden="true"></i> 检 录 处</a><br><br>
<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="font-weight:bold;font-size:21px;width:96%" target="_blank"><i class="fa fa-home" aria-hidden="true"></i> 浏 览 主 页</a>

</center>

<!--br><br><br-->

<?php include '../include/footer.php'; ?>

</body>
</html>
