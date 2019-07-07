<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改秩序册
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-23
 * @update 2018-08-27
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$type=$_POST['type']!=""?$_POST['type']:die(returnAjaxData(1,"lackType"));
	
	switch($type){
		case "edit":
			$id=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(2,"lackParam"));
			$runGroup=$_POST['runGroup']!=""?$_POST['runGroup']:die(returnAjaxData(2,"lackParam"));
			$runway=$_POST['runway']!=""?$_POST['runway']:die(returnAjaxData(2,"lackParam"));
			$name=$_POST['name']!=""?$_POST['name']:die(returnAjaxData(2,"lackParam"));
			$team=$_POST['team']!=""?$_POST['team']:die(returnAjaxData(2,"lackParam"));

			$sql="UPDATE score SET run_group=?,runway=?,name=?,team_id=? WHERE id=?";
			$query=PDOQuery($dbcon,$sql,[$runGroup,$runway,$name,$team,$id],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);
			break;
		case "del":
			$id=$_POST['id']!=""?$_POST['id']:die(returnAjaxData(2,"lackParam"));
			$sql="DELETE FROM score WHERE id=?";
			$query=PDOQuery($dbcon,$sql,[$id],[PDO::PARAM_INT]);
			break;
		case "add":
			$itemId=$_POST['itemId']!=""?$_POST['itemId']:die(returnAjaxData(2,"lackParam"));
			$runGroup=$_POST['runGroup']!=""?$_POST['runGroup']:die(returnAjaxData(2,"lackParam"));
			$runway=$_POST['runway']!=""?$_POST['runway']:die(returnAjaxData(2,"lackParam"));
			$name=$_POST['name']!=""?$_POST['name']:die(returnAjaxData(2,"lackParam"));
			$teamId=$_POST['teamId']!=""?$_POST['teamId']:die(returnAjaxData(2,"lackParam"));
	
			$sql="INSERT INTO score(item_id,run_group,runway,name,team_id) VALUES (?,?,?,?,?)";
			$query=PDOQuery($dbcon,$sql,[$itemId,$runGroup,$runway,$name,$teamId],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT]);
			break;
		default:
			die(returnAjaxData(3,"invaildType"));
	}
	

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
