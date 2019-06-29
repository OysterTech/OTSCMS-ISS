
			<div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="scene" class="col-sm-3">场次:</label>
							<div class="col-sm-9">
								<select id="scene" class="form-control" v-model="scene" @change="getItem">
									<option v-for="sceneInfo in sceneList" v-bind:value="sceneInfo['scene']">第 {{sceneInfo['scene']}} 场</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="item" class="col-sm-2">项目:</label>
							<div class="col-sm-10">
								<select id="item" class="form-control" v-model="item" @change="getGroup">
									<template v-if="gamesInfo.kind==='游泳'">
										<option v-for="(itemInfo,index) in itemList" v-bind:value="itemInfo['id']+'-'+index">第{{itemInfo['order_index']}}项 {{itemInfo['sex']}}{{itemInfo['group_name']}}{{itemInfo['name']}}</option>
									</template>
									<template v-else>
										<option v-for="(itemInfo,index) in itemList" v-bind:value="itemInfo['id']+'-'+index">[{{itemInfo['kind']}}]第{{itemInfo['order_index']}}项 {{itemInfo['sex']}}{{itemInfo['group_name']}}{{itemInfo['name']}}</option>
									</template>
								</select>

							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="group" class="col-sm-3">分组:</label>
							<div class="col-sm-9">
								<select id="group" class="form-control" v-model='group'>
									<option value="0">全部</option>
									<option v-for="num in selectItemInfo['total_group']" v-bind:value="num">第 {{num}} 组</option>
								</select>
							</div> 
						</div>
					</div>
					<div class="col-md-1">
						<button class="btn btn-info" @click="search"><i class="fa fa-search" aria-hidden="true"></i> 查 询</button>
					</div>
				</div>

				<hr class="featurette-divider">

				<div id="scoreResult" style="display:none">
					<table class="table table-bordered table-hover table-striped">
						<thead>
							<tr style="background-color: #cbeff5">
								<th style="padding:5px 2px 8px;">序</th>
								<th style="padding:5px 0 8px;">组/道</th>
								<th>姓名</th>
								<th>成绩</th>
								<th style="padding:5px 0 8px;">得分</th>
								<th style="padding:5px 0 8px;">备注</th>
							</tr>
						</thead>
						<tbody>
							<template v-for="score in scoreData">
								<tr v-bind:style="{backgroundColor:score['score']==''?'#ffe0e0':(score['rank']==1?'#ffde2fe3':(score['rank']==2?'#d5d2d2d1':(score['rank']==3?'#d1be7dc7':'')))}">
									<th v-if="score['rank']!=0" style="padding:5px 2px;vertical-align:middle;">{{score['rank']}}</th>
									<th v-else style="padding:5px 2px;vertical-align:middle;"></th>

									<td style="padding:5px 0;vertical-align:middle;">{{score['run_group']}}/{{score['runway']}}</td>
									<td style="padding:5px 1px 0 1px;">{{score['name']}}<p style="font-size:10px">[{{score['short_name']}}]</p></td>
									<td style="padding:5px 0;vertical-align:middle;">{{score['score']}}</td>

									<td v-if="score['point']!=0" style="padding:5px 0;vertical-align:middle;">{{parseInt(score['point'])}}</td>
									<td v-else style="padding:5px 0;vertical-align:middle;"></td>

									<td style="padding:5px 0;vertical-align:middle;">{{score['remark']}}</td>
								</tr>
							</template>
						</tbody>
					</table>

					<center><div style="width:96%;text-align: center;">
						<div class="alert alert-info"><i class="fa fa-info-circle" aria-hidden="true"></i> 备注：DNS 弃权、DSQ 犯规、TRI测试</div>
					</div></center>
				</div>
			</div>