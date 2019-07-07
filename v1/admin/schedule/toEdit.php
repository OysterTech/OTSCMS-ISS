<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-26
 * @update 2018-08-26
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$type=$_POST['type']!=""?$_POST['type']:die(returnAjaxData(2,"lackType"));
	
	switch($type){
		case "edit":
			$id=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(1,"lackParam"));
			$order=$_POST['order']!=""?$_POST['order']:die(returnAjaxData(1,"lackParam"));
			$sex=$_POST['sex']!=""?$_POST['sex']:die(returnAjaxData(1,"lackParam"));
			$groupName=$_POST['groupName']!=""?$_POST['groupName']:die(returnAjaxData(1,"lackParam"));
			$name=$_POST['name']!=""?$_POST['name']:die(returnAjaxData(1,"lackParam"));
			$totalGroup=$_POST['totalGroup']!=""?$_POST['totalGroup']:die(returnAjaxData(1,"lackParam"));
			$totalAth=$_POST['totalAth']!=""?$_POST['totalAth']:die(returnAjaxData(1,"lackParam"));
	
			$sql="UPDATE item SET order_index=?,sex=?,group_name=?,name=?,total_group=?,total_ath=? WHERE id=?";
			$query=PDOQuery($dbcon,$sql,[$order,$sex,$groupName,$name,$totalGroup,$totalAth,$id],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
			break;
		case "add":
			$gamesId=$_POST['gamesId']!=""?$_POST['gamesId']:die(returnAjaxData(1,"lackParam"));
			$sceneId=$_POST['sceneId']!=""?$_POST['sceneId']:die(returnAjaxData(1,"lackParam"));
			$order=$_POST['order']!=""?$_POST['order']:die(returnAjaxData(1,"lackParam"));
			$sex=$_POST['sex']!=""?$_POST['sex']:die(returnAjaxData(1,"lackParam"));
			$groupName=$_POST['groupName']!=""?$_POST['groupName']:die(returnAjaxData(1,"lackParam"));
			$name=$_POST['name']!=""?$_POST['name']:die(returnAjaxData(1,"lackParam"));
			$totalGroup=$_POST['totalGroup']!=""?$_POST['totalGroup']:die(returnAjaxData(1,"lackParam"));
			$totalAth=$_POST['totalAth']!=""?$_POST['totalAth']:die(returnAjaxData(1,"lackParam"));
	
			$sql="INSERT INTO item(games_id,scene,order_index,sex,group_name,name,total_group,total_ath) VALUES (?,?,?,?,?,?,?,?)";
			$query=PDOQuery($dbcon,$sql,[$gamesId,$sceneId,$order,$sex,$groupName,$name,$totalGroup,$totalAth],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);
			break;
		case "del":
			$gamesId=$_POST['gamesId']!=""?$_POST['gamesId']:die(returnAjaxData(1,"lackParam"));
			$itemId=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(1,"lackParam"));

			$sql="UPDATE item SET is_delete=1 WHERE games_id=? AND id=?";
			$query=PDOQuery($dbcon,$sql,[$gamesId,$itemId],[PDO::PARAM_INT,PDO::PARAM_INT]);
			break;
		default:
			die(returnAjaxData(2,"invaildType"));
	}

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
