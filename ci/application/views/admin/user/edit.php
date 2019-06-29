<?php 
/**
 * @name V-修改用户
 * @author SmallOysyer <master@xshgzs.com>
 * @since 2018-02-17
 * @version V1.0 2018-08-08
 */
?>

<!DOCTYPE html>
<html>

<head>
	<?php $this->load->view('include/header'); ?>
	<title>修改用户 / <?=$this->Setting_model->get('systemName');?></title>
</head>

<body>
<div id="wrapper">

<?php $this->load->view('include/navbar'); ?>

<div id="page-wrapper">
<!-- Page Main Content -->

<input type="hidden" id="userID" value="<?=$userID; ?>">

<!-- Page Name-->
<div class="row">
	<div class="col-lg-12">
		<h1 class="page-header">修改用户</h1>
	</div>
</div>
<!-- ./Page Name-->

<div class="panel panel-default">
	<div class="panel-heading">修改用户</div>
	<div class="panel-body">
		<div class="form-group">
			<label for="userName">用户名</label>
			<input class="form-control" id="userName" onkeyup='if(event.keyCode==13)$("#nickName").focus();' value="<?=$info['user_name']; ?>">
			<p class="help-block">请输入<font color="green">4</font>-<font color="green">20</font>字的用户名</p>
		</div>
		<br>
		<div class="form-group">
			<label for="nickName">昵称</label>
			<input class="form-control" id="nickName" onkeyup='if(event.keyCode==13)$("#phone").focus();' value="<?=$info['nick_name']; ?>">
		</div>
		<br>
		<div class="form-group">
			<label for="phone">手机号</label>
			<input type="number" class="form-control" id="phone" onkeyup='if(event.keyCode==13)$("#email").focus();' value="<?=$info['phone']; ?>">
			<p class="help-block">目前仅支持中国大陆的手机号码</p>
		</div>
		<br>
		<div class="form-group">
			<label for="email">邮箱</label>
			<input type="email" class="form-control" id="email" value="<?=$info['email']; ?>">
		</div>
		<br>
		<div class="form-group">
			<label for="roleID">角色</label>
			<select class="form-control" id="roleID">
				<option value="-1" selected disabled>--- 请选择角色 ---</option>
			</select>
		</div>

		<hr>

		<div class="form-group">
			<label>注册时间</label>
			<input class="form-control" value="<?=$info['create_time']; ?>" disabled>
		</div>
		<br>
		<div class="form-group">
			<label>最后修改时间</label>
			<input class="form-control" value="<?=$info['update_time']; ?>" disabled>
		</div>

		<hr>

		<button class="btn btn-success btn-block" onclick='edit()'>确 认 修 改 用 户 &gt;</button>
	</div>
</div>

<?php $this->load->view('include/footer'); ?>

<!-- ./Page Main Content -->
</div>
<!-- ./Page -->
</div>

<script>
var nowRoleID="<?=$info['role_id']; ?>";

window.onload=function(){
	getAllRole();
	$("#roleID").val(nowRoleID);
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


function edit(){
	lockScreen();
	userID=$("#userID").val();
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
		url:"<?=site_url('admin/user/toEdit'); ?>",
		type:"post",
		data:{<?=$this->ajax->showAjaxToken(); ?>,'userID':userID,"userName":userName,"nickName":nickName,"phone":phone,"email":email,"roleID":roleID},
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
				alert("修改成功！");
				history.go(-1);
				return true;
			}else if(ret.message=="updateFailed"){
				$("#tips").html("修改失败！！！");
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

</body>
</html>
