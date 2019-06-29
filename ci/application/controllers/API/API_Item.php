<?php
/**
* @name 生蚝体育竞赛管理系统后台-A-项目API
* @author Jerry Cheung <master@xshgzs.com>
* @since 2018-10-21
* @version 2019-03-01
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class API_Item extends CI_Controller {

	public $sessPrefix;
	
	function __construct()
	{
		parent::__construct();

		$this->sessPrefix=$this->safe->getSessionPrefix();
	}


	public function getOrder(){
		$this->safe->checkIsInGames();
		$this->ajax->checkAjaxToken(inputPost('token'));
		
		$gamesId=$this->session->userdata($this->sessPrefix.'gamesId');
		$scene=inputPost('scene');
		
		$sql="SELECT * FROM item WHERE games_id=? AND scene=? AND is_delete=0";
		
		$query=$this->db->query($sql,[$gamesId,$scene]);
		$data=$query->result_array();
				
		if(count($data)>=1){
			returnAjaxData(200,"success",['data'=>$data]);
		}else{
			returnAjaxData(500,"failed to Search");
		}
	}
	
	
	public function get()
	{
		$gamesId=inputGet('gamesId',0,1);
		$type=inputGet('type',0,1);
		
		if($type=='scene'){
			// 查找所有场次
			$sceneQuery=$this->db->query("SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId]);
			$sceneList=$sceneQuery->result_array();
			returnAjaxData(200,'success',['total'=>$sceneQuery->num_rows(),'sceneList'=>$sceneList]);
		}elseif($type=='item'){
			$scene=inputGet('scene',0,1);
			$kind=inputGet('kind',1,1);

			$sql='SELECT * FROM item WHERE games_id=? AND is_delete=0 AND scene=? ';
			$sql.=$kind!=''?'AND kind="'.$kind.'" ':'';
			$sql.='ORDER BY order_index';
			$query=$this->db->query($sql,[$gamesId,$scene]);
			$list=$query->result_array();
			returnAjaxData(200,'success',['total'=>$query->num_rows(),'itemList'=>$list]);
		}elseif($type=='all'){
			// 查找所有场次
			$sceneQuery=$this->db->query("SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId]);
			$sceneInfo=$sceneQuery->result_array();
		
			// 循环查找该场次的项目
			foreach($sceneInfo as $key=>$scene){
				$sceneId=$scene['scene'];

				$sceneItemQuery=$this->db->where('games_id',$gamesId)
				      ->where('scene',$sceneId)
				      ->where('is_delete',0)
				      ->order_by('order_index')
				      ->get('item');

				$sceneItemInfo=$sceneItemQuery->result_array();
				$sceneInfo[$key]['itemInfo']=$sceneItemInfo;
			}
		
			returnAjaxData(200,'success',['sceneInfo'=>$sceneInfo]);
		}else{
			returnAjaxData(400,'invaild Type');
		}
	}
	
	
	public function getCallingItem()
	{
		$gamesId=inputGet('gamesId',0,1);

		// 获取正在检录的项目
		$callingQuery=$this->db->select('scene,order_index,sex,group_name,name,total_group')
		                       ->where(['games_id'=>1,'is_calling'=>1])
		                       ->get('item');
		$callingInfo=$callingQuery->result_array();
		
		if($callingQuery->num_rows()==1){
			$callingInfo=$callingInfo[0];
			$callingScene=$callingInfo['scene'];
			$callingOrderIndex=$callingInfo['order_index'];
			$limitOrderIndex=$callingOrderIndex+3;
			
			// 获取准备检录项目
			$readyQuery=$this->db->select('scene,order_index,sex,group_name,name,total_group')
			                     ->where(['games_id'=>$gamesId,'scene'=>$callingScene,'order_index >'=>$callingOrderIndex,'order_index <='=>$limitOrderIndex])
			                     ->get('item');
			$readyList=$readyQuery->result_array();
			
			// 获取最新检录时间
			$gamesInfo=$this->games->search('detail',$gamesId);
			$callingBeginTime=isset($gamesInfo['gamesJson']['callingBeginTime'])?$gamesInfo['gamesJson']['callingBeginTime']:'';

			returnAjaxData(200,'success',['callingBeginTime'=>$callingBeginTime,'callingItem'=>$callingInfo,'readyItem'=>$readyList]);
		}else{
			returnAjaxData(1,'no Calling Item');
		}
	}
}
