<?php
/**
 * @name 生蚝体育比赛管理系统-Web-点赞
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-14
 * @update 2018-08-14
 */
	
require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$query1=PDOQuery($dbcon,"UPDATE games SET praise=praise+1 WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$query2=PDOQuery($dbcon,"SELECT praise FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

die($query2[0][0]['praise']);
