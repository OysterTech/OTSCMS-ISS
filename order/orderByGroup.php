<?php
/**
 * @name 生蚝体育比赛管理系统-Web-按项目查询分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-02
 */
	
require_once '../include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:blue;margin-bottom:14px;">分 组 查 询</h3>

<?php include '../include/selectGroup.php'; ?>

<!-- 提交按钮 -->
<center>
	<a class="btn btn-primary" style="width:48%" href="../order.php">< 返 回</a> <button class="btn btn-success" style="width:48%" onclick="search()">立 即 查 询 ></button>
</center>
<!-- ./提交按钮 -->

<hr>

<h3 style="text-align:center;font-weight:bold;" id="itemName"></h3>

<table id="table" class="table table-hover table-striped table-bordered orderTable">
<tr>
	<th style="text-align:center;">组</th>
	<th style="text-align:center;">道</th>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">备注</th>
</tr>
</table>

<?php include('../include/footer.php'); ?>

<script>
var gamesId="<?=$gamesId;?>";

function search(){
	lockScreen();
	sex=$("#sex").val();
	groupName=$("#groupName").val();
	name=$("#name").val();

	$.ajax({
		url:"getOrder.php",
		type:"get",
		data:{"orderBy":"group","gamesId":gamesId,"sex":sex,"groupName":groupName,"name":name},
		dataType:"json",
		success:function(ret){
			// 先清空表格和项目名
			$("#table tr:not(:first)").html("");
			$("#itemName").html("");

			unlockScreen();

			if(ret.code==1){
				$("#tips").html("无此项目！");
				$("#tipsModal").modal('show');
				return false;
			}if(ret.code==1){
				$("#tips").html("参数缺失，请联系管理员！");
				$("#tipsModal").modal('show');
				return false;
			}else if(ret.code==200){
				// 显示项目名称
				$("#itemName").html(ret.data['scene']+"/"+ret.data['orderIndex']+" "+sex+groupName+name);

				orderData=ret.data['orderData'];

				// 如果没有任何分组
				if(orderData==""){
					html="<tr>"
					    +"<td colspan='7' style='font-weight:bold;color:red;font-size:18px;'>本 项 目 暂 无 分 组 数 据</td>"
					    +"</tr>";
					$("#table").append(html);
					return;
				}

				// 同组合并单元格
				allGroup=[];
				for(j in orderData){
					runGroup=orderData[j]['run_group'];
					
					if(allGroup[runGroup]==undefined){
						allGroup[runGroup]=1;
					}else{
						allGroup[runGroup]++;
					}
				}

				// 循环显示分组秩序
				lastRunGroup=0;
				for(i in orderData){
					info=orderData[i];
					runGroup=info['run_group'];
					html="<tr>";
					
					if(runGroup!=lastRunGroup){
						html+="<td rowspan='"+allGroup[runGroup]+"' style='";

						if(lastRunGroup%2==0){
							html+="background-color:#B3E5FC;";
						}else{
							html+="background-color:#CCFF90;";
						}

						html+="text-align:center;vertical-align:middle;'>"+runGroup+"</td>";
						lastRunGroup=runGroup;
					}

					if(info['remark']==null) info['remark']="";
					
					// 组合并添加行
					html+="<td>"+info['runway']+"</td>"
					    +"<td>"+info['name']+"</td>"
					    +"<td>"+info['short_name']+"</td>"
					    +"<td>"+info['remark']+"</td>"
					    +"</tr>";
					$("#table").append(html);
				}
			}
		}
	})
}
</script>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bold;font-size:24px;text-align:center;">
					<p id="tips"></p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">返回 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
