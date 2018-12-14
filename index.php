<?php
/**
 * @name 生蚝体育比赛管理系统-Web-首页
 * @author Jerry Cheung <master@xshgzs.com>
 * @create 2018-08-09
 * @update 2018-12-08
 */

require_once 'include/public.func.php';

$query=PDOQuery($dbcon,"SELECT * FROM games WHERE is_show=1 ORDER BY end_date DESC");
?>

<html>
<head>
	<title>生蚝体育比赛信息查询系统 / 生蚝科技</title>
	<?php include 'include/header.php'; ?>
</head>
<body>
	
<center><img src="<?=IMG_PATH;?>logo.jpg" style="display: inline-block;height: auto;max-width: 100%;" alt="生蚝体育比赛信息查询系统"></center>

<hr>

<table class="table table-hover table-striped table-bordered gamesListTable">
<tr>
	<th style="text-align:center;background-color:#F5FF91;" colspan="2">点击比赛名称可查看详情</th>
</tr>
<tr>
	<th style="text-align:center;">比赛名称</th>
	<th style="text-align:center;">操作</th>
</tr>

<?php
foreach($query[0] as $info){
$info['extra_json']=json_decode($info['extra_json'],true);
?>
<tr id="tr_<?=$info['id'];?>">
	<td style="text-align:left;vertical-align:middle;font-weight:bold;color:green;font-size:16px;" onclick='showDetail("<?=$info['id'];?>","<?=$info['name'];?>","<?=$info['start_date'];?>","<?=$info['end_date'];?>","<?=$info['extra_json']['venue'];?>")'><?=$info['name'];?></td>
	<td style="text-align:center;vertical-align:middle;">
		<a href="<?=ROOT_PATH;?>gamesIndex.php?gamesId=<?=$info['id'];?>" class="btn btn-primary">进入 &gt;</a>
	</td>
</tr>
<?php } ?>
</table>

<?php include 'include/footer.php'; ?>

<div class="modal fade" id="tipsModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true"></span><span class="sr-only">Close</span></button>
				<h3 class="modal-title" id="name"></h3>
			</div>
			<div class="modal-body">
				<table class="table table-hover table-striped table-bordered">
					<tr>
						<th style="text-align:center;vertical-align:middle;">开赛<br>日期</th>
						<td style="text-align:center;vertical-align:middle;" id="startDate"></td>
					</tr>
					<tr>
						<th style="text-align:center;vertical-align:middle;">结束<br>日期</th>
						<td style="text-align:center;vertical-align:middle;" id="endDate"></td>
					</tr>
					<tr>
						<th style="text-align:center;vertical-align:middle;">比赛<br>地点</th>
						<td style="text-align:center;vertical-align:middle;" id="venue"></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" data-dismiss="modal">关闭 &gt;</button>
			</div>
		</div>
	</div>
</div>

<script>
function showDetail(id,name,startDate,endDate,venue){
	$("tr").attr("style","");
	$("#tr_"+id).attr("style","background-color:#FCE4EC;");
	$("#name").html(name);
	$("#startDate").html(startDate);
	$("#endDate").html(endDate);
	$("#venue").html(venue);
	$("#tipsModal").modal("show");
}
</script>

</body>
</html>
