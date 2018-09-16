<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台更新备注
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-08-23
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id'];
	$type=$_POST['type'];
	if($id==""){
		die(returnAjaxData(0,"failed"));
	}

	$sql="UPDATE score SET remark=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$type,$id],[PDO::PARAM_STR,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
