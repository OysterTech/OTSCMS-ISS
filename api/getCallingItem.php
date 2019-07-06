<?php
/**
 * @name 生蚝体育比赛管理系统-API-获取检录项目
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-09-01
 * @version 2018-09-07
 */
	
require_once '../include/public.func.php';

$gamesId=inputGet('gamesId',0,1);

$callingQuery=PDOQuery($dbcon,'SELECT scene,order_index,sex,group_name,name,total_group FROM item WHERE games_id=? AND is_calling=1',[$gamesId],[PDO::PARAM_INT]);

if($callingQuery[1]==1){
	$callingInfo=$callingQuery[0][0];
	$callingScene=$callingInfo['scene'];
	$callingOrderIndex=$callingInfo['order_index'];
	$limitOrderIndex=$callingOrderIndex+3;
	
	$readyQuery=PDOQuery($dbcon,'SELECT scene,order_index,sex,group_name,name,total_group FROM item WHERE games_id=? AND scene=? AND order_index>? AND order_index<=?',[$gamesId,$callingScene,$callingOrderIndex,$limitOrderIndex],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	$readyInfo=$readyQuery[0];
	$readyTotal=$readyQuery[1];

	$gamesInfo=PDOQuery($dbcon,'SELECT extra_json FROM games WHERE id=?',[$gamesId],[PDO::PARAM_INT]);

	if($gamesInfo[1]==1){
		$extraJson=$gamesInfo[0][0]['extra_json'];
		$extraJson=json_decode($extraJson,TRUE);
		$callBeginTime=$extraJson['callingBeginTime'];
	}else{
		$callBeginTime="";
	}

	die(returnAjaxData(200,"success",['calling'=>$callingInfo,'readying'=>$readyInfo,'callingBeginTime'=>$callBeginTime]));
}else{
	die(returnAjaxData(1,"Have no calling item"));
}
