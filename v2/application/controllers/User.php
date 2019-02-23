<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-用户
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-02-19
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

	public $sessPrefix;
	public $nowUserID;
	public $nowUserName;
	public $forgetPwdUserID;
	public $API_PATH;

	function __construct()
	{
		parent::__construct();
		$this->load->helper('string');

		$this->safe->checkPermission();

		$this->sessPrefix=$this->safe->getSessionPrefix();
		
		$this->API_PATH=$this->setting->get('apiPath');
		$this->nowUserID=$this->session->userdata($this->sessPrefix.'userID');
		$this->nowUserName=$this->session->userdata($this->sessPrefix.'userName');
	}


	public function updateProfile()
	{
		$this->ajax->makeAjaxToken();

		$sql="SELECT * FROM user WHERE id=?";
		$query=$this->db->query($sql,[$this->nowUserID]);
		$list=$query->result_array();
		$info=$list[0];
		$this->load->view('user/updateProfile',['info'=>$info]);
	}


	public function toUpdateProfile()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);

		$nickName=$this->input->post('nickName');
		$oldPwd=$this->input->post('oldPwd');
		$newPwd=$this->input->post('newPwd');
		$phone=$this->input->post('phone');
		$email=$this->input->post('email');

		$sql="UPDATE user SET nick_name=?,";
		$updateData=array();
		array_push($updateData,$nickName);

		if($oldPwd!=""){
			$validateOldPwd=$this->User_model->validateUser($this->nowUserID,"",$oldPwd);

			if($validateOldPwd=="200"){
				$salt=random_string('alnum');
				$hashSalt=md5($salt);
				$hashPwd=sha1($newPwd.$hashSalt);

				$sql.="password=?,salt=?,";
				array_push($updateData,$hashPwd,$salt);
			}elseif($validateOldPwd=="403"){
				$ret=$this->ajax->returnData("3","userForbidden");
				die($ret);
			}else{
				$ret=$this->ajax->returnData("4","invaildPwd");
				die($ret);
			}
		}

		$sql.="phone=?,email=? WHERE id=?";
		array_push($updateData,$phone,$email,$this->nowUserID);
		$query=$this->db->query($sql,$updateData);

		if($this->db->affected_rows()==1){
			$ret=$this->ajax->returnData("200","success");
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","updateFailed");
			die($ret);
		}
	}


	public function login()
	{
		$this->ajax->makeAjaxToken();

		$this->load->view('user/login');
	}


	public function toLogin()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);

		$userName=$this->input->post('userName');
		$pwd=$this->input->post('pwd');

		$validate=$this->User_model->validateUser(0,$userName,$pwd);

		if($validate=="200"){
			$userInfo=$this->User_model->getUserInfoByUserName($userName);
			$userID=$userInfo['id'];
			$nickName=$userInfo['nick_name'];
			$roleID=$userInfo['role_id'];
			$status=$userInfo['status'];
			
			if($status==0){
				$ret=$this->ajax->returnData("3","userForbidden");
				die($ret);
			}elseif($status==2){
				$ret=$this->ajax->returnData("4","userNotActive");
				die($ret);
			}
			
			// 获取角色名称
			$roleQuery=$this->db->query('SELECT name FROM role WHERE id=?',[$roleID]);
			if($roleQuery->num_rows()!=1){
				$ret=$this->ajax->returnData("2","noRoleInfo");
				die($ret);
			}
			
			$roleList=$roleQuery->result_array();
			$roleName=$roleList[0]['name'];
			
			// 将用户信息存入session
			$this->session->set_userdata($this->sessPrefix.'userID',$userID);
			$this->session->set_userdata($this->sessPrefix.'nickName',$nickName);
			$this->session->set_userdata($this->sessPrefix.'userName',$userName);
			$this->session->set_userdata($this->sessPrefix.'roleID',$roleID);
			$this->session->set_userdata($this->sessPrefix.'roleName',$roleName);

			$this->db->query("UPDATE user SET last_login=? WHERE id=?",[date("Y-m-d H:i:s"),$userID]);
			
			$ret=$this->ajax->returnData("200","success");
			die($ret);
		}elseif($validate=="403"){
			$ret=$this->ajax->returnData("1","userForbidden");
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","invaildPwd");
			die($ret);
		}
	}
	

	public function logout()
	{
		session_destroy();
		header("Location:".base_url('user/login'));
	}
	
	
	public function forgetPassword()
	{
		$this->ajax->makeAjaxToken();

		$this->load->view('user/forgetPassword');
	}
	
	
	public function forgetPasswordSendCode()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$email=$this->input->post('email');		
		$sql="SELECT id,user_name,nick_name FROM user WHERE email=?";
		$query=$this->db->query($sql,[$email]);
		
		if($query->num_rows()!=1){
			$ret=$this->ajax->returnData("1","noUser");
			die($ret);
		}else{
			$list=$query->result_array();
			$id=$list[0]['id'];
			$userName=$list[0]['user_name'];
			$nickName=$list[0]['nick_name'];
		}
		
		$verifyCode=mt_rand(123456,987654);
		$expireTime=time()+600;
		$this->session->set_userdata($this->sessPrefix.'forgetPwd_mailInfo',array('email'=>$email,'verifyCode'=>$verifyCode,'expireTime'=>$expireTime));
		$this->session->set_userdata($this->sessPrefix.'forgetPwd_userID',$id);
		$this->session->set_userdata($this->sessPrefix.'forgetPwd_nickName',$nickName);
		$this->session->set_userdata($this->sessPrefix.'forgetPwd_userName',$userName);
		
		$message='Email验证 / '.$this->Setting_model->get("systemName").'<hr>';
		$message.='尊敬的'.$nickName.'用户，您正在申请重置密码，请填写此验证码以继续重置密码：'.$verifyCode.'<br><br>';
		$message.='此验证码15分钟内有效，请尽快填写哦！<br><br>';
		$message.='如您本人没有申请过重置密码，请忽略本邮件。此邮件由系统自动发送，请勿回复！谢谢！<br><br>';
		$message.='如有任何问题，请<a href="mailto:service@xshgzs.com">联系我们</a>';
		$message.='<hr>';
		$message.='<center>生蚝科技 &copy;2014-'.date("Y").'</center>';
		
		$this->load->library('email');

		$this->email->from($this->config->item('smtp_user'),'生蚝科技');
		$this->email->to($email);
		$this->email->subject('['.$this->Setting_model->get("systemName").'] 重置密码邮箱认证');
		$this->email->message($message);

		$mailSend=$this->email->send();
		
		if($mailSend==TRUE){
			$ret=$this->ajax->returnData("200","success");
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","sendFailed");
			die($ret);
		}
	}
	
	
	public function forgetPasswordVerifyCode()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$email=$this->input->post('email');
		$verifyCode=$this->input->post('verifyCode');
		$info=$this->session->userdata($this->sessPrefix.'forgetPwd_mailInfo');
		
		if($email!=$info['email']){
			$ret=$this->ajax->returnData("1","invalidMail");
			die($ret);
		}elseif($verifyCode!=$info['verifyCode']){
			$ret=$this->ajax->returnData("2","invalidCode");
			die($ret);
		}elseif($info['expireTime']<=time()){
			$ret=$this->ajax->returnData("3","expired");
			die($ret);
		}else{
			$this->session->unset_userdata($this->sessPrefix.'forgetPwd_mailInfo');
			$ret=$this->ajax->returnData("200","success",base_url('user/resetPassword'));
			die($ret);
		}
	}

	
	public function resetPassword()
	{
		$this->ajax->makeAjaxToken();

		if($this->session->userdata($this->sessPrefix.'forgetPwd_userID')<1 || $this->session->userdata($this->sessPrefix.'forgetPwd_mailInfo')!=NULL){
			die('<script>alert("非法访问！");window.location.href="'.base_url('user/forgetPassword').'";</script>');
		}else{
			$this->load->view('user/resetPwd');
		}		
	}
	
	
	public function toResetPassword()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$id=$this->session->userdata($this->sessPrefix.'forgetPwd_userID');
		$userName=$this->input->post('userName');
		$pwd=$this->input->post('pwd');
		
		$salt=random_string('alnum');
		$hashSalt=md5($salt);
		$hashPwd=sha1($pwd.$hashSalt);
		
		$nowTime=date("Y-m-d H:i:s");
		$sql="UPDATE user SET password=?,salt=?,update_time=? WHERE id=?";
		$query=$this->db->query($sql,[$hashPwd,$salt,$nowTime,$id]);
		
		if($this->db->affected_rows()==1){
			$ret=$this->ajax->returnData("200","success");
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","resetFailed");
			die($ret);
		}
	}
}
