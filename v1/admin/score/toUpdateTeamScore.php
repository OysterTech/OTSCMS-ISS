<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改团体分
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-23
 * @update 2018-08-23
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id'];
	$bouns=$_POST['bouns']!=""?$_POST['bouns']:die(returnAjaxData(0,"failed"));
	$deduction=$_POST['deduction']!=""?$_POST['deduction']:die(returnAjaxData(0,"failed"));

	$sql="UPDATE team SET bouns=?,deduction=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$bouns,$deduction,$id],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
