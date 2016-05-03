<?php
if( session::verify() === true ) {
	$error = '';

//	$cc = '5523960016075626';
//	$csv = '332';
//	$exp = '2017/08';
//	echo password_hash($cc, PASSWORD_DEFAULT) . ' | ' . substr($cc, -4) . ' | ' . $exp;
//	exit(0);

	$userinfo = users::info($_SESSION['user']);

	$token = '';

	if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
	elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
	if($token != $_SESSION['csrf']) {
		session::destroy();
		$error = 'CSRF Attack Detected';
	}
	if( empty($error) ) {
		$db = new db();
		if($_POST) {
			$password	= trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
			$name		= trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
			$email		= trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

			$params = '';
			if(!empty($password)) {
				$db->add(password_hash($password, PASSWORD_DEFAULT), 's');
				if(!empty($params)) { $params .= ', '; }
				$params .= '`' . SQL_PREFIX . 'users`.`password` = ? ';
			}
			if(!empty($name)) {
				$db->add($name, 's');
				if(!empty($params)) { $params .= ', '; }
				$params .= '`' . SQL_PREFIX . 'users`.`name` = ? ';
			}
			if(!empty($email)) {
				$db->add($email, 's');
				if(!empty($params)) { $params .= ', '; }
				$params .= '`' . SQL_PREFIX . 'users`.`email` = ? ';
			}

			if(!empty($params)) {
				$db->add($userinfo['username'], 's');
				if($result = $db->query('UPDATE `' . SQL_PREFIX . 'users` SET ' . $params . '  WHERE `' . SQL_PREFIX . 'users`.`username` = ? LIMIT 1;')) {
					$smarty->assign('SUCCESS', 'Record updated');
					$userinfo = users::info($_SESSION['user']);
				} else {
					$error = 'Record was not updated';
				}

			} else {
				$error = 'No fields to update';
			}

		}

		$sql = 'SELECT `' . SQL_PREFIX . 'users`.`email`, `' . SQL_PREFIX . 'users`.`name` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`username` = ? LIMIT 1;';
		$db->add($userinfo['username'], 's');
		if($result = $db->query($sql)) {
			$smarty->assign('RUSER', $result[0]);
		}
	} else {
		redirect('error');
	}

	// user is authorized, regenerate session
	$token = session::csrf(true);
	session::regenerate();

	$smarty
		->assign('THEME',				APP_THEME)
		->assign('SMARTY_TEMPLATES',	SMARTY_DIR_TEMPLATES)
		->assign('CSRF',				$token,	true)
		->assign('ERROR',				$error)
		->assign('TITLE',				'Settings')
		->assign('USER',				$userinfo)
		->assign('ADMIN',				($userinfo['acl'] > ACL_ADMIN_MAX || $userinfo['acl'] < ACL_ADMIN_MIN) ? false : true )
		->assign('NOTIFICATIONS',		users::notifications($_SESSION['user']))

		->display(APP_THEME . '/settings.tpl');

} else {

	// this page requires the user to login
	redirect('login');
}