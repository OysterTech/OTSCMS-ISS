<?php
/**
 * @name 生蚝体育比赛管理系统-Web-后台资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-20
 * @update 2018-10-09
 */

require_once '../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);
$url='https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$extraJson=json_decode($gamesInfo[0][0]['extra_json'],true);
$fileList=$extraJson['file'];

if($fileList==null || $fileList==""){
	$fileList=array();
}

// 上传处理
if(isset($_POST) && $_POST){
	$fileName=$_POST['name'];
	$dir='../filebox/'.$gamesId.'/';

	foreach($_FILES["myfile"]["error"] as $key => $error){
		if($error == UPLOAD_ERR_OK){
			$name=$_FILES["myfile"]["name"][$key];
			$tmp_name=$_FILES["myfile"]["tmp_name"][$key];
			if(file_exists($dir.$name)){
				echo "<center><font color='red' class='tips'>".$name." 已经存在</font></center>";
			}else{
				move_uploaded_file($tmp_name,$dir.$name);
				echo "<center>";
				echo "<font color='green' class='tips'>成功上传文件：</font>";
				echo "<font color='orange' class='tips'>".$dir.$name."</font>";
				echo "<br>";
				echo "<font color='blue' class='tips'>文件大小：".($_FILES["myfile"]["size"][$key]/1024)." KB</font>";
				echo "</center>";

				array_push($fileList,array('name'=>$fileName,'url'=>ROOT_PATH.substr($dir,3).$name));
				$extraJson['file']=$fileList;
				$extraJson=json_encode($extraJson);
				$sql="UPDATE games SET extra_json=? WHERE id=?";
				$query=PDOQuery($dbcon,$sql,[$extraJson,$gamesId],[PDO::PARAM_STR,PDO::PARAM_INT]);
			}
		}elseif($_FILES["myfile"]["error"][$key]!="4"){
			echo "<font color='red' class='tips'>Error Code： ".$_FILES["myfile"]["error"][$key]."</font><br>";
		}
	}
}
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:#f0ad4e;">资 料 管 理</h3>

<hr>

<table class="table table-hover table-striped table-bordered">
<tr>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">操作</th>
</tr>
<?php foreach($fileList as $key=>$fileInfo){ ?>
<tr>
	<td><?=$fileInfo['name'];?></td>
	<td>
		<a class='btn btn-info' href="<?=$fileInfo['url'];?>"><i class="fa fa-download" aria-hidden="true"></i> 下载</a> <a class='btn btn-danger' onclick="readyDel('<?=$fileInfo['name'];?>','<?=strrchr($fileInfo['url'],'/');?>','<?=$key;?>');"><i class="fa fa-trash-o" aria-hidden="true"></i> 删除</a>
	</td>
</tr>

<?php } ?>
</table>

<hr>

<form method="post" enctype="multipart/form-data">
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">文件名</span>
			<input name="name" id="name" class="form-control" style="height:40px">
		</div>
	</div>
	<div class="col-xs-6">
		<div class="input-group">
			<span class="input-group-addon">选取文件</span>
			<input type="file" name="myfile[]" id="myfile" class="form-control" style="height:40px">
		</div>
	</div>
	<br><br><br>
	<center>
		<a class="btn btn-primary" style="width:48%" href="gamesIndex.php?gamesId=<?=$gamesId;?>">< 返 回 后 台 首 页</a> <button class="btn btn-success" style="width:48%" type="button" onclick="upload();">上 传 文 件 &gt;</button>
	</center>
</form>

<?php include '../include/footer.php'; ?>

<script>
var gamesId="<?=$gamesId;?>";
var fileName="";
var fileUrl="";
var fileKey="";

function upload(){
	name=$("#name").val();

	if($("#myfile").val().length>0 && name!=""){
		$("form").submit();
	}else if(name==""){
		alert("请输入文件名称！");
		return false;
	}else{
		alert("请选择需要上传的文件！");
		return false;
	}
}

function readyDel(name,url,key){
	$("#tips").html("确认要删除[<font color='blue'>"+name+"</font>]吗");
	$("#tipsModal").modal('show');
	fileName=name;
	fileUrl=encodeURIComponent("../filebox/"+gamesId+url);
	fileKey=key;
	return;
}

function toDel(){
	window.location.href="toDelFile.php?gamesId="+gamesId+"&fileUrl="+fileUrl+"&key="+fileKey;
}
</script>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="ModalTitle">温馨提示</h3>
			</div>
			<div class="modal-body">
				<font color="red" style="font-weight:bold;font-size:24px;text-align:center;">
					<p id="tips"></p>
				</font>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">&lt; 取消</button> <button type="button" class="btn btn-danger" onclick="toDel();">确认 &gt;</button>
			</div>
		</div>
	</div>
</div>

</body>
</html>