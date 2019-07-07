<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台删除资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-20
 * @update 2018-08-21
 */

require_once '../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

if($gamesInfo[1]!=1){
	goToIndex("admin");
}

if(!@unlink(urldecode($_GET['fileUrl']))){
	echo "<script>alert('删除失败！');window.location.href='file.php?gamesId=".$gamesId."';</script>";
}else{
	$extraJson=json_decode($gamesInfo[0][0]['extra_json'],true);
	$fileList=$extraJson['file'];
	unset($fileList[$_GET['key']]);
	$fileJson=json_encode($fileList);
	$query=PDOQuery($dbcon,"UPDATE games SET file_json=? WHERE id=?",[$fileJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
	
	if($query[1]==1){
		echo "<script>alert('删除成功！');window.location.href='file.php?gamesId=".$gamesId."';</script>";
	}else{
		echo "<script>alert('删除成功！更新数据库失败！');window.location.href='file.php?gamesId=".$gamesId."';</script>";
	}
}

?>
