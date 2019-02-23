<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-Order分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-20
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class Order extends CI_Controller {

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


	public function setAthleteMark()
	{
		$this->safe->checkIsInGames();
		$this->ajax->makeAjaxToken();
		$this->load->view('order/setAthleteMark',['gamesId'=>$this->gamesId]);
	}


	public function toSetRemark()
	{
		$id=inputPost('id',0,1);
		$type=inputPost('type',1,1);
		$type=$type==''?null:$type;

		$this->db->where('id', $id);
		$this->db->update('score', ['remark'=>$type]);

		if($this->db->affected_rows()==1){
			returnAjaxData(200,'success');
		}else{
			returnAjaxData(500,'database Error');
		}
	}


	public function search(){
		$itemId=inputGet('itemId',0,1);
		$sql='SELECT a.*,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id ORDER BY a.run_group,a.runway';
		$query=$this->db->query($sql,[$itemId]);

		returnAjaxData(200,'success',['total'=>$this->db->affected_rows(),'list'=>$query->result_array()]);
	}


	public function toAdd()
	{
		$this->safe->checkIsInGames('ajax');

		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$scene=$this->input->post('scene');
		$order=$this->input->post('order');
		$sex=$this->input->post('sex');
		$groupName=$this->input->post('groupName');
		$name=$this->input->post('name');
		$totalGroup=$this->input->post('totalGroup');
		$totalAth=$this->input->post('totalAth');
		
		$sql="INSERT INTO item(scene,order_index,sex,group_name,total_group,total_ath) VALUES (?,?,?,?,?,?)";
		$query=$this->db->query($sql,[$scene,$order,$sex,$groupName,$totalGroup,$totalAth]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData("200","success");
		}else{
			returnAjaxData("0","addFailed");
		}
	}
	
	
	public function toEdit()
	{
		$this->safe->checkIsInGames('ajax');

		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$itemId=$this->input->post('itemId');
		$order=$this->input->post('order');
		$sex=$this->input->post('sex');
		$groupName=$this->input->post('groupName');
		$name=$this->input->post('name');
		$totalGroup=$this->input->post('totalGroup');
		$totalAth=$this->input->post('totalAth');
		
		$sql="UPDATE item SET order_index=?,sex=?,group_name=?,total_group=?,total_ath=?";
		$query=$this->db->query($sql,[$order,$sex,$groupName,$totalGroup,$totalAth]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData("200","success");
		}else{
			returnAjaxData("0","editFailed");
		}
	}
	
	
	public function toDel()
	{
		$this->safe->checkIsInGames('ajax');

		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$itemId=$this->input->post('itemId');
		$type=$this->input->post('type');
		
		$sql="UPDATE item SET is_delete=1 WHERE id=?";
		$query=$this->db->query($sql,[$itemId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData("200","success");
		}else{
			returnAjaxData("0","delFailed");
		}
	}
	
	
	public function import(){
		$this->safe->checkIsInGames();
		$token=$this->ajax->makeAjaxToken();

		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$gamesName=$gamesInfo[0]['name'];
		
		$this->load->view('order/import',['token'=>$token,'gamesName'=>$gamesName]);
	}
	
	
	public function toImport(){
		$this->ajax->checkAjaxToken(inputPost('token',0,1));

		$duplicate=inputPost('duplicate',1,1);

		$checkDuplicateQuery=$this->db->query('SELECT a.id FROM score a,item b WHERE a.item_id=b.id AND b.games_id=?',[$this->gamesId]);
		if($this->db->affected_rows()>=1 && $duplicate!=='1'){
			returnAjaxData(1,'have Order',[$duplicate]);
		}

		//$deleteQuery=$this->db->query('DELETE FROM score a,item b WHERE a.item_id=b.id AND b.games_id=?',[$this->gamesId]);

		$this->load->helper('file_helper');
		$dir="../filebox/".$this->gamesId."/";

		foreach($_FILES['myfile']["error"] as $key => $error){
			if($error == UPLOAD_ERR_OK){
				$MIME=$_FILES['myfile']['type'][$key];
				$Extension=getFileExtension($MIME);
				$name=date("YmdH")."-".mt_rand(1234,9876)."-Order.".$Extension;
				$tmp_name=$_FILES["myfile"]["tmp_name"][$key];

				if(file_exists($dir.$name)){
					returnAjaxData(2,"file Exists");
					die($ret);
				}else{
					$move=move_uploaded_file($tmp_name,$dir.$name);
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
		$sql='INSERT INTO score(item_id,run_group,runway,name,team_id) VALUES ';
		$totalRow=$highestRow-1;

		// 循环读取Excel文件
		for($i=2;$i<=$highestRow;$i++){
			// 获取单元格内容
			$scene=$objPHPExcel->getActiveSheet()->getCell("B".$i)->getValue();
			$orderIndex=$objPHPExcel->getActiveSheet()->getCell("C".$i)->getValue();
			$runGroup=$objPHPExcel->getActiveSheet()->getCell("M".$i)->getValue();
			$runway=$objPHPExcel->getActiveSheet()->getCell("N".$i)->getValue();
			$name=$objPHPExcel->getActiveSheet()->getCell("V".$i)->getValue();
			$teamShortName=$objPHPExcel->getActiveSheet()->getCell("P".$i)->getValue();

			if($teamShortName=="" && $name==""){
				// 这一行没有人，总数减一
				$totalRow--;
				continue;
			}elseif($teamShortName!="" && $name==""){
				$name=$teamShortName;
			}

			$sql.='((SELECT id FROM item WHERE games_id='.$this->gamesId.' AND scene='.$scene.' AND order_index='.$orderIndex.'),'.$runGroup.','.$runway.',"'.$name.'",(SELECT id FROM team WHERE games_id='.$this->gamesId.' AND short_name="'.$teamShortName.'")),';
		}

		$sql=substr($sql,0,strlen($sql)-1);
		$query=$this->db->query($sql);

		if($this->db->affected_rows()==$totalRow){
			returnAjaxData(200,'success');
		}else{
			returnAjaxData(5003,'database Error',['total'=>$totalRow,'successRows'=>$this->db->affected_rows()]);
		}
	}
}
