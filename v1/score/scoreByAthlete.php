<?php
/**
 * @name 生蚝体育比赛管理系统-Web-按姓名查成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-11
 * @update 2018-10-25
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
$allGames="0";

if(isset($_POST) && $_POST){
	$name=$_POST['name'];
	$allGames=isset($_POST['checkAllGames'])?$_POST['checkAllGames']:"0";

	if($allGames=="1"){
		$scoreSql="SELECT a.name,a.score,a.rank,a.remark,b.sex,b.group_name,b.name AS item_name,c.name AS games_name,d.short_name FROM score a,item b,games c,team d WHERE a.name LIKE '%{$name}%' AND a.item_id=b.id AND b.games_id=c.id AND a.team_id=d.id ORDER BY c.start_date,a.rank,b.name";
	}else{
		$scoreSql="SELECT a.name,a.score,a.rank,a.remark,b.sex,b.group_name,b.name AS item_name,c.short_name FROM score a,item b,team c WHERE a.name LIKE '%{$name}%' AND a.item_id=b.id AND a.team_id=c.id AND b.games_id='{$gamesId}' ORDER BY b.scene,b.order_index";
	}
	
	$scoreQuery=PDOQuery($dbcon,$scoreSql);
	
	if($allGames=="1"){
		$totalScore=$scoreQuery[1];
		$scoreInfo=$scoreQuery[0];
		$total=1;$nowGamesKey=0;
		
		// 给各比赛写入总成绩条数，以便合并单元格
		for($i=0;$i<count($scoreQuery[0]);$i++){
			// 如果下个成绩条还是当前比赛
			if(isset($scoreInfo[$i+1]['games_name']) && $i+1<=$totalScore && $scoreInfo[$i+1]['games_name']==$scoreInfo[$i]['games_name']){
				$total++;
			}else{
				$scoreQuery[0][$nowGamesKey]['total']=$total;
				$total=1;
				$nowGamesKey=$i+1;
			}
		}
	}
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
		<input class="form-control" name="name" value="<?php if(isset($name)) echo $name;?>" required>
		<span class="input-group-addon">
			<input type="checkbox" id="checkAllGames" name="checkAllGames" value="1" <?php if($allGames=="1") echo "checked";?> onclick="_hmt.push(['_trackEvent','score','athleteAllGames','<?=$gamesId;?>']);"> <label for="checkAllGames">所有比赛</label>
		</span>
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
	<?php if($allGames=="1"){ ?>
	<th style="text-align:center;vertical-align:middle;">赛事名称</th>
	<?php } ?>
	<th style="text-align:center;vertical-align:middle;">性别</th>
	<th style="text-align:center;vertical-align:middle;">组别</th>
	<th style="text-align:center;vertical-align:middle;">项目名</th>
	<th style="text-align:center;vertical-align:middle;">名称</th>
	<th style="text-align:center;vertical-align:middle;">单位</th>
	<th style="text-align:center;vertical-align:middle;">排名</th>
	<th style="text-align:center;vertical-align:middle;">成绩</th>
	<th style="text-align:center;vertical-align:middle;">备注</th>
</tr>
<?php foreach($scoreQuery[0] as $info){ ?>
<tr>
	<?php if($allGames=="1" && isset($info['total'])){ ?>
		<td rowspan="<?=$info['total'];?>" style="text-align:center;vertical-align:middle;"><?=$info['games_name'];?></td>
	<?php } ?>
	<td style="text-align:center;vertical-align:middle;"><?=$info['sex'];?></td>
	<td style="text-align:center;vertical-align:middle;"><?=$info['group_name'];?></td>
	<td style="text-align:center;vertical-align:middle;"><?=$info['item_name'];?></td>
	<td style="text-align:center;vertical-align:middle;"><?=$info['name'];?></td>
	<td style="text-align:center;vertical-align:middle;"><?=$info['short_name'];?></td>
	<th style="text-align:center;color:blue;vertical-align:middle;"><?php if($info['rank']!=0) echo $info['rank'];?></th>
	<td style="text-align:center;vertical-align:middle;"><?=$info['score'];?></td>
	<td style="text-align:center;vertical-align:middle;"><?=$info['remark'];?></td>
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
