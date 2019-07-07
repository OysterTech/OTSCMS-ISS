<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台导入团队
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-09-17
 * @update 2018-09-17
 */

require_once '../../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
	$extraJson=json_decode($gamesInfo[0][0]['extra_json'],TRUE);
}

require_once '../../plugin/PHPExcel/PHPExcel.php';
require_once '../../plugin/PHPExcel/PHPExcel/IOFactory.php';
require_once '../../plugin/PHPExcel/PHPExcel/Reader/Excel5.php';

if(isset($_FILES) && $_FILES){
	// 先检查是否已存在日程
	$oldSql="SELECT COUNT(*) FROM item WHERE games_id=?";
	$oldQuery=PDOQuery($dbcon,$oldSql,[$gamesId],[PDO::PARAM_INT]);

	if($oldQuery[0][0]['COUNT(*)']!=0){
		// 已二次确认需要覆盖日程
		if(isset($_SESSION['sport_admin_hasSchedule'])){
			$truncateSql="DELETE FROM item WHERE games_id=?";
			$truncateQuery=PDOQuery($dbcon,$truncateSql,[$gamesId],[PDO::PARAM_INT]);
			unset($_SESSION['sport_admin_hasSchedule']);
		}else{
			// 提示用户二次确认覆盖
			$_SESSION['sport_admin_hasSchedule']=1;
			die('<script>alert("当前比赛已导入日程，是否需要覆盖旧日程？\n\n★ 如不覆盖，点确认且勿重新提交即可\n\n★ 如确认覆盖，请重新提交上传即可！");window.location.href="'.$url.'";</script>');
		}
	}

	$dir="../../filebox/".$gamesId."/";

	foreach($_FILES['myfile']["error"] as $key => $error){
		if($error == UPLOAD_ERR_OK){
			$MIME=$_FILES['myfile']['type'][$key];
			$Extension=getFileExtension($MIME);
			$name=date("YmdH")."-".mt_rand(123,987)."-日程.".$Extension;
			$tmp_name=$_FILES["myfile"]["tmp_name"][$key];

			if(file_exists($dir.$name)){
				$ret=returnAjaxData(1,"fileExists");
				die($ret);
			}else{
				move_uploaded_file($tmp_name,$dir.$name);
			}
		}elseif($_FILES["myfile"]["error"][$key]!="4"){
			die("<font color='red' class='tips'>Error Code： ".$_FILES["file"]["error"][$key]."</font><br>");
		}else{
			die("错误内容：".var_dump($_FILES["file"]["error"]));
		}
	}

	$objReader=PHPExcel_IOFactory::createReader('Excel2007');
	if(!$objReader->canRead($dir.$name)){
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
	}
	$objPHPExcel=$objReader->load($dir.$name);
	$Sheet=$objPHPExcel->getSheet(0);

	// 取得总行数
	$HighestRow=$Sheet->getHighestRow();
	// 成功导入数量
	$successRows=0;
	// SQL插入基础语句
	$sql="INSERT INTO item(games_id,scene,order_index,sex,group_name,name,total_group,total_ath) VALUES ";
	// 所有场次信息
	$sceneInfo=array();
	
	// 循环读取Excel文件
	for($i=2;$i<=$HighestRow;$i++){
		// 获取单元格内容
		$scene=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
		$orderIndex=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
		$sex=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
		$groupName=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
		$name=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue().$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
		$totalAth=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
		$totalGroup=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();

		$sql.='('.$gamesId.','.$scene.','.$orderIndex.',"'.$sex.'","'.$groupName.'","'.$name.'",'.$totalGroup.','.$totalAth.'),';
		
		if(!isset($sceneInfo[$scene])){
			$sceneInfo[$scene]="";
		}
	}
	
	$sql=substr($sql,0,-1);
	$query=PDOQuery($dbcon,$sql);
	$successRows=$query[1];

	if($HighestRow-1==$successRows){
		// 更新比赛场次数据
		$extraJson['scene']=$sceneInfo;
		$extraJson=json_encode($extraJson);
		
		$sql2="UPDATE games SET extra_json=? WHERE id=?";
		$query2=PDOQuery($dbcon,$sql2,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
		
		if($query2[1]==1){		
			die('<script>alert("导入日程成功！\n\n已导入条数：'.$successRows.' 条");window.location.href="'.$url.'";</script>');
		}else{
			die('<script>alert("导入日程成功！\n新增比赛场次数据失败！\n\n已导入条数：'.$successRows.' 条");window.location.href="'.$url.'";</script>');		
		}
	}else{
		die('<script>alert("导入日程失败！！！\n\n表格总条数：'.($HighestRow-1).' 条\n已导入条数：'.$successRows.' 条");window.location.href="'.$url.'";</script>');
	}
}


function getFileExtension($MIME){
	$MIME_XLS="application/vnd.ms-excel";
	$MIME_XLSX="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";

	if($MIME==$MIME_XLS){
		return "xls";
	}else if($MIME==$MIME_XLSX){
		return "xlsx";
	}else{
		return "";
	}
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../../include/header.php'; ?>
	<style>
	.tips{font-size:22;font-weight:bolder;}
	</style>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:blue;">导 入 日 程</h3>
<p style="line-height:8px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post" enctype="multipart/form-data">	
	<!-- 文件上传框 -->
	<center>
	<div class="input-group" style="width:98%">
		<span class="input-group-addon">上传Excel文件</span>
		<input type="file" name="myfile[]" id="myfile" class="form-control" style="height:40px">
	</div>
	</center>
	<!-- ./文件上传框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:48%" href="<?=ROOT_PATH.'admin/schedule.php?gamesId='.$gamesId;?>">< 返 回</a> <input class="btn btn-success" style="width:48%" type="submit" value="上 传 文 件 并 导 入 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php include '../../include/footer.php'; ?>

</body>
</html>