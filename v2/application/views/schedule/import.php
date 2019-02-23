<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-导入日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-08-18
 * @version 2019-02-23
 */
?>

<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>导入日程 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>导入 <b>日程</b></h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">导入日程</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">

				<center><div style="text-align:center;">
					<div class="alert alert-danger">
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 导入日程过程中，请勿刷新/打开其他页面！<br>
					</div>
				</div></center>

				<form method="post" action="<?=base_url('schedule/toImport');?>" enctype="multipart/form-data">

					<input type="hidden" name="token" value="<?=$token;?>">
					<div class="form-group">
						<label>比赛名称</label>
						<textarea class="form-control" disabled><?=$gamesName;?></textarea>
					</div>
					<br>
					<div class="form-group">
						<label for="file">Excel文件</label>
						<input type="file" name="myfile[]" id="myfile" class="form-control" style="height:40px">
					</div>

					<hr>

					<input type="submit" class="btn btn-success btn-block" value="导 入 &gt;">
				</form>
			</div>
		</div>
	</section>					
</div>

<?php $this->load->view('include/footer'); ?>

</body>
</html>
