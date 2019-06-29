<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-赛事列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-30
 * @version 2019-06-28
 */
?>

<!DOCTYPE html>
<html>
<head>	
	<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>生蚝体育竞赛管理系统 / 生蚝科技</title>

	<link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css">
	
	<script src="https://cdn.bootcss.com/jquery/3.1.0/jquery.min.js"></script>
	<!-- 开发环境版本，包含了有帮助的命令行警告 -->
	<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
	<script src="https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="back2top.js"></script>
	
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
			background-image:url('swimming.jpg');
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
	
<body style="background-color:#57c5e2">

<!-- Vue Area -->
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
				<li><a href="/test/gamesEntryList">在线报名</a></li>
				<li class="active"><a href="/">成绩查询</a></li>
			</ul>
		</div>
	</div>
</nav>

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
				<h3 class="panel-title">报名赛事列表</h3>
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
						<tr v-for="(gamesInfo,index) in gamesList">
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
					}
				}
			})
		},
		enterGames:(info)=>{
			localStorage.setItem("OTSCMS_DA2_gamesInfo",JSON.stringify(info));
			window.location.href="gamesIndex";
		}
	}
});

vm.getList();
</script>

<!-- footer -->
<div style="color:#FFFF00;text-align:center;font-weight:bold;font-size:18px;line-height:29px;">
	<hr>
	&copy; 2014-2019 生蚝科技
	<a style="color:#07C160" data-toggle="modal" data-target="#wxModal"><i class="fa fa-weixin fa-lg" aria-hidden="true"></i></a>
	<a style="color:#FF7043" onclick='launchQQ()'><i class="fa fa-qq fa-lg" aria-hidden="true"></i></a>
	<a style="color:#29B6F6" href="mailto:master@xshgzs.com"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></a>
	<a style="color:#AB47BC" href="https://github.com/OysterTech" target="_blank"><i class="fa fa-github fa-lg" aria-hidden="true"></i></a>

	<br>

	All Rights Reserved.<br>
	粤ICP备19018320号-1<br>

	<!-- 友情链接 -->
	<p style="color:white;font-size:16px;">
		友情链接：<a href="http://swimming.sport.org.cn/" target="_blank" style="color:white;">中国游泳协会</a> | <a href="http://www.gdswim.org/" target="_blank" style="color:white;">广东省游泳协会</a>
	</p>
	<!-- ./友情链接 -->

	<a href="/admin" target="_blank" style="color:white;font-size:16px;">登 入 管 理 后 台</a>

	<br><br>
</div>
<!-- ./footer -->

</body>
</html>
