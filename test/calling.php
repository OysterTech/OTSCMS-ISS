<?php
/**
 * @name 生蚝体育比赛管理系统-Web2-检录
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-07-06
 * @version 2019-07-06
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='检录处';
include 'header.php';
?>

<?php include 'component.php'; ?>

<body style="background-color:#57c5e2;">

<div id="app">

<page-navbar></page-navbar>

<games-title ref="header"></games-title>

<div class="container">
	<div class="row">
		
		<games-navbar></games-navbar>
		
		<div class="col-md-10">

			<center>
				<button class="btn btn-primary" style="width:63%;" @click="getCallingItem"><i class="fa fa-refresh" aria-hidden="true"></i> 刷 新</button>
				<button id="startAutoButton" class="btn btn-success" style="width:35%;" @click='autoRefresh'>自动刷新(30s)</button>
				<button id="stopAutoButton" class="btn btn-default" style="width:35%;display:none;" @click='autoRefresh("stop")'>停止自动刷新</button>
			</center>

			<br>

			<center>
				<font style="font-size:17px;" v-if="callingBeginTime!=''">本项开始检录时间：<b>{{callingBeginTime}}</b></font>
			</center>

			<br>

			<table class="table table-hover table-striped table-bordered">
				<tr v-if="scene!=0 && scene!=''">
					<th style="text-align:center;background-color:#BBFFBB;font-size:17px;font-weight:bold;" colspan="6">第 {{scene}} 场</th>
				</tr>
				<tr style="text-align:center;">
					<th style="vertical-align:middle;width:14%">状态</th>
					<th style="vertical-align:middle;width:10%">项次</th>
					<th style="vertical-align:middle;width:14%">性别</th>
					<th style="vertical-align:middle;">组别</th>
					<th style="vertical-align:middle;">名称</th>
					<th style="vertical-align:middle;width:10%">组数</th>
				</tr>
				<tr v-if="Object.keys(callingItem).length>=1" style="background-color:#C4E1FF;text-align: center;">
					<td style="background-color:#C4E1FF;vertical-align:middle;font-weight:bold;">正在<br>检录</td>
					<td style="vertical-align:middle;font-weight:bold;">{{callingItem['order_index']}}</td>
					<td style="vertical-align:middle;">{{callingItem['sex']}}</td>
					<td style="vertical-align:middle;">{{callingItem['group_name']}}</td>
					<td style="vertical-align:middle;">{{callingItem['name']}}</td>
					<td style="vertical-align:middle;">{{callingItem['total_group']}}</td>
				</tr>
				<tr v-if="Object.keys(readyingInfo).length>=1" style="text-align:center;">
					<td style="vertical-align:middle;font-weight:bold;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
				</tr>
				<tr v-if="Object.keys(callingItem).length>=2" style="text-align:center;background-color:#FFDAC8;">
					<td id="ready_tips" style="background-color:#FFDAC8;vertical-align:middle;font-weight:bold;">准备<br>检录</td>
					<td style="vertical-align:middle;font-weight:bold;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
				</tr>
				<tr v-if="Object.keys(callingItem).length>=3" style="text-align:center;">
					<td style="vertical-align:middle;font-weight:bold;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
					<td style="vertical-align:middle;"></td>
				</tr>
				<tr v-if="Object.keys(callingItem).length<1">
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
		</div>
	</div>
</div>

</div>

<script>
var vm = new Vue({
	el:'#app',
	data:{
		gamesInfo:{},
		countdown:30,
		timerTaskId:0,
		callingItem:{},
		readyingInfo:{},
		callingBeginTime:'',
		scene:0
	},
	methods:{
		getCallingItem:()=>{
			lockScreen();
			vm.clean();

			$.ajax({
				url:"/api/getCallingItem",
				data:{"gamesId":vm.gamesInfo['id']},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("网络不稳定，请点击刷新重试！<hr>Tips:建议先前往检录处查看检录情况，以免错过检录");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();

					if(ret.code==200){
						// 正在检录
						let callingInfo=ret.data['calling'];
						vm.readyingInfo=ret.data['readying'];

						vm.callingBeginTime=ret.data['callingBeginTime'];
						vm.scene=callingInfo['scene'];
						vm.callingItem=callingInfo;

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
						showModalTips("系统错误，请联系管理员！<hr>Tips:建议先前往检录处查看检录情况，以免错过检录");
						console.log(ret);
						return false;
					}
				}
			});
		},
		autoRefresh:(type)=>{
			if(vm.countdown==0){
				vm.getCallingItem();
				vm.countdown=30;
			}else{
				vm.countdown--;
			}

			if(type=="stop"){
				clearTimeout(vm.timerTaskId);
				$("#startAutoButton").attr("style","width:35%;");
				$("#stopAutoButton").attr("style","width:35%;display:none;");
			}else{
				$("#startAutoButton").attr("style","width:35%;display:none;");
				$("#stopAutoButton").attr("style","width:35%;");
				vm.timerTaskId=setTimeout(function(){vm.autoRefresh()},1000);
			}
		},
		clean:()=>{
			// 清除在检行内容
			$("#call_tips").attr("rowspan",1);
			$("#call_orderIndex").html("");
			$("#call_sex").html("");
			$("#call_groupName").html("");
			$("#call_name").html("");
			$("#call_totalGroup").html("");

			vm.scene=0;
			vm.callingBeginTime='';
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
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
	}
});

vm.getCallingItem();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
