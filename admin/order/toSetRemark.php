<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台更新备注
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-11-03
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id'];
	$type=$_POST['type'];
	$sql="UPDATE score SET remark=";

	if($id==""){
		die(returnAjaxData(0,"failed"));
	}

	if($type==""){
		$sql.="null";
	}else{
		$sql.='"'.$type.'"';
	}

	$sql.=" WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$id],[PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
