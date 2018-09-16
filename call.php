<?php
/**
 * @name 生蚝体育比赛管理系统-Web-检录
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-20
 * @update 2018-09-07
 */
	
require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:#FF79BC;">检 录 处</h3>

<hr>

<center>
	<button class="btn btn-primary" style="width:63%;" onclick="getCallingItem()"><i class="fa fa-refresh" aria-hidden="true"></i> 刷 新</button>
	<button id="startAutoButton" class="btn btn-success" style="width:35%;" onclick='autoRefresh()'>自动刷新(30s)</button>
	<button id="stopAutoButton" class="btn btn-default" style="width:35%;display:none;" onclick='autoRefresh("stop")'>停止自动刷新</button>
</center>

<br>

<center>
	<font style="font-size:17px;" id="callBeginTime"></font>
</center>

<br>

<table id="table" class="table table-hover table-striped table-bordered scheduleTable">
<tr>
	<th style="text-align:center;background-color:#BBFFBB;font-size:17px;font-weight:bold;" colspan="6" id="sceneId"></th>
</tr>
<tr>
	<th style="vertical-align:middle;text-align:center;width:14%">状态</th>
	<th style="vertical-align:middle;text-align:center;width:10%">项次</th>
	<th style="vertical-align:middle;text-align:center;width:14%">性别</th>
	<th style="vertical-align:middle;text-align:center;">组别</th>
	<th style="vertical-align:middle;text-align:center;">名称</th>
	<th style="vertical-align:middle;text-align:center;width:10%">组数</th>
</tr>
<tr id="tr_1" style="display:none;background-color:#C4E1FF;">
	<td id="call_tips" style="background-color:#C4E1FF;text-align:center;vertical-align:middle;font-weight:bold;">正在<br>检录</td>
	<td id="call_orderIndex" style="font-weight:bold;text-align:center;vertical-align:middle;"></td>
	<td id="call_sex" style="text-align:center;vertical-align:middle;"></td>
	<td id="call_groupName" style="text-align:center;vertical-align:middle;"></td>
	<td id="call_name" style="text-align:center;vertical-align:middle;"></td>
	<td id="call_totalGroup" style="text-align:center;vertical-align:middle;"></td>
</tr>
<tr id="tr_2" style="display:none;">
	<td id="ready_1_orderIndex" style="font-weight:bold;text-align:center;vertical-align:middle;"></td>
	<td id="ready_1_sex" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_1_groupName" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_1_name" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_1_totalGroup" style="text-align:center;vertical-align:middle;"></td>
</tr>
<tr id="tr_3" style="display:none;background-color:#FFDAC8;">
	<td id="ready_tips" style="background-color:#FFDAC8;text-align:center;vertical-align:middle;font-weight:bold;">准备<br>检录</td>
	<td id="ready_2_orderIndex" style="font-weight:bold;text-align:center;vertical-align:middle;"></td>
	<td id="ready_2_sex" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_2_groupName" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_2_name" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_2_totalGroup" style="text-align:center;vertical-align:middle;"></td>
</tr>
<tr id="tr_4" style="display:none;">
	<td id="ready_3_orderIndex" style="font-weight:bold;text-align:center;vertical-align:middle;"></td>
	<td id="ready_3_sex" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_3_groupName" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_3_name" style="text-align:center;vertical-align:middle;"></td>
	<td id="ready_3_totalGroup" style="text-align:center;vertical-align:middle;"></td>
</tr>
<tr id="tr_5" style="display:none;">
	<td colspan="6" style="color:red;font-weight:bold;font-size:18px;">暂 无 检 录 信 息</td>
</tr>
</table>


<div style="width:98%;text-align:center;margin: 0 auto;">
	<div class="alert alert-warning">
		<i class="fa fa-info-circle" aria-hidden="true"></i> 如有疑问，请前往检录处咨询<br>
		数据仅供参考，建议<b>提早前往检录</b>，以免过号！
	</div>
	<div class="alert alert-info">
		<i class="fa fa-clock-o" aria-hidden="true"></i> 您可参考 当前项开始时间 及 组数 以估算您的检录时间
	</div>
	<div class="alert alert-success">
		<i class="fa fa-refresh fa-spin" aria-hidden="true"></i> 点击“自动刷新”后，系统将每30s自动刷新一次
	</div>
</div>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:98%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

<script>
var gamesId="<?=$gamesId;?>";
var countdown=30;
var timerTaskId=0;

getCallingItem();

