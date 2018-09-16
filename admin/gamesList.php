<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台比赛列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-14
 * @update 2018-08-16
 */
	
require_once '../include/public.func.php';
checkLogin();

$query=PDOQuery($dbcon,"SELECT * FROM games");
?>

<html>
<head>
	<title>生蚝体育比赛管理系统后台 / 生蚝科技</title>
	<?php include '../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>

<hr>

<table class="table table-hover table-striped table-bordered">
<tr>
	<th style="text-align:center;">比赛名称</th>
	<th style="text-align:center;">比赛日期</th>
	<th style="text-align:center;">操作</th>
</tr>

<?php foreach($query[0] as $info){ ?>
<tr>
	<td style="text-align:center;vertical-align:middle;font-weight:bold;"><?=$info['name'];?></td>
	<td><?=$info['start_date'].'<br>~<br>'.$info['end_date'];?></td>
	<td style="text-align:center;vertical-align:middle;">
		<a href="<?=ROOT_PATH;?>admin/gamesIndex.php?gamesId=<?=$info['id'];?>" class="btn btn-success">比 赛 管 理 &gt;</a>
		<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$info['id'];?>" class="btn btn-primary" target="_blank">浏 览 主 页 &gt;</a>
	</td>
</tr>
<?php } ?>

</table>

<?php include '../include/footer.php'; ?>

</body>
</html>
