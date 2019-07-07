<?php
/**
 * @name 生蚝体育比赛管理系统-Web2-检录
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-07-06
 * @version 2019-07-07
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='检录处';
include 'include/header.php';
?>

<?php include 'include/component.php'; ?>

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

				<!-- 正在检录 -->
				<tr v-if="Object.keys(callingItem).length>=1" style="background-color:#C4E1FF;text-align:center;">
					<td v-if="Object.keys(readyingInfo).length>=1" style="vertical-align:middle;font-weight:bold;" rowspan="2">正在<br>检录</td>
					<td v-else style="vertical-align:middle;font-weight:bold;">正在<br>检录</td>

					<td style="vertical-align:middle;font-weight:bold;">{{callingItem['order_index']}}</td>
					<td style="vertical-align:middle;">{{callingItem['sex']}}</td>
					<td style="vertical-align:middle;">{{callingItem['group_name']}}</td>
					<td style="vertical-align:middle;">{{callingItem['name']}}</td>
					<td style="vertical-align:middle;">{{callingItem['total_group']}}</td>
				</tr>
				<!-- ./正在检录 -->

				<!-- 准备检录 -->
				<tr v-if="Object.keys(readyingInfo).length>=1" style="text-align:center;">
					<td style="vertical-align:middle;font-weight:bold;">{{readyingInfo[0]['order_index']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[0]['sex']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[0]['group_name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[0]['name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[0]['total_group']}}</td>
				</tr>

				<tr v-if="Object.keys(readyingInfo).length>=2" style="text-align:center;background-color:#FFDAC8;">
					<td v-if="Object.keys(readyingInfo).length>=3" style="vertical-align:middle;font-weight:bold;" rowspan="2">准备<br>检录</td>
					<td v-else style="vertical-align:middle;font-weight:bold;">准备<br>检录</td>

					<td style="vertical-align:middle;font-weight:bold;">{{readyingInfo[1]['order_index']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[1]['sex']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[1]['group_name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[1]['name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[1]['total_group']}}</td>
				</tr>

				<tr v-if="Object.keys(readyingInfo).length>=3" style="text-align:center;">
					<td style="vertical-align:middle;font-weight:bold;">{{readyingInfo[2]['order_index']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[2]['sex']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[2]['group_name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[2]['name']}}</td>
					<td style="vertical-align:middle;">{{readyingInfo[2]['total_group']}}</td>
				</tr>
				<!-- ./准备检录 -->

				<!-- 没有在检项目 -->
				<tr v-if="Object.keys(callingItem).length<1">
					<td colspan="6" style="color:red;font-weight:bold;font-size:18px;">暂 无 检 录 信 息</td>
				</tr>
				<!-- ./没有在检项目 -->
			</table>

			<!-- 温馨提醒 -->
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
			<!-- ./温馨提醒 -->

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
					if(ret.code==200){
						vm.readyingInfo=ret.data['readying'];
						vm.callingBeginTime=ret.data['callingBeginTime'];
						vm.scene=ret.data['calling']['scene'];
						vm.callingItem=ret.data['calling'];
						unlockScreen();
						return;
					}else if(ret.code==1){
						vm.clean();
						unlockScreen();
						return false;
					}else{
						unlockScreen();
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
			vm.callingItem={};
			vm.readyingInfo={};
			vm.callingBeginTime='';
			vm.scene=0;
		}
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
	}
});

vm.getCallingItem();
</script>

<?php include 'include/footer.php'; ?>

</body>
</html>
