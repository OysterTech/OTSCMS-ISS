<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改开场时间
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-21
 * @update 2018-08-26
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$gamesId=$_POST['gamesId']!=""?$_POST['gamesId']:die(returnAjaxData(1,"lackParam"));
	$id=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(1,"lackParam"));
	$time=$_POST['time']!=""?$_POST['time']:die(returnAjaxData(1,"lackParam"));
	
	if(strlen($time)>=20){
		die(returnAjaxData(2,"invaildTime"));
	}
	
	$sql="SELECT extra_json FROM games WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$gamesId],[PDO::PARAM_INT]);

	if($query[1]!=1){
		die(returnAjaxData(3,"noData"));
	}

	$extraJson=json_decode($query[0][0]['extra_json'],true);
	$extraJson['scene'][$id]=$time;
	$extraJson=json_encode($extraJson);

	$sql="UPDATE games SET extra_json=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
