<?php
require_once '../include/public.func.php';

if(isset($_POST) && $_POST){
	$userName=$_POST['userName'];
	$password=$_POST['password'];

	$query=PDOQuery($dbcon,"SELECT * FROM admin WHERE user_name=?",[$userName],[PDO::PARAM_STR]);
	
	if($query[1]!=1){
		die(returnAjaxData(0,"failedAuth"));
	}

	$salt=$query[0][0]['salt'];
	$hash=sha1($password.md5($salt));

	if($hash==$query[0][0]['password']){
		$sql2="UPDATE admin SET last_login=? WHERE user_name=?";
		$query2=PDOQuery($dbcon,$sql2,[date("Y-m-d H:i:s"),$userName],[PDO::PARAM_STR,PDO::PARAM_STR]);
		
		$_SESSION['sport_admin_isLogin']=1;
		$_SESSION['sport_admin_userName']=$userName;
		$_SESSION['sport_admin_level']=$query[0][0]['level'];
		die(returnAjaxData(200,"success",['url'=>ROOT_PATH.'admin/gamesList.php']));
	}else{
		die(returnAjaxData(0,"failedAuth"));
	}
}

?>