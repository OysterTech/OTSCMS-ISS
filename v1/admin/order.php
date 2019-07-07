<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台分组管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-17
 * @update 2018-08-31
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
<a href="<?=ROOT_PATH;?>admin/order/manageTeam.php?gamesId=<?=$gamesId;?>" class="btn btn-info" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-users" aria-hidden="true"></i> 团 体 管 理</a><br><br>
<a href="<?=ROOT_PATH;?>admin/order/import.php?gamesId=<?=$gamesId;?>" class="btn btn-primary" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-upload" aria-hidden="true"></i> 导 入 秩 序 册</a><br><br>
<a href="<?=ROOT_PATH;?>admin/order/edit.php?gamesId=<?=$gamesId;?>" class="btn btn-warning" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-edit" aria-hidden="true"></i> 修 改 秩 序 册</a><br><br>
<a href="<?=ROOT_PATH;?>admin/order/setRemark.php?gamesId=<?=$gamesId;?>" class="btn btn-success" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-bookmark-o" aria-hidden="true"></i> 标 记 运 动 员 备 注</a><br><br>
<a href="<?=ROOT_PATH;?>admin/order/importRelayStickName.php?gamesId=<?=$gamesId;?>" class="btn btn-success" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-handshake-o" aria-hidden="true"></i> 接 力 棒 次 名 单 录 入</a><br><br>
<a href="<?=ROOT_PATH;?>admin/gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 后 台 比 赛 主 页</a>
</center>

<!--br><br><br-->

<?php include '../include/footer.php'; ?>

</body>
</html>
