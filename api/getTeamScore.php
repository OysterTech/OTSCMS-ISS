<?php
/**
 * @name 生蚝体育比赛管理系统-API-获取团体分
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-07-19
 * @version 2019-07-19
 */

require_once '../include/public.func.php';

$gamesId=inputGet('id',0,1);
$groupBy=inputGet('groupBy',1,1);
$orderBy=inputGet('orderBy',1,1);

if($groupBy=='sex'){
	$sql='SELECT SUM(c.allround_point) AS totalAllroundPoint,SUM(c.point) AS totalPoint,b.sex,a.name FROM team a,item b,score c WHERE c.item_id=b.id AND c.team_id=a.id AND b.games_id=? GROUP BY b.sex,a.name ORDER BY b.sex';
}elseif($groupBy=='groupName'){
	$sql='SELECT SUM(c.allround_point) AS totalAllroundPoint,SUM(c.point) AS totalPoint,b.group_name AS groupName,a.name FROM team a,item b,score c WHERE c.item_id=b.id AND c.team_id=a.id AND b.games_id=? GROUP BY b.group_name,a.name ORDER BY b.group_name';
}elseif($groupBy=='total'){
	$sql='SELECT SUM(c.allround_point) AS totalAllroundPoint,SUM(c.point) AS totalPoint,a.name FROM team a,item b,score c WHERE c.item_id=b.id AND c.team_id=a.id AND b.games_id=? GROUP BY a.name ORDER BY ';
}elseif($groupBy=='sexGroup'){
	$sql='SELECT SUM(c.allround_point) AS totalAllroundPoint,SUM(c.point) AS totalPoint,b.sex,b.group_name AS groupName,a.name FROM team a,item b,score c WHERE c.item_id=b.id AND c.team_id=a.id AND b.games_id=? GROUP BY b.sex,b.group_name,a.name ORDER BY b.sex,b.group_name';
}else{
	returnAjaxData(4001,'Invaild groupby');
}

if($groupBy=='total') $sql.=($orderBy=='allroundPoint')?'totalAllroundPoint DESC,totalPoint DESC':'totalPoint DESC,totalAllroundPoint DESC';
else $sql.=($orderBy=='allroundPoint')?',totalAllroundPoint DESC,totalPoint DESC':',totalPoint DESC,totalAllroundPoint DESC';

$query=PDOQuery($dbcon,$sql,[$gamesId]);

returnAjaxData(200,'success',[$sql,'data'=>$query[0]]);
