<?php
/**
 * @name 生蚝体育竞赛管理系统-API-处理查询成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-08-27
 * @version 2019-06-14
 */

require_once '../include/public.func.php';

$orderBy=isset($_GET['orderBy'])&&$_GET['orderBy']!=""?$_GET['orderBy']:die(returnAjaxData(0,"Lack Parameter"));

// 根据搜索条件获取条件数据并拼接SQL语句
if($orderBy=="item"){
	$itemId=isset($_GET['itemId'])&&$_GET['itemId']!=""?$_GET['itemId']:die(returnAjaxData(0,"Lack Parameter"));
}elseif($orderBy=="group"){
	$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']!=""?$_GET['gamesId']:die(returnAjaxData(0,"Lack Parameter"));
	$sex=isset($_GET['sex'])&&$_GET['sex']!=""?$_GET['sex']:die(returnAjaxData(0,"Lack Parameter"));
	$groupName=isset($_GET['groupName'])&&$_GET['groupName']!=""?$_GET['groupName']:die(returnAjaxData(0,"Lack Parameter"));
	$name=isset($_GET['name'])&&$_GET['name']!=""?$_GET['name']:die(returnAjaxData(0,"Lack Parameter"));
	$itemSql='SELECT id,sex,group_name,name AS item_name,is_allround,scene,order_index FROM item WHERE games_id=? AND is_delete=0 AND sex=? AND group_name=? AND name=?';
	$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId,$sex,$groupName,$name],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR]);

	if($itemQuery[1]==1){
		$itemId=$itemQuery[0][0]['id'];
	}else{
		die(returnAjaxData(1,"No item info"));
	}
}

// 查询成绩数据
$scoreSql="SELECT a.rank,a.run_group,a.runway,a.name,a.score,a.point,a.allround_point,a.remark,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id ORDER BY a.remark,LENGTH(a.score),a.rank,a.score,a.run_group,a.runway";
$scoreQuery=PDOQuery($dbcon,$scoreSql,[$itemId],[PDO::PARAM_INT]);

// 返回数据
die(returnAjaxData(200,"success",['scoreData'=>$scoreQuery[0]]));
