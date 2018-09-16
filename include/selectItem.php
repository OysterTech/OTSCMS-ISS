<?php
$gamesInfo=isset($_SESSION['swim_gamesInfo'])?$_SESSION['swim_gamesInfo']:goToIndex();
$gamesId=$gamesInfo['id'];
$sceneList=PDOQuery($dbcon,"SELECT DISTINCT(scene) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);
$orderIndexList=PDOQuery($dbcon,"SELECT DISTINCT(order_index) FROM item WHERE games_id=? AND is_delete=0",[$gamesId],[PDO::PARAM_INT]);

if($sceneList[1]<1 || $orderIndexList[1]<1){
	die('<script>alert("暂未录入日程，敬请期待！");history.go(-1);</script>');
}
?>

<!-- 项次选择框 -->
<div class="col-xs-6">
	<div class="input-group">
		<span class="input-group-addon">第</span>
		<select id="scene" class="form-control">
			<?php foreach($sceneList[0] as $sceneInfo){ ?>
				<option value="<?=$sceneInfo['scene'];?>"><?=$sceneInfo['scene'];?></option>
			<?php } ?>
		</select>
		<span class="input-group-addon">场</span>
	</div>
</div>
<div class="col-xs-6">
	<div class="input-group">
		<span class="input-group-addon">第</span>
		<select id="orderIndex" class="form-control">
			<?php foreach($orderIndexList[0] as $orderIndexInfo){ ?>
				<option value="<?=$orderIndexInfo['order_index'];?>"><?=$orderIndexInfo['order_index'];?></option>
			<?php } ?>
		</select>
		<span class="input-group-addon">项</span>
	</div>
</div>
<!-- ./项次选择框 -->

<p style="line-height:5px;">&nbsp;</p>
