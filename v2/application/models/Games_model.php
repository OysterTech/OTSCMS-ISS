<?php
/**
* @name M-Games比赛
* @author Jerry Cheung <master@xshgzs.com>
* @since 2018-10-02
* @version 2019-02-17
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Games_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}


	/**
	 * 查询比赛信息
	 * @param String 查询类型
	 */
	public function search($type='',$gamesId=0,$name="")
	{
		$sql="SELECT * FROM games ";
		$conditionData=array();
		
		if($type!="index"){
			$sql.="WHERE ";
		
			if($gamesId!=0){
				$sql.="id=? AND ";
				array_push($conditionData,$gamesId);
			}
			if($name!=""){
				$sql.="name=? AND ";
				array_push($conditionData,$name);
			}
			
			$sql=substr($sql,0,strlen($sql)-4);
		}
		
		$query=$this->db->query($sql.'ORDER BY start_date DESC',$conditionData);
		$list=$query->result_array();
		foreach($list as $key=>$info){
			$gamesJson=json_decode($info['extra_json'],TRUE);
			$list[$key]['extra_json']=$gamesJson;
		}
		return $list;
	}
}
