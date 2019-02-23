<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-日程列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-08-21
 * @version 2019-02-23
 */
?>

<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>日程列表 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>日程列表</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">日程列表</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">

				<center><div style="width:96%;text-align: center;">
					<div class="alert alert-warning">
						项目标黄为全能项目<br>
						全能项目仅显示成绩分，不显示得分
					</div>
				</div></center>

				<?php
				foreach($sceneInfo as $scene){
					$sceneId=$scene['scene'];
				?>

				<table id="table_<?=$sceneId;?>" class="table table-hover table-striped table-bordered scheduleTable">
					<tr>
						<th style="text-align:center;font-size:16px;background-color:#C4E1FF" colspan="7">
							第 <?=$sceneId;?> 场<?php if(isset($gamesJson['scene'][$sceneId])) echo "（".$gamesJson['scene'][$sceneId]."）";?>
							<input id="time_<?=$sceneId;?>" value="<?php if(isset($gamesJson['scene'][$sceneId])) echo $gamesJson['scene'][$sceneId];?>" style="display:none;">
							<button id="time_btn_1_<?=$sceneId;?>" class="btn btn-primary" onClick="editStartTime('<?=$sceneId;?>');">修改开场时间</button>
							<button id="time_btn_2_<?=$sceneId;?>" class="btn btn-success" onClick="saveStartTime('<?=$sceneId;?>');" style="display:none;">保存开场时间</button>
							<button id="btn_1_<?=$sceneId;?>" class="btn btn-success" onClick="add('<?=$sceneId;?>');">新增项目</button>
						</th>
					</tr>
					
					<tr>
						<th style="text-align:center;">项次</th>
						<th style="text-align:center;">性别</th>
						<th style="text-align:center;">组别</th>
						<th style="text-align:center;">项目名</th>
						<th style="text-align:center;">组数</th>
						<th style="text-align:center;">人(队)数</th>
						<th style="text-align:center;">操作</th>
					</tr>
					
					<?php
					$sceneItemInfo=$scene['itemInfo'];
					$totalItem=count($sceneItemInfo);

					foreach($sceneItemInfo as $info){
						$itemId=$info['id'];
					?>
					<tr id="tr_<?=$itemId;?>" <?php if($info['is_allround']==1) echo 'style="background-color:#F4FF81"';?>>
						<td>
							<p id="order_1_<?=$itemId;?>"><?=$info['order_index'];?></p>
							<input id="order_2_<?=$itemId;?>" value="<?=$info['order_index'];?>" class="form-control" style="display:none;">
						</td>
						<td>
							<p id="sex_1_<?=$itemId;?>"><?=$info['sex'];?></p>
							<select id="sex_2_<?=$itemId;?>" value="<?=$info['sex'];?>" class="form-control" style="display:none;">
								<option value="男子">男子</option>
								<option value="女子">女子</option>
								<option value="男女">男女</option>
							</select>
						</td>
						<td>
							<p id="groupName_1_<?=$itemId;?>"><?=$info['group_name'];?></p>
							<input id="groupName_2_<?=$itemId;?>" value="<?=$info['group_name'];?>" class="form-control" style="display:none;">
						</td>
						<td>
							<p id="name_1_<?=$itemId;?>"><?=$info['name'];?></p>
							<input id="name_2_<?=$itemId;?>" value="<?=$info['name'];?>" class="form-control" style="display:none;">
						</td>
						<td>
							<p id="totalGroup_1_<?=$itemId;?>"><?=$info['total_group'];?></p>
							<input id="totalGroup_2_<?=$itemId;?>" value="<?=$info['total_group'];?>" class="form-control" style="display:none;">
						</td>
						<td>
							<p id="totalAth_1_<?=$itemId;?>"><?=$info['total_ath'];?></p>
							<input id="totalAth_2_<?=$itemId;?>" value="<?=$info['total_ath'];?>" class="form-control" style="display:none;">
						</td>
						<td>
							<button id="button_1_<?=$itemId;?>" onclick='edit("<?=$itemId;?>")' class="btn btn-primary">编辑</button>
							<button id="button_2_<?=$itemId;?>" onclick='cancel("<?=$itemId;?>")' class="btn btn-success" style="display:none;">取消</button>
							<button id="button_3_<?=$itemId;?>" onclick='save("<?=$itemId;?>")' class="btn btn-warning" style="display:none;">保存</button>
							<?php if($info['is_allround']==0){ ?>
								<button id="button_4_<?=$itemId;?>" onclick='setType("<?=$itemId;?>","allround")' class="btn btn-info" style="display:none;">设为<b>全能</b></button>
							<?php }else{ ?>
								<button id="button_5_<?=$itemId;?>" onclick='setType("<?=$itemId;?>","single")' class="btn btn-info" style="display:none;">设为<b>单项</b></button>
							<?php } ?>
							<button id="button_6_<?=$itemId;?>" onclick='del("<?=$itemId;?>","<?=$sceneId;?>","<?=$info['order_index'];?>","<?=$info['sex'].$info['group_name'].$info['name'];?>")' class="btn btn-danger" style="display:none;">删除</button>

						</td>
					</tr>
					<?php } ?>
				</table><br>
				<?php } ?>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var addingScene=-1;

