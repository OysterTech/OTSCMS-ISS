<?php
/**
 * @name 生蚝体育竞赛管理系统后台-V-成绩修改
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-28
 * @version 2019-02-28
 */
?>
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>成绩修改 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>成绩修改</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">成绩修改</li>
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
							<th>组/道</th>
							<th>名称</th>
							<th>单位</th>
							<th>成绩</th>
							<th>分</th>
							<th>标记</th>
							<th>操作</th>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
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
	lockScreen();
	itemId=$("#orderIndex").val();
	itemId=itemId.split('-');
	itemId=itemId[1];

	$.ajax({
		url:'<?=base_url("order/search");?>',
		data:{'itemId':itemId},
		dataType:'json',
		error:function(e){
			unlockScreen();
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
					    +'<td style="background-color:'+((info['run_group']%2==0)?'#C4E1FF':'#CEFFCE')+'">'+info['run_group']+'/'+info['runway']+'</td>'
					    +'<td>'
					    +'<p style="color:#FF9224;font-size:17px;font-weight:bold;">'+info['name']+'</p>'
					    +'</td>'
					    +'<td>'+info['short_name']+'</td>'
					    +'<td>'
					    +'<p id="remark_'+info['id']+'">'+(info['remark']==null?'':info['remark'])+'</p>'
					    +'</td>'
					    +'<td>'
					    +(info['remark']!='TRI'?'<button class="btn btn-info" onclick="setRemark('+info['id']+','+"'TRI'"+')">TRI测验</button> ':'')
					    +(info['remark']!='DNS'?'<button class="btn" style="background-color:#e56bec;color:white;" onclick="setRemark('+info['id']+','+"'DNS'"+')">DNS弃权</button> ':'')
					    +(info['remark']!='DSQ'?'<button class="btn btn-danger" onclick="setRemark('+info['id']+','+"'DSQ'"+')">DSQ犯规</button> ':'')
					    +(info['remark']!=null?'<button class="btn btn-success" onclick="setRemark('+info['id']+','+"''"+')">取消标记</button>':'')
					    +'</td>'
					    +'</tr>';
					$("#table").append(html);
				}

				unlockScreen();
			}else{
				unlockScreen();
				showModalTips('系统错误！<br>获取场次列表失败！');
				console.log(ret);
			}
		}
	});
}

function setRemark(id,type){
	lockScreen();
	$.ajax({
		url:"<?=base_url("order/toSetRemark");?>",
		type:"post",
		data:{"id":id,"type":type},
		dataType:"json",
		error:function(e){
			unlockScreen();
			showModalTips('服务器错误！<br>获取场次列表失败！');
			console.log(e);
		},
		success:function(ret){
			unlockScreen();
			if(ret.code==200){
				alert("修改成功！");
				search();
			}else{
				console.log(ret);
				alert("修改失败！");
			}
		}
	});
}
</script>

</body>
</html>
