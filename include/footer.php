<!-- footer -->
<div style="color:#FFFF00;text-align:center;font-weight:bold;font-size:18px;line-height:29px;">
	<hr>
	&copy; 2014-<?=date('Y');?> 生蚝科技
	<a style="color:#07C160" data-toggle="modal" data-target="#wxModal"><i class="fa fa-weixin fa-lg" aria-hidden="true"></i></a>
	<a style="color:#FF7043" onclick='launchQQ()'><i class="fa fa-qq fa-lg" aria-hidden="true"></i></a>
	<a style="color:#29B6F6" href="mailto:master@xshgzs.com"><i class="fa fa-envelope fa-lg" aria-hidden="true"></i></a>
	<a style="color:#AB47BC" href="https://github.com/OysterTech" target="_blank"><i class="fa fa-github fa-lg" aria-hidden="true"></i></a>

	<br>

	All Rights Reserved.<br>
	<a href="http://beian.miit.gov.cn" target="_blank" style="color:#FFFF00;">粤ICP备19018320号-1</a><br>

	<!-- 友情链接 -->
	<p style="color:white;font-size:16px;">
		友情链接：<a href="http://swimming.sport.org.cn/" target="_blank" style="color:white;">中国游泳协会</a> | <a href="http://www.gdswim.org/" target="_blank" style="color:white;">广东省游泳协会</a>
	</p>
	<!-- ./友情链接 -->

	<a href="/ci" target="_blank" style="color:white;font-size:16px;">登 入 管 理 后 台 (V2.0)</a>

	<br><br>
</div>
<!-- ./footer -->

<script>
function launchQQ(){
	if(/Android|webOS|iPhone|iPod|BlackBerry/i.test(navigator.userAgent)){
		window.location.href="mqqwpa://im/chat?chat_type=wpa&uin=571339406";
	}else{
		window.open("http://wpa.qq.com/msgrd?v=3&uin=571339406");
	}
}
</script>


<div class="modal fade" id="tipsModal" z-index="99999">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bold;font-size:23px;text-align:center;">
					<p id="tips"></p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="wxModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3 class="modal-title">微信公众号二维码</h3>
			</div>
			<div class="modal-body">
				<center><img src="https://www.xshgzs.com/resource/index/images/wxOfficialAccountQRCode.jpg" style="width:85%"></center>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick='$("#wxModal").modal("hide");'>关闭 &gt;</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
