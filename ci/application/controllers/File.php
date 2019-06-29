<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-03
 * @version 2019-02-26
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {

	public $sessPrefix;
	public $gamesId;
	public $API_PATH;

	function __construct()
	{
		parent::__construct();

		$this->safe->checkPermission();
		$this->sessPrefix=$this->safe->getSessionPrefix();

		$this->API_PATH=$this->setting->get('apiPath');
		$this->gamesId=$this->session->userdata($this->sessPrefix.'gamesId');
	}


	public function list()
	{
		$this->safe->checkIsInGames();

		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$fileList=$gamesInfo['gamesJson']['file'];
		
		$this->load->view('file/list',['list'=>$fileList]);
	}


	public function toUpload()
	{
		$fileName=$this->input->post('fileName');

		$dir="../filebox/".$this->gamesId."/";

		foreach($_FILES['myfile']["error"] as $key => $error){
			if($error == UPLOAD_ERR_OK){
				$name=$_FILES["myfile"]["name"][$key];
				$tmp_name=$_FILES["myfile"]["tmp_name"][$key];

				if(file_exists($dir.$name)){
					returnAjaxData(1,"file Exist");
				}else{
					$move=move_uploaded_file($tmp_name,$dir.$name);
					if($move==true){
						$gamesInfo=$this->games->search("detail",$this->gamesId);
						$gamesJson=$gamesInfo['gamesJson'];
						array_push($gamesJson['file'],['name'=>$fileName,'url'=>'https://sport.xshgzs.com/filebox/'.$this->gamesId.'/'.$name]);
						$this->db->where('id', $this->gamesId);
						$this->db->update('games',['extra_json'=>json_encode($gamesJson)]);
						returnAjaxData(200,"success");
					}
				}
			}elseif($_FILES["myfile"]["error"][$key]!="4"){
				returnAjaxData($_FILES["file"]["error"][$key],"unknownError");
			}else{
				returnAjaxData(var_dump($_FILES["file"]["error"]),"unknownError");
			}
		}
	}
}
