<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-主页
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-20
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
		<h1><?=$this->setting->get('systemName'); ?><small>首页</small></h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li class="active">首页</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<!-- ▼ 通知栏 ▼ -->
		<ul class="list-group">
			<?php foreach($allNotice as $info){ ?>
				<li class="list-group-item">
					<div class="row">
						<div class="col-xs-8">
							<a href="<?=base_url('notice/detail/').$info['id'];?>" target="_blank">
								<i class="fa fa-bullhorn"></i> <?=$info['title'];?>
							</a>
						</div>

						<div class="col-xs-4" style="text-align:right;">
							<?=substr($info['create_time'],0,10);?>
						</div>		
					</div>
				</li>
			<?php } ?>
		</ul>
		<!-- ▲ 通知栏 ▲ -->

		<!-- ▼ 通知栏 ▼ -->
		<ul class="list-group">
			<li class="list-group-item" style="background-color:#02C874;">
				<div class="row">
					<div class="col-xs-12" style="text-align:center;font-size:22px;color:yellow;font-weight:bold;">
						点击比赛名称进行管理
					</div>
				</div>
			</li>
			<?php foreach($gamesList as $info){ ?>
				<li class="list-group-item">
					<div class="row">
						<div class="col-xs-12">
							<a href="<?=base_url('games/index/').$info['id'];?>">
								<i class="fa fa-flag-checkered"></i> <?=$info['name'];?>
							</a>&nbsp;&nbsp;
							<small class="label label-info"><i class="fa fa-clock-o"></i> <?=$info['start_date'].' ~ '.$info['end_date'];?></small>
						</div>
					</div>
				</li>
			<?php } ?>
		</ul>
		<!-- ▲ 通知栏 ▲ -->
	</section>
	<!-- ./页面主要内容 -->
</div>
<!-- ./页面内容 -->

<?php $this->load->view('include/footer'); ?>

<!-- ./Page Main Content -->
</div>
<!-- ./Page -->
</div>
</body>
</html>
