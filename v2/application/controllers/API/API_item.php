<?php
/**
* @name A-项目API
* @author Jerry Cheung <master@xshgzs.com>
* @since 2018-10-21
* @version 2018-10-21
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class API_item extends CI_Controller {

	public $sessPrefix;
	
	function __construct()
	{
		parent::__construct();

		$this->sessPrefix=$this->safe->getSessionPrefix();
	}


	public function getOrder(){
		$this->safe->checkIsInGames();

		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$gamesId=$this->session->userdata($this->sessPrefix.'gamesId');
		$scene=$this->input->post('scene');
		$sql="SELECT * FROM item WHERE games_id=? AND scene=? AND is_delete=0";
		
		$query=$this->db->query($sql,[$gamesId,$scene]);
		$data=$query->result_array();
				
		if(count($data)>=1){
			$ret=$this->ajax->returnData("200","success",['data'=>$data]);
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","searchFailed");
			die($ret);
		}
	}
}
