<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改项目类型
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-23
 * @update 2018-08-23
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(0,"failed"));
	$type=$_POST['type']!=""?$_POST['type']:die(returnAjaxData(0,"failed"));

	if($type=="allround") $type=1;
	elseif($type=="single") $type=0;

	$sql="UPDATE item SET is_allround=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$type,$id],[PDO::PARAM_INT,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
