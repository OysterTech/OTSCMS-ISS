<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台录入棒次
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-17
 * @update 2018-08-17
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id'];
	$name=$_POST['name'];
	if($id=="" || $name==""){
		die(returnAjaxData(0,"failed"));
	}

	$sql="UPDATE score SET name=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$name,$id],[PDO::PARAM_STR,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
