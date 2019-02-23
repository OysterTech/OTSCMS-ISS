<?php 
/**
 * @name V-赛事基础数据编辑
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-04
 * @version 2019-02-03
 */
?>

<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view('include/header'); ?>
  <title>赛事基础数据编辑 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>赛事基础数据编辑</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">基础数据编辑</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">
				<div class="form-group">
					<label for="name">比赛名称</label>
					<input class="form-control" id="name" onkeyup='if(event.keyCode==13)$("#startDate").focus();' value="<?=$info['name'];?>">
				</div>
				<br>
				<div class="form-group">
					<label for="startDate">开赛时间</label>
					<input class="form-control" id="startDate" onkeyup='if(event.keyCode==13 || this.value.length==8)$("#endDate").focus();' value="<?=str_replace('-','',$info['start_date']);?>">
					<p class="help-block">请直接输入<font color="green" style="font-weight:bold;font-size:16px;">8</font>位数字 (如：<?=date('Ymd');?>)</p>
				</div>
				<br>
				<div class="form-group">
					<label for="endDate">结束时间</label>
					<input class="form-control" id="endDate" onkeyup='if(event.keyCode==13 || this.value.length==8)$("#venue").focus();' value="<?=str_replace('-','',$info['end_date']);?>">
					<p class="help-block">请直接输入<font color="green" style="font-weight:bold;font-size:16px;">8</font>位数字 (如：<?=date('Ymd');?>)</p>
				</div>
				<br>
				<div class="form-group">
					<label for="venue">比赛地点</label>
					<input class="form-control" id="venue" onkeyup='if(event.keyCode==13)$("#software").focus();' value="<?=$gamesJson['venue'];?>">
				</div>
				<hr>
				<div class="form-group">
					<label for="software">编排软件服务商(顺序不分先后)</label>
					<select class="form-control" id="software" onchange='$("#teamScore").focus();'>
						<option selected disabled>::: 请选择软件服务商 :::</option>
						<option value="mxx">● 梅雪雄</option>
						<option value="jbt">● 江伯滔（运动汇）</option>
						<option value="cy">● 蔡毅</option>
						<option value="sz">● 数智体育</option>
						<option value="sr">● 数锐体育</option>
					</select>
				</div>
				<br>
				<div class="form-group">
					<label for="teamScore">团体分排序条件</label>
					<select class="form-control" id="teamScore">
						<option selected disabled>::: 请选择排序条件 :::</option>
						<option value="">● 分性别、分组别</option>
						<option value="Sex">● 分性别、不分组别</option>
						<option value="Group">● 不分性别、分组别</option>
						<option value="Total">● 不分性别、不分组别</option>
						<option value="0">▲ 不显示团体分</option>
					</select>
				</div>
				<hr>
				<button class="btn btn-success btn-block" onclick='edit()'>确 认 编 辑 &gt;</button>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
$("#software").val("<?=$gamesJson['software'];?>");
$("#teamScore").val("<?=$gamesJson['teamScore'];?>");

function edit(){
	lockScreen();
	name=$("#name").val();
	startDate=$("#startDate").val();
	endDate=$("#endDate").val();
	venue=$("#venue").val();
	software=$("#software").val();
	teamScore=$("#teamScore").val();

	if(name==""){
		unlockScreen();
		$("#tips").html("请输入比赛名称！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(startDate=="" || startDate.length!=8 || startDate.substr(0,2)!="20"){
		unlockScreen();
		$("#tips").html("请正确输入8位数字的开赛日期！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(endDate=="" || endDate.length!=8 || endDate.substr(0,2)!="20"){
		unlockScreen();
		$("#tips").html("请正确输入8位数字的结束日期！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(venue==""){
		unlockScreen();
		$("#tips").html("请输入比赛地点！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(software==null){
		unlockScreen();
		$("#tips").html("请选择比赛所用的编排软件服务商！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(teamScore==null){
		unlockScreen();
		$("#tips").html("请选择显示团体分的排序条件！");
		$("#tipsModal").modal('show');
		return false;
	}

	// 日期格式处理
	startDate=startDate.substr(0,4)+"-"+startDate.substr(4,2)+"-"+startDate.substr(6);
	endDate=endDate.substr(0,4)+"-"+endDate.substr(4,2)+"-"+endDate.substr(6);

	$.ajax({
		url:"<?=base_url('games/toEdit'); ?>",
		type:"post",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"name":name,"startDate":startDate,"endDate":endDate,"venue":venue,"software":software,"teamScore":teamScore},
		dataType:"json",
		error:function(e){
			console.log(JSON.stringify(e));
			unlockScreen();
			$("#tips").html("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			$("#tipsModal").modal('show');
			return false;
		},
		success:function(ret){
			unlockScreen();
			console.log(JSON.stringify(ret));
			if(ret.code=="200"){
				$("#tips").html("编辑成功！");
				$("#tipsModal").modal('show');
				return true;
			}else if(ret.code=="0"){
				$("#tips").html("编辑失败！！！<br>数据未被保存！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.message=="haveName"){
				$("#tips").html("此比赛名称已存在！<br>请更换其他名称！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.code=="403"){
				$("#tips").html("Token无效！<hr>Tips:请勿在提交前打开另一页面哦~");
				$("#tipsModal").modal('show');
				return false;
			}else{
				$("#tips").html("系统错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+ret.code+"</font>");
				$("#tipsModal").modal('show');
				return false;
			}
		}
	});
}
</script>

</body>
</html>
