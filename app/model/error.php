<?php

$message	= 'Bad Request';
$code		= 400;
if(isset($_REQUEST['missing']) || defined('PAGE_GONE')) {
	$message	= "Gone";
	$code		= 410;
}
header("HTTP/1.0 " . $code . " " . $message);
$smarty
	->assign('THEME',				APP_THEME)
	->assign('SMARTY_TEMPLATES',	SMARTY_DIR_TEMPLATES)
	->assign('TITLE',				$message)
	->assign('CODE',				$code)
	->display(APP_THEME . '/error.tpl');
