<?php
/**
 * @name 生蚝体育比赛管理系统-Web-公用函数库
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-28
 */

session_start();
require_once 'PDOConn.php';

define('ROOT_PATH','https://sport.xshgzs.com/');
define('JS_PATH',ROOT_PATH.'resource/js/');
define('IMG_PATH',ROOT_PATH.'resource/image/');
define('CSS_PATH',ROOT_PATH.'resource/css/');


/**
 * goToIndex 返回系统首页
 * @param String 来源
 */
function goToIndex($from="",$returnUrl="")
{
	if($from=="admin"){
		die(header('Location:'.ROOT_PATH.'admin/logout.php?returnUrl='.$returnUrl));
	}else{
		die(header('Location:'.ROOT_PATH.'index.php'));
	}
}


/**
 * getRanSTR 获取随机字母串
 * @param int    欲获取的随机字符串长度
 * @param 0|1|2  0:只要大写|1:只要小写|2:无限制
 * @return String 随机字符串
 */
function getRanSTR($length,$LettersType=2)
{
	if($LettersType==0){
		$str="ZXCVBNQWERTYASDFGHJKLUPM";
	}elseif($LettersType==1){
		$str="qwertyasdfghzxcvbnupmjk";
	}else{
		$str="qwertyZXCVBNasdfghQWERTYzxcvbnASDFGHupJKLnmUPjk";
	}

	$ranstr="";
	$strlen=strlen($str)-1;
	for($i=1;$i<=$length;$i++){
		$ran=mt_rand(0,$strlen);
		$ranstr.=$str[$ran];
	}

	return $ranstr;
}


/**
 * returnAjaxData 返回JSON数据
 * @param  string 状态码
 * @param  string 待返回的数据
 * @return JSON   已处理好的JSON数据
 */
function returnAjaxData($code,$msg,$data=""){
	$ret=array('code'=>$code,'message'=>$msg,'data'=>$data);
	return json_encode($ret);
}


/**
 * checkLogin 后台检查登录状态
 * @param INT 页面所需权限等级
 */
function checkLogin($level=0){
	$returnUrl=urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
	
	if(!isset($_SESSION['sport_admin_isLogin'])){
		goToIndex("admin",$returnUrl);
	}elseif($_SESSION['sport_admin_isLogin']!=1){
		goToIndex("admin",$returnUrl);
	}
	
	if($level!=0 && $_SESSION['sport_admin_level']>$level){
		goToIndex("admin",$returnUrl);
	}
}