function add(sceneId){
	if(addingScene==-1){
		addingScene=sceneId;
		html="<tr id='tr_0'>"
			+"<td><input id='order_0' class='form-control'></td>"
			+"<td><select id='sex_0' class='form-control'><option value='男子'>男子</option><option value='女子'>女子</option><option value='男女'>男女</option></select></td>"
			+"<td><input id='groupName_0' class='form-control'></td>"
			+"<td><input id='name_0' class='form-control'></td>"
			+"<td><input id='totalGroup_0' class='form-control'></td>"
			+"<td><input id='totalAth_0' class='form-control'></td>"
			+"<td><button onclick='cancelAdd()' class='btn btn-warning'>取消</button> <button onclick='saveAdd()' class='btn btn-success'>保存新增</button></td>"
			+"</tr>";
		$("#table_"+sceneId).append(html);
		$("#order_0").focus();
	}else{
		alert("请先保存正在新增的第 "+addingScene+" 场项目数据！");
		$("#order_0").focus();
		return false;
	}
}


function saveAdd(){
	lockScreen();
	order=$("#order_0").val();
	sex=$("#sex_0").val();
	groupName=$("#groupName_0").val();
	name=$("#name_0").val();
	totalGroup=$("#totalGroup_0").val();
	totalAth=$("#totalAth_0").val();

	if(order=="" || sex==null || groupName=="" || name=="" || totalGroup=="" || totalAth==""){
		unlockScreen();
		showModalTips("请完整输入所有项目数据！");
		return false;
	}
	if(parseInt(totalGroup)>=parseInt(totalAth)){
		unlockScreen();
		showModalTips("请正确输入 组数 和 人(队)数！");
		return false;
	}

	$.ajax({
		url:"<?=base_url('schedule/toAdd');?>",
		type:"post",
		dataType:"json",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"scene":addingScene,"order":order,"sex":sex,"groupName":groupName,"name":name,"totalGroup":totalGroup,"totalAth":totalAth},
		error:function(e){
			console.log(JSON.stringify(e));
			unlockScreen();
			showModalTips("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			return false;
		},
		success:function(ret){
			if(ret.code==200){
				alert("新增成功！");
				location.reload();
			}else{
				console.log(ret);
				showModalTips("新增失败！！！");
			}
		}
	})
}


function cancelAdd(){
	$("#tr_0").remove();
	addingScene=-1;
}


