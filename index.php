<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-30
 * @version 2019-07-22
 */
?>

<!DOCTYPE html>
<html>
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>生蚝体育竞赛管理系统 / 生蚝科技</title>

	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<script src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
	<!-- 开发环境版本，包含了有帮助的命令行警告 -->
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="/resource/js/back2top.js"></script>
	<script src="/resource/js/util.js"></script>

	<style type="text/css">
		html,body {
			font-family:微软雅黑;
			margin: 0;
			padding:0;
			height: 100%;
		}
		th{
			text-align:center;
		}
		#container {
			min-height:60%;
			height: auto !important;
			height: 60%;
			position: relative;
		}
		.bs-docs-header{
			color:white;
			margin-top:-20px;
			background-image:url('/resource/image/swimming.jpg');
			height:230px;
			background-size: 100% 100%; 
			background-repeat: no-repeat;
			padding:30px 15px;
		}
		@media (min-width:768px){
			.bs-docs-header{padding-top:60px;padding-bottom:60px;text-align:left}
			.bs-docs-header h1{font-size:60px;line-height:1}
		}
		@media (min-width:992px){
			.bs-docs-header h1,.bs-docs-header p{margin-right:380px}
		}
	</style>
</head>

<?php include 'include/component.php'; ?>
	
<body style="background-color:#57c5e2">

<!-- Vue Area -->
<div id="app">

<page-navbar></page-navbar>

<div class="bs-docs-header">
	<div class="container">
		<h1>赛事数据查询</h1>
		<h2>赛事日程、秩序单、成绩、资料下载</h2>
	</div>
</div>

<div class="container" style="padding-top: 15px;">
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">报名赛事列表（点击按钮 / 表格行进入查询）</h3>
			</div>
			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>赛事名称</th>
							<th>开始日</th>
							<th>结束日</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						<tr v-for="(gamesInfo,index) in gamesList" @click="enterGames(gamesInfo)">
							<td style="text-align:center;vertical-align:middle;">{{(nowPage-1)*perPageRow+index+1}}</td>
							<td style="vertical-align:middle;">{{gamesInfo['name']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{gamesInfo['startDate']}}</td>
							<td style="text-align:center;vertical-align:middle;">{{gamesInfo['endDate']}}</td>
							<td style="text-align:center;vertical-align:middle;"><button class="btn btn-primary" @click="enterGames(gamesInfo)"><i class="fa fa-search" aria-hidden="true"></i> 查 询</button></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- 分页条 -->
	<div style="text-align:center;margin-top:-20px;">
		<ul id="myPaginator" class="pagination">
			<li v-for="pageNum in totalPage" v-if="pageNum==nowPage" class="active"><a>{{pageNum}}</a></li>
			<li v-else><a @click="getList(pageNum)">{{pageNum}}</a></li>
		</ul>
	</div>

	<!-- 记录数 -->
	<div class="row">
		<div class="col-md-12" style="text-align:center;">
			<p style="color:#ffffff">记录总数：<span class="badge">{{totalRow}}</span></p>
		</div>
	</div>
</div>

</div>
<!-- ./Vue Area -->

<script>
var totalPages=1;

var vm = new Vue({
	el:'#app',
	data:{
		nowPage:1,
		gamesList:{},
		totalRow:0,
		totalPage:0,
		perPageRow:10
	},
	methods:{
		getList:(page=1)=>{
			lockScreen();

			$.ajax({
				url:'/api/getGamesList',
				data:{'page':page,'rows':vm.perPageRow},
				crossDomain:true,
				dataType:'json',
				success:function(ret){
					if(ret.code==200){
						let list=ret.data['list'];

						for(i in list){
							let extraJson=JSON.parse(list[i]['extra_json']);
							for(j in extraJson){
								list[i][j]=extraJson[j];
							}

							delete list[i]['extra_json'];
						}

						vm.nowPage=page;
						vm.gamesList=list;
						vm.totalRow=ret.data['totalRow'];
						vm.totalPage=ret.data['totalPage'];
						unlockScreen();
					}
				}
			})
		},
		enterGames:(info)=>{
			lockScreen();
			sessionStorage.setItem("OTSCMS_DA2_gamesInfo",JSON.stringify(info));
			window.location.href="gamesIndex";
		}
	}
});

vm.getList();
</script>

<?php include 'include/footer.php'; ?>

</body>
</html>
