<?php 
/**
 * @name 生蚝体育竞赛管理系统后台-V-文件列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-03
 * @version 2019-02-26
 */ 
?>

<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view('include/header'); ?>
  <title>文件列表 / <?=$this->setting->get('systemName'); ?></title>
</head>

<body class="hold-transition skin-cyan sidebar-mini">
<div class="wrapper">

<?php $this->load->view('include/navbar'); ?>

<!-- 页面内容 -->
<div id="app" class="content-wrapper">
	<!-- 头部(显示页面名称和路径) -->
	<section class="content-header">
		<h1>文件列表</h1>
		<ol class="breadcrumb">
			<li><a href="<?=base_url('dashborad');?>"><i class="fa fa-dashboard"></i> <?=$this->setting->get('systemName'); ?></a></li>
			<li><a href="<?=base_url('games/index/').$this->gamesId;?>">赛事主页</a></li>
			<li class="active">文件列表</li>
		</ol>
	</section>

	<!-- 页面主要内容 -->
	<section class="content">
		<div class="box">
			<div class="box-body">
				<button onclick="$('#uploadModal').modal('show');" class="btn btn-success btn-block"><i class="fa fa-upload"></i> 上 传 新 文 件 &gt;</button><br>

				<table id="table" class="table table-bordered table-hover table-striped">
					<thead>
						<tr>
							<th>文件名</th>
							<th>下载地址</th>
							<th>操作</th>
						</tr>
					</thead>

					<tbody>
						<?php foreach($list as $info){ ?>
							<tr>
								<td><?php echo $info['name']; ?></td>
								<td><a href="<?=$info['url'];?>"><?php echo $info['url']; ?></a></td>
								<td></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</section>
</div>

<?php $this->load->view('include/footer'); ?>

<script>
window.onload=function(){ 	
	$('#table').DataTable({
		responsive: true,
		"order":[[0,'asc']],
		"columnDefs":[{
			"targets":[2],
			"orderable": false
		}]
	});
};

function toUpload(){
	lockScreen();
	
	if($("#fileName").val()==""){
		unlockScreen();
		showModalTips('请输入文件名！');
		return false;
	}

	if($("#myfile").val().length>0){
		var formData = new FormData($('form')[0]);
		formData.append('file',$('#myfile')[0].files[0]);
		
		$.ajax({
			url:"<?=base_url('file/toUpload');?>",
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
				if(ret.code==200){
					alert("上传成功！");
					location.reload();
				}else if(ret.code==1){
					showModalTips('文件已存在！');
					return false;
				}else{
					showModalTips('文件上传失败！错误码：'+ret.code);
					return false;
				}
			}
		});
	}else{
		alert("请选择需要上传的文件！");
		$("#myfile").focus();
		return false;
	}
}
</script>

<div class="modal fade" id="uploadModal" tabindex="99">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3 class="modal-title">上传新文件</h3>
			</div>
			<div class="modal-body">
				<form method="post" enctype="multipart/form-data">
					<div class="input-group">
						<span class="input-group-addon">文件名称</span>
						<input class="form-control" name="fileName">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon">请选文件</span>
						<input type="file" class="form-control" name="myfile[]" id="myfile">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button class="btn btn-primary" data-dismiss="modal">&lt; 取消</button> <button class="btn btn-success" onclick="toUpload()">确认 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
