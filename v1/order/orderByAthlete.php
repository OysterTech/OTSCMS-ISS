<?php
/**
 * @name 生蚝体育比赛管理系统-Web-按姓名查询分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-13
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];

if(isset($_POST) && $_POST){
	$name=$_POST['name'];
	$sql="SELECT a.run_group,a.runway,b.scene,b.order_index,b.scene,b.order_index,b.sex,b.group_name,b.name AS item_name,c.short_name FROM score a,item b,team c WHERE a.name=? AND a.item_id=b.id AND a.team_id=c.id AND b.games_id=?";
	$query=PDOQuery($dbcon,$sql,[$name,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
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

<h3 style="font-weight:bold;text-align:center;color:blue;">分 组 查 询</h3>
<p style="line-height:2px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post">
	<!-- 名称输入框 -->
	<div class="input-group">
		<span class="input-group-addon">姓名</span>
		<input class="form-control" name="name" value="<?php if(isset($name)) echo $name; ?>" required>
	</div>
	<!-- ./名称输入框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:48%" href="../order.php">< 返 回</a> <input type="submit" class="btn btn-success" style="width:48%" value="立 即 查 询 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php if(isset($_POST) && $_POST){ ?>
<hr>

<table class="table table-hover table-striped table-bordered orderTable" style="border-radius: 5px; border-collapse: separate;text-align:center;">
<tr>
	<th style="text-align:center;">场/项</th>
	<th style="text-align:center;">项目名</th>
	<th style="text-align:center;">组/道</th>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">单位</th>
</tr>
<?php if($query[1]==0){ ?>
<tr>
	<th colspan="5" style="text-align:center;color:red;">无 此 运 动 员 数 据</th>
</tr>
<?php }else{foreach($query[0] as $info){ ?>
<tr>
	<td><?=$info['scene'].'/'.$info['order_index'];?></td>
	<td><?=$info['sex'].$info['group_name'].$info['item_name'];?></td>
	<td><?=$info['run_group'].'/'.$info['runway'];?></td>
	<td><?=$name;?></td>
	<td><?=$info['short_name'];?></td>
</tr>
<?php } } ?>
</table>
<?php } ?>

<?php include('../include/footer.php'); ?>

</body>
</html>
