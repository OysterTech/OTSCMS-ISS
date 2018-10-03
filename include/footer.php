<hr>
<center>
	<!-- 更多比赛 -->
	<p style="font-weight:bold;font-size:20px;line-height:26px;">
		<a href="<?=ROOT_PATH;?><?php if(strpos($_SERVER['PHP_SELF'],'admin')!=FALSE){?>admin<?php } ?>">&lt;&lt; 查 看 更 多 比 赛</a><br>
	</p>
	<!-- ./更多比赛 -->
	
	<hr>
	
	<!-- 页脚版权 -->
	<p style="font-weight:bold;font-size:20px;line-height:26px;">
	
		&copy; <a href="https://www.xshgzs.com" target="_blank" style="font-size:21px;">生蚝科技</a> 2014-<?=date('Y');?>
		<a style="color:#FF7043" onclick='launchQQ()'><i class="fa fa-qq fa-lg" aria-hidden="true"></i></a>
		<a style="color:#29B6F6" href="mailto:master@xshgzs.com"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></a>
		<a style="color:#AB47BC" href="https://github.com/OysterTech" target="_blank"><i class="fa fa-github fa-lg" aria-hidden="true"></i></a>
		
		<br>
		
		All Rights Reserved.<br>		
		<a href="http://www.miitbeian.gov.cn/" target="_blank" style="color:black;">粤ICP备18045107号-2</a><br>

	</p>
	<!-- ./页脚版权 -->
	
	<!-- 友情链接 -->
	<p style="font-size:15px;line-height:26px;">
		友情链接：<a href="http://swimming.sport.org.cn/" target="_blank" style="color:black;">中国游泳协会</a> | <a href="http://www.gdswim.org/" target="_blank" style="color:black;">广东省游泳协会</a>
		<!-- ./友情链接 -->
		
		<br>
		
		<?php if(strpos($_SERVER['PHP_SELF'],"admin")===FALSE){ ?>
		<a href="<?=ROOT_PATH;?>admin/login.php" target="_blank" style="color:black;">登入管理平台</a>
		<?php }else{ ?>
		<a href="<?=ROOT_PATH;?>admin/logout.php" style="color:green;font-weight:bold;font-size:18px;">退 出 后 台</a>
		<?php } ?>
	</p>
</center>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="<?=JS_PATH;?>util.js"></script>

<script>
function launchQQ(){		
	if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
	window.location.href="mqqwpa://im/chat?chat_type=wpa&uin=571339406";
	}else{
		window.open("http://wpa.qq.com/msgrd?v=3&uin=571339406");
	}
}
</script>
