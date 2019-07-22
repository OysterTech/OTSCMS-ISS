<?php
/**
 * @name 生蚝体育竞赛管理系统-Web2-团体分
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-07-19
 * @version 2019-07-22
 */
?>

<!DOCTYPE html>
<html>

<?php
$pageName='团体分';
include 'include/header.php';
?>

<?php include 'include/component.php'; ?>
	
<body style="background-color:#57c5e2;">

<div id="app">

<choose-games-modal></choose-games-modal>

<page-navbar></page-navbar>

<games-title ref="header"></games-title>

<div class="container">
	<div class="row">

		<games-navbar></games-navbar>
		
		<div class="col-md-10">
			<div class="row">
				<div class="col-md-10">
					<div class="row form-group">
						<label class="col-sm-1">排序条件:</label>
						<div class="col-sm-6">
							<select class="form-control" v-model="groupBy" @change="clean">
								<option value=''>::: 请选择分类方式 :::</option>
								<option disabled>----------------------</option>
								<option value='sex'>分性别，不分组别</option>
								<option value='groupName'>不分性别，分组别</option>
								<option disabled>----------------------</option>
								<option value='sexGroup'>分性别，分组别</option>
								<option value='total'>不分性别，不分组别</option>
							</select>
						</div>
						<div class="col-sm-5">
							<select class="form-control" v-model="orderBy" @change="clean">
								<option value=''>::: 请选择计算条件 :::</option>
								<option disabled>----------------------</option>
								<option value='point'>合计 [ 总分 ]</option>
								<option disabled>----------------------</option>
								<option value='allroundPoint'>合计 [ 全能分 ]</option>
							</select>
						</div>
					</div>
				</div>

				<div class="col-md-1" style="margin-top: 15px;">
					<button class="btn btn-info" @click="search"><i class="fa fa-search" aria-hidden="true"></i> 查 询</button>
				</div>
			</div>

			<hr class="featurette-divider">

			<!-- 分组表格显示 -->
			<div id="totalScoreResult" style="display:none">
				<table class="table table-bordered table-hover table-striped">
					<thead>
						<tr style="background-color: #cbeff5">
							<th v-if="groupBy=='sex'||groupBy=='sexGroup'">性别</th>
							<th v-if="groupBy=='groupName'||groupBy=='sexGroup'">组别</th>
							<th>名次</th>
							<th>团体名</th>
							<th v-if="orderBy=='point'">总分</th>
							<th v-else-if="orderBy=='allroundPoint'">全能总分</th>
						</tr>
					</thead>
					<tbody id="table" style="font-size:17px;">
					</tbody>
				</table>
			</div>
			<!-- ./分组表格显示 -->

		</div>
	</div>
</div>

</div>

