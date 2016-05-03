<?php
define('LIVE', true);
require_once dirname(__FILE__).'/lib/autoload.php';

// get token
$token = isset($_SESSION['csrf']) ? $_SESSION['csrf'] : '';

// get the current page
$page = get_this_page();

if(!empty($page)) {
	if(($page == 'error') && isset($_REQUEST['missing']) && isset($_REQUEST['rewritten'])) { redirect('error?missing=' . filter_input(INPUT_GET, 'missing', FILTER_SANITIZE_SPECIAL_CHARS)); }

	// load the current page if model exists
	if(file_exists('model/' . $page . '.php')) {
		include('model/' . $page . '.php');
		exit(0);
	}
} else {
	// load the home page if $page is empty
	if(file_exists('model/app.php')) {
		include('model/app.php');
		exit(0);
	}
}
// load the error page model if it exists
if(file_exists('model/error.php')) { redirect('error?missing=' . filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS)); }

// display generic page not found if all else fails
echo 'Page not found';

// get page and slug it. removes all stray characters
function get_this_page(){
	$page = '';
	if(isset($_REQUEST['page'])) { $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS); }
	if(!empty($page)) {
		$page = trim(preg_replace('~[^\\pL\d]+~u', '-', $page), '-');
		if (function_exists('iconv')) { $page = iconv('utf-8', 'us-ascii//TRANSLIT', $page); }
		//$page = preg_replace('~[^-\w]+~', '', strtolower($text));
	}
	return $page;
}

function redirect($page = '') {
	header('location: ' . APP_ROOT . $page);
	exit(0);
}