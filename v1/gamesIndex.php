<?php
/**
 * @name 生蚝体育竞赛管理系统-Web-比赛首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-09
 * @update 2018-12-08
 */
	
require_once 'include/public.func.php';

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex();
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE is_show=1 AND id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex();
}else{
	$gamesName=$gamesInfo[0][0]['name'];
	$startDate=$gamesInfo[0][0]['start_date'];
	$endDate=$gamesInfo[0][0]['end_date'];
	$praise=$gamesInfo[0][0]['praise'];
	$gamesJson=json_decode($gamesInfo[0][0]['extra_json'],true);
	
	$_SESSION['swim_gamesInfo']=$gamesInfo[0][0];
	$_SESSION['swim_gamesJson']=$gamesJson;
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育竞赛管理系统"></center>

<h2 style="text-align: center;"><?=$gamesName;?></h2>
<p style="text-align: center;font-size:21px;font-weight:bold;color:#FF7043;">
	<?php if($startDate==$endDate){echo "举办日期：".$startDate;}else{echo $startDate."~".$endDate;} ?>
	<a id="praise" onclick="praise();">
		<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>(<?=$praise;?>)
	</a>
</p>

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
	<a <?php if($gamesId==4){ ?>onclick="callTips()"<?php }else{ ?>href="call.php"<?php } ?> class="btn btn-block" style="background-color:#ffa6ff;font-weight:bold;font-size:21px;color:white;"><i class="fa fa-volume-up" aria-hidden="true"></i> 检 录</a>
</div>

<br><br><br>

<?php if($gamesJson['teamScore']=="0"){ ?>
<div class="col-xs-12">
	<a href="score.php" class="btn btn-success btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-trophy" aria-hidden="true"></i> 成 绩</a>
</div>
<?php }else{ ?>
<div class="col-xs-6">
	<a href="score.php" class="btn btn-success btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-trophy" aria-hidden="true"></i> 成 绩</a>
</div>
<div class="col-xs-6">
	<a href="teamScore<?=$gamesJson['teamScore'];?>.php" class="btn btn-warning btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-users" aria-hidden="true"></i> 团 体 分</a>
</div>
<?php } ?>

<?php if(isset($gamesJson['swimmingAssociation'])){ ?>
<br><br><br>

<div class="col-xs-12">
	<a href="https://sport.xshgzs.com/filebox/7/%E8%8E%B7%E5%A5%96%E5%90%8D%E5%8D%95.pdf" class="btn btn-warning btn-block" style="font-weight:bold;font-size:20px;">广 州 泳 协 年 度 优 秀 运 动 员</a>
</div>
<?php } ?>

<br><br>

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

function callTips(){
	$("#tips").html("本比赛暂未启用检录功能！");
	$("#tipsModal").modal('show');
	return false;
}
</script>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bold;font-size:24px;text-align:center;">
					<p id="tips"></p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>
</body>
</html>
