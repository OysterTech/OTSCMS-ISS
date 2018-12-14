<?php
/**
 * @name 生蚝体育比赛管理系统-Web-泳协-优秀运动员统计-获取详情
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-12-12
 * @update 2018-12-14
 */
	
require_once '../../include/public.func.php';

$group=isset($_GET['group'])&&$_GET['group']!=""?$_GET['group']:die(returnAjaxData(0,"lackParam"));
$name=isset($_GET['name'])&&$_GET['name']!=""?$_GET['name']:die(returnAjaxData(1,"lackParam"));
$year=isset($_GET['year'])&&$_GET['year']>=2018?$_GET['year']:die(returnAjaxData(2,"lackParam"));

$sql="SELECT b.name,a.score,a.allround_point AS point,a.remark FROM score a,item b,games c WHERE a.item_id=b.id AND a.name=? AND b.group_name LIKE '%{$group}' AND b.games_id=c.id AND c.extra_json LIKE '%".'"year":'.$year."%' ORDER BY b.games_id,b.name";
$query=PDOQuery($dbcon,$sql,[$name]);

die(returnAjaxData(200,"success",['data'=>$query[0]]));
