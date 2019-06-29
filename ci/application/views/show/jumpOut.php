<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view('include/header'); ?>
  <title><?=$this->Setting_model->get('systemName');?></title>
</head>

<body>
<div id="wrapper">

<?php $this->load->view('include/navbar'); ?>

<div id="page-wrapper">
<!-- Page Content -->

<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header"><?=$name!=""?$name:"页面跳转";?></h1>
  </div>
</div>

<a href="<?=$url;?>" target="_blank" class="btn btn-success" style="width: 98%">请 点 此 手 动 跳 转</a>
<br><br>
<a onclick="history.go(-1);" class="btn btn-primary" style="width: 98%">返 回 上 一 页</a>

<?php $this->load->view('include/footer'); ?>

<!-- ./Page Content -->
</div>
</div>
</body>
</html>
