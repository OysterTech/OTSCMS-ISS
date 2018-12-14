<?php
/**
 * @name 生蚝体育比赛管理系统-Web-泳协-优秀运动员统计-统计
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-12-07
 * @update 2018-12-14
 */
	
require_once '../../include/public.func.php';

$year=isset($_GET['year'])&&$_GET['year']!=""?$_GET['year']:die(returnAjaxData(0,"lackParam"));
$condition=isset($_GET['condition'])&&$_GET['condition']!=""?$_GET['condition']:die(returnAjaxData(1,"lackParam"));
$totalPoint=array();
$totalGroup=array("A","B","C","D","E","F");
$totalLevel=array("7532"=>"甲","4e59"=>"乙");

if($condition=="y"){
	// 需要分甲乙
	
	foreach($totalLevel as $utf=>$level){
		$allAthSql="SELECT DISTINCT(a.name) AS ath_name,b.sex,b.group_name,c.name AS team_name,c.short_name FROM score a,item b,team c,games d WHERE a.item_id=b.id AND a.team_id=c.id AND b.games_id=d.id AND d.extra_json LIKE '%".'"year":'.$year.',"group":"%'.$utf.'"'."%';";
		$allAthQuery=PDOQuery($dbcon,$allAthSql);

		if($allAthQuery[1]<1){
			die(returnAjaxData(2,"noAthlete"));
		}else{
			$allAthInfo=$allAthQuery[0];
		}
		
		// 处理每个运动员的成绩
		foreach($allAthInfo as $athInfo){
			$name=$athInfo['ath_name'];
			$sex=$athInfo['sex'];
			$groupName=$athInfo['group_name'];
			$teamName=$athInfo['team_name'];
		
			$scoreSql="SELECT SUM(a.allround_point) AS total_point FROM score a,item b WHERE a.name='".$name."' AND a.item_id=b.id AND b.sex='".$sex."' AND b.group_name LIKE '%".$groupName."%';";
			$scoreQuery=PDOQuery($dbcon,$scoreSql);
			
			$totalPoint[$level][$sex][$groupName][$name."|".$teamName]=(int)$scoreQuery[0][0]['total_point'];
		}
		
		// 各组成绩排序
		foreach($totalGroup as $group){
			arsort($totalPoint[$level]["男子"][$group."组"]);
			arsort($totalPoint[$level]["女子"][$group."组"]);
			$totalPoint[$level]["男子"][$group."组"]=array_filter($totalPoint[$level]["男子"][$group."组"]);
			$totalPoint[$level]["女子"][$group."组"]=array_filter($totalPoint[$level]["女子"][$group."组"]);
		}
	}
}elseif($condition=="n"){
	// 不需要分甲乙
	$allAthSql="SELECT DISTINCT(a.name) AS ath_name,b.sex,b.group_name,c.name AS team_name,c.short_name FROM score a,item b,team c,games d WHERE a.item_id=b.id AND a.team_id=c.id AND b.games_id=d.id AND d.extra_json LIKE '%".'"year":'.$year."%';";
	$allAthQuery=PDOQuery($dbcon,$allAthSql);
	
	if($allAthQuery[1]<1){
		die(returnAjaxData(2,"noAthlete"));
	}else{
		$allAthInfo=$allAthQuery[0];
	}
	
	// 处理每个运动员的成绩
	foreach($allAthInfo as $athInfo){
		$name=$athInfo['ath_name'];
		$sex=$athInfo['sex'];
		$groupName=$athInfo['group_name'];
		$teamName=$athInfo['team_name'];
		if(strlen($groupName)==10) $groupName=substr($groupName,-4);// 为避免如:甲级A组这种名称
		
		$scoreSql="SELECT SUM(a.allround_point) AS total_point FROM score a,item b WHERE a.name='".$name."' AND a.item_id=b.id AND b.sex='".$sex."' AND b.group_name LIKE '%".$groupName."%';";
		$scoreQuery=PDOQuery($dbcon,$scoreSql);
		
		$totalPoint[$sex][$groupName][$name."|".$teamName]=(int)$scoreQuery[0][0]['total_point'];
	}

	// 各组成绩排序
	foreach($totalGroup as $group){
		arsort($totalPoint["男子"][$group."组"]);
		arsort($totalPoint["女子"][$group."组"]);
		$totalPoint["男子"][$group."组"]=array_filter($totalPoint["男子"][$group."组"]);
		$totalPoint["女子"][$group."组"]=array_filter($totalPoint["女子"][$group."组"]);
	}
}else{
	die(returnAjaxData(1,"invaildCondition"));
}

$json=json_encode($totalPoint,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
die(returnAjaxData(200,"success",['data'=>$json]));
