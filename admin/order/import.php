<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台导入秩序册
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-21
 * @update 2018-08-31
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
}

require_once '../../plugin/PHPExcel/PHPExcel.php';
require_once '../../plugin/PHPExcel/PHPExcel/IOFactory.php';
require_once '../../plugin/PHPExcel/PHPExcel/Reader/Excel5.php';

if(isset($_FILES) && $_FILES){
	$dir="../../filebox/".$gamesId."/";

	foreach($_FILES['myfile']["error"] as $key => $error){
		if($error == UPLOAD_ERR_OK){
			$MIME=$_FILES['myfile']['type'][$key];
			$Extension=getFileExtension($MIME);
			$name=date("YmdH")."-".mt_rand(123,987)."-秩序册.".$Extension;
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
	$totalRow=$HighestRow;
  	// 成功导入数量
	$successRows=0;
	// SQL插入基础语句
	$sql="INSERT INTO score(item_id,run_group,runway,name,team_id) VALUES ";

  	// 循环读取Excel文件
	for($i=2;$i<=$HighestRow;$i++){
	 	// 获取单元格内容
		$scene=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
		$orderIndex=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
		$runGroup=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
		$runway=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
		$name=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
		$teamShortName=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();

		if($teamShortName=="" && $name==""){
			$totalRow--;
			continue;
		}elseif($teamShortName!="" && $name==""){
			$name=$teamShortName;
		}

		$sql.='((SELECT id FROM item WHERE games_id='.$gamesId.' AND scene='.$scene.' AND order_index='.$orderIndex.'),'.$runGroup.','.$runway.',"'.$name.'",(SELECT id FROM team WHERE games_id='.$gamesId.' AND short_name="'.$teamShortName.'")),';
	}
	
	$sql=substr($sql,0,-1);
	$query=PDOQuery($dbcon,$sql);
	$successRows=$query[1];

	if($totalRow-1==$successRows){
		die('<script>alert("导入秩序册成功！\n\n已导入条数：'.$successRows.' 条");window.location.href="'.$url.'";</script>');
	}else{
		die('<script>alert("导入秩序册失败！！！\n请检查是否未导入团体！\n\n表格总条数：'.($totalRow-1).' 条\n已导入条数：'.$successRows.' 条");window.location.href="'.$url.'";</script>');

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

<h3 style="font-weight:bold;text-align:center;color:green;">导 入 秩 序 册</h3>
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
		<a class="btn btn-primary" style="width:48%" href="<?=ROOT_PATH.'admin/order.php?gamesId='.$gamesId;?>">< 返 回</a> <input class="btn btn-success" style="width:48%" type="submit" value="上 传 文 件 并 导 入 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php include '../../include/footer.php'; ?>

</body>
</html>