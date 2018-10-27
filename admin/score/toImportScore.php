<?php
/**
 * @name 生蚝体育比赛管理系统-Web-导入成绩处理
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-10-13
 */

require_once '../../include/public.func.php';
checkLogin();

require_once '../../plugin/PHPExcel/PHPExcel.php';
require_once '../../plugin/PHPExcel/PHPExcel/IOFactory.php';
require_once '../../plugin/PHPExcel/PHPExcel/Reader/Excel5.php';

if(isset($_POST) && $_POST){

	$gamesId=$_SESSION['sport_admin_gamesId'];
	$gamesJson=$_SESSION['sport_admin_gamesJson'];
	$software=$gamesJson['software'];
	$kind=$gamesJson['kind'];
	
	if($kind=="田径"){
		$postGroupName=$_POST['groupName'];
		$postName=$_POST['name'];
		$postSex=$_POST['sex'];
		//$postIsFinal=$_POST['isFinal'];
		$itemSql="SELECT id,sex,group_name,name,scene,order_index FROM item WHERE sex='{$postSex}' AND group_name='{$postGroupName}' AND name='{$postName}' AND is_final=1 AND games_id=? AND is_delete=0";
	}else{
		$postScene=$_POST['scene'];
		$postOrderIndex=$_POST['orderIndex'];
		$itemSql="SELECT id,sex,group_name,name,scene,order_index FROM item WHERE scene={$postScene} AND order_index={$postOrderIndex} AND games_id=? AND is_delete=0";
	}
	
	// 获取项目信息
	$itemInfo=PDOQuery($dbcon,$itemSql,[$gamesId],[PDO::PARAM_INT]);
	if($itemInfo[1]==1){
		$itemInfo=$itemInfo[0][0];
		$itemId=$itemInfo['id'];
		$itemName=$itemInfo['sex'].$itemInfo['group_name'].$itemInfo['name'];
	}else{
		$ret=returnAjaxData(2,"noItem");
		die($ret);
	}

	$dir="../../filebox/".$gamesId."/";
	
	foreach($_FILES['myfile']["error"] as $key => $error){
		if($error == UPLOAD_ERR_OK){
			$MIME=$_FILES['myfile']['type'][$key];
			$extension=getFileExtension($MIME);
			$name=date("YmdH")."-".mt_rand(123,987)."-Score.".$extension;
			$tmp_name=$_FILES["myfile"]["tmp_name"][$key];

			if(file_exists($dir.$name)){
				$ret=returnAjaxData(1,"fileExists");
				die($ret);
			}else{
				move_uploaded_file($tmp_name,$dir.$name);
			}

		}elseif($_FILES["myfile"]["error"][$key]!="4"){
			$ret=returnAjaxData($_FILES["file"]["error"][$key],"unknownError");
			die($ret);
		}else{
			$ret=returnAjaxData(var_dump($_FILES["file"]["error"]),"unknownError");
			die($ret);
		}
	}

	// 成功导入数量
	$successRows=0;
	// 提示需要标记的行数
	$tipsRemarkRows=0;
	$tipsRemarkNames=array();
	$shouldSuccessRows=0;

	// 根据不同编排软件服务商来导入
	if($software=="mxx"){
		// 梅雪雄软件格式
		
		if($extension=="xls"){
			$objReader=PHPExcel_IOFactory::createReader('Excel5');
		}elseif($extension=="xlsx"){
			$objReader=PHPExcel_IOFactory::createReader('Excel2007');
		}else{
			$ret=returnAjaxData(3,"invaildExtension");
			die($ret);
		}

		$objPHPExcel=$objReader->load($dir.$name);
		$Sheet=$objPHPExcel->getSheet(0);

		// 取得总行数
		$HighestRow=$Sheet->getHighestRow();
		$shouldSuccessRows=$HighestRow-1;
		
		for($i=2;$i<=$HighestRow;$i++){
			$sql="UPDATE score SET rank=?,score=?,point=?,allround_point=? WHERE item_id=? AND name=?";

			// 获取单元格内容
			$rank=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
			$name=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
			$score=$objPHPExcel->getActiveSheet()->getCell("AC".$i)->getValue();
			$point=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
			$allroundPoint=$objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue();
			
			// 没成绩没排名，提醒加备注
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
	}elseif($software=="jbt"){
		// 江伯滔软件格式
		$file2=fopen($dir.$name,"r");
		$m=array();$i=0;
		while(!feof($file2)){
			$c=fgetcsv($file2);
			$c=explode(';',mb_convert_encoding($c[0],"UTF-8","GBK"));
			array_push($m,$c);
			$i++;
		}
		unset($m[$i-1],$m[0]);
		foreach($m as $key=>$info){
			foreach($info as $key2=>$info2){
				if($key2!=0) $info[$key2]=substr($info2,1,strlen($info2)-2);
			}
			$m[$key]=$info;
		}

		$shouldSuccessRows=count($m);
		
		foreach($m as $key=>$info){
			$sql="UPDATE score SET rank=?,score=?,point=?,remark=? WHERE item_id=? AND name=? AND run_group=? AND runway=?";

			// 获取单元格内容
			$rank=$info[0];
			$runGroup=$info[1];
			$runway=$info[2];
			$name=$info[3];
			$score=$info[5];
			$point=$info[6];
			$DSQ=$info[7];
			$DNS=$info[8];
			$DNF=$info[9];
			$TRI=$info[10];
			$FNL=$info[11];
			$GR=$info[12];
			
			// 判断运动员标签
			if($DSQ=="True"){
				$remark="DSQ";
			}elseif($DNS=="True"){
				$remark="DNS";
			}elseif($DNF=="True"){
				$remark="DNF";
			}elseif($TRI=="True"){
				$remark="TRI";
			}elseif($FNL=="True"){
				// (临时)暂不显示进决赛
				$remark="";
			}elseif($GR=="True"){
				$remark="GR";
			}else{
				$remark="";
			}

			if($rank=="") $rank=0;
			if($score==null) $score="";
			
			// 修改成绩
			$query=PDOQuery($dbcon,$sql,[$rank,$score,$point,$remark,$itemId,$name,$runGroup,$runway],[PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT,PDO::PARAM_STR,PDO::PARAM_INT,PDO::PARAM_INT]);
			if($query[1]==1){
				$successRows++;
			}
		}
	}

	$tipsRemarkNames=implode(" | ",$tipsRemarkNames);

	if($shouldSuccessRows==$successRows){
		$ret=returnAjaxData(200,"success",['itemName'=>$itemName,'rows'=>$successRows,'tipsRows'=>$tipsRemarkRows,'tipsNames'=>$tipsRemarkNames]);
		die($ret);
	}else{
		$ret=returnAjaxData(0,"failed",['itemName'=>$itemName,'total'=>$shouldSuccessRows,'rows'=>$successRows,'tipsRows'=>$tipsRemarkRows,'tipsNames'=>$tipsRemarkNames]);
		die($ret);
	}
}else{
	die(var_dump($_POST));
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