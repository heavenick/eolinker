<?php
if (file_exists(realpath('./server/RTP/config/eo_config.php')))
	include_once ('./server/RTP/config/eo_config.php');
defined('ALLOW_REGISTER') or define('ALLOW_REGISTER', TRUE);
defined('ALLOW_UPDATE') or define('ALLOW_UPDATE', TRUE);
defined('WEBSITE_NAME') or define('WEBSITE_NAME', 'eolinker接口管理工具开源版本');
?>
<!DOCTYPE html>
<html data-ng-app="eolinker">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width">
		<meta name="robots" content="noarchive">
		<meta name="author" content="广州银云信息科技有限公司">
		<title data-ng-bind="user.title" data-ng-init='user.title="<?php echo defined('WEBSITE_NAME') ? WEBSITE_NAME : ' '; ?>"'></title>
		<link href="assets/images/fav.ico" rel="shortcut icon">
		<base href="">
		<meta name="renderer" content="webkit">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<link rel="stylesheet" href="styles/app-2da8347606.css">
		<script>
var allowRegister =<?php echo ALLOW_REGISTER ? 'true' : 'false'; ?>;
var allowUpdate =<?php echo ALLOW_UPDATE ? 'true' : 'false'; ?>;</script>
	</head>
	<!--[if lt IE 8]>
	<style>html,body{overflow:hidden;height:100%}</style>
	<div class="tb-ie-updater-layer"></div>
	<div class="tb-ie-updater-box" data-spm="20161112">
	<a href="https://www.google.cn/intl/zh-CN/chrome/browser/desktop/" class="tb-ie-updater-google" target="_blank" data-spm-click="gostr=/tbieupdate;locaid=d1;name=google">谷歌 Chrome</a>
	<a href="http://www.uc.cn/ucbrowser/download/" class="tb-ie-updater-uc" target="_blank" data-spm-click="gostr=/tbieupdate20161112;locaid=d2;name=uc">UC 浏览器</a>"
	</div>
	<![endif]-->

	<body>
		<div ui-view=""></div>
		<eo-navbar></eo-navbar>
		<eo-model></eo-model>
	</body>
	<script src="scripts/app-d0e83dcd2d.js"></script>

</html>