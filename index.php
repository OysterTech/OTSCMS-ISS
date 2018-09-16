<?php
/**
 * @name 生蚝体育比赛管理系统-Web-首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-09
 * @update 2018-08-17
 */

require_once 'include/public.func.php';

$query=PDOQuery($dbcon,"SELECT * FROM games");
?>

<html>
<head>
	<title>生蚝体育比赛信息查询系统 / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body style="margin-top: 20px;">
	
<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>

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
		<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$info['id'];?>" class="btn btn-primary">进 入 比 赛 &gt;</a>
	</td>
</tr>
<?php } ?>
</table>

<?php include 'include/footer.php'; ?>

</body>
</html>
