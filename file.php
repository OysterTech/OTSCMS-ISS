<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-02
 * @version 2019-07-07
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事资料下载';
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
				url:'/api/getFile',
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

<?php include 'include/footer.php'; ?>

</body>
</html>
