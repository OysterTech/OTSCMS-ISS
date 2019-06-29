<?php
/**
 * @name 生蚝体育比赛管理系统-API-获取项目列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-16
 * @version 2019-06-16
 */

require_once '../include/public.func.php';

$gamesId=inputGet('gamesId',0,1);
$type=inputGet('type',0,1);

if($type=='scene'){
	$sql='SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0 ORDER BY scene';
	$query=PDOQuery($dbcon,$sql,[$gamesId],[PDO::PARAM_INT]);
	
	if($query[1]>=1) returnAjaxData(200,'success',['total'=>$query[1],'sceneList'=>$query[0]]);
	else returnAjaxData(404,'Item not found');
}elseif($type=='item'){
	$scene=inputGet('scene',0,1);
	$kind=inputGet('kind',1,1);

	$sql='SELECT * FROM item WHERE games_id=? AND is_delete=0 AND scene=? ';
	$sql.=$kind!=''?'AND kind='.$kind.' ':'';
	$sql.='ORDER BY order_index';

	$query=PDOQuery($dbcon,$sql,[$gamesId,$scene],[PDO::PARAM_INT,PDO::PARAM_INT]);
	
	if($query[1]>=1) returnAjaxData(200,'success',['total'=>$query[1],'itemList'=>$query[0]]);
	else returnAjaxData(404,'Item not found');
}elseif($type=='group'){
	$groupSql="SELECT DISTINCT(group_name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY group_name";
	$groupQuery=PDOQuery($dbcon,$groupSql,[$gamesId],[PDO::PARAM_INT]);
	$nameSql="SELECT DISTINCT(name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY name";
	$nameQuery=PDOQuery($dbcon,$nameSql,[$gamesId],[PDO::PARAM_INT]);
	
	if($groupQuery[1]<1 || $nameQuery[1]<1){
		returnAjaxData(404,'Item not found');
	}else{
		$groupList=[];$nameList=[];
		foreach($groupQuery[0] as $groupInfo){
			array_push($groupList,$groupInfo['group_name']);
		}
		foreach($nameQuery[0] as $nameInfo){
			array_push($nameList,$nameInfo['name']);
		}
		
		returnAjaxData(200,'success',['group'=>$groupList,'name'=>$nameList]);
	}
}else{
	returnAjaxData(500,'Invaild type');
}
