<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-Score成绩
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-24
 * @version 2019-02-27
 */

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Score extends CI_Controller {

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
		$this->load->view('order/list',['gamesId'=>$this->gamesId]);
	}
	
	
	public function import(){
		$this->safe->checkIsInGames();
		$token=$this->ajax->makeAjaxToken();

		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$software=$gamesInfo['gamesJson']['software'];
		
		$this->load->view('score/import',['gamesId'=>$this->gamesId,'token'=>$token]);
	}
	
	
	public function toImport(){
		$this->ajax->checkAjaxToken(inputPost('token',0,1));

		$duplicate=inputPost('duplicate',1,1);
		$itemId=inputPost('itemId',0,1);
		$checkDuplicateQuery=$this->db->query('SELECT id FROM score WHERE item_id=? AND score!=""',[$itemId]);
		
		if($this->db->affected_rows()>=1 && $duplicate!=='1'){
			returnAjaxData(1,'have Order',[$duplicate]);
		}

		$emptyScoreQuery=$this->db->query('UPDATE score SET score=null WHERE item_id=?',[$itemId]);

		$this->load->helper('file_helper');
		$dir="../filebox/".$this->gamesId."/data/";

		foreach($_FILES['myfile']["error"] as $key => $error){
			if($error == UPLOAD_ERR_OK){
				$MIME=$_FILES['myfile']['type'][$key];
				$extension=getFileExtension($MIME);
				$name=date("YmdH")."-".mt_rand(1234,9876)."-Score.".$extension;
				$tmpName=$_FILES["myfile"]["tmp_name"][$key];

				if(file_exists($dir.$name)){
					returnAjaxData(2,"file Exists");
				}else{
					$move=move_uploaded_file($tmpName,$dir.$name);
					if($move!==true) returnAjaxData(5001,"failed to Move File");
				}
			}elseif($_FILES["myfile"]["error"][$key]!="4"){
				returnAjaxData($_FILES["file"]["error"][$key],"unknown Error");
			}else{
				returnAjaxData(5002,"unknown Error",[$_FILES["file"]["error"]]);
			}
		}

		require 'vendor/autoload.php';
		$objReader = new Xls();
		$objReader->setReadDataOnly(TRUE);
		$objPHPExcel = $objReader->load($dir.$name);
		$sheet = $objPHPExcel->getSheet(0);
		$highestRow = $sheet->getHighestRow();
		$highestColumn = $sheet->getHighestColumn();
		$totalRow=$highestRow-1;
		// 成功导入数量
		$successRow=0;
		// 提示需要标记的行数
		$tipsRemarkRow=0;
		$tipsRemarkName=array();

		// 根据不同编排软件服务商来导入
		if($software=="mxx"){
			// 梅雪雄软件格式
			for($i=2;$i<=$highestRow;$i++){
				// 获取单元格内容
				$rank=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();
				$name=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
				$score=$objPHPExcel->getActiveSheet()->getCell("AC".$i)->getValue();
				$point=$objPHPExcel->getActiveSheet()->getCell("R".$i)->getValue();
				$allroundPoint=$objPHPExcel->getActiveSheet()->getCell("AD".$i)->getValue();
				$runGroup=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
				$runway=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();

				//$sql="UPDATE score SET rank='{$rank}',score='{$score}',point='{$point}' WHERE item_id='{$itemId}' AND run_group='{$runGroup}' AND runway='{$runway}'";
				$sql="UPDATE score SET rank=?,score=?,point=?,allround_point=? WHERE item_id=? AND run_group=? AND runway=?";
			
				// 没成绩没排名，提醒加备注
				if($score=="" && $rank==0){
					$tipsRemarkRow++;
					array_push($tipsRemarkName,$runGroup.'/'.$runway." ".$name);
				}
				
				$this->db->query($sql,[$rank,$score,$point,$allroundPoint,$itemId,$runGroup,$runway]);
				
				if($this->db->affected_rows()==1){
					$successRow++;
				}
			}
		}

		$tipsRemarkName=implode(" | ",$tipsRemarkName);

		if($totalRows==$successRows){
			returnAjaxData(200,"success",['successRow'=>$successRow,'tipsRow'=>$tipsRemarkRow,'tipsName'=>$tipsRemarkName]);
		}else{
			returnAjaxData(0,"failed",['totalRow'=>$totalRow,'successRow'=>$successRow,'tipsRow'=>$tipsRemarkRow,'tipsName'=>$tipsRemarkName]);
		}
	}
}
