<?php
/**
 * @name 生蚝体育比赛管理系统-Web-比赛首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-09
 * @update 2018-08-19
 */
	
require_once 'include/public.func.php';

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex();
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex();
}else{
	$gamesName=$gamesInfo[0][0]['name'];
	$startDate=$gamesInfo[0][0]['start_date'];
	$endDate=$gamesInfo[0][0]['end_date'];
	$praise=$gamesInfo[0][0]['praise'];
	
	$_SESSION['swim_gamesInfo']=$gamesInfo[0][0];
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>
<p style="text-align: center;font-size:21px;"><?=$startDate."~".$endDate;?> <a id="praise" onclick="praise();"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>(<?=$praise;?>)</a></p>

<hr>

<div class="col-xs-6">
	<a href="schedule.php" class="btn btn-default btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-list-alt" aria-hidden="true"></i> 日 程</a>
</div>
<div class="col-xs-6">
	<a href="file.php" class="btn btn-primary btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-files-o" aria-hidden="true"></i> 资 料</a>
</div>

<br><br><br>

<div class="col-xs-6">
	<a href="order.php" class="btn btn-info btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-table" aria-hidden="true"></i> 分 组</a>
</div>
<div class="col-xs-6">
	<a href="call.php" class="btn btn-block" style="background-color:#ffa6ff;font-weight:bold;font-size:21px;color:white;"><i class="fa fa-volume-up" aria-hidden="true"></i> 检 录</a>
</div>

<br><br><br>

<div class="col-xs-6">
	<a href="score.php" class="btn btn-success btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-trophy" aria-hidden="true"></i> 成 绩</a>
</div>
<div class="col-xs-6">
	<a href="teamScoreTotal.php" class="btn btn-warning btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-users" aria-hidden="true"></i> 团 体 分</a>
<br><br>
</div>

<?php include 'include/footer.php'; ?>

<script>
function praise(){
	$.ajax({
		url:"toPraise.php",
		success:function(got){
			alert("谢谢你的点赞！\n小生蚝会继续努力完善哈~\n\n欢迎各位点击底部“生蚝科技”了解我哦！");
			$("#praise").html('<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>('+got+')');
			return true;
		}
	});
}
</script>
</body>
</html>
