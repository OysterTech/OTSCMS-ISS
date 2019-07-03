<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-27
 * @version 2019-07-03
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事分组';
include 'header.php';
?>

<?php include 'component.php'; ?>
	
<body style="background-color:#57c5e2;">

<div id="app">
<nav class="navbar navbar-default navbar-static-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="/">
				<img style="margin-top:-10px; height:40px" alt="生蚝体育科技" src="https://sport.xshgzs.com/resource/image/logo.jpg">
			</a>
		</div>
		<div class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li><a href="/">在线报名</a></li>
				<li class="active"><a href="/">成绩查询</a></li>
			</ul>
		</div>
	</div>
</nav>

<games-title ref="header"></games-title>

<div class="container">
	<div class="row">

		<games-navbar></games-navbar>
		
		<div class="col-md-10">
			<ul class="nav nav-tabs">
				<li id="orderTab" class="active"><a onclick="vm.type=1;$('#orderSelect').show();$('#groupSelect').hide();$('#athleteSelect').hide();$('#orderTab').attr('class','active');$('#groupTab').attr('class','');$('#athleteTab').attr('class','')">按项次查询</a></li>
				<li id="groupTab"><a onclick="vm.type=2;$('#orderSelect').hide();$('#athleteSelect').hide();$('#groupSelect').show();$('#orderTab').attr('class','');$('#athleteTab').attr('class','');$('#groupTab').attr('class','active')">按组别项目查询</a></li>
				<li id="athleteTab"><a onclick="vm.type=3;$('#orderSelect').hide();$('#groupSelect').hide();$('#athleteSelect').show();$('#orderTab').attr('class','');$('#groupTab').attr('class','');$('#athleteTab').attr('class','active')">按运动员姓名查询</a></li>
			</ul>

			<div class="row">
				<!-- 按项次查询 选择框组 -->
				<div id="orderSelect">
					<div class="col-md-2">
						<div class="row form-group">
							<label for="scene" class="col-sm-1">场次:</label>
							<div class="col-sm-8">
								<select id="scene" class="form-control" v-model="scene" @change="getItem">
									<option v-for="sceneInfo in sceneList" v-bind:value="sceneInfo['scene']">{{sceneInfo['scene']}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-8">
						<div class="row form-group">
							<label for="item" class="col-sm-1">项目:</label>
							<div class="col-sm-10">
								<select id="item" class="form-control" v-model="item" @change="getItemGroup">
									<template v-if="gamesInfo.kind==='游泳'">
										<option v-for="(itemInfo,index) in itemList" v-bind:value="itemInfo['id']+'-'+index">第{{itemInfo['order_index']}}项 {{itemInfo['sex']}}{{itemInfo['group_name']}}{{itemInfo['name']}}</option>
									</template>
									<template v-else>
										<option v-for="(itemInfo,index) in itemList" v-bind:value="itemInfo['id']+'-'+index">[{{itemInfo['kind']}}]第{{itemInfo['order_index']}}项 {{itemInfo['sex']}}{{itemInfo['group_name']}}{{itemInfo['name']}}</option>
									</template>
								</select>

							</div>
						</div>
					</div>
					<!--div class="col-md-3">
						<div class="row form-group">
							<label for="group" class="col-sm-1">分组:</label>
							<div class="col-sm-9">
								<select id="group" class="form-control" v-model='group'>
									<option value="0">全部</option>
									<option v-for="num in selectItemInfo['total_group']" v-bind:value="num">第 {{num}} 组</option>
								</select>
							</div> 
						</div>
					</div-->
				</div>
				<!-- ./按项次查询 选择框组 -->

				<!-- 按组别查询 选择框组 -->
				<div id="groupSelect" style="display:none">
					<div class="col-md-3">
						<div class="row form-group">
							<label for="sex" class="col-sm-1">性别:</label>
							<div class="col-sm-9">
								<select id="sex" class="form-control" v-model="sex">
									<option value="男子">男子</option>
									<option value="女子">女子</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="row form-group">
							<label for="groupName" class="col-sm-1">组别:</label>
							<div class="col-sm-9">
								<select id="groupName" class="form-control" v-model="groupName" @change="getItemGroup">
									<option v-for="groupInfo in groupList" v-bind:value="groupInfo">{{groupInfo}}</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="row form-group">
							<label for="name" class="col-sm-1">项目:</label>
							<div class="col-sm-10">
								<select id="name" class="form-control" v-model='name'>
									<option value="0">全部</option>
									<option v-for="name in nameList" v-bind:value="name">{{name}}</option>
								</select>
							</div> 
						</div>
					</div>
				</div>
				<!-- ./按组别查询 选择框组 -->

				<!-- 按运动员项目查询 输入框 -->
				<div id="athleteSelect" style="display:none">
					<div class="col-md-10">
						<div class="row form-group">
							<label for="athleteName" class="col-sm-1">姓名:</label>
							<div class="col-sm-11">
								<input id="athleteName" class="form-control" v-model="athleteName">
							</div>
						</div>
					</div>
				</div>
				<!-- ./按运动员项目查询 输入框 -->

				<div class="col-md-1" style="margin-top: 15px;">
					<button class="btn btn-info" @click="search"><i class="fa fa-search" aria-hidden="true"></i> 查 询</button>
				</div>
			</div>

			<hr class="featurette-divider">

			<!-- 分组表格显示 -->
			<div id="orderResult" style="display:none">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr style="background-color: #cbeff5">
							<th>组</th>
							<th>道</th>
							<th>姓名</th>
							<th>备注</th>
						</tr>
					</thead>
					<tbody style="font-size:17px;">
						<template v-for="(runGroupData,runGroupNum) in orderData">
							<tr v-for="(runwayData,index) in runGroupData">
								<template v-if="index==0">
									<th v-if="runGroupNum%2==0" style="vertical-align:middle;background-color:#B3E5FC;" v-bind:rowspan="runGroupData.length">{{runwayData['run_group']}}</th>
									<th v-else style="vertical-align:middle;background-color:#CCFF90;" v-bind:rowspan="runGroupData.length">{{runwayData['run_group']}}</th>
								</template>
								
								<th style="vertical-align:middle;">{{runwayData['runway']}}</th>
								<td style="vertical-align:middle;padding:5px 5px 0 5px;">{{runwayData['name']}}<p style="font-size:12px">[{{runwayData['short_name']}}]</p></td>
								<td style="vertical-align:middle;">{{runwayData['remark']}}</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<!-- ./分组表格显示 -->

			<!-- 运动员分组表格显示 -->
			<div id="athleteOrderResult" style="display:none">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr style="background-color: #cbeff5">
							<th>场/项</th>
							<th>组/道</th>
							<th>项目名</th>
							<th>姓名</th>
							<th>代表队</th>
						</tr>
					</thead>
					<tbody style="font-size:17px;">
						<template v-for="(runGroupData,runGroupNum) in orderData">
							<tr v-for="(runwayData,index) in runGroupData">
								<template v-if="index==0">
									<th v-if="runGroupNum%2==0" style="vertical-align:middle;background-color:#B3E5FC;" v-bind:rowspan="runGroupData.length">{{runwayData['run_group']}}</th>
									<th v-else style="vertical-align:middle;background-color:#CCFF90;" v-bind:rowspan="runGroupData.length">{{runwayData['run_group']}}</th>
								</template>
								
								<th style="vertical-align:middle;">{{runwayData['runway']}}</th>
								<td style="vertical-align:middle;padding:5px 5px 0 5px;">{{runwayData['name']}}<p style="font-size:12px">[{{runwayData['short_name']}}]</p></td>
								<td style="vertical-align:middle;">{{runwayData['remark']}}</td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<!-- ./运动员分组表格显示 -->
			
			<center><div style="width:96%;text-align: center;">
				<div class="alert alert-info"><i class="fa fa-info-circle" aria-hidden="true"></i> 备注：DNS 弃权、DSQ 犯规、TRI测试</div>
			</div></center>

		</div>
	</div>
</div>

</div>

<script>
var vm = new Vue({
	el:'#app',
	data:{
		gamesInfo:{},
		type:1,
		scene:1,
		sceneList:{},
		item:1,
		itemList:{},
		group:0,
		selectItemInfo:{},
		orderData:{},
		sex:'男子',
		groupList:{},
		groupName:'',
		nameList:{},
		name:'',
		athleteName:''
	},
	methods:{
		getScene:()=>{
			vm.clean();
			lockScreen();

			$.ajax({
				url:'/api/getItemInfo',
				data:{'gamesId':vm.gamesInfo['id'],'type':'scene'},
				dataType:'json',
				error:function(e){
					unlockScreen();
					showModalTips('服务器错误！<br>获取场次失败！');
					return false;
				},
				success:function(ret){
					unlockScreen();

					if(ret.code==200){
						vm.sceneList=ret.data['sceneList'];
						vm.scene=ret.data['sceneList'][0]['scene'];
						vm.getItem();
					}
				}
			})
		},
		getItem:()=>{
			vm.clean();
			lockScreen();

			$.ajax({
				url:'/api/getItemInfo',
				data:{'gamesId':vm.gamesInfo['id'],'type':'item','scene':vm.scene},
				dataType:'json',
				error:function(e){
					unlockScreen();
					showModalTips('服务器错误！<br>获取场次项目失败！');
					return false;
				},
				success:function(ret){
					unlockScreen();
					
					if(ret.code==200){
						vm.itemList=ret.data['itemList'];
						vm.item=ret.data['itemList'][0]['id']+'-0';
						vm.selectItemInfo=vm.itemList[0];
						
						vm.selectItemInfo['total_group']=parseInt(vm.selectItemInfo['total_group']);
					}
				}
			})
		},
		getItemGroup:()=>{
			vm.clean();

			let item=vm.item.split('-');
			vm.selectItemInfo=vm.itemList[item[1]];
			vm.selectItemInfo['total_group']=parseInt(vm.selectItemInfo['total_group']);
		},
		getGroup:()=>{
			vm.clean();
			lockScreen();

			$.ajax({
				url:'/api/getItemInfo',
				data:{'gamesId':vm.gamesInfo['id'],'type':'group'},
				dataType:'json',
				error:function(e){
					unlockScreen();
					showModalTips('服务器错误！<br>获取组别失败！');
					return false;
				},
				success:function(ret){
					unlockScreen();

					if(ret.code==200){
						vm.groupList=ret.data['group'];
						vm.nameList=ret.data['name'];
						vm.groupName=vm.groupList[0];
						vm.name=vm.nameList[0];
					}
				}
			})
		},
		search:()=>{
			if(vm.type==3){
				vm.athleteSearch();
				return;
			}

			lockScreen();
			let item=vm.item.split('-');
			let itemId=item[0];
			let postData=(vm.type==2)?{'orderBy':"group",'gamesId':vm.gamesInfo['id'],'sex':vm.sex,'groupName':vm.groupName,'name':vm.name}:{"orderBy":"item",'gamesId':vm.gamesInfo['id'],"itemId":itemId};
			
			$.ajax({
				url:'/api/getOrder',
				data:postData,
				dataType:'json',
				error:e=>{
					unlockScreen();
					showModalTips("服务器错误！");
					console.log(e);
				},
				success:ret=>{
					if(ret.code==200){
						let data=ret.data['orderData'];
						let retData=[];
						
						for(i in data){
							let info=data[i];
							
							if(retData[info['run_group']]===undefined) retData[info['run_group']]=[];
							
							retData[info['run_group']].push(info);
						}
						
						vm.orderData=retData;
						$("#orderResult").show(500);
						unlockScreen();
						return true;
					}else{
						showModalTips("系统错误！");
						unlockScreen();
					}
				}
			})
		},
		athleteSearch:()=>{
			lockScreen();

			$.ajax({
				url:'/api/getAthleteData',
				data:{'type':'order','gamesId':vm.gamesInfo['id'],'name':vm.athleteName},
				dataType:'json',
				error:e=>{
					unlockScreen();
					showModalTips("服务器错误！");
					console.log(e);
				},
				success:ret=>{
					if(ret.code==200){
						let data=ret.data['list'];
						console.log(data);
						unlockScreen();
					}else if(ret.code==404){
						showModalTips('无此运动员数据！');
						unlockScreen();
						return false;
					}
				}
			});
		},
		clean:()=>{
			$("#orderResult").hide(400);
			vm.orderData={};
		}
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
	}
});

vm.getScene();
vm.getGroup();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
