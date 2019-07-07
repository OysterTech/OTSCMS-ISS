<?php
/**
 * @name 生蚝体育比赛管理系统-Web-总分表不分组
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-09-02
 */

require_once 'include/public.func.php';

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$gamesName=$gamesInfo['name'];

$teamSql="SELECT id,name,bouns,deduction FROM team WHERE games_id=?";
$teamQuery=PDOQuery($dbcon,$teamSql,[$gamesId],[PDO::PARAM_INT]);
$totalTeam=$teamQuery[1];

$show=array();

for($i=0;$i<$totalTeam;$i++){
	$teamId=$teamQuery[0][$i]['id'];
	$teamName=$teamQuery[0][$i]['name'];
	$bouns=$teamQuery[0][$i]['bouns'];
	$deduction=$teamQuery[0][$i]['deduction'];
	$scoreSql="SELECT SUM(b.point) AS total_score FROM item a,score b WHERE a.games_id=? AND b.team_id=? AND a.id=b.item_id";
	$scoreQuery=PDOQuery($dbcon,$scoreSql,[$gamesId,$teamId],[PDO::PARAM_INT,PDO::PARAM_INT]);
	$score=$scoreQuery[0][0]['total_score'];
	$show[$i]['teamName']=$teamName;
	$show[$i]['bouns']=number_format($bouns,2);
	$show[$i]['deduction']=number_format($deduction,2);
	$show[$i]['score']=number_format($score,2);
	$show[$i]['totalScore']=$score+$bouns-$deduction;
}

$score=array_column($show,'totalScore');
array_multisort($score,SORT_DESC,$show);
//var_dump($show);
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
	<th style="text-align:center;">名次</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">得分</th>
	<th style="text-align:center;">加分</th>
	<th style="text-align:center;">扣分</th>
	<th style="text-align:center;">总分</th>
</tr>

<?php if($totalTeam==0){ ?>
<tr>
	<th style="text-align:center;color:red;font-size:20px;" colspan="6">暂 无 团 体 总 分</th>
</tr>
<?php } ?>

<?php for($j=0;$j<$totalTeam;$j++){ ?>
<tr>
	<td style="font-weight:bold;color:blue;">
	<?php
	if($show[$j]['totalScore']>0){
		// 如果和上一名同分
		if($j>0&&$show[$j]['totalScore']==$show[$j-1]['totalScore']){echo $j;}
		else{echo $j+1;}
	}
	?></td>
	<td><?=$show[$j]['teamName'];?></td>
	<td><?php if($show[$j]['score']!=0) echo $show[$j]['score'];?></td>
	<td><?php if($show[$j]['bouns']!=0) echo $show[$j]['bouns'];?></td>
	<td><?php if($show[$j]['deduction']!=0) echo $show[$j]['deduction'];?></td>
	<td><?php if($show[$j]['totalScore']!=0) echo $show[$j]['totalScore'];?></td>
</tr>
<?php } ?>
</table>

<center>
	<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 首 页</a>
</center>

<?php include 'include/footer.php'; ?>

</body>
</html>
