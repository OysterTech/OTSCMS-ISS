<?php
/**
 * @name 生蚝体育比赛管理系统-Web-处理查询分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-28
 * @update 2018-09-22
 */

require_once '../include/public.func.php';

$orderBy=isset($_GET['orderBy'])&&$_GET['orderBy']!=""?$_GET['orderBy']:die(returnAjaxData(0,"lackParam"));
$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']!=""?$_GET['gamesId']:die(returnAjaxData(0,"lackParam"));
$itemSql="SELECT id,sex,group_name,name AS item_name,scene,order_index FROM item WHERE games_id=? AND is_delete=0 ";

// 根据搜索条件获取条件数据并拼接SQL语句
if($orderBy=="item"){
	$scene=isset($_GET['scene'])&&$_GET['scene']!=""?$_GET['scene']:die(returnAjaxData(0,"lackParam"));
	$orderIndex=isset($_GET['orderIndex'])&&$_GET['orderIndex']!=""?$_GET['orderIndex']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND scene='{$scene}' AND order_index='{$orderIndex}'";
}elseif($orderBy=="group"){
	$sex=isset($_GET['sex'])&&$_GET['sex']!=""?$_GET['sex']:die(returnAjaxData(0,"lackParam"));
	$groupName=isset($_GET['groupName'])&&$_GET['groupName']!=""?$_GET['groupName']:die(returnAjaxData(0,"lackParam"));
	$name=isset($_GET['name'])&&$_GET['name']!=""?$_GET['name']:die(returnAjaxData(0,"lackParam"));
	$isFinal=isset($_GET['isFinal'])&&$_GET['isFinal']!=""?$_GET['isFinal']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND sex='{$sex}' AND group_name='{$groupName}' AND name='{$name}' AND is_final='{$isFinal}'";
}

// 查询项目数据
$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId],[PDO::PARAM_INT]);
if($itemQuery[1]==1){
	$itemId=$itemQuery[0][0]['id'];
	$itemName=$itemQuery[0][0]['sex'].$itemQuery[0][0]['group_name'].$itemQuery[0][0]['item_name'];
}else{
	die(returnAjaxData(1,"noItem"));
}

// 查询分组数据
$orderSql="SELECT a.run_group,a.runway,a.name,a.remark,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id ORDER BY a.run_group,a.runway";
$orderQuery=PDOQuery($dbcon,$orderSql,[$itemId],[PDO::PARAM_INT]);

// 返回数据
die(returnAjaxData(200,"success",['itemName'=>$itemName,'scene'=>$itemQuery[0][0]['scene'],'orderIndex'=>$itemQuery[0][0]['order_index'],'orderData'=>$orderQuery[0]]));
