<?php
session_start();
session_destroy();

$url="login.php";

if(isset($_GET) && $_GET){
	$url.="?returnUrl=".urlencode($_GET['returnUrl']);
}

header("Location:".$url);
?>