<?php 
/**
 * @name V-新增用户
 * @author SmallOysyer <master@xshgzs.com>
 * @since 2018-02-14
 * @version V1.0 2018-08-08
 */
?>

<!DOCTYPE html>
<html>

<head>
	<?php $this->load->view('include/header'); ?>
	<title>新增用户 / <?=$this->Setting_model->get('systemName');?></title>
</head>

<body>
<div id="wrapper">

<?php $this->load->view('include/navbar'); ?>

<div id="page-wrapper">
<!-- Page Main Content -->

<!-- Page Name-->
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">新增用户</h1>
	</div>
</div>
<!-- ./Page Name-->

<div class="panel panel-default">
	<div class="panel-heading">新增用户</div>
	<div class="panel-body">
		<div class="form-group">
			<label for="userName">用户名</label>
			<input class="form-control" id="userName" onkeyup='if(event.keyCode==13)$("#nickName").focus();'>
			<p class="help-block">请输入<font color="green">4</font>-<font color="green">20</font>字的用户名</p>
		</div>
		<br>
		<div class="form-group">
			<label for="nickName">昵称</label>
			<input class="form-control" id="nickName" onkeyup='if(event.keyCode==13)$("#phone").focus();'>
		</div>
		<br>
		<div class="form-group">
			<label for="phone">手机号</label>
			<input type="number" class="form-control" id="phone" onkeyup='if(event.keyCode==13)$("#email").focus();'>
			<p class="help-block">目前仅支持中国大陆的手机号码</p>
		</div>
		<br>
		<div class="form-group">
			<label for="email">邮箱</label>
			<input type="email" class="form-control" id="email">
		</div>
		<br>
		<div class="form-group">
			<label for="roleID">角色</label>
			<select class="form-control" id="roleID">
				<option value="-1" selected disabled>--- 请选择角色 ---</option>
			</select>
		</div>

		<hr>

		<button class="btn btn-success btn-block" onclick='add()'>确 认 新 增 用 户 &gt;</button>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>

<!-- ./Page Main Content -->
</div>
<!-- ./Page -->
</div>

<script>
window.onload=function(){
	getAllRole();
}


function getAllRole(){
	lockScreen();

	$.ajax({
		url:"<?=site_url('api/getAllRole'); ?>",
		type:"post",
		data:{<?=$this->ajax->showAjaxToken(); ?>},
		dataType:'json',
		error:function(e){
			console.log(JSON.stringify(e));
			unlockScreen();
			$("#tips").html("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");

			$("#tipsModal").modal('show');
			return false;
		},
		success:function(ret){
			unlockScreen();
			
			if(ret.code=="200"){
				for(i in ret.data['list']){
					roleID=ret.data['list'][i]['id']
					roleName=ret.data['list'][i]['name'];
					$("#roleID").append('<option value="'+roleID+'">'+roleID+'. '+roleName+'</option>');
				}
				return true;
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


function add(){
	lockScreen();
	userName=$("#userName").val();
	nickName=$("#nickName").val();
	phone=$("#phone").val();
	email=$("#email").val();
	roleID=$("#roleID").val();

	if(userName==""){
		unlockScreen();
		$("#tips").html("请输入用户名！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(userName.length<4 || userName.length>20){
		unlockScreen();
		$("#tips").html("请输入 4-20字 的用户名！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(nickName==""){
		unlockScreen();
		$("#tips").html("请输入昵称！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(phone==""){
		unlockScreen();
		$("#tips").html("请输入手机号！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(phone.length!=11){
		unlockScreen();
		$("#tips").html("请正确输入手机号！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(email==""){
		unlockScreen();
		$("#tips").html("请输入邮箱！");
		$("#tipsModal").modal('show');
		return false;
	}
	if(roleID=="-1"){
		unlockScreen();
		$("#tips").html("请选择角色！");
		$("#tipsModal").modal('show');
		return false;
	}

	$.ajax({
		url:"<?=site_url('admin/user/toAdd'); ?>",
		type:"post",
		data:{<?=$this->ajax->showAjaxToken(); ?>,"userName":userName,"nickName":nickName,"phone":phone,"email":email,"roleID":roleID},
		dataType:'json',
		error:function(e){
			console.log(e);
			unlockScreen();
			$("#tips").html("服务器错误！<hr>请联系技术支持并提交以下错误码：<br><font color='blue'>"+e.status+"</font>");
			$("#tipsModal").modal('show');
			return false;
		},
		success:function(ret){
			unlockScreen();
			
			if(ret.code=="200"){
				$("#info_userName_show").html(userName);
				$("#info_nickName_show").html(nickName);
				$("#info_phone_show").html(phone);
				$("#info_email_show").html(email);
				$("#info_originPwd_show").html(ret.data['originPwd']);
				$("#infoModal").modal('show');
				return true;
			}else if(ret.message=="insertFailed"){
				$("#tips").html("新增失败！！！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.message=="haveUserName"){
				$("#tips").html("此用户名已存在！<br>请输入其他用户名！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.message=="havePhone"){
				$("#tips").html("此手机号已存在！<br>请输入其他手机号！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.message=="haveEmail"){
				$("#tips").html("此邮箱已存在！<br>请输入其他邮箱！");
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

<?php $this->load->view('include/tipsModal'); ?>

<div class="modal fade" id="infoModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        <h3 class="modal-title" id="ModalTitle">用户详细资料</h3>
      </div>
      <div class="modal-body">
        <table class="table table-hover table-striped table-bordered" style="border-radius: 5px; border-collapse: separate;text-align: center;">
        <tr>
          <td>用户名</td>
          <th><p id="info_userName_show"></p></th>
        </tr>
        <tr>
          <td>昵称</td>
          <th><p id="info_nickName_show"></p></th>
        </tr>
        <tr>
          <td>手机</td>
          <th><p id="info_phone_show"></p></th>
        </tr>
        <tr>
          <td>邮箱</td>
          <th><p id="info_email_show"></p></th>
        </tr>
        <tr>
          <td>初始密码</td>
          <th><p id="info_originPwd_show" style="color: green;font-weight: bold;"></p></th>
        </tr>
      </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="history.go(-1);">确认 &gt;</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
</html>
