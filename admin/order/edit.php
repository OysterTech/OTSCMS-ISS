<?php
/**
 * @name 生蚝体育比赛管理系统-Web-修改秩序册
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-23
 * @update 2018-08-27
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
$teamSql="SELECT id,short_name FROM team WHERE games_id=?";
$teamQuery=PDOQuery($dbcon,$teamSql,[$gamesId],[PDO::PARAM_INT]);

if($sceneList[1]==0 && $orderIndexList[1]==0){
		die('<script>alert("暂未录入日程！");history.go(-1);</script>');
}

if(isset($_POST) && $_POST){
	$scene=$_POST['scene'];
	$orderIndex=$_POST['orderIndex'];
	$itemSql="SELECT id,sex,group_name,name,scene,order_index FROM item WHERE games_id=? AND scene=? AND order_index=? AND is_delete=0";
	$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId,$scene,$orderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	
	if($itemQuery[1]!=1){
		die('<script>alert("无此项目！");window.location.href="'.$url.'";</script>');
	}else{
		$itemName=$itemQuery[0][0]['sex'].$itemQuery[0][0]['group_name'].$itemQuery[0][0]['name'];
		$itemId=$itemQuery[0][0]['id'];
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

<h3 style="font-weight:bold;text-align:center;color:green;">修 改 秩 序 册</h3>
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

<h3 style="text-align:center;font-weight:bold;"><?=$itemName;?></h3>
<input type="hidden" id="itemId" value="<?=$itemId;?>">

<table id="table" class="table table-hover table-striped table-bordered scoreTable">
<tr>
	<th style="text-align:center;">组</th>
	<th style="text-align:center;">道</th>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">操作</th>
</tr>
<?php for($i=0;$i<$scoreQuery[1];$i++){ ?>
<tr id="tr_<?=$scoreQuery[0][$i]['id'];?>">
	<td>
		<p id="runGroup_1_<?=$scoreQuery[0][$i]['id'];?>"><?=$scoreQuery[0][$i]['run_group'];?></p>
		<input id="runGroup_2_<?=$scoreQuery[0][$i]['id'];?>" value="<?=$scoreQuery[0][$i]['run_group'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="runway_1_<?=$scoreQuery[0][$i]['id'];?>"><?=$scoreQuery[0][$i]['runway'];?></p>
		<input id="runway_2_<?=$scoreQuery[0][$i]['id'];?>" value="<?=$scoreQuery[0][$i]['runway'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="name_1_<?=$scoreQuery[0][$i]['id'];?>"><?=$scoreQuery[0][$i]['name'];?></p>
		<input id="name_2_<?=$scoreQuery[0][$i]['id'];?>" value="<?=$scoreQuery[0][$i]['name'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="team_1_<?=$scoreQuery[0][$i]['id'];?>"><?=$scoreQuery[0][$i]['short_name'];?></p>
		<select id="team_2_<?=$scoreQuery[0][$i]['id'];?>" class="form-control" style="display:none;">
			<option value="-1" selected disabled>-- 请选择单位 --</option>
			<?php foreach($teamQuery[0] as $teamInfo){ ?>
			<option value="<?=$teamInfo['id'];?>"><?=$teamInfo['short_name'];?></option>
			<?php } ?>
		</select>
	</td>
	<td>
		<button id="button_1_<?=$scoreQuery[0][$i]['id'];?>" onclick="readyUpdate('<?=$scoreQuery[0][$i]['id'];?>');" class="btn btn-primary">修改</button>
		<button id="button_2_<?=$scoreQuery[0][$i]['id'];?>" onclick="cancel('<?=$scoreQuery[0][$i]['id'];?>');" class="btn btn-success" style="display:none;">取消</button>
		<button id="button_3_<?=$scoreQuery[0][$i]['id'];?>" onclick="toUpdate('<?=$scoreQuery[0][$i]['id'];?>');" class="btn btn-warning" style="display:none;">保存</button>
		<button id="button_4_<?=$scoreQuery[0][$i]['id'];?>" onclick="del('<?=$scoreQuery[0][$i]['id'];?>');" class="btn btn-danger">删除</button>
	</td>
</tr>
<?php } ?>
</table>

<center>
	<button onclick="add()" style="width:98%" class="btn btn-success">新 增 运 动 员</button>
</center>
<?php } ?>

<?php include('../../include/footer.php'); ?>

<script>
var adding=0;

function del(id){
	if(confirm("确认要删除吗？")){
		$.ajax({
			url:"toEdit.php",
			type:"POST",
			data:{"type":"del","id":id},
			dataType:"JSON",
			success:function(ret){
				if(ret.code==200){
					$("#tr_"+id).remove();
					alert("删除成功！");
				}else{
					console.log(ret);
					alert("删除失败！！！");
				}
			}
		})
	}
}

function add(){
	if(adding==1){
		alert("请先保存上一条新增运动员资料！");
		return false;
	}else{
		adding=1;
	}

	tableHtml="<tr>"
	         +"<td><input id='runGroup_0' class='form-control'></td>"
	         +"<td><input id='runway_0' class='form-control'></td>"
	         +"<td><input id='name_0' class='form-control'></td>"
	         +"<td><select id='teamId_0' class='form-control'><option value='-1' selected disabled>-- 请选择单位 --</option><?php foreach($teamQuery[0] as $teamInfo){ ?><option value='<?=$teamInfo['id'];?>'><?=$teamInfo['short_name'];?></option><?php } ?></select></td>"
	         +"<td><button onclick='saveAdd()' class='btn btn-success'>保存新增</button></td>"
	         +"</tr>";
	$("#table").append(tableHtml);
}


function saveAdd(){
	itemId=$("#itemId").val();
	runGroup=$("#runGroup_0").val();
	runway=$("#runway_0").val();
	name=$("#name_0").val();
	teamId=$("#teamId_0").val();

	$.ajax({
		url:"toEdit.php",
		type:"POST",
		data:{"type":"add","itemId":itemId,"runGroup":runGroup,"runway":runway,"name":name,"teamId":teamId},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				adding=0;
				alert("新增成功！");
				window.location.href="<?=$url;?>";
			}else{
				console.log(ret);
				alert("新增失败！！！\n数据未被保存！");
			}
		}
	});
}


function cancel(id){
	$("#runGroup_2_"+id).attr("style","display:none");
	$("#runGroup_1_"+id).attr("style","");
	$("#runway_2_"+id).attr("style","display:none");
	$("#runway_1_"+id).attr("style","");
	$("#name_2_"+id).attr("style","display:none");
	$("#name_1_"+id).attr("style","");
	$("#team_2_"+id).attr("style","display:none");
	$("#team_1_"+id).attr("style","");
	$("#button_4_"+id).attr("style","");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}


function readyUpdate(id){
	$("#runGroup_1_"+id).attr("style","display:none");
	$("#runGroup_2_"+id).attr("style","");
	$("#runway_1_"+id).attr("style","display:none");
	$("#runway_2_"+id).attr("style","");
	$("#name_1_"+id).attr("style","display:none");
	$("#name_2_"+id).attr("style","");
	$("#team_1_"+id).attr("style","display:none");
	$("#team_2_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");
	$("#button_4_"+id).attr("style","display:none");
}

function toUpdate(id){
	runGroup=$("#runGroup_2_"+id).val();
	runway=$("#runway_2_"+id).val();
	name=$("#name_2_"+id).val();
	team=$("#team_2_"+id).val();
	teamName=$("#team_2_"+id).find("option:selected").text();

	cancel(id);

	$.ajax({
		url:"toEdit.php",
		type:"POST",
		data:{"type":"edit","id":id,"runGroup":runGroup,"runway":runway,"name":name,"team":team},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				$("#runGroup_1_"+id).html(runGroup);
				$("#runway_1_"+id).html(runway);
				$("#name_1_"+id).html(name);
				$("#team_1_"+id).html(teamName);
			}else{
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}
</script>

</body>
</html>
