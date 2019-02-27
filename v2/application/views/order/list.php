<?php
/**
 * @name 生蚝体育竞赛管理系统后台-V-秩序册管理
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-16
 * @version 2019-02-26
 */
?>
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>秩序册管理 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>秩序册管理</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">秩序册管理</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">

				<input type="hidden" id="gamesId" value="<?=$gamesId;?>">

				<!-- 查询表单 -->
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon">第</span>
						<select id="scene" class="form-control" onchange="getItemList(this.value)">
							<option value="" selected disabled>::: 请选择场次 :::</option>
						</select>
						<span class="input-group-addon">场</span>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="input-group">
						<span class="input-group-addon">第</span>
						<select id="orderIndex" class="form-control" onchange="showItemName(this.value)">
							<option value="" selected disabled>::: 请选择项次 :::</option>
						</select>
						<span class="input-group-addon">项</span>
					</div>
				</div>
				<!-- ./项次选择框 -->

				<p style="line-height:4px;">&nbsp;</p>

				<center>
					<b><font id="itemName" color="red" size="6" style="display:none;"></font></b>
				</center>

				<p style="line-height:2px;">&nbsp;</p>

				<!-- 提交按钮 -->
				<center>
					<button class="btn btn-success" style="width:98%" onclick="search()">立 即 查 询 &gt;</button>
				</center>
				<!-- ./提交按钮 -->

				<div id="dataDiv" style="display:none">
					<hr>
					<table id="table" class="table table-hover table-striped table-bordered scoreTable">
						<tr>
							<th style="text-align:center;">组</th>
							<th style="text-align:center;">道</th>
							<th style="text-align:center;">名称</th>
							<th style="text-align:center;">单位</th>
							<th style="text-align:center;">操作</th>
						</tr>
					</table>

					<center>
						<button onclick="add()" style="width:98%" class="btn btn-primary">新 增 运 动 员</button>
					</center>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var adding=0;
var itemData=[];

getSceneList();

function getSceneList(){
	lockScreen();
	gamesId=$("#gamesId").val();

	$.ajax({
		url:"<?=base_url('api/getItemInfo');?>",
		data:{'gamesId':gamesId,'type':'scene'},
		dataType:'json',
		error:function(e){
			unlockScreen();
			showModalTips('服务器错误！<br>获取场次列表失败！');
			console.log(e);
		},
		success:function(ret){
			unlockScreen();
			
			if(ret.code==200){
				data=ret.data['sceneList'];
				
				for(i in data){
					$("#scene").append('<option value="'+data[i]['scene']+'">'+data[i]['scene']+'</option>');
				}
			}else{
				showModalTips("系统错误！<br>获取场次列表失败！");
			}
		}
	})
}


function getItemList(scene=0){
	lockScreen();
	$("#dataDiv").css('display','none');
	gamesId=$("#gamesId").val();

	$.ajax({
		url:"<?=base_url('api/getItemInfo');?>",
		data:{'gamesId':gamesId,'type':'item','scene':scene},
		dataType:'json',
		error:function(e){
			unlockScreen();
			showModalTips('服务器错误！<br>获取项目列表失败！');
			console.log(e);
		},
		success:function(ret){
			unlockScreen();
			
			if(ret.code==200){
				itemData=ret.data['itemList'];
				$("#orderIndex option:gt(0)").remove();
				
				for(i in itemData){
					$("#orderIndex").append('<option value="'+itemData[i]['order_index']+"-"+itemData[i]['id']+'">'+itemData[i]['order_index']+'</option>');
				}
			}else{
				showModalTips("系统错误！<br>获取项目列表失败！");
			}
		}
	})
}


function showItemName(orderIndex){
	$("#dataDiv").css('display','none');
	orderIndex=orderIndex.split('-');

	for(i in itemData){
		if(itemData[i]['order_index']==orderIndex[0]){
			$("#itemName").css('display','');
			$("#itemName").html(itemData[i]['sex']+itemData[i]['group_name']+itemData[i]['name']);
		}
	}
}


