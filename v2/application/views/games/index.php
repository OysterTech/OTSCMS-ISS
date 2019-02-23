<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-赛事主页
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-03
 * @version 2019-02-23
 */
?>
<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view('include/header'); ?>
  <title><?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1><?=$info['name'];?></h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li class="active">赛事管理</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">

		<div class="col-xs-6">
			<a href="<?=base_url('games/edit');?>" class="btn btn-info btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-info-circle" aria-hidden="true"></i> 赛 事 信 息 管 理</a>
		</div>
		<div class="col-xs-6">
			<a href="<?=base_url('file/list');?>" class="btn btn-primary btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-files-o" aria-hidden="true"></i> 文 件</a>
		</div>

		<br><br><br>

		<div class="col-xs-6">
			<a href="<?=base_url('schedule/list');?>" class="btn btn-default btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-list-alt" aria-hidden="true"></i> 日 程</a>
		</div>
		<div class="col-xs-6">
			<a href="<?=base_url('checkin/index');?>" class="btn btn-block" style="background-color:#ffa6ff;font-weight:bold;font-size:21px;color:white;"><i class="fa fa-volume-up" aria-hidden="true"></i> 检 录</a>
		</div>

		<br><br><br>

		<div class="col-xs-6">
			<a onclick="showNavTips();" class="btn btn-success btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-trophy" aria-hidden="true"></i> 分 组 / 成 绩</a>
		</div>
		<div class="col-xs-6">
			<a onclick="showManageTeam()" class="btn btn-warning btn-block" style="font-weight:bold;font-size:21px;"><i class="fa fa-users" aria-hidden="true"></i> 团 体</a>
		</div>

		<br><br>

	</section>
	<!-- ./页面主要内容 -->
</div>
<!-- ./页面内容 -->

<?php $this->load->view('include/footer'); ?>

<script>
function showManageTeam(){
	$("#teamModal").modal('show');
}

function showNavTips(){
	$("#navTipsModal").modal('show');
}
</script>

<div class="modal fade" id="navTipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="name">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bold;font-size:24px;text-align:center;">
					<p id="tips">请通过导航栏进入管理页面，谢谢！</p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="teamModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="name">团体管理</h3>
			</div>
			<div class="modal-body">
				<a href="<?=base_url('group/list');?>" class="btn btn-info" style="width:98%;font-size:16px;font-weight:bold;"><i class="fa fa-group" aria-hidden="true"></i> 团 体 管 理</a><br><br>
				<a href="<?=base_url('group/updatePoint');?>" class="btn btn-danger" style="width:98%;font-size:16px;font-weight:bold;"><i class="fa fa-bar-chart" aria-hidden="true"></i> 人工加/扣 团体分</a>
				<!--font color="red" style="font-weight:bold;font-size:24px;text-align:center;">
				</font-->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
