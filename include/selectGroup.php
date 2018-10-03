<?php
/**
 * @name 生蚝体育比赛管理系统-Web-分组选择框
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-10
 * @update 2018-09-22
 */

$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$groupSql="SELECT DISTINCT(group_name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY group_name";
$groupQuery=PDOQuery($dbcon,$groupSql,[$gamesId],[PDO::PARAM_INT]);
$nameSql="SELECT DISTINCT(name) FROM item WHERE games_id=? AND is_delete=0 ORDER BY name DESC";
$nameQuery=PDOQuery($dbcon,$nameSql,[$gamesId],[PDO::PARAM_INT]);

if($groupQuery[1]<1 || $nameQuery[1]<1){
	die('<script>alert("暂未录入日程，敬请期待！");history.go(-1);</script>');
}

$gamesKind=$_SESSION['swim_gamesJson']['kind'];

?>

<!-- 组别分类选择框 -->
<div class="col-xs-7">
	<div class="form-group">
		<select id="groupName" class="form-control">
			<?php foreach($groupQuery[0] as $groupInfo){ ?>
			<option value="<?=$groupInfo['group_name'];?>"><?=$groupInfo['group_name'];?></option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="col-xs-5">
	<div class="form-group">
		<select id="sex" class="form-control">
			<option value="男子">男子</option>
			<option value="女子">女子</option>
			<option value="男女">男女</option>
		</select>
	</div>
</div>
<br>
<?php if($gamesKind=="田径"){ ?>
<div class="col-xs-7">
<?php }else{ ?>
<div class="col-xs-12">
<?php } ?>
	<div class="form-group">
		<select id="name" class="form-control">
			<?php foreach($nameQuery[0] as $nameInfo){ ?>
			<option value="<?=$nameInfo['name'];?>"><?=$nameInfo['name'];?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if($gamesKind=="田径"){ ?>
<div class="col-xs-5">
	<div class="form-group">
		<select id="isFinal" class="form-control">
			<option value="0">预赛</option>
			<option value="1">决赛</option>
		</select>
	</div>
</div>
<?php } ?>
<!-- ./组别分类选择框 -->

<p style="line-height:5px;">&nbsp;</p>
