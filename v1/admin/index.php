<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-16
 * @update 2018-08-16
 */
	
require_once '../include/public.func.php';
checkLogin();

header("location:".ROOT_PATH."admin/gamesList.php");
?>
