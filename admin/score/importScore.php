<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台导入成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-14
 * @update 2018-09-24
 */
	
require_once '../../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

$_SESSION['sport_admin_gamesId']=$gamesId;
$gamesKind=$_SESSION['sport_admin_gamesJson']['kind'];

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$scene=0;
$orderIndex=0;
$sceneList=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);
$orderIndexList=PDOQuery($dbcon,"SELECT DISTINCT(order_index) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);

$groupSql="SELECT DISTINCT(group_name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY group_name";
$groupQuery=PDOQuery($dbcon,$groupSql,[$gamesId],[PDO::PARAM_INT]);
$nameSql="SELECT DISTINCT(name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY name DESC";
$nameQuery=PDOQuery($dbcon,$nameSql,[$gamesId],[PDO::PARAM_INT]);

if($sceneList[1]<1 || $orderIndexList[1]<1 || $groupQuery[1]<1 || $nameQuery[1]<1){
	die('<script>alert("暂未录入日程，敬请期待！");history.go(-1);</script>');
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../../include/header.php'; ?>
	<style>
	.tips{font-size:22;font-weight:bolder;}
	</style>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:green;">成 绩 上 传</h3>
<p style="line-height:8px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post" enctype="multipart/form-data">
	<?php if($gamesKind=="田径"){ ?>
	<!-- 田径-组别分类选择框 -->
	<div class="col-xs-7">
		<div class="form-group">
			<select id="groupName" name="groupName" class="form-control">
				<?php foreach($groupQuery[0] as $groupInfo){ ?>
				<option value="<?=$groupInfo['group_name'];?>"><?=$groupInfo['group_name'];?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-xs-5">
		<div class="form-group">
			<select id="sex" name="sex" class="form-control">
				<option value="男子">男子</option>
				<option value="女子">女子</option>
				<option value="男女">男女</option>
			</select>
		</div>
	</div>
	<br>
	<div class="col-xs-7">
		<div class="form-group">
			<select id="name" name="name" class="form-control">
				<?php foreach($nameQuery[0] as $nameInfo){ ?>
				<option value="<?=$nameInfo['name'];?>"><?=$nameInfo['name'];?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="col-xs-5">
		<div class="form-group">
			<select id="isFinal" name="isFinal" class="form-control">
				<option value="0">预赛</option>
				<option value="1">决赛</option>
			</select>
		</div>
	</div>
	<!-- ./田径-组别分类选择框 -->
	<?php }else{ ?>
	<!-- 项次选择框 -->
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">第</span>
			<select id="scene" name="scene" class="form-control">
				<?php foreach($sceneList[0] as $sceneInfo){ ?>
					<option value="<?=$sceneInfo['scene'];?>"><?=$sceneInfo['scene'];?></option>
				<?php } ?>
			</select>
			<span class="input-group-addon">场</span>
		</div>
	</div>
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">第</span>
			<select id="orderIndex" name="orderIndex" class="form-control">
				<?php foreach($orderIndexList[0] as $orderIndexInfo){ ?>
					<option value="<?=$orderIndexInfo['order_index'];?>"><?=$orderIndexInfo['order_index'];?></option>
				<?php } ?>
			</select>
			<span class="input-group-addon">项</span>
		</div>
	</div>
	<!-- ./项次选择框 -->
	<?php } ?>
	
	<br><br><br>
	
	<!-- 文件上传框 -->
	<center>
	<div class="input-group" style="width:98%">
		<span class="input-group-addon">上传Excel文件</span>
		<input type="file" name="myfile[]" id="myfile" class="form-control" style="height:40px">
	</div>
	</center>
	<!-- ./文件上传框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:32%" href="<?=ROOT_PATH.'admin/score.php?gamesId='.$gamesId;?>">< 返 回</a> <?php if($gamesKind!="田径"){ ?><a href="<?=ROOT_PATH;?>admin/order/setRemark.php?gamesId=<?=$gamesId;?>" target="_blank" class="btn btn-warning" style="width:32%"><i class="fa fa-bookmark-o" aria-hidden="true"></i> 标 记 运 动 员 备 注</a><?php } ?> <button class="btn btn-success" style="width:32%" type="button" onclick="upload();">上 传 文 件 并 导 入 &gt;</button>
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php include '../../include/footer.php'; ?>

<script>
function upload(){
	if($("#myfile").val().length>0){
		var formData = new FormData($('form')[0]);
		formData.append('file',$('#myfile')[0].files[0]);

		$.ajax({
			url:'<?=ROOT_PATH;?>admin/score/toImportScore.php',
			type: "post",
			data: formData,
			dataType:"json",
			cache: false,
			contentType: false,
			processData: false,
			error:function(e){
				console.log(JSON.stringify(e));
				$("#tips").html("服务器错误！请联系管理员！");
				$("#tipsModal").modal('show');
				return false;
			},
			success:function(ret){
				console.log(ret);
				if(ret.code==200){
					html="导入成功！<hr>"
					    +"<font color='black'>"
					    +"项目名：<font color='blue'>"+ret.data['itemName']+"</font><br>"
					    +"已导入条数：<font color='green'>"+ret.data['rows']+"</font>"
					    +"</font>";

					if(ret.data['tipsRows']!=0){
						html+="<hr>请注意有 <font color='blue'>"+ret.data['tipsRows']+"</font> 名运动员无成绩<br>"
						    +"如为犯规/弃权或其他特殊情况请尽快标记<br>"
						    +"<font color='green'>"+ret.data['tipsNames']+"</font>";
					}

					$("#tips").html(html);
					$("#tipsModal").modal('show');
					return;
				}else if(ret.code==0){
					html="导入失败！！！<hr>"
					    +"<font color='black'>"
					    +"项目名：<font color='blue'>"+ret.data['itemName']+"</font><br>"
					    +"表格总条数：<font color='green'>"+ret.data['total']+"</font><br>"
					    +"已导入条数：<font color='green'>"+ret.data['rows']+"</font>"
					    +"</font>";
					if(ret.data['tipsRows']!=0){
						html+="<hr>请注意有 <font color='blue'>"+ret.data['tipsRows']+"</font> 名运动员无成绩<br>"
						    +"如为犯规/弃权或其他特殊情况请尽快标记<br>"
						    +"<font color='green'>"+ret.data['tipsNames']+"</font>";
					}
					$("#tips").html(html);
					$("#tipsModal").modal('show');
					return;
				}else if(ret.code==1){
					$("#tips").html("导入失败！！！<hr>成绩文件已存在！");
					$("#tipsModal").modal('show');
					return;
				}else if(ret.code==2){
					$("#tips").html("导入失败！！！<hr>没有此项目！");
					$("#tipsModal").modal('show');
					return;
				}else{
					$("#tips").html("未知系统错误！<br>请提交错误码["+ret.code+ret.message+"]给管理员");
					$("#tipsModal").modal('show');
					return;
				}
			}
		});
	}else{
		alert("请选择需要上传的成绩Excel文件！");
		$("#file").focus();
		return false;
	}
}
</script>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<form method="post">
					<font color="red" style="font-weight:bolder;font-size:22px;text-align:center;">
						<p id="tips"></p>
					</font>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" onclick='isAjaxing=0;$("#tipsModal").modal("hide");'>返回 &gt;</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>