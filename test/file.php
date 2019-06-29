<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-02
 * @version 2019-06-27
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事资料下载';
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
					<tr class="text-center bg-info">
						<th class="text-center">文 件 名 称</th>
						<th class="text-center">下 载</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-center" v-if="fileList.length<1">
						<td colspan="2">暂 无 资 料</td>
					</tr>
					<tr class="text-center" v-else v-for="fileInfo in fileList">
						<td>{{fileInfo['name']}}</td>
						<td><a v-bind:href="fileInfo['url']" target="_blank">查看并下载</a></td>
					</tr>
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
		fileList:{}
	},
	methods:{
		getFileList:()=>{
			$.ajax({
				url:'/api/getGamesFile',
				data:{'id':vm.gamesInfo['id']},
				dataType:'json',
				error:e=>{
					console.log(e);
				},
				success:ret=>{
					vm.fileList=ret.data;
				}
			})
		}
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
	}
});

vm.getFileList();
</script>

<?php include 'footer.php'; ?>

</body>
</html>
