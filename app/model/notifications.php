<?php
if( session::verify() === true ) {
	$error = '';

	$userinfo = users::info($_SESSION['user']);

	// user is authorized, regenerate session
	$token = session::csrf(true);
	session::regenerate();

	$smarty
		->assign('THEME',				APP_THEME)
		->assign('SMARTY_TEMPLATES',	SMARTY_DIR_TEMPLATES)
		->assign('CSRF',				$token,	true)
		->assign('ERROR',				$error)
		->assign('TITLE',				'Notifications')
		->assign('USER',				$userinfo)
		->assign('ADMIN',				($userinfo['acl'] > ACL_ADMIN_MAX || $userinfo['acl'] < ACL_ADMIN_MIN) ? false : true )
		->assign('ALLNOTIFICATIONS',	users::notifications($_SESSION['user'], true))

		->display(APP_THEME . '/notifications.tpl');

} else {

	// this page requires the user to login
	redirect('login');
}