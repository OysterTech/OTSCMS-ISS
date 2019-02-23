<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-Schedule日程
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-10
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Schedule extends CI_Controller {

	public $sessPrefix;
	public $nowUserID;
	public $nowUserName;
	public $gamesId;
	public $API_PATH;

	function __construct()
	{
		parent::__construct();

		$this->safe->checkPermission();
		
		$this->sessPrefix=$this->safe->getSessionPrefix();

		$this->API_PATH=$this->setting->get('apiPath');
		$this->nowUserID=$this->session->userdata($this->sessPrefix.'userID');
		$this->nowUserName=$this->session->userdata($this->sessPrefix.'userName');
		$this->gamesId=$this->session->userdata($this->sessPrefix.'gamesId');
	}


	public function list()
	{
		$this->safe->checkIsInGames();
		$this->ajax->makeAjaxToken();
		
		$gamesInfo=$this->games->search("detail",$this->gamesId);
		
		if($gamesInfo==array()){
			header('location:'.base_url('/'));
		}
		
		// 查找所有场次
		$sceneQuery=$this->db->query("SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$this->gamesId]);
		$sceneInfo=$sceneQuery->result_array();
		
		// 循环查找该场次的项目
		foreach($sceneInfo as $key=>$scene){
			$sceneId=$scene['scene'];
			$sceneItemQuery=$this->db->query("SELECT * FROM item WHERE games_id=? AND scene=? AND is_delete=0 ORDER BY order_index",[$this->gamesId,$sceneId]);
			$sceneItemInfo=$sceneItemQuery->result_array();
			$sceneInfo[$key]['itemInfo']=$sceneItemInfo;
		}
		
		$this->load->view('schedule/list',['info'=>$gamesInfo[0],'gamesJson'=>$gamesInfo[0]['extra_json'],'sceneInfo'=>$sceneInfo]);
	}


	public function edit()
	{
		$this->safe->checkIsInGames();
		$this->ajax->makeAjaxToken();

		$gamesInfo=$this->games->search("detail",$this->gamesId);
		
		if($gamesInfo==array()){
			header('location:'.base_url('/'));
		}
		
		$this->load->view('games/edit',['info'=>$gamesInfo[0],'gamesJson'=>$gamesInfo[0]['extra_json']]);
	}


	public function toAdd()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token');
		$this->ajax->checkAjaxToken($token);
		
		$scene=inputPost('scene');
		$order=inputPost('order');
		$sex=inputPost('sex');
		$groupName=inputPost('groupName');
		$name=inputPost('name');
		$totalGroup=inputPost('totalGroup');
		$totalAth=inputPost('totalAth');
		
		$sql="INSERT INTO item(scene,order_index,sex,group_name,total_group,total_ath) VALUES (?,?,?,?,?,?)";
		$query=$this->db->query($sql,[$scene,$order,$sex,$groupName,$totalGroup,$totalAth]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"addFailed");
		}
	}
	
	
	public function toEdit()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token',0,1);
		$this->ajax->checkAjaxToken($token);
		
		$itemId=inputPost('itemId',0,1);
		$order=inputPost('order',0,1);
		$sex=inputPost('sex',0,1);
		$groupName=inputPost('groupName',0,1);
		$name=inputPost('name',0,1);
		$totalGroup=inputPost('totalGroup',0,1);
		$totalAth=inputPost('totalAth',0,1);
		
		$sql="UPDATE item SET order_index=?,sex=?,group_name=?,total_group=?,total_ath=? WHERE id=?";
		$query=$this->db->query($sql,[$order,$sex,$groupName,$totalGroup,$totalAth,$itemId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"editFailed");
		}
	}
	
	
	public function toDel()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token');
		$this->ajax->checkAjaxToken($token);
		
		$itemId=inputPost('itemId');
		$type=inputPost('type');
		
		$sql="UPDATE item SET is_delete=1 WHERE id=?";
		$query=$this->db->query($sql,[$itemId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"delFailed");
		}
	}


	public function toSetItemType()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token');
		$this->ajax->checkAjaxToken($token);
		
		$itemId=inputPost('itemId');
		$type=inputPost('type');
		
		if($type=="allround") $type=1;
		elseif($type=="single") $type=0;
		
		$sql="UPDATE item SET is_allround=? WHERE id=?";
		$query=$this->db->query($sql,[$type,$itemId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"setFailed");
		}
	}
	
	
	public function toSetStartTime()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token');
		$this->ajax->checkAjaxToken($token);
		
		$sceneId=inputPost('sceneId');
		$time=inputPost('time');
		
		if(!in_array(strlen($time),array(10,16,19))){
			returnAjaxData(1,"invaild Time Length");
		}
		
		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$gamesJson=$gamesInfo[0]['extra_json'];
		$gamesJson['scene'][$sceneId]=$time;
		$json=json_encode($gamesJson);
		
		$sql="UPDATE games SET extra_json=? WHERE id=?";
		$query=$this->db->query($sql,[$json,$this->gamesId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"setFailed");
		}
	}
	
	
	public function import(){
		$this->safe->checkIsInGames();
		$token=$this->ajax->makeAjaxToken();
		
		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$gamesName=$gamesInfo[0]['name'];
		
		$this->load->view('schedule/import',['token'=>$token,'gamesName'=>$gamesName]);
	}
	
	
	public function toImport(){
		$this->safe->checkIsInGames();
		$this->load->helper('file_helper');

		//$this->ajax->checkAjaxToken(inputPost('token',0,1));

		/*$duplicate=inputPost('duplicate',1,1);
		$checkDuplicateQuery=$this->db->query('SELECT id FROM item WHERE games_id=?',[$gamesId]);
		if($this->db->affected_rows()>=1 && $duplicate!=='1'){
			returnAjaxData(1,'have Schedule',[$duplicate]);
		}*/

		$dir="../filebox/".$this->gamesId."/";

		foreach($_FILES['myfile']["error"] as $key => $error){
			if($error == UPLOAD_ERR_OK){
				$MIME=$_FILES['myfile']['type'][$key];
				$Extension=getFileExtension($MIME);
				$name=date("YmdH")."-".mt_rand(1234,9876)."-Schedule.".$Extension;
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
		
		$this->db->where('games_id', $this->gamesId);
		$deleteQuery=$this->db->delete("item");
		
		require 'vendor/autoload.php';
		$objReader = new Xls();
		$objReader->setReadDataOnly(TRUE);
		$objPHPExcel = $objReader->load($dir.$name);
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$sql='INSERT INTO item(games_id,scene,order_index,sex,group_name,name,total_group,total_ath) VALUES ';

		// 循环读取Excel文件
		for($i=2;$i<=$highestRow;$i++){
			// 获取单元格内容
			$scene=$objPHPExcel->getActiveSheet()->getCell("A".$i)->getValue();
			$orderIndex=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
			$sex=$objPHPExcel->getActiveSheet()->getCell("E".$i)->getValue();
			$groupName=$objPHPExcel->getActiveSheet()->getCell("F".$i)->getValue();
			$name=$objPHPExcel->getActiveSheet()->getCell("H".$i)->getValue().$objPHPExcel->getActiveSheet()->getCell("I".$i)->getValue();
			$totalAth=$objPHPExcel->getActiveSheet()->getCell("K".$i)->getValue();
			$totalGroup=$objPHPExcel->getActiveSheet()->getCell("L".$i)->getValue();

			$sql.='('.$this->gamesId.','.$scene.','.$orderIndex.',"'.$sex.'","'.$groupName.'","'.$name.'",'.$totalGroup.','.$totalAth.'),';

			if(!isset($sceneInfo[$scene])){
				$sceneInfo[$scene]="";
			}
		}

		$sql=substr($sql,0,strlen($sql)-1);
		$query=$this->db->query($sql);

		if($this->db->affected_rows()==($highestRow-1)){
			die('<script>alert("成功导入'.($highestRow-1).'条项目数据！");history.go(-1);</script>');
		}else{
			die('<script>alert("导入失败！\n\n共有'.($highestRow-1).'条\n已成功导入'.$this->db->affected_rows().'条！");history.go(-1);</script>');
		}
	}
}