function del(id,scene,orderIndex,name){
	if(confirm("确认要删除 ["+scene+"/"+orderIndex+" "+name+"] 吗？")){
		$.ajax({
			url:"<?=base_url('schedule/toDel');?>",
			type:"post",
			dataType:"json",
			data:{<?=$this->ajax->showAjaxToken(); ?>,"itemId":id},
			error:function(e){
				console.log(JSON.stringify(e));
				unlockScreen();
				showModalTips("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
				return false;
			},
			success:function(ret){
				if(ret.code==200){
					$("#tr_"+id).remove();
					showModalTips("删除成功！");
					return true;
				}else{
					console.log(ret);
					showModalTips("删除失败！！！");
				}
			}
		})
	}else{
		return;
	}
}


function editStartTime(sceneId){
	$("#time_"+sceneId).attr("style","");
	$("#time_btn_1_"+sceneId).attr("style","display:none;");
	$("#time_btn_2_"+sceneId).attr("style","");
}


function saveStartTime(sceneId){
	time=$("#time_"+sceneId).val();

	$.ajax({
		url:"<?=base_url('schedule/toSetStartTime');?>",
		type:"post",
		dataType:"json",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"sceneId":sceneId,"time":time},
		error:function(e){
			console.log(JSON.stringify(e));
			unlockScreen();
			showModalTips("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			return false;
		},
		success:function(ret){
			if(ret.code==200){
				$("#time_"+sceneId).attr("style","display:none;");
				$("#time_btn_1_"+sceneId).attr("style","");
				$("#time_btn_2_"+sceneId).attr("style","display:none;");
				alert("修改成功！");
				location.reload();
			}else{
				$("#time_"+sceneId).attr("style","display:none;");
				$("#time_btn_1_"+sceneId).attr("style","");
				$("#time_btn_2_"+sceneId).attr("style","display:none;");
				console.log(ret);
				showModalTips("修改失败！！！\n数据未被保存！");
			}
		}
	})
}


function edit(id){
	$("#order_1_"+id).attr("style","display:none");
	$("#order_2_"+id).attr("style","");
	$("#sex_1_"+id).attr("style","display:none");
	$("#sex_2_"+id).attr("style","");
	$("#groupName_1_"+id).attr("style","display:none");
	$("#groupName_2_"+id).attr("style","");
	$("#name_1_"+id).attr("style","display:none");
	$("#name_2_"+id).attr("style","");
	$("#totalGroup_1_"+id).attr("style","display:none");
	$("#totalGroup_2_"+id).attr("style","");
	$("#totalAth_1_"+id).attr("style","display:none");
	$("#totalAth_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_4_"+id).attr("style","");
	$("#button_5_"+id).attr("style","");
	$("#button_6_"+id).attr("style","");
}


function save(id){
	cancel(id);
	
	order=$("#order_2_"+id).val();
	sex=$("#sex_2_"+id).val();
	groupName=$("#groupName_2_"+id).val();
	name=$("#name_2_"+id).val();
	totalGroup=$("#totalGroup_2_"+id).val();
	totalAth=$("#totalAth_2_"+id).val();

	if(order=="" || sex==null || groupName=="" || name=="" || totalGroup=="" || totalAth==""){
		showModalTips("请完整输入所有项目数据！");
		return false;
	}
	if(parseInt(totalGroup)>=parseInt(totalAth)){
		showModalTips("请正确输入 组数 和 人(队)数！");
		return false;
	}

	$.ajax({
		url:"<?=base_url('schedule/toEdit');?>",
		type:"post",
		dataType:"json",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"itemId":id,"order":order,"sex":sex,"groupName":groupName,"name":name,"totalGroup":totalGroup,"totalAth":totalAth},
		error:function(e){
			console.log(JSON.stringify(e));
			showModalTips("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			unlockScreen();
			return false;
		},
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				location.reload();
			}else{
				console.log(ret);
				showModalTips("修改失败！！！\n数据未被保存！");
				unlockScreen();
			}
		}
	})
}


function setType(id,type){
	cancel(id);
	
	$.ajax({
		url:"<?=base_url('schedule/toSetItemType');?>",
		type:"post",
		dataType:"json",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"itemId":id,"type":type},
		error:function(e){
			console.log(JSON.stringify(e));
			unlockScreen();
			showModalTips("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			return false;
		},
		success:function(ret){
			if(ret.code==200){
				alert("修改项目类型成功！");
				location.reload();
			}else{
				console.log(ret);
				showModalTips("修改项目类型失败！！！\n数据未被保存！");
			}
		}
	})
}


function cancel(id){
	$("#order_2_"+id).attr("style","display:none");
	$("#order_1_"+id).attr("style","");
	$("#sex_2_"+id).attr("style","display:none");
	$("#sex_1_"+id).attr("style","");
	$("#groupName_2_"+id).attr("style","display:none");
	$("#groupName_1_"+id).attr("style","");
	$("#name_2_"+id).attr("style","display:none");
	$("#name_1_"+id).attr("style","");
	$("#totalGroup_2_"+id).attr("style","display:none");
	$("#totalGroup_1_"+id).attr("style","");
	$("#totalAth_2_"+id).attr("style","display:none");
	$("#totalAth_1_"+id).attr("style","");
	$("#button_6_"+id).attr("style","display:none");
	$("#button_5_"+id).attr("style","display:none");
	$("#button_4_"+id).attr("style","display:none");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}
</script>

</body>
</html>
