<?php
/**
 * @name 生蚝体育比赛管理系统-Web-处理查询分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-29
 * @version 2019-07-05
 */

require_once '../include/public.func.php';

$orderBy=inputGet('orderBy',0,1);
$gamesId=inputGet('gamesId',0,1);
$itemSql="SELECT id,sex,group_name,name AS item_name,scene,order_index FROM item WHERE games_id=? AND is_delete=0 ";

// 根据搜索条件获取条件数据并拼接SQL语句
if($orderBy=="item"){
	$itemId=inputGet('itemId',0,1);
	$itemSql.="AND id={$itemId}";
}elseif($orderBy=="group"){
	$sex=inputGet('sex',0,1);
	$groupName=inputGet('groupName',0,1);
	$name=inputGet('name',0,1);
	//$isFinal=isset($_GET['isFinal'])&&$_GET['isFinal']!=""?$_GET['isFinal']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND sex='{$sex}' AND group_name='{$groupName}' AND name='{$name}'";
}

// 查询项目数据
$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId],[PDO::PARAM_INT]);
if($itemQuery[1]==1){
	$itemId=$itemQuery[0][0]['id'];
	$itemName=$itemQuery[0][0]['sex'].$itemQuery[0][0]['group_name'].$itemQuery[0][0]['item_name'];
}else{
	returnAjaxData(1,"Item not found");
}

// 查询分组数据
$orderSql="SELECT a.run_group,a.runway,a.name,a.remark,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id ORDER BY a.run_group,a.runway";
$orderQuery=PDOQuery($dbcon,$orderSql,[$itemId],[PDO::PARAM_INT]);

// 返回数据
returnAjaxData(200,"success",['itemName'=>$itemName,'scene'=>$itemQuery[0][0]['scene'],'orderIndex'=>$itemQuery[0][0]['order_index'],'orderData'=>$orderQuery[0]]);
