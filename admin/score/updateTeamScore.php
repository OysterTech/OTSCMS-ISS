<?php
/**
 * @name 生蚝体育比赛管理系统-Web-人工加扣分
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-18
 * @update 2018-08-23
 */

require_once '../../include/public.func.php';
$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex();
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex();
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$teamSql="SELECT id,name,bouns,deduction FROM team WHERE games_id=?";
$teamQuery=PDOQuery($dbcon,$teamSql,[$gamesId],[PDO::PARAM_INT]);
$totalTeam=$teamQuery[1];

for($i=0;$i<$totalTeam;$i++){
	$teamId=$teamQuery[0][$i]['id'];
	$teamName=$teamQuery[0][$i]['name'];
	$bouns=$teamQuery[0][$i]['bouns'];
	$deduction=$teamQuery[0][$i]['deduction'];
	$scoreSql="SELECT SUM(b.point) AS total_score FROM item a,score b WHERE b.team_id=? AND a.id=b.item_id";
	$scoreQuery=PDOQuery($dbcon,$scoreSql,[$teamId],[PDO::PARAM_INT]);
	$score=$scoreQuery[0][0]['total_score'];
	$show[$i]['teamId']=$teamId;
	$show[$i]['teamName']=$teamName;
	$show[$i]['bouns']=$bouns;
	$show[$i]['deduction']=$deduction;
	$show[$i]['score']=$score;
	$show[$i]['totalScore']=$score+$bouns-$deduction;
}

$score=array_column($show,'totalScore');
array_multisort($score,SORT_DESC,$show);

?>

<html>
<head>
	<title><?=$gamesName;?> / 生蚝科技</title>
	<?php include '../../include/header.php'; ?>
</head>
<body>

<center><img src="<?=IMG_PATH;?>adminLogo.png" style="display: inline-block;height: auto;max-width: 100%;"></center>
<h2 style="text-align: center;"><?=$gamesName;?></h2>

<hr>

<h3 style="font-weight:bold;text-align:center;color:orange;">人 工 加/扣 团 体 总 分</h3>

<hr>

<table class="table table-hover table-striped table-bordered teamScoreTable">
<tr>
	<th style="text-align:center;">名次</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">得分</th>
	<th style="text-align:center;">加分</th>
	<th style="text-align:center;">扣分</th>
	<th style="text-align:center;">总分</th>
	<th style="text-align:center;">操作</th>
</tr>

<?php for($j=0;$j<$totalTeam;$j++){ ?>
<tr>
	<td style="font-weight:bold;color:blue;">
	<?php
	if($show[$j]['totalScore']>0){
		if($j>0&&$show[$j]['totalScore']==$show[$j-1]['totalScore']){echo $j;}
		else{echo $j+1;}
	}
	?></td>
	<td><?=$show[$j]['teamName'];?></td>
	<td><?=$show[$j]['score'];?></td>
	<td>
		<p id="bouns_1_<?=$show[$j]['teamId'];?>"><?php if($show[$j]['bouns']!=0) echo $show[$j]['bouns'];?></p>
		<input type="num" id="bouns_2_<?=$show[$j]['teamId'];?>" value="<?=$show[$j]['bouns'];?>" class="form-control" style="display:none;">
	</td>
	<td>
		<p id="deduction_1_<?=$show[$j]['teamId'];?>"><?php if($show[$j]['deduction']!=0) echo $show[$j]['deduction'];?></p>
		<input type="num" id="deduction_2_<?=$show[$j]['teamId'];?>" value="<?=$show[$j]['deduction'];?>" class="form-control" style="display:none;">
	</td>
	<td><?=$show[$j]['totalScore'];?></td>
	<td>
		<button id="button_1_<?=$show[$j]['teamId'];?>" class="btn btn-primary" onclick="readyUpdate('<?=$show[$j]['teamId'];?>')">修改</button>
		<button id="button_2_<?=$show[$j]['teamId'];?>" class="btn btn-warning" onclick="cancel('<?=$show[$j]['teamId'];?>')" style="display:none;">取消</button>
		<button id="button_3_<?=$show[$j]['teamId'];?>" class="btn btn-success" onclick="toUpdate('<?=$show[$j]['teamId'];?>')" style="display:none;">保存</button>
	</td>
</tr>
<?php } ?>
</table>

<center>
	<a href="<?=ROOT_PATH;?>admin/score.php?gamesId=<?=$gamesId;?>" class="btn btn-default" style="width:96%"><i class="fa fa-home" aria-hidden="true"></i> 返 回 上 一 页</a>
</center>

<?php include '../../include/footer.php'; ?>

<script>
function cancel(id){
	$("#bouns_2_"+id).attr("style","display:none");
	$("#bouns_1_"+id).attr("style","");
	$("#deduction_2_"+id).attr("style","display:none");
	$("#deduction_1_"+id).attr("style","");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");
}


function readyUpdate(id){
	$("#bouns_1_"+id).attr("style","display:none");
	$("#bouns_2_"+id).attr("style","");
	$("#deduction_1_"+id).attr("style","display:none");
	$("#deduction_2_"+id).attr("style","");
	$("#button_3_"+id).attr("style","");
	$("#button_2_"+id).attr("style","");
	$("#button_1_"+id).attr("style","display:none");	
}

function toUpdate(id){
	bouns=$("#bouns_2_"+id).val();
	deduction=$("#deduction_2_"+id).val();

	if(bouns==""){
		bouns=0;
	}
	if(deduction==""){
		deduction=0;
	}
	
	$("#bouns_2_"+id).attr("style","display:none");
	$("#bouns_1_"+id).attr("style","");
	$("#deduction_2_"+id).attr("style","display:none");
	$("#deduction_1_"+id).attr("style","");
	$("#button_3_"+id).attr("style","display:none");
	$("#button_2_"+id).attr("style","display:none");
	$("#button_1_"+id).attr("style","");

	$.ajax({
		url:"toUpdateTeamScore.php",
		type:"POST",
		data:{"id":id,"bouns":bouns,"deduction":deduction},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				location.reload();
			}else{
				console.log(ret);
				alert("修改失败！！！\n数据未被保存！");
			}
		}
	})
}
</script>

</body>
</html>
