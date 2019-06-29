<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-检录
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2018-02-07
 * @version 2019-03-10
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Calling extends CI_Controller {

	public $sessPrefix;
	public $API_PATH;
	public $gamesId;

	function __construct()
	{
		parent::__construct();

		$this->safe->checkPermission();
		$this->sessPrefix=$this->safe->getSessionPrefix();
		$this->API_PATH=$this->setting->get('apiPath');
		$this->gamesId=$this->session->userdata($this->sessPrefix.'gamesId');
	}


	public function index()
	{
		$this->safe->checkIsInGames();
		$this->ajax->makeAjaxToken();
		$this->load->view('calling/index');
	}
	
	
	public function toCall()
	{
		$this->safe->checkIsInGames('ajax');
		$this->ajax->checkAjaxToken(inputPost('token',0,1));		
		
		$type=inputPost('type',0,1);
		
		switch($type){
			case 'start':
				self::toStart(inputPost('scene',0,1));
				break;
			case 'end':
				self::toEnd();
				break;
			case 'next':
				self::toNext();
				break;
			case 'back':
				self::toBack();
				break;
		}
	}
	
	
	private function toStart($scene=0)
	{
		$gamesId=$this->gamesId;
		
		$this->db->where('games_id',$gamesId)
		         ->update('item',['is_calling'=>0]);

		$this->db->where('games_id',$gamesId)
		         ->where('scene',$scene)
		         ->where('order_index',1)
		         ->update('item',['is_calling'=>1]);
		
		if($this->db->affected_rows()>=1){
			self::updateCallingTime();
			returnAjaxData(200,'success');
		}else{
			returnAjaxData(1,'failed to Start');
		}
	}


	private function toEnd()
	{
		$gamesId=$this->gamesId;

		$nowQuery=$this->db->where('games_id',$gamesId)
		                   ->where('is_calling',1)
		                   ->get('item');

		if($this->db->affected_rows()!==1){
			returnAjaxData(1,'no Start Calling');
		}else{
			$nowInfo=$nowQuery->result_array();
			$nowScene=$nowInfo[0]['scene'];
		}

		$this->db->where('games_id',$gamesId)
		         ->update('item',['is_calling'=>0]);
		
		if($this->db->affected_rows()>=1){
			self::updateCallingTime();
			returnAjaxData(200,'success',['endScene'=>$nowScene]);
		}else{
			returnAjaxData(2,'failed to End');
		}
	}


	private function toNext()
	{
		$gamesId=$this->gamesId;

		$nowQuery=$this->db->where('games_id',$gamesId)
		                   ->where('is_calling',1)
		                   ->get('item');

		if($this->db->affected_rows()!==1){
			returnAjaxData(1,'no Start Calling');
		}else{
			$nowInfo=$nowQuery->result_array();
			$nowScene=$nowInfo[0]['scene'];
			$nowOrderIndex=$nowInfo[0]['order_index'];
			$nextOrderIndex=$nowOrderIndex+1;
		}

		$endQuery=$this->db->where('games_id',$gamesId)
		                   ->update('item',['is_calling'=>0]);

		if($this->db->affected_rows()<1){
			returnAjaxData(2,'failed to End Last Item',['nowOrderIndex'=>$nowOrderIndex]);
		}

		$startQuery=$this->db->where('games_id',$gamesId)
		                     ->where('scene',$nowScene)
		                     ->where('order_index',$nextOrderIndex)
		                     ->update('item',['is_calling'=>1]);

		if($this->db->affected_rows()===1){
			self::updateCallingTime();
			returnAjaxData(2001,'success',['callingOrderIndex'=>$nextOrderIndex,'lastOrderIndex'=>$nowOrderIndex]);
		}else{
			self::updateCallingTime();
			returnAjaxData(2002,'success and End this Scene',['endOrderIndex'=>$nowOrderIndex,'endScene'=>$nowScene]);
		}
	}


	private function toBack()
	{
		$gamesId=$this->gamesId;

		$nowQuery=$this->db->where('games_id',$gamesId)
		                   ->where('is_calling',1)
		                   ->get('item');

		if($this->db->affected_rows()!==1){
			returnAjaxData(1,'no Start Calling');
		}else{
			$nowInfo=$nowQuery->result_array();
			$nowScene=$nowInfo[0]['scene'];
			$nowOrderIndex=$nowInfo[0]['order_index'];
			$lastOrderIndex=$nowOrderIndex-1;

			if($lastOrderIndex<=0){
				returnAjaxData(4,'it Is Already the Last Item');
			}
		}

		$endQuery=$this->db->where('games_id',$gamesId)
		                   ->update('item',['is_calling'=>0]);

		if($this->db->affected_rows()<1){
			returnAjaxData(2,'failed to End Last Item',['nowOrderIndex'=>$nowOrderIndex]);
		}

		$startQuery=$this->db->where('games_id',$gamesId)
		                     ->where('scene',$nowScene)
		                     ->where('order_index',$lastOrderIndex)
		                     ->update('item',['is_calling'=>1]);

		if($this->db->affected_rows()===1){
			self::updateCallingTime();
			returnAjaxData(200,'success',['callingOrderIndex'=>$lastOrderIndex]);
		}else{
			returnAjaxData(3,'failed to Start Calling');
		}
	}


	private function updateCallingTime()
	{
		$gamesId=$this->gamesId;
		$gamesInfo=$this->games->search('detail',$gamesId);

		$gamesInfo['gamesJson']['callingBeginTime']=isset($gamesInfo['gamesJson']['callingBeginTime'])?date('Y-m-d H:i:s'):returnAjaxData(1,'no Games');
		$gamesJson=json_encode($gamesInfo['gamesJson']);

		$this->db->where('id',$gamesId)
                 ->update('games',['extra_json'=>$gamesJson]);

        if($this->db->affected_rows()===1){
        	return true;
        }else{
        	returnAjaxData(500,'failed to Update Calling Time');
        }
	}
}
