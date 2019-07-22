<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-组件
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-30
 * @version 2019-07-22
 */
?>

<template id="page-navbar-template">
	<nav class="navbar navbar-default navbar-static-top" role="navigation">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/">
					<img style="margin-top:-10px; height:40px" alt="生蚝体育科技" src="/resource/image/logo.jpg">
				</a>
			</div>
			<div class="navbar-collapse collapse">
				<ul class="nav navbar-nav">
					<li class="active"><a href="/">成绩查询</a></li>
					<li><a href="/gamesEntryList">在线报名</a></li>
				</ul>
			</div>
		</div>
	</nav>
</template>

<script>
Vue.component('page-navbar', {
	template: '#page-navbar-template'
})
</script>

<template id="games-navbar-template">
	<div class="col-md-2">
		<div class="list-group" style="margin-left:-15px;">
			<a v-for="(navInfo,navPath) in navList" v-bind:href="['/'+navPath]" class="list-group-item" v-bind:class="[nowPageName==navPath?activeClass:'']">&nbsp;<i v-if="navInfo[1]!=''" v-bind:class="['fa fa-'+navInfo[1]]" aria-hidden="true"></i> {{navInfo[0]}}</a>
			<a href="/" class="list-group-item">&nbsp;<i class="fa fa-arrow-circle-left" aria-hidden="true"></i> 返回赛事列表</a>
		</div>
	</div>
</template>

<script>
Vue.component('games-navbar', {
	data: function () {
		return {
			activeClass:'active',
			nowPageName:'',
			navList:{
				'gamesIndex': ['赛事介绍','info-circle'],
				'schedule': ['赛事日程','list-alt'],
				'file': ['下载资料','files-o'],
				'order': ['秩序册','table'],
				'calling': ['检录处','volume-up'],
				'score': ['成绩公告','trophy'],
				'teamScore': ['团体分','users']
			}
		}
	},
	mounted:function(){
		let nowUrl=window.location.href;
		let nowUrlParam=nowUrl.split("/");
		this.nowPageName=nowUrlParam[nowUrlParam.length-1];
	},
	template: '#games-navbar-template'
})
</script>


<template id="games-title-template">
	<div class="bs-docs-header" v-bind:style="{backgroundImage:'url('+headerImg+')'}">
		<h2 style="color:white;vertical-align:middle;padding-left:15%;text-shadow:10px 9px 10px grey;">{{gamesInfo['name']}}</h2>
	</div>
</template>

<script>
Vue.component('games-title', {
	data: function () {
		return {
			gamesInfo:{},
			headerImg:''
		}
	},
	mounted:function(){
		let gamesInfo=JSON.parse(sessionStorage.getItem("OTSCMS_DA2_gamesInfo"));
		if(gamesInfo==undefined){
			return;
		}
		
		this.gamesInfo=gamesInfo;
		if(gamesInfo.kind=="田径") this.headerImg="/resource/image/athletics.jpg";
		else this.headerImg="/resource/image/swimming.jpg";
	},
	template: '#games-title-template'
})
</script>


<template id="choose-games-modal-template">
<div class="modal fade" id="chooseGamesModal" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">请选择比赛</h3>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table table-hover table-striped" style="text-align: left;">
						<thead>
							<tr>
								<th>赛事名称</th>
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<tr v-for="(gamesInfo,index) in gamesList">
								<td style="vertical-align:middle;">{{gamesInfo['name']}}</td>
								<td style="text-align:center;vertical-align:middle;"><button class="btn btn-primary" @click="enterGames(gamesInfo)"><i class="fa fa-search" aria-hidden="true"></i> 查 询</button></td>
							</tr>
						</tbody>
					</table>
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
						<p>记录总数：<span class="badge" style="color:#ffffff">{{totalRow}}</span></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</template>

<script>
Vue.component('choose-games-modal', {
	data:function(){
		return {
			nowPage:1,
			gamesList:{},
			totalRow:0,
			totalPage:0,
			perPageRow:5
		}
	},
	methods:{
		getList:function(page=1){
			var _this=this;

			$.ajax({
				url:'/api/getGamesList',
				data:{'page':page,'rows':_this.perPageRow},
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

						_this.nowPage=page;
						_this.gamesList=list;
						_this.totalRow=ret.data['totalRow'];
						_this.totalPage=ret.data['totalPage'];
					}
				}
			})
		},
		enterGames:(info)=>{
			sessionStorage.setItem("OTSCMS_DA2_gamesInfo",JSON.stringify(info));
			location.reload();
		}
	},
	mounted:function(){
		let gamesInfo=JSON.parse(sessionStorage.getItem("OTSCMS_DA2_gamesInfo"));

		if(gamesInfo==undefined){
			this.getList();
			$("#chooseGamesModal").modal("show");
		}
	},
	template: '#choose-games-modal-template'
})
</script>
