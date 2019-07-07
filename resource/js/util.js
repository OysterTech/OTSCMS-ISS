/**
 * getURLParam 获取URL参数
 * @param String 参数名称
 **/
function getURLParam(name){
	var reg = new RegExp("(^|&)"+name+"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if(r!=null){return decodeURI(r[2]);}
	else{return null;}
}


/**
* lockScreen 屏幕锁定，显示加载图标
**/
function lockScreen(){
	$('body').append(
		'<div class="loadingwrap" id="loadingwrap"><div class="spinner"><div class="rect1"></div><div class="rect2"></div><div class="rect3"></div><div class="rect4"></div><div class="rect5"></div></div></div>'
		);
}


/**
* unlockScreen 屏幕解锁
**/
function unlockScreen(){
	// 0.3s后再删除，防止闪现
	setTimeout(function(){
		$('#loadingwrap').remove();
	},300);	
}


/**
* showModalTips 模态框显示提醒消息
* @param String 消息内容
* @param String 消息标题
**/
function showModalTips(msg,title='温馨提示'){
	$("#tips").html(msg);
	$("#tipsTitle").html(title);
	$("#tipsModal").modal("show");
}