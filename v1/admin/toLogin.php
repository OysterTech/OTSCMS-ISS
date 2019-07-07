<?php
require_once '../include/public.func.php';

if(isset($_POST) && $_POST){
	$userName=$_POST['userName'];
	$password=$_POST['password'];

	/*$query=PDOQuery($dbcon,"SELECT * FROM admin WHERE user_name=?",[$userName],[PDO::PARAM_STR]);
	
	if($query[1]!=1){
		die(returnAjaxData(0,"failedAuth"));
	}

	$salt=$query[0][0]['salt'];
	$hash=sha1($password.md5($salt));*/

	//if($hash==$query[0][0]['password']){
	if($userName=="super" && $password=="027368"){
		//$sql2="UPDATE admin SET last_login=? WHERE user_name=?";
		//$query2=PDOQuery($dbcon,$sql2,[date("Y-m-d H:i:s"),$userName],[PDO::PARAM_STR,PDO::PARAM_STR]);
		
		//$_SESSION['sport_admin_isLogin']=1;
		//$_SESSION['sport_admin_userName']=$userName;
		//$_SESSION['sport_admin_level']=$query[0][0]['level'];
		$_SESSION['sport_admin_isLogin']=1;
		$_SESSION['sport_admin_userName']="管理员";
		$_SESSION['sport_admin_level']=1;
		die(returnAjaxData(200,"success",['url'=>ROOT_PATH.'admin/gamesList.php']));
	}elseif($userName=="gzswim" && $password=="123456"){
		$_SESSION['sport_admin_isLogin']=1;
		$_SESSION['sport_admin_userName']="广州泳协";
		$_SESSION['sport_admin_level']=1;
		die(returnAjaxData(200,"success",['url'=>ROOT_PATH.'admin/gamesIndex.php?gamesId=3']));
	}elseif($userName=="1222" && $password=="1222"){
		$_SESSION['sport_admin_isLogin']=1;
		$_SESSION['sport_admin_userName']="系列赛总决赛";
		$_SESSION['sport_admin_level']=9;
		die(returnAjaxData(200,"success",['url'=>ROOT_PATH.'admin/call.php?gamesId=7']));
	}else{
		die(returnAjaxData(0,"failedAuth"));
	}
}

?>