function search(){
	itemId=$("#orderIndex").val();
	itemId=itemId.split('-');
	itemId=itemId[1];

	$.ajax({
		url:'<?=base_url("order/search");?>',
		data:{'itemId':itemId},
		dataType:'json',
		error:function(e){
			showModalTips('服务器错误！<br>获取场次列表失败！');
			console.log(e);
		},
		success:function(ret){
			if(ret.code==200){
				$("#dataDiv").css('display','');
				$("#table tr:gt(0)").remove();
				data=ret.data['list'];

				for(i in data){
					info=data[i];
					html='<tr id="tr_'+info['id']+'">'
					    +'<td style="background-color:'+((info['run_group']%2==0)?'#C4E1FF':'#CEFFCE')+'">'
					    +'<p id="runGroup_1_'+info['id']+'">'+info['run_group']+'</p>'
					    +'<input id="runGroup_2_'+info['id']+'" value="'+info['run_group']+'" class="form-control" style="display:none;">'
					    +'</td>'
					    +'<td>'
					    +'<p id="runway_1_'+info['id']+'">'+info['runway']+'</p>'
					    +'<input id="runway_2_'+info['id']+'" value="'+info['runway']+'" class="form-control" style="display:none;">'
					    +'</td>'
					    +'<td>'
					    +'<p id="name_1_'+info['id']+'" style="color:#FF9224;font-size:17px;font-weight:bold;">'+info['name']+'</p>'
					    +'<input id="name_2_'+info['id']+'" value="'+info['name']+'" class="form-control" style="display:none;">'
					    +'</td>'
					    +'<td>'
					    +'<p id="team_1_'+info['id']+'">'+info['short_name']+'</p>'
					    +'<select id="team_2_'+info['id']+'" class="form-control" style="display:none;">'
					    +'<option value="-1" selected disabled>-- 请选择单位 --</option>'
					    // TODO:获取队伍
					    //+'<option value="$teamInfo['id']">$teamInfo['short_name']</option>'
					    +'</select>'
					    +'</td>'
					    +'<td>'
					    /*+'<button id="button_1_'+info['id']+'" onclick="readyUpdate('+info['id']+');" class="btn btn-primary">修改</button>'
					    +'<button id="button_2_'+info['id']+'" onclick="cancel('+info['id']+');" class="btn btn-success" style="display:none;">取消</button>'
					    +'<button id="button_3_'+info['id']+'" onclick="toUpdate('+info['id']+');" class="btn btn-warning" style="display:none;">保存</button>'
					    +'<button id="button_4_'+info['id']+'" onclick="del('+info['id']+');" class="btn btn-danger">删除</button>'
					    +'</td>'*/
					    +'</tr>';
					$("#table").append(html);
				}
			}
		}
	});
}


function del(id){
	if(confirm("确认要删除吗？")){
		$.ajax({
			url:"toEdit.php",
			type:"POST",
			data:{"type":"del","id":id},
			dataType:"JSON",
			success:function(ret){
				if(ret.code==200){
					$("#tr_"+id).remove();
					alert("删除成功！");
				}else{
					console.log(ret);
					alert("删除失败！！！");
				}
			}
		})
	}
}

function add(){
	if(adding==1){
		alert("请先保存上一条新增运动员资料！");
		return false;
	}else{
		adding=1;
	}

	/*tableHtml="<tr>"
	         +"<td><input id='runGroup_0' class='form-control'></td>"
	         +"<td><input id='runway_0' class='form-control'></td>"
	         +"<td><input id='name_0' class='form-control'></td>"
	         +"<td><select id='teamId_0' class='form-control'><option value='-1' selected disabled>-- 请选择单位 --</option></select></td>"
	         +"<td><button onclick='saveAdd()' class='btn btn-success'>保存新增</button></td>"
	         +"</tr>";*/
	$("#table").append(tableHtml);
}


function saveAdd(){
	itemId=$("#itemId").val();
	runGroup=$("#runGroup_0").val();
	runway=$("#runway_0").val();
	name=$("#name_0").val();
	teamId=$("#teamId_0").val();

	$.ajax({
		url:"toEdit.php",
		type:"POST",
		data:{"type":"add","itemId":itemId,"runGroup":runGroup,"runway":runway,"name":name,"teamId":teamId},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				adding=0;
				alert("新增成功！");
			}else{
				console.log(ret);
				alert("新增失败！！！\n数据未被保存！");
			}
		}
	});
}


function cancel(id){
	$("#runGroup_2_"+id).attr("style","display:none");
	$("#runGroup_1_"+id).attr("style","");
	$("#runway_2_"+id).attr("style","display:none");
	$("#runway_1_"+id).attr("style","");
	$("#name_2_"+id).attr("style","display:none");
	$("#name_1_"+id).attr("style","");
	$("#team_2_"+id).attr("style","display:none");
	$("#team_1_"+id).attr("style","");
	$("#button_4_"+id).attr("style","");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}


function readyUpdate(id){
	$("#runGroup_1_"+id).attr("style","display:none");
	$("#runGroup_2_"+id).attr("style","");
	$("#runway_1_"+id).attr("style","display:none");
	$("#runway_2_"+id).attr("style","");
	$("#name_1_"+id).attr("style","display:none");
	$("#name_2_"+id).attr("style","");
	$("#team_1_"+id).attr("style","display:none");
	$("#team_2_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");
	$("#button_4_"+id).attr("style","display:none");
}

function toUpdate(id){
	runGroup=$("#runGroup_2_"+id).val();
	runway=$("#runway_2_"+id).val();
	name=$("#name_2_"+id).val();
	team=$("#team_2_"+id).val();
	teamName=$("#team_2_"+id).find("option:selected").text();

	cancel(id);

	$.ajax({
		url:"toEdit.php",
		type:"POST",
		data:{"type":"edit","id":id,"runGroup":runGroup,"runway":runway,"name":name,"team":team},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				$("#runGroup_1_"+id).html(runGroup);
				$("#runway_1_"+id).html(runway);
				$("#name_1_"+id).html(name);
				$("#team_1_"+id).html(teamName);
			}else{
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}
</script>

</body>
</html>
