<?php
/**
 * @name 生蚝体育比赛管理系统-Web-总分表
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-11
 * @update 2018-09-02
 */

require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];

$teamSql="SELECT id,name FROM team WHERE games_id=?";
$teamQuery=PDOQuery($dbcon,$teamSql,[$gamesId],[PDO::PARAM_INT]);
$totalTeam=$teamQuery[1];
$groupSql="SELECT DISTINCT(group_name) FROM item WHERE games_id=? AND is_delete=0";
$groupQuery=PDOQuery($dbcon,$groupSql,[$gamesId],[PDO::PARAM_INT]);
$totalGroup=$groupQuery[1];

$totalMale=0;$totalFemale=0;
$showMale=[];$showFemale=[];

for($i=0;$i<$totalGroup;$i++){
	$groupName=$groupQuery[0][$i]['group_name'];
	$showMale[$groupName]=[];$showFemale[$groupName]=[];
	
	// 每组中的每队
	for($j=0;$j<$totalTeam;$j++){
		$teamId=$teamQuery[0][$j]['id'];
		$teamName=$teamQuery[0][$j]['name'];
		$score1Sql="SELECT SUM(b.point) AS total_score FROM item a,score b WHERE a.group_name=? AND a.sex='男子' AND b.team_id=? AND a.id=b.item_id";
		$score1Query=PDOQuery($dbcon,$score1Sql,[$groupName,$teamId],[PDO::PARAM_STR,PDO::PARAM_INT]);
		$score1=$score1Query[0][0]['total_score'];
		$score2Sql="SELECT SUM(b.point) AS total_score FROM item a,score b WHERE a.group_name=? AND a.sex='女子' AND b.team_id=? AND a.id=b.item_id";
		$score2Query=PDOQuery($dbcon,$score2Sql,[$groupName,$teamId],[PDO::PARAM_STR,PDO::PARAM_INT]);
		$score2=$score2Query[0][0]['total_score'];
		if($score1!="" && $score1!=0){$showMale[$groupName][$teamName]['score']=$score1;$totalMale++;}
		if($score2!="" && $score2!=0){$showFemale[$groupName][$teamName]['score']=$score2;$totalFemale++;}
	}
	
	// 得分排序
	if($showMale[$groupName]!=[]){
		$score=array_column($showMale[$groupName],'score');
		array_multisort($score,SORT_DESC,$showMale[$groupName]);
	}
	if($showFemale[$groupName]!=[]){
		$score=array_column($showFemale[$groupName],'score');
		array_multisort($score,SORT_DESC,$showFemale[$groupName]);
	}
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

<h3 style="font-weight:bold;text-align:center;color:orange;">团 体 总 分</h3>

<hr>

<table class="table table-hover table-striped table-bordered teamScoreTable">
<tr>
	<th style="text-align:center;">性别</th>
	<th style="text-align:center;">组别</th>
	<th style="text-align:center;">名次</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">总分</th>
</tr>
<!-- 男子 -->
<?php
$i=-1;
foreach($showMale as $groupName=>$group){
	$j=-1;$i++;
	$totalTeam=count($group);
	foreach($group as $teamName=>$team){
		$j++;
?>
<tr>
	<?php if($i==0 && $j==0){ ?><td rowspan="<?=$totalMale;?>" style="text-align:center;vertical-align:middle;">男子</td><?php } ?>
	<?php if($j==0){ ?><td rowspan="<?=$totalTeam;?>" style="text-align:center;vertical-align:middle;"><?=$groupName;?></td><?php } ?>
	<td style="font-weight:bold;color:blue;"><?=$j+1;?></td>
	<td><?=$teamName;?></td>
	<td><?=$team['score'];?></td>
</tr>
<?php } } ?>
<!-- ./男子 -->

<!-- 女子 -->
<?php
$i=-1;
foreach($showFemale as $groupName=>$group){
	$j=-1;$i++;
	$totalTeam=count($group);
	foreach($group as $teamName=>$team){
		$j++;
?>
<tr>
	<?php if($i==0 && $j==0){ ?><td rowspan="<?=$totalFemale;?>" style="text-align:center;vertical-align:middle;">女子</td><?php } ?>
	<?php if($j==0){ ?><td rowspan="<?=$totalTeam;?>" style="text-align:center;vertical-align:middle;"><?=$groupName;?></td><?php } ?>
	<td style="font-weight:bold;color:red;"><?=$j+1;?></td>
	<td><?=$teamName;?></td>
	<td><?=$team['score'];?></td>
</tr>
<?php } } ?>
<!-- ./女子 -->

</table>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

</body>
</html>
