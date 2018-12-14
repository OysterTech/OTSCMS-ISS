<?php
/**
 * @name 生蚝体育比赛管理系统-Web-泳协-优秀运动员统计-前台显示结果
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-12-14
 * @update 2018-12-14
 */
	
require_once '../../include/public.func.php';

$totalGroup=array("A","B","C","D","E","F");
$allSex=array("M"=>"男子","F"=>"女子");
?>

<html>
<head>
	<title>赛事优秀运动员统计 / 生蚝科技</title>
	<?php include '../../include/header.php'; ?>
	<style>
	.nowStep{font-weight:bold;}
	</style>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;"></center>

<hr>

<h3 style="font-weight:bold;text-align:center;color:#FF4081;">广州泳协-年度优秀运动员列表</h3>

<br>

<center>
	<div style="width:96%;">
		<div id="chooseYear" class="input-group">
			<span class="input-group-addon">统计年度</span>
			<select id="year_select" class="form-control">
				<option value="" selected disabled>::: 请选择年份 :::</option>
				<?php for($i=2018;$i<=date("Y");$i++){ ?>
				<option value="<?=$i;?>"><?php if($i==date("Y")){echo "★ ";}?><?=$i;?> 年度</option>
				<?php } ?>
			</select>
			<span class="input-group-addon">&lt;</span>
		</div>
	</div>
	
	<br>
	
	<a onclick="history.go(-1);" class="btn btn-primary" style="width:49%;">&lt; 返 回 首 页</a> <button class="btn btn-success" style="width:49%;" onclick="search();">查 询 &gt;</button>
</center>

<br>

<?php foreach($allSex as $key=>$name){ ?>
<div id="<?=$key;?>_group_table" style="display:none;">
	<br>
	<table class="table table-hover table-striped table-bordered">
		<tr>
			<td colspan="4" style="font-weight:bold;background-color:#E1F5FE;"><?=$name;?></td>
		</tr>
		<tr>
			<td style="width:14%">组别</td>
			<td style="width:25%">姓名</td>
			<td>代表队</td>
			<td style="width:16%">总分</td>
		</tr>
		<?php foreach($totalGroup as $group){ ?>
		<tr>
			<td><?=$group;?>组</td>
			<td id="<?=$key;?>_<?=$group;?>_name" style="font-weight:bold;background-color:#CCFF90;font-size:17px;"></td>
			<td id="<?=$key;?>_<?=$group;?>_team"></td>
			<td id="<?=$key;?>_<?=$group;?>_point" style="font-weight:bold;color:red;"></td>
		</tr>
		<?php } ?>
	</table>
</div>
<?php } ?>

<?php include '../../include/footer.php'; ?>

<script>
var allGroup=new Array("A","B","C","D","E","F");

function toStatistics(){
	year=$("#year").val();
	lockScreen();

	$.ajax({
		url:"getResult.php",
		//type:"post",
		data:{"year":year},
		dataType:"json",
		error:function(e){
			unlockScreen();
			$("#tips").html("服务器错误！统计失败！");
			$("#tipsModal").modal('show');
			return false;
		},
		success:function(ret){
			unlockScreen();

			if(ret.code==200){
				data=ret.data['data'];
				console.log(JSON.parse(data));
				data=JSON.parse(data);
					
				for(i=0;i<allGroup.length;i++){
					score_m=getFirstElement(data["男子"][allGroup[i]+"组"]);
					score_f=getFirstElement(data["女子"][allGroup[i]+"组"]);
						
					$("#M_"+allGroup[i]+"_name").html("<a onclick='showDetail("+'"'+allGroup[i]+'组","'+score_m[0]+'"'+");'>"+score_m[0]+'</a>');
					$("#F_"+allGroup[i]+"_name").html("<a onclick='showDetail("+'"'+allGroup[i]+'组","'+score_f[0]+'"'+");'>"+score_f[0]+'</a>');
					$("#M_"+allGroup[i]+"_team").html(score_m[1]);
					$("#F_"+allGroup[i]+"_team").html(score_f[1]);
					$("#M_"+allGroup[i]+"_point").html(score_m[2]);
					$("#F_"+allGroup[i]+"_point").html(score_f[2]);
				}
					
				// 修改选择框显示样式
				$("#chooseYear").attr("style","display:none;");
				
				$("#M_group_table").attr("style","");
				$("#F_group_table").attr("style","");
					
				return true;
			}else{
				alert(ret.code);
			}
		}
	});
}


function showDetail(group,name){
	lockScreen();
	
	$.ajax({
		url:"getDetail.php",
		data:{"group":group,"name":name},
		dataType:"json",
		error:function(e){
			unlockScreen();
			$("#tips").html("服务器错误！");
			$("#tipsModal").modal('show');
			return false;
		},
		success:function(ret){
			unlockScreen();
			
			if(ret.code==200){
				data=ret.data['data'];
				$("#detailTable tr:gt(0)").empty();
				
				for(num in data){
					itemName=data[num]['name'];
					score=data[num]['score'];
					point=data[num]['point'];
					remark=data[num]['remark'];
					html="<tr>"
					    +"<td>"+itemName+"</td>";
					
					if(score=="" && remark!=""){
					    html+="<td colspan=2 style='color:red;font-weight:bold;'>"+remark+"</td>";
					}else{
					    html+="<td style='color:green;font-weight:bold;'>"+score+"</td>";
					    html+="<td style='color:blue;font-weight:bold;'>"+point+"</td>";
					}

					html+="</tr>";
					$("#detailTable").append(html);
				}
				
				$("#detailTitle").html("["+name+"]的项目详细记录");
				$("#detailModal").modal("show");
			}
		}
	});
}


function getFirstElement(obj){
	for(nameStr in obj){
		nameArr=nameStr.split("|");
		name=nameArr[0];
		teamName=nameArr[1];
		
		rtn=new Array(name,teamName,obj[nameStr]);
		return rtn;
	}
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
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="detailModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="detailTitle"></h3>
			</div>
			<div class="modal-body">
				<table id="detailTable" class="table table-hover table-striped table-bordered">
					<tr>
						<th>项目名</th>
						<th>成绩</th>
						<th>成绩分</th>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>
