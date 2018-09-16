<?php
/**
 * @name 生蚝体育比赛管理系统-Web-按姓名查询成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-11
 * @update 2018-09-13
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];

if(isset($_POST) && $_POST){
	$name=$_POST['name'];
	$scoreSql="SELECT a.*,b.scene,b.order_index,b.sex,b.group_name,b.name AS item_name,c.short_name FROM score a,item b,team c WHERE a.name=? AND a.item_id=b.id AND a.team_id=c.id AND b.games_id=? ORDER BY b.scene,b.order_index";
	$scoreQuery=PDOQuery($dbcon,$scoreSql,[$name,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
}
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

<h3 style="font-weight:bold;text-align:center;color:green;">成 绩 查 询</h3>
<p style="line-height:2px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post">
	<!-- 名称输入框 -->
	<div class="input-group">
		<span class="input-group-addon">姓名</span>
		<input class="form-control" name="name" required>
	</div>
	<!-- ./名称输入框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:48%" href="../score.php">< 返 回</a> <input type="submit" class="btn btn-success" style="width:48%" value="立 即 查 询 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php if(isset($_POST) && $_POST){ ?>
<hr>

<table class="table table-hover table-striped table-bordered scoreTable" style="border-radius: 5px; border-collapse: separate;text-align:center;">
<tr>
	<th style="text-align:center;">性别</th>
	<th style="text-align:center;">组别</th>
	<th style="text-align:center;">项目名</th>
	<th style="text-align:center;width:16%">名称</th>
	<th style="text-align:center;width:21%">单位</th>
	<th style="text-align:center;width:5%">排名</th>
	<th style="text-align:center;">成绩</th>
	<th style="text-align:center;">备注</th>
</tr>
<?php foreach($scoreQuery[0] as $info){ ?>
<tr>
	<td><?=$info['sex'];?></td>
	<td><?=$info['group_name'];?></td>
	<td><?=$info['item_name'];?></td>
	<td><?=$name;?></td>
	<td><?=$info['short_name'];?></td>
	<td><?php if($info['rank']!=0) echo $info['rank'];?></td>
	<td><?=$info['score'];?></td>
	<td><?=$info['remark'];?></td>
</tr>
<?php } ?>
</table>
<?php } ?>

<center><div style="width:96%;text-align: center;">
	<div class="alert alert-info"><i class="fa fa-info-circle" aria-hidden="true"></i> 备注：DNS 弃权、DSQ 犯规、TRI测试</div>
</div><center>

<?php include('../include/footer.php'); ?>

</body>
</html>
