<?php
/**
 * @name 生蚝体育比赛管理系统-Web-修改日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-21
 * @update 2018-09-01
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
	$extraJson=json_decode($gamesInfo[0][0]['extra_json'],true);
}

$sceneInfo=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);
$sceneInfo=$sceneInfo[0];
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

<h3 style="font-weight:bold;text-align:center;color:red;">比 赛 日 程 表</h3>

<hr>

<center><div style="width:96%;text-align: center;">
	<div class="alert alert-success">
		项目标黄为全能项目<br>
		全能项目仅显示成绩分，不显示得分
	</div>
</div><center>

<?php
foreach($sceneInfo as $scene){
	$sceneId=$scene['scene'];
?>

<table id="table_<?=$sceneId;?>" class="table table-hover table-striped table-bordered scheduleTable">
<tr>
	<th style="text-align:center;font-size:16px;background-color:#C4E1FF" colspan="7">
		第 <?=$sceneId;?> 场<?php if(isset($extraJson['scene'][$sceneId])) echo "（".$extraJson['scene'][$sceneId]."）";?>
		<input id="time_<?=$sceneId;?>" value="<?php if(isset($extraJson['scene'][$sceneId])) echo $extraJson['scene'][$sceneId];?>" style="display:none;">
		<button id="time_btn_1_<?=$sceneId;?>" class="btn btn-primary" onClick="editStartTime('<?=$sceneId;?>');">修改开场时间</button>
		<button id="time_btn_2_<?=$sceneId;?>" class="btn btn-success" onClick="saveStartTime('<?=$sceneId;?>');" style="display:none;">保存开场时间</button>
		<button id="btn_1_<?=$sceneId;?>" class="btn btn-success" onClick="add('<?=$sceneId;?>');">新增项目</button>
	</th>
</tr>
<tr>
	<th style="text-align:center;">项次</th>
	<th style="text-align:center;">性别</th>
	<th style="text-align:center;">组别</th>
	<th style="text-align:center;">项目名</th>
	<th style="text-align:center;">组数</th>
	<th style="text-align:center;">人(队)数</th>
	<th style="text-align:center;">操作</th>
</tr>
<?php
	$sceneItemInfoSql="SELECT * FROM item WHERE games_id=? AND scene=? AND is_delete=0 ORDER BY order_index";
	$sceneItemInfo=PDOQuery($dbcon,$sceneItemInfoSql,[$gamesId,$sceneId],[PDO::PARAM_INT,PDO::PARAM_INT]);
	$totalItem=$sceneItemInfo[1];
	$itemInfo=$sceneItemInfo[0];
	foreach($itemInfo as $info){
		$itemId=$info['id'];
?>
<tr id="tr_<?=$itemId;?>" <?php if($info['is_allround']==1) echo 'style="background-color:#F4FF81"';?>>
	<td>
		<p id="order_1_<?=$itemId;?>"><?=$info['order_index'];?></p>
		<input id="order_2_<?=$itemId;?>" value="<?=$info['order_index'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="sex_1_<?=$itemId;?>"><?=$info['sex'];?></p>
		<select id="sex_2_<?=$itemId;?>" value="<?=$info['sex'];?>" class="form-control" style="display:none;">
			<option value="男子">男子</option>
			<option value="女子">女子</option>
			<option value="男女">男女</option>
		</select>
	</td>
	<td>
		<p id="groupName_1_<?=$itemId;?>"><?=$info['group_name'];?></p>
		<input id="groupName_2_<?=$itemId;?>" value="<?=$info['group_name'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="name_1_<?=$itemId;?>"><?=$info['name'];?></p>
		<input id="name_2_<?=$itemId;?>" value="<?=$info['name'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="totalGroup_1_<?=$itemId;?>"><?=$info['total_group'];?></p>
		<input id="totalGroup_2_<?=$itemId;?>" value="<?=$info['total_group'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="totalAth_1_<?=$itemId;?>"><?=$info['total_ath'];?></p>
		<input id="totalAth_2_<?=$itemId;?>" value="<?=$info['total_ath'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<button id="button_1_<?=$itemId;?>" onclick='edit("<?=$itemId;?>")' class="btn btn-primary">编辑</button>
		<button id="button_2_<?=$itemId;?>" onclick='cancel("<?=$itemId;?>")' class="btn btn-success" style="display:none;">取消</button>
		<button id="button_3_<?=$itemId;?>" onclick='save("<?=$itemId;?>")' class="btn btn-warning" style="display:none;">保存</button>
		<?php if($info['is_allround']==0){ ?>
		<button id="button_4_<?=$itemId;?>" onclick='setType("<?=$itemId;?>","allround")' class="btn btn-info" style="display:none;">设为<b>全能</b></button>
		<?php }else{ ?>
		<button id="button_5_<?=$itemId;?>" onclick='setType("<?=$itemId;?>","single")' class="btn btn-info" style="display:none;">设为<b>单项</b></button>
		<?php } ?>
		<button id="button_6_<?=$itemId;?>" onclick='del("<?=$itemId;?>","<?=$sceneId;?>","<?=$info['order_index'];?>","<?=$info['sex'].$info['group_name'].$info['name'];?>")' class="btn btn-danger" style="display:none;">删除</button>

	</td>
</tr>
<?php } ?>
</table><br>
<?php } ?>

<center>
	<a href="<?=ROOT_PATH;?>admin/schedule.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 日 程 管 理</a>
</center>

<?php include '../../include/footer.php'; ?>

<script>
var gamesId=getURLParam("gamesId");
var addingScene=-1;

function add(sceneId){
	if(addingScene==-1){
		addingScene=sceneId;
		html="<tr id='tr_0'>"
			+"<td><input id='order_0' class='form-control'></td>"
			+"<td><select id='sex_0' value='<?=$info['sex'];?>' class='form-control'><option value='男子'>男子</option><option value='女子'>女子</option><option value='男女'>男女</option></select></td>"
			+"<td><input id='groupName_0' class='form-control'></td>"
			+"<td><input id='name_0' class='form-control'></td>"
			+"<td><input id='totalGroup_0' class='form-control'></td>"
			+"<td><input id='totalAth_0' class='form-control'></td>"
			+"<td><button onclick='cancelAdd()' class='btn btn-warning'>取消</button> <button onclick='saveAdd()' class='btn btn-success'>保存新增</button></td>"
			+"</tr>";
		$("#table_"+sceneId).append(html);
		$("#order_0").focus();
	}else{
		alert("请先保存正在新增的第 "+addingScene+" 场项目数据！");
		$("#order_0").focus();
		return false;
	}
}


function saveAdd(){
	order=$("#order_0").val();
	sex=$("#sex_0").val();
	groupName=$("#groupName_0").val();
	name=$("#name_0").val();
	totalGroup=$("#totalGroup_0").val();
	totalAth=$("#totalAth_0").val();

	if(order=="" || sex==null || groupName=="" || name=="" || totalGroup=="" || totalAth==""){
		alert("逻辑检查结果：请完整输入所有项目数据！");
		return false;
	}
	if(parseInt(totalGroup)>=parseInt(totalAth)){
		alert("逻辑检查结果：请正确输入 组数 和 人(队)数！");
		return false;
	}

	$.ajax({
		url:"toEdit.php",
		type:"post",
		dataType:"json",
		data:{"type":"add","gamesId":gamesId,"sceneId":addingScene,"order":order,"sex":sex,"groupName":groupName,"name":name,"totalGroup":totalGroup,"totalAth":totalAth},
		success:function(ret){
			if(ret.code==200){
				alert("新增成功！");
				location.reload();
			}else{
				console.log(ret);
				alert("新增失败！！！");
			}
		}
	})
}


function cancelAdd(){
	$("#tr_0").remove();
	addingScene=-1;
}


function del(id,scene,orderIndex,name){
	if(confirm("确认要删除 ["+scene+"/"+orderIndex+" "+name+"] 吗？")){
		$.ajax({
			url:"toEdit.php",
			type:"post",
			dataType:"json",
			data:{"type":"del","gamesId":gamesId,"id":id},
			success:function(ret){
				if(ret.code==200){
					alert("删除成功！");
					$("#tr_"+id).remove();
				}else{
					console.log(ret);
					alert("删除失败！！！");
				}
			}
		})
	}else{
		return;
	}
}


function editStartTime(sceneId){
	$("#time_"+sceneId).attr("style","");
	$("#time_btn_1_"+sceneId).attr("style","display:none;");
	$("#time_btn_2_"+sceneId).attr("style","");
}


function saveStartTime(sceneId){
	time=$("#time_"+sceneId).val();
	url="toSaveStartTime.php?gamesId="+gamesId;

	$.ajax({
		url:url,
		type:"post",
		dataType:"json",
		data:{"gamesId":gamesId,"id":sceneId,"time":time},
		success:function(ret){
			if(ret.code==200){
				$("#time_"+sceneId).attr("style","display:none;");
				$("#time_btn_1_"+sceneId).attr("style","");
				$("#time_btn_2_"+sceneId).attr("style","display:none;");
				alert("修改成功！");
				location.reload();
			}else{
				$("#time_"+sceneId).attr("style","display:none;");
				$("#time_btn_1_"+sceneId).attr("style","");
				$("#time_btn_2_"+sceneId).attr("style","display:none;");
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}


function edit(id){
	$("#order_1_"+id).attr("style","display:none");
	$("#order_2_"+id).attr("style","");
	$("#sex_1_"+id).attr("style","display:none");
	$("#sex_2_"+id).attr("style","");
	$("#groupName_1_"+id).attr("style","display:none");
	$("#groupName_2_"+id).attr("style","");
	$("#name_1_"+id).attr("style","display:none");
	$("#name_2_"+id).attr("style","");
	$("#totalGroup_1_"+id).attr("style","display:none");
	$("#totalGroup_2_"+id).attr("style","");
	$("#totalAth_1_"+id).attr("style","display:none");
	$("#totalAth_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_4_"+id).attr("style","");
	$("#button_5_"+id).attr("style","");
	$("#button_6_"+id).attr("style","");
}


function save(id){
	cancel(id);
	
	order=$("#order_2_"+id).val();
	sex=$("#sex_2_"+id).val();
	groupName=$("#groupName_2_"+id).val();
	name=$("#name_2_"+id).val();
	totalGroup=$("#totalGroup_2_"+id).val();
	totalAth=$("#totalAth_2_"+id).val();

	if(order=="" || sex==null || groupName=="" || name=="" || totalGroup=="" || totalAth==""){
		alert("逻辑检查结果：请完整输入所有项目数据！");
		return false;
	}
	if(parseInt(totalGroup)>=parseInt(totalAth)){
		alert("逻辑检查结果：请正确输入 组数 和 人(队)数！");
		return false;
	}

	$.ajax({
		url:"toEdit.php",
		type:"post",
		dataType:"json",
		data:{"type":"edit","id":id,"order":order,"sex":sex,"groupName":groupName,"name":name,"totalGroup":totalGroup,"totalAth":totalAth},
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				location.reload();
			}else{
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}


function setType(id,type){
	cancel(id);
	
	$.ajax({
		url:"toEditItemType.php",
		type:"post",
		dataType:"json",
		data:{"id":id,"type":type},
		success:function(ret){
			if(ret.code==200){
				alert("修改项目类型成功！");
				location.reload();
			}else{
				console.log(ret);
				alert("修改项目类型失败！！！\n数据未被保存！");
			}
		}
	})
}


function cancel(id){
	$("#order_2_"+id).attr("style","display:none");
	$("#order_1_"+id).attr("style","");
	$("#sex_2_"+id).attr("style","display:none");
	$("#sex_1_"+id).attr("style","");
	$("#groupName_2_"+id).attr("style","display:none");
	$("#groupName_1_"+id).attr("style","");
	$("#name_2_"+id).attr("style","display:none");
	$("#name_1_"+id).attr("style","");
	$("#totalGroup_2_"+id).attr("style","display:none");
	$("#totalGroup_1_"+id).attr("style","");
	$("#totalAth_2_"+id).attr("style","display:none");
	$("#totalAth_1_"+id).attr("style","");
	$("#button_6_"+id).attr("style","display:none");
	$("#button_5_"+id).attr("style","display:none");
	$("#button_4_"+id).attr("style","display:none");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}
</script>

</body>
</html>
