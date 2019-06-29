<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-27
 * @version 2019-06-28
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事日程';
include 'header.php';
?>

<?php include 'navbar.php'; ?>
	
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
			<table class="table table-bordered table-hover">
				<thead>
					<tr>
						<th style="padding:5px 0px;">类型</th>
						<th style="padding:5px 0px;">项次</th>
						<th style="padding:5px 2px;">项目名</th>
						<th style="padding:5px 2px;">组数</th>
						<th style="padding:5px 0px;">人/队数</th>
					</tr>
				</thead>
				<tbody>
					<template v-if="Object.keys(scheduleList).length===0">
						<tr>
							<td colspan="5">暂 无 日 程 表</td>
						</tr>
					</template>
					<template v-else v-for="(scheduleSceneList,scene) in scheduleList">
						<tr>
							<th v-if="gamesInfo['scene'][scene]!=''" style="text-align:center;font-size:16px;background-color:#C4E1FF" colspan="5">第 {{scene}} 场（{{gamesInfo['scene'][scene]}}）</th>
							<th v-else style="text-align:center;font-size:16px;background-color:#C4E1FF" colspan="5">第 {{scene}} 场</th>
						</tr>
						<tr v-for="info in scheduleSceneList" style="font-size:12px">
							<td style="padding:8px 2px;">{{info['kind']}}</td>
							<td>{{info['order_index']}}</td>
							<td>{{info['sex']+info['group_name']+info['name']}}</td>
							<td>{{info['total_group']}}</td>
							<td>{{info['total_ath']}}</td>
						</tr>
					</template>
				</tbody>
			</table>
		</div>
	</div>
</div>

</div>

<script>
var vm = new Vue({
	el:'#app',
	data:{
		gamesInfo:{},
		scheduleList:{}
	},
	methods:{
		getSchedule:()=>{
			lockScreen();
			
			$.ajax({
				url:'/api/getSchedule',
				data:{'id':vm.gamesInfo['id']},
				dataType:'json',
				error:e=>{
					unlockScreen();
					showModalTips("服务器错误！");
					console.log(e);
				},
				success:ret=>{
					unlockScreen();
					vm.scheduleList=ret.data['list'];
				}
			})
		}
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
	}
});

vm.getSchedule();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
