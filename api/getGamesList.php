<?php
/**
 * @name 生蚝体育比赛管理系统-API-获取赛事列表
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-05-31
 * @version 2019-07-03
 */

require_once '../include/public.func.php';

$page=isset($_GET['page'])?$_GET['page']:1;
$rows=isset($_GET['rows'])?$_GET['rows']:10;

$query1=PDOQuery($dbcon,'SELECT COUNT(id) FROM games');
$total=$query1[0][0]['COUNT(id)'];
$query2=PDOQuery($dbcon,'SELECT id,name,extra_json,praise,start_date AS startDate,end_date AS endDate,venue,organizer FROM games WHERE is_show=1 ORDER BY start_date DESC LIMIT '.($page-1)*$rows.','.$rows);
$list=$query2[0];

returnAjaxData(200,'success',['totalRow'=>(int)$total,'totalPage'=>ceil($total/$rows),'list'=>$list]);
