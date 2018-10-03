<?php
/**
 * @name 生蚝体育比赛管理系统-Web-日程表
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-23
 */

require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];
$gamesKind=$_SESSION['swim_gamesJson']['kind'];

$sceneInfo=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);
if($sceneInfo[1]==0){
	die("<script>alert('暂未录入日程，敬请期待！');history.go(-1);</script>");
}else{
	$sceneInfo=$sceneInfo[0];
}
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

<h3 style="font-weight:bold;text-align:center;color:red;">比 赛 日 程 表</h3>

<hr>

<?php
foreach($sceneInfo as $scene){
	$sceneId=$scene['scene'];
?>
<table class="table table-hover table-striped table-bordered scheduleTable">
<tr>
	<th style="text-align:center;font-size:16px;background-color:#C4E1FF" colspan="5">第 <?=$sceneId;?> 场<?php if($_SESSION['swim_gamesJson']['scene'][$sceneId]!="") echo "（".$_SESSION['swim_gamesJson']['scene'][$sceneId]."）";?></th>
</tr>
<tr>
	<th style="text-align:center;vertical-align:middle;width:14%;">类型</th>
	<th style="text-align:center;vertical-align:middle;width:10%;">项次</th>
	<th style="text-align:center;vertical-align:middle;">项 目 名</th>
	<th style="text-align:center;vertical-align:middle;width:10%;">组数</th>
	<th style="text-align:center;vertical-align:middle;width:17%;">人(队)数</th>
</tr>
<?php
	$sceneItemInfoSql="SELECT * FROM item WHERE games_id=? AND scene=? AND is_delete=0 ORDER BY kind,order_index";
	$sceneItemInfo=PDOQuery($dbcon,$sceneItemInfoSql,[$gamesId,$sceneId],[PDO::PARAM_INT,PDO::PARAM_INT]);
	$totalItem=$sceneItemInfo[1];
	$itemInfo=$sceneItemInfo[0];
	foreach($itemInfo as $info){
?>
<tr>
	<td><?=$info['kind'];?></td>
	<td><?=$info['order_index'];?></td>
	<td><?php echo $info['sex'].$info['group_name'].$info['name'];if($info['is_final']==0)echo "(预赛)";?></td>
	<td><?=$info['total_group'];?></td>
	<td><?=$info['total_ath'];?></td>
</tr>
<?php } ?>
</table>
<br>
<?php } ?>

<center>
	<a href="gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

</body>
</html>