function getCallingItem(){
	lockScreen();
	clean();

	$.ajax({
		url:"getCallingItem.php",
		data:{"gamesId":gamesId},
		dataType:"json",
		error:function(e){
			unlockScreen();
			alert("网络不稳定，请点击刷新重试！\n\nTips:建议先前往检录处查看检录情况，以免错过检录");
			console.log(e);
			return false;
		},
		success:function(ret){
			unlockScreen();

			if(ret.code==200){
				// 正在检录
				callingInfo=ret.data['calling'];
				readyingInfo=ret.data['readying'];
				callBeginTime=ret.data['callBeginTime'];

				$("#tr_1").attr("style","background-color:#C4E1FF;");
				$("#callBeginTime").html("本项开始检录时间：<b>"+callBeginTime+"</b>");
				$("#sceneId").html("第 "+callingInfo['scene']+" 场");
				$("#call_orderIndex").html(callingInfo['order_index']);
				$("#call_sex").html(callingInfo['sex']);
				$("#call_groupName").html(callingInfo['group_name']);
				$("#call_name").html(callingInfo['name']);
				$("#call_totalGroup").html(callingInfo['total_group']);

				if(readyingInfo.length>=1){
					$("#tr_2").attr("style","");
					$("#tr_3").attr("style","display:none;");
					$("#tr_4").attr("style","display:none;");
					$("#call_tips").attr("rowspan",2);
					$("#ready_1_orderIndex").html(readyingInfo[0]['order_index']);
					$("#ready_1_sex").html(readyingInfo[0]['sex']);
					$("#ready_1_groupName").html(readyingInfo[0]['group_name']);
					$("#ready_1_name").html(readyingInfo[0]['name']);
					$("#ready_1_totalGroup").html(readyingInfo[0]['total_group']);
				}

				if(readyingInfo.length>=2){
					$("#tr_3").attr("style","background-color:#FFDAC8;");
					$("#tr_4").attr("style","display:none;");
					$("#ready_2_orderIndex").html(readyingInfo[1]['order_index']);
					$("#ready_2_sex").html(readyingInfo[1]['sex']);
					$("#ready_2_groupName").html(readyingInfo[1]['group_name']);
					$("#ready_2_name").html(readyingInfo[1]['name']);
					$("#ready_2_totalGroup").html(readyingInfo[1]['total_group']);
				}

				if(readyingInfo.length>=3){
					$("#tr_4").attr("style","");
					$("#ready_tips").attr("rowspan",2);
					$("#ready_3_orderIndex").html(readyingInfo[2]['order_index']);
					$("#ready_3_sex").html(readyingInfo[2]['sex']);
					$("#ready_3_groupName").html(readyingInfo[2]['group_name']);
					$("#ready_3_name").html(readyingInfo[2]['name']);
					$("#ready_3_totalGroup").html(readyingInfo[2]['total_group']);
				}
			}else if(ret.code==1){
				$("#tr_1").attr("style","display:none;");
				$("#tr_2").attr("style","display:none;");
				$("#tr_3").attr("style","display:none;");
				$("#tr_4").attr("style","display:none;");
				$("#tr_5").attr("style","");
			}else{
				alert("系统错误，请联系管理员！\n\nTips:建议先前往检录处查看检录情况，以免错过检录");
				console.log(ret);
				return false;
			}
		}
	});
}


function autoRefresh(type=""){
	if(countdown==0){
		getCallingItem();
		countdown=30;
	}else{
		countdown--;
	}

	if(type=="stop"){
		clearTimeout(timerTaskId);
		$("#startAutoButton").attr("style","width:35%;");
		$("#stopAutoButton").attr("style","width:35%;display:none;");
	}else{
		$("#startAutoButton").attr("style","width:35%;display:none;");
		$("#stopAutoButton").attr("style","width:35%;");
		timerTaskId=setTimeout(function(){autoRefresh()},1000);
	}
}


function clean(){
	// 清除在检行内容
	$("#call_tips").attr("rowspan",1);
	$("#call_orderIndex").html("");
	$("#call_sex").html("");
	$("#call_groupName").html("");
	$("#call_name").html("");
	$("#call_totalGroup").html("");

	$("#sceneId").html("");
	$("#callBeginTime").html("");
	$("#ready_tips").attr("rowspan",1);

	// 所有行都要隐藏
	$("#tr_1").attr("style","display:none;");
	$("#tr_2").attr("style","display:none;");
	$("#tr_3").attr("style","display:none;");
	$("#tr_4").attr("style","display:none;");
	$("#tr_5").attr("style","display:none;");

	// 循环清理预检行内容
	for(i=1;i<=3;i++){
		$("#ready_"+i+"_orderIndex").html("");
		$("#ready_"+i+"_sex").html("");
		$("#ready_"+i+"_groupName").html("");
		$("#ready_"+i+"_name").html("");
		$("#ready_"+i+"_totalGroup").html("");
	}
}
</script>

</body>
</html>
