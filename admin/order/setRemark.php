<?php
/**
 * @name 生蚝体育比赛管理系统-Web-设置备注
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-08-24
 */
	
require_once '../../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex();
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

if($gamesInfo[1]!=1){
	goToIndex();
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$scene=0;
$orderIndex=0;
$sceneList=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);
$orderIndexList=PDOQuery($dbcon,"SELECT DISTINCT(order_index) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);

if(isset($_POST) && $_POST){
	$scene=$_POST['scene'];
	$orderIndex=$_POST['orderIndex'];
	$itemSql="SELECT id,sex,group_name,name,scene,order_index FROM item WHERE games_id=? AND scene=? AND order_index=? AND is_delete=0";
	$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId,$scene,$orderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	
	if($itemQuery[1]!=1){
		die('<script>alert("无此项目！");window.location.href="'.$url.'";</script>');
	}

	$scoreSql="SELECT a.*,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id ORDER BY a.run_group,a.runway";
	$scoreQuery=PDOQuery($dbcon,$scoreSql,[$itemQuery[0][0]['id']],[PDO::PARAM_INT]);
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:green;">标 记 备 注</h3>
<p style="line-height:2px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post">
	<!-- 项次选择框 -->
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">第</span>
			<select name="scene" class="form-control">
				<?php foreach($sceneList[0] as $sceneInfo){ ?>
					<option value="<?=$sceneInfo['scene'];?>" <?php if($sceneInfo['scene']==$scene){?>selected<?php } ?>><?=$sceneInfo['scene'];?></option>
				<?php } ?>
			</select>
			<span class="input-group-addon">场</span>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">第</span>
			<select name="orderIndex" class="form-control">
				<?php foreach($orderIndexList[0] as $orderIndexInfo){ ?>
					<option value="<?=$orderIndexInfo['order_index'];?>" <?php if($orderIndexInfo['order_index']==$orderIndex){?>selected<?php } ?>><?=$orderIndexInfo['order_index'];?></option>
				<?php } ?>
			</select>
			<span class="input-group-addon">项</span>
		</div>
	</div>
	<!-- ./项次选择框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:48%" href="<?=ROOT_PATH.'admin/order.php?gamesId='.$gamesId;?>">< 返 回</a> <input type="submit" class="btn btn-success" style="width:48%" value="立 即 查 询 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php if(isset($_POST) && $_POST){ ?>
<hr>

<h3 style="text-align:center;font-weight:bold;"><?=$itemQuery[0][0]['sex'].$itemQuery[0][0]['group_name'].$itemQuery[0][0]['name'];?></h3>

<table class="table table-hover table-striped table-bordered scoreTable">
<tr>
	<th style="text-align:center;">组/道</th>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">备注</th>
	<th style="text-align:center;">操作</th>
</tr>
<?php for($i=0;$i<$scoreQuery[1];$i++){ ?>
<tr>
	<td><?=$scoreQuery[0][$i]['run_group'].'/'.$scoreQuery[0][$i]['runway'];?></td>
	<td><?=$scoreQuery[0][$i]['name'];?></td>
	<td><?=$scoreQuery[0][$i]['short_name'];?></td>
	<td><?=$scoreQuery[0][$i]['remark'];?></td>
	<td>
		<?php if($scoreQuery[0][$i]['remark']!="DNS"){ ?>
		<button class="btn btn-primary" onclick="setRemark(<?=$scoreQuery[0][$i]['id'];?>,'DNS')">DNS弃权</button>
		<?php }if($scoreQuery[0][$i]['remark']!="DSQ"){ ?>
		<button class="btn btn-danger" onclick="setRemark(<?=$scoreQuery[0][$i]['id'];?>,'DSQ')">DSQ犯规</button>
		<?php }if($scoreQuery[0][$i]['remark']!="TRI"){ ?>
		<button class="btn btn-success" onclick="setRemark(<?=$scoreQuery[0][$i]['id'];?>,'TRI')">TRI测验</button>
		<?php }if($scoreQuery[0][$i]['remark']!=""){ ?>
		<button class="btn btn-success" onclick="setRemark(<?=$scoreQuery[0][$i]['id'];?>,'')">清空备注</button>
		<?php } ?>
	</td>
</tr>
<?php } ?>
</table>
<?php } ?>

<?php include('../../include/footer.php'); ?>

<script>
function setRemark(id,type){
	$.ajax({
		url:"<?=ROOT_PATH;?>admin/order/toSetRemark.php",
		type:"post",
		data:{"id":id,"type":type},
		dataType:"json",
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
			}else{
				console.log(ret);
				alert("修改失败！");
			}
		}
	});
}
</script>

</body>
</html>
