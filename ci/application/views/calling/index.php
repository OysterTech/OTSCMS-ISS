<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-检录处
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-03-01
 * @version 2019-03-10
 */
?>

<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>检录处 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>检录处</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">检录处</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">

				<template v-if="Object.keys(callingItem).length>0">
					<button v-on:click="getCallingItem" class="btn btn-primary btn-block"><i class="fa fa-refresh" aria-hidden="true"></i> 刷 新</button>
					<br>
					<button v-on:click="goBack" class="btn btn-warning" style="width:49%"><i class="fa fa-arrow-left" aria-hidden="true"></i> 返 回 上 一 项</button>
					<button v-on:click="goNext" class="btn btn-success" style="width:49%">下 一 项 <i class="fa fa-arrow-right" aria-hidden="true"></i></button>
					<br><br>
					<button v-on:click="goEnd" class="btn btn-danger" style="width:99%"><i class="fa fa-power-off" aria-hidden="true"></i> 提 前 结 束 本 场 检 录</button>
				</template>
				<template v-else>
					<div class="input-group">
						<span class="input-group-addon">场 次</span>
						<select id="scene" class="form-control">
							<option v-for="sceneInfo in sceneList" v-bind:value="sceneInfo.scene">第 {{sceneInfo.scene}} 场</option>
						</select>
						<span class="input-group-btn">
							<button v-on:click="goStart" class="btn btn-primary"><i class="fa fa-play-circle" aria-hidden="true"></i> 开 始 检 录</button>
						</span>
					</div>
				</template>

				<hr>

				<center v-if="Object.keys(callingItem).length>0">
					<font style="font-size:17px;">本项开始检录时间：<b>{{callingBeginTime}}</b></font>
				</center>

				<br>

				<table v-if="Object.keys(callingItem).length>0" class="table table-hover table-striped table-bordered">
					<tr>
						<th style="text-align:center;background-color:#BBFFBB;font-size:17px;font-weight:bold;" colspan="6">第 {{callingItem['scene']}} 场</th>
					</tr>
					<tr>
						<th style="width:14%">状态</th>
						<th style="width:10%">项次</th>
						<th style="width:14%">性别</th>
						<th>组别</th>
						<th>名称</th>
						<th style="width:10%">组数</th>
					</tr>
					<template v-if="Object.keys(callingItem).length>0">
						<tr style="background-color:#C4E1FF;">
							<td v-if="Object.keys(readyItem).length>0" rowspan="2" style="background-color:#C4E1FF;text-align:center;vertical-align:middle;font-weight:bold;">正在<br>检录</td>
							<td v-else style="background-color:#C4E1FF;text-align:center;vertical-align:middle;font-weight:bold;">正在<br>检录</td>
							<td style="text-align:center;vertical-align:middle;"><b>{{callingItem['order_index']}}</b></td>
							<td style="text-align:center;vertical-align:middle;">{{callingItem['sex']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{callingItem['group_name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{callingItem['name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{callingItem['total_group']}}</td>
						</tr>
						<tr v-if="Object.keys(readyItem).length>0">
							<td style="text-align:center;vertical-align:middle;"><b>{{readyItem[0]['order_index']}}</b></td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[0]['sex']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[0]['group_name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[0]['name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[0]['total_group']}}</td>
						</tr>
						<tr v-if="Object.keys(readyItem).length>1" style="background-color:#FFDAC8;">
							<td v-if="Object.keys(readyItem).length>2" rowspan="2" style="background-color:#FFDAC8;text-align:center;vertical-align:middle;font-weight:bold;">准备<br>检录</td>
							<td v-else style="background-color:#FFDAC8;text-align:center;vertical-align:middle;font-weight:bold;">准备<br>检录</td>
							<td style="text-align:center;vertical-align:middle;"><b>{{readyItem[1]['order_index']}}</b></td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[1]['sex']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[1]['group_name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[1]['name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[1]['total_group']}}</td>
						</tr>
						<tr v-if="Object.keys(readyItem).length>2">
							<td style="text-align:center;vertical-align:middle;"><b>{{readyItem[2]['order_index']}}</b></td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[2]['sex']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[2]['group_name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[2]['name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{readyItem[2]['total_group']}}</td>
						</tr>
					</template>
					<template v-else>
						<tr>
							<td colspan="6" style="color:red;font-weight:bold;font-size:18px;text-align: center;">暂 无 检 录 信 息</td>
						</tr>
					</template>
				</table>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var vm = new Vue({
	el:'#app',
	data:{
		gamesId:"<?=$this->gamesId;?>",
		sceneList:{},
		callingItem:{},
		readyItem:{},
		callingBeginTime:""
	},
	methods:{
		getCallingItem:function(){
			vm.callingItem={};
			vm.readyItem={};
			vm.callingBeginTime="";
			lockScreen();

			$.ajax({
				url:headerVm.apiPath+"getCallingItem",
				data:{"gamesId":vm.gamesId},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("网络不稳定，请刷新重试！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();

					if(ret.code==200){
						vm.callingItem=ret.data['callingItem'];
						vm.readyItem=ret.data['readyItem'];
						vm.callingBeginTime=ret.data['callingBeginTime'];
					}else if(ret.code==1){
						vm.callingItem={};
						vm.readyItem={};
						vm.callingBeginTime="";
					}else{
						showModalTips("系统错误，请联系管理员！");
						console.log(ret);
						return false;
					}
				}
			});
		},
		goStart:function(){
			lockScreen();
			scene=$("#scene").val();

			$.ajax({
				url:"<?=base_url('calling/toCall');?>",
				type:"post",
				data:{"token":headerVm.token,"type":"start","scene":scene},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("服务器错误！<br>操作失败！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();
					
					if(ret.code==200){
						showModalTips("[第"+scene+"场] 开场成功！");
						vm.getCallingItem();
					}else if(ret.code==403001){
						showModalTips("签名校验失败！<br>请刷新页面重试！<hr>提示：请勿在操作前后打开其它页面！");
					}else if(ret.code==403002){
						alert("当前未选择需要管理的比赛！\n请回到首页选择！");
						window.location.href=headerVm.rootUrl;
					}else{
						showModalTips("系统错误！<br>操作失败！");
						console.log(ret);
					}
				}
			})
		},
		goEnd:function(){
			if(!confirm("确定要提前结束[第"+vm.callingItem['scene']+"场]的检录吗？")){
				return false;
			}
			
			lockScreen();
			$.ajax({
				url:"<?=base_url('calling/toCall');?>",
				type:"post",
				data:{"token":headerVm.token,"type":"end"},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("服务器错误！<br>操作失败！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();
					
					if(ret.code==200){
						showModalTips("成功提前结束[第"+ret.data['endScene']+"场]检录！");
						vm.getCallingItem();
					}else if(ret.code==1){
						alert("本比赛尚未开始检录！\n请刷新页面重试！");
						location.reload();
					}else if(ret.code==2){
						showModalTips("提前结束检录失败！！！");
						vm.getCallingItem();
					}else if(ret.code==403001){
						showModalTips("签名校验失败！<br>请刷新页面重试！<hr>提示：请勿在操作前后打开其它页面！");
					}else if(ret.code==403002){
						alert("当前未选择需要管理的比赛！\n请回到首页选择！");
						window.location.href=headerVm.rootUrl;
					}else{
						showModalTips("系统错误！<br>操作失败！");
						console.log(ret);
					}
				}
			})		
		},
		goNext:function(){
			lockScreen();
			$.ajax({
				url:"<?=base_url('calling/toCall');?>",
				type:"post",
				data:{"token":headerVm.token,"type":"next"},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("服务器错误！<br>操作失败！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();
					data=ret.data;

					if(ret.code==2001){
						showModalTips("[第"+data['lastOrderIndex']+"项] 检录结束！<br>正在检录：第"+data['callingOrderIndex']+"项");
						vm.getCallingItem();
					}else if(ret.code==2002){
						showModalTips("[第"+data['endOrderIndex']+"项] 检录结束！<br>[第"+data['endScene']+"场]项目全部检录完毕！");
						vm.getCallingItem();
					}else if(ret.code==1){
						alert("本比赛尚未开始检录！\n请刷新页面重试！");
						location.reload();
					}else if(ret.code==2){
						showModalTips("结束检录上一项失败！");
						vm.getCallingItem();
					}else if(ret.code==403001){
						showModalTips("签名校验失败！<br>请刷新页面重试！<hr>提示：请勿在操作前后打开其它页面！");
					}else if(ret.code==403002){
						alert("当前未选择需要管理的比赛！\n请回到首页选择！");
						window.location.href=headerVm.rootUrl;
					}else{
						showModalTips("系统错误！<br>操作失败！");
						console.log(ret);
					}
				}
			})		
		},
		goBack:function(){
			if(vm.callingItem["order_index"]==1){
				showModalTips("当前项目已是本场比赛第一项！<br>请注意，无法再次返回！");
				return false;
			}
			
			lockScreen();
			$.ajax({
				url:"<?=base_url('calling/toCall');?>",
				type:"post",
				data:{"token":headerVm.token,"type":"back"},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("服务器错误！<br>操作失败！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();
					
					if(ret.code==200){
						showModalTips("成功返回检录上一项！<br>正在检录：第"+ret.data['callingOrderIndex']+"项");
						vm.getCallingItem();
					}else if(ret.code==1){
						alert("本比赛尚未开始检录！\n请刷新页面重试！");
						location.reload();
					}else if(ret.code==2){
						showModalTips("结束检录上一项失败！");
						vm.getCallingItem();
					}else if(ret.code==3){
						alert("检录返回失败！\n请刷新页面重试！");
						location.reload();
					}else if(ret.code==4){
						showModalTips("当前项目已是本场比赛第一项！<br>请注意，无法再次返回！");
						vm.getCallingItem();
					}else if(ret.code==403001){
						showModalTips("签名校验失败！<br>请刷新页面重试！<hr>提示：请勿在操作前后打开其它页面！");
					}else if(ret.code==403002){
						alert("当前未选择需要管理的比赛！\n请回到首页选择！");
						window.location.href=headerVm.rootUrl;
					}else{
						showModalTips("系统错误！<br>操作失败！");
						console.log(ret);
					}
				}
			})		
		},
		getSceneList:function(){
			lockScreen();
			$.ajax({
				url:headerVm.apiPath+"getItemInfo",
				data:{"gamesId":vm.gamesId,"type":"scene"},
				dataType:"json",
				error:function(e){
					unlockScreen();
					showModalTips("服务器错误！<br>获取场次列表失败！");
					console.log(e);
					return false;
				},
				success:function(ret){
					unlockScreen();
					
					if(ret.code==200){
						vm.sceneList=ret.data['sceneList'];
						console.log(vm.sceneList);
						return true;
					}else{
						showModalTips("系统错误！<br>获取场次列表失败！");
					}
				}
			})
		}
	}
});

vm.getCallingItem();
vm.getSceneList();
</script>

</body>
</html>
