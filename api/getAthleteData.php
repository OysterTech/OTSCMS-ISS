<?php
/**
 * @name 生蚝体育比赛管理系统-Web-处理运动员查询数据
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-07-03
 * @version 2019-07-06
 */

require_once '../include/public.func.php';

$type=inputGet('type',0,1);
$name=inputGet('name',0,1);

if($type=='order'){
	$gamesId=inputGet('gamesId',0,1);
	$query=PDOQuery($dbcon,'SELECT a.run_group,a.runway,b.scene,b.order_index,b.scene,b.order_index,b.sex,b.group_name,b.name AS item_name,c.short_name FROM score a,item b,team c WHERE a.name=? AND a.item_id=b.id AND a.team_id=c.id AND b.games_id=?',[$name,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
	
	if($query[1]>=1) returnAjaxData(200,'success',['list'=>$query[0]]);
	else returnAjaxData(404,'Data not found');
}elseif($type=='score'){
	$allGames=inputGet('allGames',1,1);
	
	if($allGames=='true'){
		$scoreQuery=PDOQuery($dbcon,'SELECT a.name,a.score,a.rank,a.remark,b.sex,b.group_name,b.name AS item_name,c.name AS games_name,d.short_name FROM score a,item b,games c,team d WHERE a.name LIKE ? AND a.item_id=b.id AND b.games_id=c.id AND a.team_id=d.id ORDER BY c.start_date,a.rank,b.name',['%'.$name.'%'],[PDO::PARAM_STR]);
		
		$totalScore=$scoreQuery[1];
		$scoreInfo=$scoreQuery[0];
		$total=1;$nowGamesKey=0;
		
		// 给各比赛写入总成绩条数，以便合并单元格
		for($i=0;$i<count($scoreQuery[0]);$i++){
			// 如果下个成绩条还是当前比赛
			if(isset($scoreInfo[$i+1]['games_name']) && $i+1<=$totalScore && $scoreInfo[$i+1]['games_name']==$scoreInfo[$i]['games_name']){
				$total++;
			}else{
				$scoreQuery[0][$nowGamesKey]['total']=$total;
				$total=1;
				$nowGamesKey=$i+1;
			}
		}
	}else{
		$gamesId=inputGet('gamesId',0,1);
		$scoreQuery=PDOQuery($dbcon,'SELECT a.name,a.score,a.rank,a.remark,b.sex,b.group_name,b.name AS item_name,c.short_name FROM score a,item b,team c WHERE a.name LIKE ? AND a.item_id=b.id AND a.team_id=c.id AND b.games_id=? ORDER BY b.scene,b.order_index',['%'.$name.'%',$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
	}
	
	returnAjaxData(200,'success',['list'=>$scoreQuery[0]]);
}else{
	returnAjaxData(400,'Invaild Type');
}
