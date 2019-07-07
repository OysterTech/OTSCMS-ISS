<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-比赛首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-30
 * @version 2019-07-07
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事首页';
include 'include/header.php';
?>

<?php include 'include/component.php'; ?>

<body style="background-color:#57c5e2;">

<style type="text/css">
	td{
		text-align: center;
	}
</style>

<div id="app">

<page-navbar></page-navbar>

<games-title ref="header"></games-title>

<div class="container">
	<div class="row">
		
		<games-navbar></games-navbar>
		
		<div class="col-md-10">
			<table class="table table-bordered table-hover table-striped">
				<tbody>
					<tr>
						<th style="width: 22%;vertical-align: middle;">赛事名：</th>
						<td>{{gamesInfo['name']}}</td>
					</tr>
					<tr>
						<th style="vertical-align: middle;">主办方：</th>
						<td style="vertical-align: middle;">{{gamesInfo['organizer']}}</td>
					</tr>
					<tr>
						<th style="vertical-align: middle;">举办地：</th>
						<td style="vertical-align: middle;">{{gamesInfo['venue']}}</td>
					</tr>
					<tr>
						<th style="vertical-align: middle;">开始日：</th>
						<td style="vertical-align: middle;">{{gamesInfo['startDate']}}</td>
					</tr>
					<tr>
						<th style="vertical-align: middle;">结束日：</th>
						<td style="vertical-align: middle;">{{gamesInfo['endDate']}}</td>
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
		gamesInfo:{}
	},
	methods:{
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
		
		//this.getList();
	}
});
</script>

<?php include 'include/footer.php'; ?>

</body>
</html>
