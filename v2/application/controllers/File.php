<?php
/**
 * @name 生蚝体育竞赛管理系统后台-C-资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @since 2019-02-03
 * @version 2019-02-23
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class File extends CI_Controller {

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

		$gamesInfo=$this->games->search("detail",$this->gamesId);
		$fileList=$gamesInfo[0]['extra_json']['file'];
		
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
					$ret=$this->ajax->returnData(1,"fileExists");
					die($ret);
				}else{
					$move=move_uploaded_file($tmp_name,$dir.$name);
					if($move==true){
						$gamesInfo=$this->games->search("detail",$this->gamesId);
						$extraJson=$gamesInfo[0]['extra_json'];
						array_push($extraJson['file'],['name'=>$fileName,'url'=>'https://sport.xshgzs.com/filebox/'.$this->gamesId.'/'.$name]);
						$this->db->where('id', $this->gamesId);
						$this->db->update('games',['extra_json'=>json_encode($extraJson)]);
						$ret=$this->ajax->returnData(200,"success");
						die($ret);
					}
				}

			}elseif($_FILES["myfile"]["error"][$key]!="4"){
				$ret=$this->ajax->returnData($_FILES["file"]["error"][$key],"unknownError");
				die($ret);
			}else{
				$ret=$this->ajax->returnData(var_dump($_FILES["file"]["error"]),"unknownError");
				die($ret);
			}
		}
	}
}
