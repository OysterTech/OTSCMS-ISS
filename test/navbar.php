<template id="games-navbar-template">
	<div class="col-md-2">
		<div class="list-group" style="margin-left:-15px;">
			<a v-for="(navInfo,navPath) in navList" v-bind:href="['/test/'+navPath]" class="list-group-item" v-bind:class="[nowPageName==navPath?activeClass:'']">&nbsp;<i v-if="navInfo[1]!=''" v-bind:class="['fa fa-'+navInfo[1]]" aria-hidden="true"></i> {{navInfo[0]}}</a>
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
				'athleteItem': ['运动员项目','group'],
				'checkin': ['检录处','volume-up'],
				'score': ['成绩公告','trophy'],
				'athleteScore': ['运动员成绩','user-o']
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
		let gamesInfo=JSON.parse(localStorage.getItem("OTSCMS_DA2_gamesInfo"));
		if(gamesInfo==undefined){
			window.location.href="index";
		}
		
		this.gamesInfo=gamesInfo;
		if(gamesInfo.kind=="田径") this.headerImg="athletics.jpg";
		else this.headerImg="swimming.jpg";
	},
	template: '#games-title-template'
})
</script>
