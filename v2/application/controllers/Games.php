<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-Games比赛
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-10-02
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Games extends CI_Controller {

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


	public function index($gamesId){		
		$gamesInfo=$this->games->search("detail",$gamesId);
		
		if($gamesInfo==array()){
			header('location:'.base_url('/'));
		}
		
		$this->session->set_userdata($this->sessPrefix.'gamesId',$gamesId);
		$this->session->set_userdata($this->sessPrefix.'gamesName',$gamesInfo[0]['name']);
		
		$this->load->view('games/index',['info'=>$gamesInfo[0],'gamesJson'=>$gamesInfo[0]['extra_json']]);
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

	public function toEdit()
	{
		$this->safe->checkIsInGames('ajax');

		$token=inputPost('token',0,1);
		$this->ajax->checkAjaxToken($token);
		
		$name=inputPost('name',0,1);
		$startDate=inputPost('startDate',0,1);
		$endDate=inputPost('endDate',0,1);
		$venue=inputPost('venue',0,1);
		$software=inputPost('software',0,1);
		$teamScore=inputPost('teamScore',1,1);
		
		$existName=$this->games->search("",0,$name);
		if(isset($existName[0]) && $existName[0]['name']==$name && $existName[0]['id']!=$this->gamesId){
			returnAjaxData(1,"haveName");
		}
		
		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$gamesInfo=$gamesInfo[0];
		$extraJson=$gamesInfo['extra_json'];
		$extraJson['venue']=$venue;
		$extraJson['software']=$software;
		$extraJson['teamScore']=$teamScore;
		$gamesJson=json_encode($extraJson);
		
		$sql="UPDATE games SET name=?,start_date=?,end_date=?,extra_json=? WHERE id=?";
		$query=$this->db->query($sql,[$name,$startDate,$endDate,$gamesJson,$this->gamesId]);
				
		if($this->db->affected_rows()==1){
			returnAjaxData(200,"success");
		}else{
			returnAjaxData(0,"editFailed");
		}
	}
}
