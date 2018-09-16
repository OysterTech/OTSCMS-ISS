<?php
/**
 * @name 生蚝体育比赛管理系统-Web-导入成绩处理
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-08-31
 */

require_once '../../include/public.func.php';
checkLogin();

require_once '../../plugin/PHPExcel/PHPExcel.php';
require_once '../../plugin/PHPExcel/PHPExcel/IOFactory.php';
require_once '../../plugin/PHPExcel/PHPExcel/Reader/Excel5.php';

if(isset($_POST) && $_POST){
	$gamesId=$_SESSION['swim_admin_gamesId'];
	$postScene=$_POST['scene'];
	$postOrderIndex=$_POST['orderIndex'];
	$dir="../../filebox/".$gamesId."/";

	$sceneInfo=PDOQuery($dbcon,'SELECT id,sex,group_name,name,scene,order_index FROM item WHERE scene=? AND order_index=? AND games_id=? AND is_delete=0',[$postScene,$postOrderIndex,$gamesId],[PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT]);
	if($sceneInfo[1]==1){
		$sceneInfo=$sceneInfo[0][0];
		$itemId=$sceneInfo['id'];
		$itemName=$sceneInfo['sex'].$sceneInfo['group_name'].$sceneInfo['name'];
	}else{
		$ret=returnAjaxData(2,"noItem");
		die($ret);
	}

	foreach($_FILES['myfile']["error"] as $key => $error){
		if($error == UPLOAD_ERR_OK){
			$MIME=$_FILES['myfile']['type'][$key];
			$Extension=getFileExtension($MIME);
			$name=date("YmdH")."-".mt_rand(123,987)."-Schedule.".$Extension;
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
	$objPHPExcel=$objReader->load($dir.$name,$encode='utf-8');
	$Sheet=$objPHPExcel->getSheet(0);

  	// 取得总行数
	$HighestRow=$Sheet->getHighestRow();
  	// 成功导入数量
	$successRows=0;
	// 提示需要标记的行数
	$tipsRemarkRows=0;
	$tipsRemarkNames=array();

  	// 循环读取Excel文件
	for($i=2;$i<=$HighestRow;$i++){
		$sql="UPDATE score SET rank=?,score=?,point=?,allround_point=? WHERE item_id=? AND name=?";

    	// 获取单元格内容
		$rank=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
		$name=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
		$score=$objPHPExcel->getActiveSheet()->getCell("AC".$i)->getValue();
		$point=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
		$allroundPoint=$objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue();

		if($score=="" && $rank==0){
			$tipsRemarkRows++;
			array_push($tipsRemarkNames,$name);
		}

		// 修改成绩
		$query=PDOQuery($dbcon,$sql,[$rank,$score,$point,$allroundPoint,$itemId,$name],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_STR]);
		if($query[1]==1){
			$successRows++;
		}
	}

	$tipsRemarkNames=implode(" | ",$tipsRemarkNames);

	if($HighestRow-1==$successRows){
		$ret=returnAjaxData(200,"success",['itemName'=>$itemName,'rows'=>$successRows,'tipsRows'=>$tipsRemarkRows,'tipsNames'=>$tipsRemarkNames]);
		die($ret);
	}else{
		$ret=returnAjaxData(0,"failed",['itemName'=>$itemName,'total'=>($HighestRow-1),'rows'=>$successRows,'tipsRows'=>$tipsRemarkRows,'tipsNames'=>$tipsRemarkNames]);
		die($ret);
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