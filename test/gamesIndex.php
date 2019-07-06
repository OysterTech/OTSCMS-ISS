<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-比赛首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-30
 * @version 2019-07-06
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='赛事首页';
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
			<table class="table table-bordered table-hover table-striped">
				<tbody>
					<tr>
						<th>赛事名称：</th>
						<td>{{gamesInfo['name']}}</td>
					</tr>
					<tr>
						<th>主办方：</th>
						<td style="vertical-align: middle;text-align: center;">{{gamesInfo['organizer']}}</td>
					</tr>
					<tr>
						<th>举办地：</th>
						<td style="vertical-align: middle;text-align: center;">{{gamesInfo['venue']}}</td>
					</tr>
					<tr>
						<th>开始日期：</th>
						<td style="vertical-align: middle;text-align: center;">{{gamesInfo['startDate']}}</td>
					</tr>
					<tr>
						<th>结束日期：</th>
						<td style="vertical-align: middle;text-align: center;">{{gamesInfo['endDate']}}</td>
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

<?php include 'footer.php'; ?>

</body>
</html>
