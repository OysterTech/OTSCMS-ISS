<?php
/**
 * @name 生蚝体育比赛管理系统-Web-新增团体
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-25
 * @update 2018-08-25
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$type=$_POST['type'];
	
	switch($type){
		// 编辑团体信息
		case "edit":
			$id=$_POST['id'];
			$name=$_POST['name'];
			$shortName=$_POST['shortName'];

			if($id=="" || $name=="" || $shortName==""){
				die(returnAjaxData(1,"lackParam"));
			}

			$sql="UPDATE team SET name=?,short_name=? WHERE id=?";
			$query=PDOQuery($dbcon,$sql,[$name,$shortName,$id],[PDO::PARAM_STR,PDO::PARAM_STR,PDO::PARAM_INT]);
			break;
			
		// 新增团体
		case "add":
			$gamesId=$_POST['gamesId'];
			$name=$_POST['name'];
			$shortName=$_POST['shortName'];

			if($gamesId=="" || $name=="" || $shortName==""){
				die(returnAjaxData(1,"lackParam"));
			}

			$sql="INSERT INTO team(games_id,name,short_name) VALUES (?,?,?)";
			$query=PDOQuery($dbcon,$sql,[$gamesId,$name,$shortName],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_STR]);
			break;
		// 删除团体
		case "del":
			$gamesId=$_POST['gamesId'];
			$id=$_POST['id'];

			if($gamesId=="" || $id==""){
				die(returnAjaxData(1,"lackParam"));
			}
			
			// 删除团体资料
			$sql="DELETE FROM team WHERE id=? AND games_id=?";
			$query=PDOQuery($dbcon,$sql,[$id,$gamesId],[PDO::PARAM_INT,PDO::PARAM_INT]);
			// 删除旗下的运动员资料
			$sql2="DELETE FROM score WHERE team_id=?";
			$query2=PDOQuery($dbcon,$sql2,[$id],[PDO::PARAM_INT]);
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
