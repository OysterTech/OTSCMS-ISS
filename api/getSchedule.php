<?php
/**
 * @name 生蚝体育比赛管理系统-API-获取日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-27
 * @version 2019-06-28
 */

require_once '../include/public.func.php';

$gamesId=inputGet('id',0,1);
$rtn=[];

$query=PDOQuery($dbcon,'SELECT scene,order_index,kind,sex,group_name,name,total_group,total_ath,is_allround,is_final FROM item WHERE games_id=? AND is_delete=0 ORDER BY scene,kind,order_index',[$gamesId]);
$list=$query[0];

foreach($list as $info){
	$rtn[$info['scene']][$info['order_index']]=$info;
}

returnAjaxData(200,'success',['list'=>$rtn]);
