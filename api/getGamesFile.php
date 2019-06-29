<?php
/**
 * @name 生蚝体育竞赛管理系统-API-获取赛事文件
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-06-03
 * @version 2019-06-03
 */

require_once '../include/public.func.php';

$gamesId=inputGet('id',0,1);

$query=PDOQuery($dbcon,'SELECT extra_json FROM games WHERE id=?',[$gamesId],[PDO::PARAM_INT]);
if($query[1]!=1) returnAjaxData(404,'Games not found');

$param=json_decode($query[0][0]['extra_json'],true);
$file=$param['file'];

returnAjaxData(200,'success',$file);
