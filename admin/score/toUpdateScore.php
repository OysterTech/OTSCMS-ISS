<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台处理修改成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-15
 * @update 2018-08-21
 */
	
require_once '../../include/public.func.php';

if(isset($_POST) && $_POST){
	$id=$_POST['id'];
	$rank=$_POST['rank']!=""?$_POST['rank']:null;
	$score=$_POST['score']!=""?$_POST['score']:null;
	$point=$_POST['point']!=""?$_POST['point']:null;
	$remark=$_POST['remark']!=""?$_POST['remark']:null;
	$sql="UPDATE score SET rank=?,score=?,point=?,remark=? WHERE id=?";
	$query=PDOQuery($dbcon,$sql,[$rank,$score,$point,$remark,$id],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT]);

	if($query[1]==1){
		die(returnAjaxData(200,"success"));
	}else{
		die(returnAjaxData(0,"failed"));
	}
}
