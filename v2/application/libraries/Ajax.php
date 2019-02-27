<?php
/**
* @name 生蚝体育竞赛管理系统后台-L-Ajax
* @author Jerry Cheung <master@xshgzs.com>
* @since 2018-02-10
* @version 2019-02-24
*/

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax {
	
	protected $_CI;
	public $sessPrefix;

	public function __construct()
	{
		$this->_CI =& get_instance();
		$this->_CI->load->model(array('Setting_model'));

		$this->sessPrefix=$this->_CI->Setting_model->get('sessionPrefix');
	}


	/**
	 * 显示AJAX-Token
	 * @return string Token名+值
	 */
	public function showAjaxToken(){
		return '"token":"'.$this->_CI->session->userdata($this->sessPrefix.'AJAX_token').'"';
	}


	/**
	 * 创建AJAX-Token
	 * @return String AJAX-Token值
	 */
	public function makeAjaxToken()
	{
		// 设置AJAX-Token
		$timestamp=time();
		$hashTimestamp=md5($timestamp);
		$token=sha1(session_id().$hashTimestamp);
		
		$this->_CI->session->set_userdata($this->sessPrefix.'AJAX_token',$token);
		
		return $token;
	}

	
	/**
	 * 校验AJAX-Token的有效性
	 * @param String 待校验的Token值
	 */
	public function checkAjaxToken($token)
	{
		if($token!=$this->_CI->session->userdata($this->sessPrefix.'AJAX_token')){
			returnAjaxData("403","invaild Token");
		}
	}
}
