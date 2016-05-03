<?php
// setup files to include
include  dirname(__FILE__).'/../config.inc.php';
require dirname(__FILE__).'/smarty/Smarty.class.php';
require dirname(__FILE__).'/session.class.php';
require dirname(__FILE__).'/otp.class.php';
require dirname(__FILE__).'/users.class.php';
require dirname(__FILE__).'/db.class.php';

// initialize smarty engine
$smarty = new Smarty;
$smarty->force_compile = SMARTY_FORCE_COMPILE;
$smarty->debugging = SMARTY_DEBUGGING;
$smarty->caching = SMARTY_CACHING;
$smarty->cache_lifetime = SMARTY_CACHE_LIFETIME;
$smarty->setTemplateDir(SMARTY_DIR_TEMPLATES)
       ->setCompileDir(SMARTY_DIR_TEMPLATES_C)
       ->setCacheDir(SMARTY_DIR_CACHE)
	   ->assign('NAME',				APP_NAME)
	   ->assign('ROOT',				APP_ROOT);