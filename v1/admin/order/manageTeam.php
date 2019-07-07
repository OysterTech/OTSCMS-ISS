<?php
/**
 * @name 生蚝体育比赛管理系统-Web-团体管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-25
 * @update 2018-09-06
 */

require_once '../../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex();
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex();
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$teamSql="SELECT id,name,short_name FROM team WHERE games_id=?";
$teamQuery=PDOQuery($dbcon,$teamSql,[$gamesId],[PDO::PARAM_INT]);

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

<h3 style="font-weight:bold;text-align:center;color:orange;">团 体 管 理</h3>

<hr>

<table class="table table-hover table-striped table-bordered teamScoreTable">
<tr>
	<td colspan="3"><button class="btn btn-success" onClick="$('#0').attr('style','');$('#name_0').focus();" style="width:98%;">新 增 团 体</button></td>
</tr>
<tr>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">简称</th>
	<th style="text-align:center;">操作</th>
</tr>

<?php foreach($teamQuery[0] as $teamInfo){ ?>
<tr id="tr_<?=$teamInfo['id'];?>">
	<td style="text-align:center;vertical-align:middle;">
		<p id="name_1_<?=$teamInfo['id'];?>"><?=$teamInfo['name'];?></p>
		<input class="form-control" id="name_2_<?=$teamInfo['id'];?>" value="<?=$teamInfo['name'];?>" style="display:none">
	</td>
	<td style="text-align:center;vertical-align:middle;">
		<p id="shortName_1_<?=$teamInfo['id'];?>"><?=$teamInfo['short_name'];?></p>
		<input class="form-control" id="shortName_2_<?=$teamInfo['id'];?>" value="<?=$teamInfo['short_name'];?>" style="display:none">
	</td>
	<td>
		<button id="button_1_<?=$teamInfo['id'];?>" class="btn btn-primary" onClick="readyEdit('<?=$teamInfo['id'];?>')">修改</button>
		<button id="button_2_<?=$teamInfo['id'];?>" class="btn btn-warning" onClick="cancel('<?=$teamInfo['id'];?>')" style="display:none;">取消</button>
		<button id="button_3_<?=$teamInfo['id'];?>" class="btn btn-success" onClick="toEdit('<?=$teamInfo['id'];?>')" style="display:none;">保存</button>
		<button id="button_4_<?=$teamInfo['id'];?>" class="btn btn-danger" onClick="del('<?=$teamInfo['id'];?>','<?=$teamInfo['short_name'];?>')" style="display:none;">删除</button>
	</td>
</tr>
<?php } ?>
<tr style="display:none;" id="0">
	<td><input class="form-control" id="name_0"></td>
	<td><input class="form-control" id="shortName_0"></td>
	<td><button id="button_0" class="btn btn-success" onClick="toAdd()">保存</button></td>
</tr>
</table>

<center>
	<a href="<?=ROOT_PATH;?>admin/order.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 上 一 页</a>
</center>

<?php include '../../include/footer.php'; ?>

<script>
var gamesId=getURLParam("gamesId");

function cancel(id){
	$("#name_2_"+id).attr("style","display:none");
	$("#name_1_"+id).attr("style","");
	$("#shortName_2_"+id).attr("style","display:none");
	$("#shortName_1_"+id).attr("style","");
	$("#button_4_"+id).attr("style","display:none");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}


function readyEdit(id){
	$("#name_1_"+id).attr("style","display:none");
	$("#name_2_"+id).attr("style","");
	$("#shortName_1_"+id).attr("style","display:none");
	$("#shortName_2_"+id).attr("style","");
	$("#button_4_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");	
}

function toEdit(id){
	name=$("#name_2_"+id).val();
	shortName=$("#shortName_2_"+id).val();
	
	cancel(id);
	
	$.ajax({
		url:"toManageTeam.php",
		type:"POST",
		data:{"type":"edit","id":id,"name":name,"shortName":shortName},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				$("#name_1_"+id).html(name);
				$("#shortName_1_"+id).html(shortName);
				alert("修改成功！");
			}else{
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}


function toAdd(){
	name=$("#name_0").val();
	shortName=$("#shortName_0").val();
	
	$("#name_0").attr("disabled","disabled");
	$("#shortName_0").attr("disabled","disabled");
	$("#button_0").attr("style","display:none;");

	$.ajax({
		url:"toManageTeam.php",
		type:"POST",
		data:{"type":"add","gamesId":gamesId,"name":name,"shortName":shortName},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				alert("新增成功！");
				location.reload();
			}else{
				$("#name_0").removeAttr("disabled");
				$("#shortName_0").removeAttr("disabled");
				$("#button_0").attr("style","");
				console.log(ret);
				alert("新增失败！！！\n数据未被保存！");
			}
		}
	})
}


function del(id,shortName){
	if(confirm("确认要删除 ["+shortName+"] 队吗？")){
		$.ajax({
			url:"toManageTeam.php",
			type:"POST",
			data:{"type":"del","gamesId":gamesId,'id':id},
			dataType:"JSON",
			success:function(ret){
				if(ret.code==200){
					alert("删除成功！");
					$("#tr_"+id).remove();
				}else{
					console.log(ret);
					alert("删除失败！！！");
				}
			}
		});
	}else{
		return;
	}
}
</script>

</body>
</html>
