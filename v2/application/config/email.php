<?php
/**
* @name 邮件配置
* @author SmallOysyer <master@xshgzs.com>
* @since 2018-03-04
* @version V1.0 2018-03-08
*/

defined('BASEPATH') OR exit('No direct script access allowed');

// QQ邮箱地址(个人邮箱仅需填写QQ号)
$config['smtp_user'] = 'zhangjinghao@itrclub.com';
// QQ邮箱的16位IMTP授权码
$config['smtp_pass'] = 'Sozx027368';


/* !!!!!!!!!! 下方配置无需修改 !!!!!!!!!! */
$config['protocol'] = 'smtp';
$config['charset'] = 'utf-8';
$config['wordwrap'] = TRUE;
$config['smtp_host'] = 'ssl://smtp.exmail.qq.com';
$config['smtp_port'] = 465;
$config['mailtype'] = 'html';
$config['crlf']="\r\n";
$config['newline']="\r\n";
/* !!!!!!!!!! 上方配置无需修改 !!!!!!!!!!! */
