<?php
/**
 * @name 生蚝体育比赛管理系统-Web-比赛资料
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-16
 * @update 2018-09-02
 */
	
require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
$extraJson=json_decode($gamesInfo['extra_json'],TRUE);

$fileJson=$extraJson['file'];
?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:blue;">比 赛 资 料</h3>

<hr>

<table class="table table-hover table-striped table-bordered">
<tr>
	<th style="text-align:center;">文件名</th>
	<th style="text-align:center;">下载</th>
</tr>
<?php if($fileJson==[]){ ?>
<tr>
	<td style="color:red;font-weight:bold;font-size:18px;" colspan="2">暂 无 比 赛 资 料</td>
</tr>
<?php }else{ ?>
<?php foreach($fileJson as $info){ ?>
<tr>
	<td style="text-align:center;vertical-align:middle;"><?=$info['name'];?></td>
	<td><a href="<?=$info['url'];?>" class="btn btn-success">下 载 &gt;</button></td>
</tr>
<?php } } ?>
</table>

<hr>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

</body>
</html>
