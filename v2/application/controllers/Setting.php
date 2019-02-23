<?php
/**
 * @name C-系统配置
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-03-03
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	public $sessPrefix;
	public $nowUserID;
	public $nowUserName;
	public $API_PATH;

	function __construct()
	{
		parent::__construct();

		$this->safe->checkPermission();
		
		$this->sessPrefix=$this->safe->getSessionPrefix();

		$this->API_PATH=$this->setting->get('apiPath');
		$this->nowUserID=$this->session->userdata($this->sessPrefix.'userID');
		$this->nowUserName=$this->session->userdata($this->sessPrefix.'userName');
	}


	public function toList()
	{
		$this->ajax->makeAjaxToken();
		$list=$this->Setting_model->list();
		
		$this->load->view('admin/sys/setting/list',['list'=>$list]);
	}


	public function toSave()
	{
		$token=$this->input->post('token');
		$this->ajax->checkAjaxToken($token);
		
		$name=$this->input->post('name');
		$value=$this->input->post('value');
		
		$saveStatus=$this->Setting_model->save($name,$value);
		
		if($saveStatus==TRUE){
			$ret=$this->ajax->returnData("200","success");
			die($ret);
		}else{
			$ret=$this->ajax->returnData("0","saveFailed");
			die($ret);
		}
	}
}
