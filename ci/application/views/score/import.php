<?php
/**
 * @name 生蚝体育竞赛管理系统后台-V-成绩导入
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-24
 * @version 2019-02-27
 */
?>
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>成绩导入 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>成绩导入</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">成绩导入</li>
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

				<div id="importDiv" style="display: none;">

					<center><div style="text-align:center;">
						<div class="alert alert-danger" style="font-size: 18px;">
							<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 导入成绩过程中，请勿刷新/打开其他页面！<br>
							确认上传前，请再次核对项次及项目名是否正确！<br>
							上传成功后可点击“下一项”直接跳转哦~
						</div>
					</div></center>

					<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="token" value="<?=$token;?>">
						<div class="form-group">
							<label for="file">Excel文件</label>
							<input type="file" name="myfile[]" id="myfile" class="form-control" style="height:40px">
						</div>
						<a href="<?=base_url('order/setAthleteRemark');?>" target="_blank" class="btn btn-info" style="width:33%;"><i class="fa fa-bookmark"></i> 设置运动员标记</a>
						<button type="button" onclick="toImport()" class="btn btn-success" style="width:33%;"><i class="fa fa-upload"></i> 导 入 √</button>
						<button type="button" onclick="nextItem()" class="btn btn-warning" style="width:33%;"><i class="fa fa-arrow-circle-right"></i> 下 一 项 &gt;</button>
					</form>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var itemData=[];
var nextOrderIndex="0";
var duplicate=0;

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
	now=0;

	for(i in itemData){
		if(itemData[i]['order_index']==orderIndex[0]){
			$("#itemName").css('display','');
			$("#itemName").html(itemData[i]['sex']+itemData[i]['group_name']+itemData[i]['name']);
			now=1;
		}else if(now==1){
			nextOrderIndex=itemData[i]['order_index']+"-"+itemData[i]['id'];
			now=0;
		}else{
			now=0;
		}
	}

	$("#importDiv").show(600);
}


function nextItem(){
	$("#orderIndex").find("option[value='"+nextOrderIndex+"']").attr("selected",true);
	showItemName(nextOrderIndex);
}


function toImport(){
	if(duplicate==1){
		if(confirm("当前项目已有成绩数据！\n再次导入将会覆盖所有数据！请再次确认！")){

		}else{
			return;
		}
	}

	lockScreen();
	orderIndex=$("#orderIndex").val();
	orderIndex=orderIndex.split('-');

	if($("#myfile").val().length>0){
		var formData = new FormData($('form')[0]);
		formData.append('file',$('#myfile')[0].files[0]);
		formData.append('itemId',orderIndex[1]);
		formData.append('duplicate',duplicate);
		
		$.ajax({
			url:"<?=base_url('score/toImport');?>",
			type: "post",
			data: formData,
			dataType:"json",
			cache: false,
			contentType: false,
			processData: false,
			error:function(e){
				unlockScreen();
				console.log(JSON.stringify(e));
				$("#tips").html("服务器错误！请联系管理员！");
				$("#tipsModal").modal('show');
				return false;
			},
			success:function(ret){
				unlockScreen();
				duplicate=0;

				if(ret.code==200){
					alert("导入成功！");
					location.reload();
				}else if(ret.code==1){
					duplicate=1;
					showModalTips('当前项目已有成绩数据！<br>如需覆盖，请再次点击上传！');
					return false;
				}else if(ret.code==403){
					showModalTips('签名校验失败！<br>请刷新页面重试！');
					return false;
				}else if(ret.code==5003){
					showModalTips('导入失败！<br><br>Excel总条数：<font color="blue">'+ret.data['total']+'</font><br>成功导入条数：<font color="green">'+ret.data['successRows']+'</font>');
					return false;
				}else{
					showModalTips('导入失败！错误码：'+ret.code);
					return false;
				}
			}
		});
	}else{
		unlockScreen();
		alert("请选择需要导入的Excel文件！");
		$("#myfile").focus();
		return false;
	}
}
</script>

</body>
</html>