<script>
var vm = new Vue({
	el:'#app',
	data(){
		return{
			gamesInfo:{},
			groupBy:'',
			orderBy:'',
			scoreData:{}
		}
	},
	methods:{
		search:()=>{
			$("#table").html('');
			$("#totalScoreResult").hide(400);
			lockScreen();

			$.ajax({
				url:'/api/getTeamScore',
				data:{'id':vm.gamesInfo['id'],'groupBy':vm.groupBy,'orderBy':vm.orderBy},
				dataType:'json',
				error:e=>{
					unlockScreen();
					showModalTips("服务器错误！");
					console.log(e);
				},
				success:ret=>{
					if(ret.code==200){
						let data=ret.data['data'];
						let retData=[];

						if(vm.groupBy!='sexGroup' && vm.groupBy!='total'){
							let lastName='';
							let index=0;
							let bgColor='';

							for(i in data){
								let info=data[i];
								if(retData[info[vm.groupBy]]==undefined) retData[info[vm.groupBy]]=[];
								
								if(lastName==info[vm.groupBy]){
									index++;
								}else{
									lastName=info[vm.groupBy];
									index=0;
								}
								retData[info[vm.groupBy]][index]=info;
							}

							lastName='';

							// 结果显示
							let n=0;
							for(j in retData){
								let groupByData=retData[j];
								n++;

								if(n%2==1) bgColor='background-color:#B3E5FC;';
								else bgColor='background-color:#CCFF90;';

								for(k in groupByData){
									let teamData=groupByData[k];

									if(teamData['totalAllroundPoint']==null) teamData['totalAllroundPoint']=0;

									html='';
									html+='<tr>';
									html+=(lastName!=teamData[vm.groupBy])?'<td style="text-align:center;vertical-align:middle;'+bgColor+'" rowspan="'+groupByData.length+'">'+j+'</td>':'';
									html+='<td>'+(parseInt(k)+1)+'</td>';
									html+='<td>'+teamData['name']+'</td>';
									html+=((vm.orderBy=='point'&&teamData['totalPoint']!=0)||(vm.orderBy=='allroundPoint'&&teamData['totalAllroundPoint']!=0))?'<td style="color:red;font-weight:bold;">':'<td>';
									html+=(vm.orderBy=='allroundPoint')?teamData['totalAllroundPoint']:teamData['totalPoint'];
									html+='</td>';
									html+='</tr>';

									lastName=(lastName!=teamData[vm.groupBy])?teamData[vm.groupBy]:lastName;

									$("#table").append(html);
								}
							}
						}else if(vm.groupBy=='sexGroup'){
							let lastSex='';
							let lastGroup='';
							let count=0;
							let html='';
							let j=0;
							let bgColor='';
							let bgColor2='';

							// 数据处理（分组、计算总条数）
							for(i in data){
								let info=data[i];

								if(retData[info['sex']]==undefined) retData[info['sex']]=[];
								if(retData[info['sex']][info['groupName']]==undefined) retData[info['sex']][info['groupName']]=[];
								
								// 统计性别总条数
								if(lastSex=='') lastSex=info['sex'];
								if(lastSex!==info['sex'] && i<data.length-1){
									// 如果和上一条的性别不同且不是最后一条记录
									retData[lastSex]['count']=count;
									lastSex=info['sex'];
									count=1;
								}else if(i==data.length-1){
									// 如果是最后一条记录
									retData[lastSex]['count']=count+1;
								}else{
									count++;
								}

								if(lastGroup==info['groupName']){
									j++;
								}else{
									lastGroup=info['groupName'];
									j=0;
								}
								retData[info['sex']][info['groupName']][j]=info;
							}

							lastSex='';
							lastGroup='';

							// 数据显示
							let n=1;
							let p=1;
							for(j in retData){
								let sexData=retData[j];
								n++;

								if(n%2==1) bgColor='background-color:#B3E5FC;';
								else bgColor='background-color:#CCFF90;';

								for(k in sexData){
									let groupData=sexData[k];
									p++;

									if(p%2==1) bgColor2='background-color:#FFFFCE';
									else bgColor2='background-color:#FFE4CA';

									for(m in groupData){
										let teamData=groupData[m];

										if(teamData['totalAllroundPoint']==null) teamData['totalAllroundPoint']=0;

										html='';
										html+='<tr>';
										html+=(lastSex!=teamData['sex'])?'<td style="text-align:center;vertical-align:middle;'+bgColor+'" rowspan="'+sexData['count']+'">'+j+'</td>':'';
										html+=(lastGroup!=teamData['groupName'])?'<td style="text-align:center;vertical-align:middle;'+bgColor2+'" rowspan="'+groupData.length+'">'+k+'</td>':'';
										html+='<td>'+(parseInt(m)+1)+'</td>';
										html+='<td>'+teamData['name']+'</td>';
										html+=((vm.orderBy=='point'&&teamData['totalPoint']!=0)||(vm.orderBy=='allroundPoint'&&teamData['totalAllroundPoint']!=0))?'<td style="color:red;font-weight:bold;">':'<td>';
										html+=(vm.orderBy=='allroundPoint')?teamData['totalAllroundPoint']:teamData['totalPoint'];
										html+='</td>';
										html+='</tr>';

										lastSex=(lastSex!=teamData['sex'])?teamData['sex']:lastSex;
										lastGroup=(lastGroup!=teamData['groupName'])?teamData['groupName']:lastGroup;

										$("#table").append(html);
									}
								}
							}
						}else if(vm.groupBy=='total'){
							for(i in data){
								html='';
								html+='<tr>';
								html+='<td>'+(parseInt(i)+1)+'</td>';
								html+='<td>'+data[i]['name']+'</td>';
								html+='<td style="color:red;font-weight:bold;">';
								html+=(vm.orderBy=='allroundPoint')?data[i]['totalAllroundPoint']:data[i]['totalPoint'];
								html+='</td>';
								html+='</tr>';

								$("#table").append(html);
							}
						}

						$("#totalScoreResult").show(500);
						unlockScreen();
						return true;
					}else{
						showModalTips("系统错误！");
						unlockScreen();
					}
				}
			})
		},
		clean:()=>{
			$("#totalScoreResult").hide(400);
			$("#table").html('');
		}
	},
	mounted:function(){
		this.gamesInfo=this.$refs.header.gamesInfo;
		this.groupBy=this.gamesInfo['teamScore']['groupBy'];
		this.orderBy=this.gamesInfo['teamScore']['orderBy'];
	}
});
</script>

<?php include 'include/footer.php'; ?>

</body>
</html>
