<?php
/**
 * @name 生蚝体育比赛管理系统-Web-按项次查询成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-22
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
$gamesKind=$_SESSION['swim_gamesJson']['kind'];

if($gamesKind=="田径"){
	die(header("Location:".ROOT_PATH));
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

<h3 style="font-weight:bold;text-align:center;color:green;margin-bottom:14px;">成 绩 查 询</h3>

<?php include '../include/selectItem.php'; ?>

<!-- 提交按钮 -->
<center>
	<a class="btn btn-primary" style="width:48%" href="../score.php">< 返 回</a> <button class="btn btn-success" style="width:48%" onclick="search()">立 即 查 询 ></button>
</center>
<!-- ./提交按钮 -->

<hr>

<h3 style="text-align:center;font-weight:bold;" id="itemName"></h3>

<table id="table" class="table table-hover table-striped table-bordered scoreTable">
<tr>
	<th style="text-align:center;width:5%">排名</th>
	<th style="text-align:center;width:9%">组/道</th>
	<th style="text-align:center;width:16%">名称</th>
	<th style="text-align:center;width:21%">单位</th>
	<th style="text-align:center;">成绩</th>
	<th style="text-align:center;" id="scoreName">得分</th>
	<th style="text-align:center;">备注</th>
</tr>
</table>

<center><div style="width:96%;text-align: center;">
	<div class="alert alert-info"><i class="fa fa-info-circle" aria-hidden="true"></i> 备注：DNS 弃权、DSQ 犯规、TRI测试</div>
</div><center>

<?php include('../include/footer.php'); ?>

<script>
var gamesId="<?=$gamesId;?>";

function search(){
	lockScreen();
	scene=$("#scene").val();
	orderIndex=$("#orderIndex").val();

	$.ajax({
		url:"getScore.php",
		type:"get",
		data:{"orderBy":"item","gamesId":gamesId,"scene":scene,"orderIndex":orderIndex},
		dataType:"json",
		success:function(ret){
			// 先清空表格和项目名
			$("#table tr:not(:first)").html("");
			$("#itemName").html("");

			unlockScreen();

			if(ret.code==1){
				$("#tips").html("当前选中的项次("+scene+"/"+orderIndex+")<br>无对应项目！");
				$("#tipsModal").modal('show');
				return false;
			}if(ret.code==1){
				$("#tips").html("参数缺失，请联系管理员！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.code==200){
				// 显示项目名称
				$("#itemName").html(ret.data['itemName']);

				// 显示成绩分/得分名称
				if(ret.data['isAllround']==1){
					isAllround=1;
					$("#scoreName").html('成绩分');
				}else if(ret.data['isAllround']==0){
					isAllround=0;
					$("#scoreName").html('得分');
				}

				scoreData=ret.data['scoreData'];

				// 如果没有任何成绩
				if(scoreData=="" || scoreData[0]['rank']==null){
					html="<tr>"
					    +"<td colspan='7' style='font-weight:bold;color:red;font-size:18px;'>本 项 目 暂 未 上 传 成 绩</td>"
					    +"</tr>";
					$("#table").append(html);
					return;
				}

				// 循环显示成绩条
				for(i in scoreData){
					info=scoreData[i];

					// 若为空则不显示
					if(info['rank']==0 || info['rank']==null) info['rank']="";
					if(info['score']==null) info['score']="";
					if(info['point']==null) info['point']="";
					if(info['allround_point']==null) info['allround_point']="";
					if(info['remark']==null) info['remark']="";

					// 判断显示全能成绩分/得分
					if(isAllround==1){
						if(info['allround_point']==0) point="";
						else point=info['allround_point'];
					}else if(isAllround==0){
						if(info['point']==0) point="";
						else point=info['point'];
					}

					// 组合并添加行
					html="<tr>"
					    +"<td style='font-weight:bold;color:blue;'>"+info['rank']+"</td>"
					    +"<td>"+info['run_group']+"/"+info['runway']+"</td>"
					    +"<td>"+info['name']+"</td>"
					    +"<td>"+info['short_name']+"</td>"
					    +"<td>"+info['score']+"</td>"
					    +"<td>"+point+"</td>"
					    +"<td>"+info['remark']+"</td>"
					    +"</tr>";
					$("#table").append(html);
				}
			}
		}
	})
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
				<button type="button" class="btn btn-primary" data-dismiss="modal">返回 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
