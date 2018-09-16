<?php
/**
 * @name 生蚝体育比赛管理系统-Web-获取检录项目
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-09-01
 * @update 2018-09-07
 */
	
require_once 'include/public.func.php';

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']!=""?$_GET['gamesId']:die(returnAjaxData(0,"lackParam"));

$callingSql="SELECT scene,order_index,sex,group_name,name,total_group FROM item WHERE games_id=? AND is_calling=1";
$callingQuery=PDOQuery($dbcon,$callingSql,[$gamesId],[PDO::PARAM_INT]);

if($callingQuery[1]==1){
	$callingInfo=$callingQuery[0][0];
	$callingScene=$callingInfo['scene'];
	$callingOrderIndex=$callingInfo['order_index'];
	$limitOrderIndex=$callingOrderIndex+3;
	
	$readySql="SELECT scene,order_index,sex,group_name,name,total_group FROM item WHERE games_id=? AND scene=? AND order_index>? AND order_index<=?";
	$readyQuery=PDOQuery($dbcon,$readySql,[$gamesId,$callingScene,$callingOrderIndex,$limitOrderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	$readyInfo=$readyQuery[0];
	$readyTotal=$readyQuery[1];

	$gamesInfo=PDOQuery($dbcon,"SELECT extra_json FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

	if($gamesInfo[1]==1){
		$extraJson=$gamesInfo[0][0]['extra_json'];
		$extraJson=json_decode($extraJson,TRUE);
		$callBeginTime=$extraJson['call']['beginTime'];
	}else{
		$callBeginTime="";
	}

	die(returnAjaxData(200,"success",['calling'=>$callingInfo,'readying'=>$readyInfo,'callBeginTime'=>$callBeginTime]));
}else{
	die(returnAjaxData(1,"noCallingItem"));
}
