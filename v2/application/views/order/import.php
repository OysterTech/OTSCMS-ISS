<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-导入分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-17
 * @version 2019-02-23
 */
?>

<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view('include/header'); ?>
  <title>导入秩序册 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>导入 <b>秩序册</b></h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">导入秩序册</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">

				<center><div style="text-align:center;">
					<div class="alert alert-warning">
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> 导入分组过程中，请勿刷新/打开其他页面！<br>
					</div>
				</div></center>

				<form method="post" enctype="multipart/form-data">

					<input type="hidden" name="token" value="<?=$token;?>">

					<div class="panel panel-default">
						<div class="panel-heading">通过Excel导入分组</div>
						<div class="panel-body">
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

							<button type="button" onclick="toImport()" class="btn btn-success btn-block">导 入 &gt;</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
var duplicate=0;

function toImport(){
	if(duplicate==1){
		if(confirm("当前比赛已有分组数据！\n再次导入将会覆盖所有数据！请再次确认！")){

		}else{
			return;
		}
	}

	lockScreen();

	if($("#myfile").val().length>0){
		var formData = new FormData($('form')[0]);
		formData.append('file',$('#myfile')[0].files[0]);
		formData.append('duplicate',duplicate);
		
		$.ajax({
			url:"<?=base_url('order/toImport');?>",
			type: "post",
			data: formData,
			dataType:"json",
			cache: false,
			contentType: false,
			processData: false,
			error:function(e){
				unlockScreen();
				console.log(JSON.stringify(e));
				showModalTips("服务器错误！请联系管理员！");
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
					showModalTips('当前比赛已有分组数据！<br>如需覆盖，请再次点击上传！');
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
