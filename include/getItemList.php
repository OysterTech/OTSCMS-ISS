<?php
/**
 * @name 生蚝体育比赛管理系统-Web-API获取项目
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-09-10
 * @update 2018-09-10
 */

require_once 'public.func.php';

$type=isset($_GET['type'])&&$_GET['type']!=""?$_GET['type']:die(returnAjaxData(0,"lackParam"));
$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']!=""?$_GET['gamesId']:die(returnAjaxData(0,"lackParam"));

$itemSql="SELECT id,group_name,name,scene,order_index FROM item WHERE games_id=? AND is_delete=0 ";

// 根据搜索条件获取条件数据并拼接SQL语句
if($type=="item"){
	$scene=isset($_GET['scene'])&&$_GET['scene']!=""?$_GET['scene']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND scene=$scene";
}elseif($type=="group"){
	$sex=isset($_GET['sex'])&&$_GET['sex']!=""?$_GET['sex']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND sex='{$sex}'";
}elseif($type=="group2"){
	$sex=isset($_GET['sex'])&&$_GET['sex']!=""?$_GET['sex']:die(returnAjaxData(0,"lackParam"));
	$groupName=isset($_GET['groupName'])&&$_GET['groupName']!=""?$_GET['groupName']:die(returnAjaxData(0,"lackParam"));
	$itemSql.="AND sex='{$sex}' AND group_name='{$groupName}'";
}

// 查询项目数据
$itemQuery=PDOQuery($dbcon,$itemSql,[$gamesId],[PDO::PARAM_INT]);
if($itemQuery[1]>=1){
	die(returnAjaxData(200,"success",['itemData'=>$itemQuery[0]]));
}else{
	die(returnAjaxData(1,"noItem",[$itemSql]));
}
