<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台检录
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-19
 * @update 2018-09-07
 */
	
require_once '../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
	$extraJson=json_decode($gamesInfo[0][0]['extra_json'],TRUE);
}

$callingSql="SELECT * FROM item WHERE games_id=? AND is_calling=1";
$callingQuery=PDOQuery($dbcon,$callingSql,[$gamesId],[PDO::PARAM_INT]);
$scene=0;
$sceneList=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);

if($sceneList[1]<=0){
	die('<script>alert("本比赛暂未录入日程，不开放检录功能！");window.location.href="gamesIndex.php?gamesId='.$gamesId.'";</script>');
}

if($callingQuery[1]==1){
	$calling=1;
	$callingInfo=$callingQuery[0][0];
	$callingScene=$callingInfo['scene'];
	$callingOrderIndex=$callingInfo['order_index'];
	$readySql="SELECT * FROM item WHERE games_id=? AND scene=? AND order_index>?";
	$readyQuery=PDOQuery($dbcon,$readySql,[$gamesId,$callingScene,$callingOrderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	$readyInfo=$readyQuery[0];
	$readyTotal=$readyQuery[1];

	if($readyTotal>3) $readyTotal=3;
}else{
	$calling=0;
}

if(isset($_POST) && $_POST){
	$type=$_POST['type'];

	if($type=="next"){
		// 检录下一项
		$extraJson['call']['beginTime']=date("Y-m-d H:i:s");
		$extraJson=json_encode($extraJson);

		$nextOrderIndex=$callingOrderIndex+1;
		$lastSql="UPDATE item SET is_calling=0 WHERE games_id=? AND scene=?";
		$lastQuery=PDOQuery($dbcon,$lastSql,[$gamesId,$callingScene],[PDO::PARAM_INT,PDO::PARAM_INT]);
		$nextSql="UPDATE item SET is_calling=1 WHERE games_id=? AND scene=? AND order_index=?";
		$nextQuery=PDOQuery($dbcon,$nextSql,[$gamesId,$callingScene,$nextOrderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
		$infoSql="UPDATE games SET extra_json=? WHERE id=?";
		$infoQuery=PDOQuery($dbcon,$infoSql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);

		if($nextQuery[1]==1){
			die('<script>alert("检录完成第'.$callingOrderIndex.'项成功！\n\n当前正在检录：第'.$nextOrderIndex.'项");window.location.href="'.$url.'";</script>');
		}else{
			if($lastQuery[1]==1){
				die('<script>alert("检录完成第'.$callingOrderIndex.'项成功！\n\n第 '.$callingScene.' 场 检录结束成功！");window.location.href="'.$url.'";</script>');
			}else{
				die('<script>alert("检录完成第'.$callingOrderIndex.'项失败！！！");window.location.href="'.$url.'";</script>');
			}
		}
	}elseif($type=="revert"){
		// 返回检录上一项
		$extraJson['call']['beginTime']=date("Y-m-d H:i:s");
		$extraJson=json_encode($extraJson);

		$nextOrderIndex=$callingOrderIndex-1;
		$lastSql="UPDATE item SET is_calling=0 WHERE games_id=? AND scene=?";
		$lastQuery=PDOQuery($dbcon,$lastSql,[$gamesId,$callingScene],[PDO::PARAM_INT,PDO::PARAM_INT]);
		$nextSql="UPDATE item SET is_calling=1 WHERE games_id=? AND scene=? AND order_index=?";
		$nextQuery=PDOQuery($dbcon,$nextSql,[$gamesId,$callingScene,$nextOrderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
		$infoSql="UPDATE games SET extra_json=? WHERE id=?";
		$infoQuery=PDOQuery($dbcon,$infoSql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);

		if($nextQuery[1]==1){
			die('<script>alert("检录返回成功！\n\n当前正在检录：第'.$nextOrderIndex.'项");window.location.href="'.$url.'";</script>');
		}else{
			die('<script>alert("检录失败！！！");window.location.href="'.$url.'";</script>');
		}
	}elseif($type=="begin"){
		// 开场第一项
		$extraJson['call']['beginTime']=date("Y-m-d H:i:s");
		$extraJson=json_encode($extraJson);

		$scene=$_POST['scene'];
		$lastSql="UPDATE item SET is_calling=0 WHERE games_id=? AND scene=?";
		$lastQuery=PDOQuery($dbcon,$lastSql,[$gamesId,$scene],[PDO::PARAM_INT,PDO::PARAM_INT]);
		$nextSql="UPDATE item SET is_calling=1 WHERE games_id=? AND scene=? AND order_index=1";
		$nextQuery=PDOQuery($dbcon,$nextSql,[$gamesId,$scene],[PDO::PARAM_INT,PDO::PARAM_INT]);
		$infoSql="UPDATE games SET extra_json=? WHERE id=?";
		$infoQuery=PDOQuery($dbcon,$infoSql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);

		if($nextQuery[1]==1){
			die('<script>alert("第 '.$scene.' 场 开场成功！");window.location.href="'.$url.'";</script>');
		}else{
			die('<script>alert("第 '.$scene.' 场 开场失败！！！");window.location.href="'.$url.'";</script>');
		}
	}elseif($type=="end"){
		// 提前结束
		$extraJson['call']['beginTime']="";
		$extraJson=json_encode($extraJson);

		$endSql="UPDATE item SET is_calling=0 WHERE games_id=?";
		$endQuery=PDOQuery($dbcon,$endSql,[$gamesId],[PDO::PARAM_INT]);
		$infoSql="UPDATE games SET extra_json=? WHERE id=?";
		$infoQuery=PDOQuery($dbcon,$infoSql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);

		if($endQuery[1]==1){
			die('<script>alert("第 '.$callingScene.' 场 提前结束检录成功！");window.location.href="'.$url.'";</script>');
		}else{
			die('<script>alert("第 '.$callingScene.' 场 提前结束检录失败！！！");window.location.href="'.$url.'";</script>');
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

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:#FF79BC;">检 录 处</h3>

<center>
<button class="btn btn-primary" style="width:96%" onclick="location.reload()"><i class="fa fa-refresh" aria-hidden="true"></i> 刷 新</button>
</center>

<br>

<center>
	<form method="post">
		<input type="hidden" id="type" name="type">
		<?php if($calling==1){ ?>
		<button type="button" class="btn btn-warning" style="width:48%" onclick="call('revert')"><i class="fa fa-arrow-left" aria-hidden="true"></i> 返 回 上 一 项</button>
		<button type="button" class="btn btn-success" style="width:48%" onclick="call('next')">下 一 项 <i class="fa fa-arrow-right" aria-hidden="true"></i></button><br><br>
		<button type="button" class="btn btn-danger" style="width:96%" onclick="call('end')"><i class="fa fa-power-off" aria-hidden="true"></i> 提 前 结 束 本 场 检 录</button>
		<?php }else{ ?>
		<div class="col-xs-12">
			<div class="input-group">
				<span class="input-group-addon">场 次</span>
				<select class="form-control" name="scene">
					<?php foreach($sceneList[0] as $sceneInfo){ ?>
					<option value="<?=$sceneInfo['scene'];?>" <?php if($sceneInfo['scene']==$scene){?>selected<?php } ?>><?=$sceneInfo['scene'];?></option>
					<?php } ?>
				</select>
				<span class="input-group-btn">
					<button type="button" class="btn btn-primary" onclick="call('begin')">开 始 检 录</button>
				</span>
			</div>
		</div>
		<br><br><br>
		<?php } ?>
	</form>
</center>

<?php if($calling==1){ ?>
<hr>

<table class="table table-hover table-striped table-bordered scheduleTable">
<tr>
	<th style="text-align:center;background-color:#BBFFBB" colspan="5">第 <?=$callingScene;?> 场</th>
</tr>
<tr>
	<th style="text-align:center;width:15%">状态</th>
	<th style="text-align:center;width:15%">项次</th>
	<th style="text-align:center;">性别</th>
	<th style="text-align:center;">组别</th>
	<th style="text-align:center;">名称</th>
</tr>
<tr style="background-color:#C4E1FF;">
	<td rowspan="2" style="background-color:#C4E1FF;text-align:center;vertical-align:middle;font-weight:bold;">正在<br>检录</td>
	<td style="font-weight:bold;"><?=$callingOrderIndex;?></td>
	<td><?=$callingInfo['sex'];?></td>
	<td><?=$callingInfo['group_name'];?></td>
	<td><?=$callingInfo['name'];?></td>
</tr>
<?php if($readyTotal>=1){ ?>
<tr>
	<td style="font-weight:bold;"><?=$readyInfo[0]['order_index'];?></td>
	<td><?=$readyInfo[0]['sex'];?></td>
	<td><?=$readyInfo[0]['group_name'];?></td>
	<td><?=$readyInfo[0]['name'];?></td>
</tr>
<?php } ?>
<?php if($readyTotal>=2){ ?>
<tr style="background-color:#FFDAC8;">
	<td rowspan="2" style="background-color:#FFDAC8;text-align:center;vertical-align:middle;font-weight:bold;">准备<br>检录</td>
	<td style="font-weight:bold;"><?=$readyInfo[1]['order_index'];?></td>
	<td><?=$readyInfo[1]['sex'];?></td>
	<td><?=$readyInfo[1]['group_name'];?></td>
	<td><?=$readyInfo[1]['name'];?></td>
</tr>
<?php } ?>
<?php if($readyTotal>=3){ ?>
<tr>
	<td style="font-weight:bold;"><?=$readyInfo[2]['order_index'];?></td>
	<td><?=$readyInfo[2]['sex'];?></td>
	<td><?=$readyInfo[2]['group_name'];?></td>
	<td><?=$readyInfo[2]['name'];?></td>
</tr>
<?php } ?>
</table>
<?php }else{ ?>
<center>
	<p style="color:red;font-size:23px;font-weight:bold;">暂无检录信息！</p>
</center>
<?php } ?>

<center>
	<a href="<?=ROOT_PATH;?>admin/gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="font-weight:bold;font-size:21px;width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 后 台 比 赛 主 页</a>
</center>

<?php include '../include/footer.php'; ?>

<script>
function call(type){
	// 特别提醒
	if(type=="revert"){
		if(confirm("确定要返回前一项吗？")===false){
			return;
		}
	}else if(type=="end"){
		if(confirm("确定要提前结束本场检录吗？")===false){
			return;
		}
	}

	$("#type").val(type);
	$("form").submit();
}
</script>

</body>
</html>
