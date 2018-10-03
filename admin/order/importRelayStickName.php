<?php
/**
 * @name 生蚝体育比赛管理系统-Web-录入接力棒次
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-17
 * @update 2018-08-31
 */
	
require_once '../../include/public.func.php';
checkLogin();

$gamesId=isset($_GET['gamesId'])&&$_GET['gamesId']>=1?$_GET['gamesId']:goToIndex("admin");
$gamesInfo=PDOQuery($dbcon,"SELECT * FROM games WHERE id=?",[$gamesId],[PDO::PARAM_INT]);

if($gamesInfo[1]!=1){
	goToIndex("admin");
}else{
	$gamesName=$gamesInfo[0][0]['name'];
}

$itemId=0;
$itemList=PDOQuery($dbcon,"SELECT * FROM item WHERE name LIKE '%接力%' AND games_id=?",[$gamesId],[PDO::PARAM_INT]);

if($itemList[1]<1){
	die('<script>alert("本比赛无接力项目！\n不开放录入棒次功能！");history.go(-1);</script>');
}

if(isset($_POST) && $_POST){
	$itemId=$_POST['itemId'];
	$orderSql="SELECT a.*,b.short_name FROM score a,team b WHERE a.item_id=? AND a.team_id=b.id";
	$orderQuery=PDOQuery($dbcon,$orderSql,[$itemId],[PDO::PARAM_INT]);
}
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

<h3 style="font-weight:bold;text-align:center;color:blue;">接 力 棒 次 名 单 录 入</h3>
<p style="line-height:2px;">&nbsp;</p>

<!-- 查询表单 -->
<form method="post">
	<!-- 项次选择框 -->
	<div class="col-xs-12">
		<div class="input-group">
			<span class="input-group-addon">项目名</span>
			<select name="itemId" class="form-control">
				<?php foreach($itemList[0] as $itemInfo){ ?>
					<option value="<?=$itemInfo['id'];?>" <?php if($itemInfo['id']==$itemId){?>selected<?php } ?>><?=$itemInfo['scene'].'/'.$itemInfo['order_index'].' '.$itemInfo['sex'].$itemInfo['group_name'].$itemInfo['name'];?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<!-- ./项次选择框 -->

	<p style="line-height:8px;">&nbsp;</p>

	<!-- 提交按钮 -->
	<center>
		<a class="btn btn-primary" style="width:48%" href="<?=ROOT_PATH.'admin/order.php?gamesId='.$gamesId;?>">< 返 回</a> <input type="submit" class="btn btn-success" style="width:48%" value="查 询 分 组 >">
	</center>
	<!-- ./提交按钮 -->
</form>
<!-- ./查询表单 -->

<?php if(isset($_POST) && $_POST){ ?>
<hr>

<h3 style="text-align:center;font-weight:bold;"></h3>

<table class="table table-hover table-striped table-bordered orderTable">
<tr>
	<th style="text-align:center;">组/道</th>
	<th style="text-align:center;">名称</th>
	<th style="text-align:center;">单位</th>
	<th style="text-align:center;">操作</th>
</tr>
<?php for($i=0;$i<$orderQuery[1];$i++){ ?>
<tr>
	<td><?=$orderQuery[0][$i]['run_group'].'/'.$orderQuery[0][$i]['runway'];?></td>
	<td><p id="name_<?=$orderQuery[0][$i]['id'];?>"><?=$orderQuery[0][$i]['name'];?></p></td>
	<td><?=$orderQuery[0][$i]['short_name'];?></td>
	<td><button onclick="update('<?=$orderQuery[0][$i]['id'];?>','<?=$orderQuery[0][$i]['short_name'];?>');" class="btn btn-primary">修改</button></td>
</tr>
<?php } ?>
</table>
<?php } ?>

<?php include('../../include/footer.php'); ?>

<script>
function update(id,shortName){
	// 先根据<br>判断是否已录入
	athleteName=$("#name_"+id).html();
	if(athleteName.indexOf("<br>")!=-1){
		if(confirm("本项接力["+shortName+"]队已录入棒次名单\n\n确认要再次录入以覆盖原数据吗？")===false){
			return;
		}
	}

	// 再分别输入每棒姓名
	name1=prompt("请输入["+shortName+"]队的第一棒运动员姓名");
	if(name1=="" || name1==null){
		alert("未输入第一棒运动员姓名！\n录入失败，数据未保存！");
		return false;
	}

	name2=prompt("请输入["+shortName+"]队的第二棒运动员姓名");
	if(name2=="" || name2==null){
		alert("未输入第二棒运动员姓名！\n录入失败，数据未保存！");
		return false;
	}

	name3=prompt("请输入["+shortName+"]队的第三棒运动员姓名");
	if(name3=="" || name3==null){
		alert("未输入第三棒运动员姓名！\n录入失败，数据未保存！");
		return false;
	}

	name4=prompt("请输入["+shortName+"]队的第四棒运动员姓名");
	if(name4=="" || name4==null){
		alert("未输入第四棒运动员姓名！\n录入失败，数据未保存！");
		return false;
	}

	// 组合
	name=name1+" "+name2+"<br>"+name3+" "+name4;

	// 请求修改
	$.ajax({
		url:"<?=ROOT_PATH;?>admin/order/toImportRelayStickName.php",
		type:"POST",
		data:{"id":id,"name":name},
		dataType:"JSON",
		success:function(ret){
			if(ret.code==200){
				alert("修改成功！");
				$("#name_"+id).html(name);
			}else{
				console.log(ret);
				alert("修改失败！！！\n棒次名单未被保存！");
			}
		}
	})
}
</script>

</body>
</html>
