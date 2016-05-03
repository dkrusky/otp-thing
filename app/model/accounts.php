<?php
if( session::verify() === true ) {
	$error = '';

	$userinfo = users::info($_SESSION['user']);

	// if acl is not authoritave for administrating lower accounts, deny access to this suite of tools and display an error
	if($userinfo['acl'] > ACL_ADMIN_MAX || $userinfo['acl'] < ACL_ADMIN_MIN) { redirect('error'); }

	$show = isset($_REQUEST['show']) ? trim(filter_input(INPUT_GET, 'show', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))) : 'list';


	switch($show) {
		case 'list':
			$sql = 'SELECT `' . SQL_PREFIX . 'users`.`id`,  `' . SQL_PREFIX . 'users`.`username`, `' . SQL_PREFIX . 'users`.`enabled`, `' . SQL_PREFIX . 'users`.`otp_key`, `' . SQL_PREFIX . 'users`.`otp_last_time`, `' . SQL_PREFIX . 'users`.`acl`, `' . SQL_PREFIX . 'users`.`name`, `' . SQL_PREFIX . 'users`.`email` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`acl` < ' . $userinfo['acl'] . ' ORDER BY `' . SQL_PREFIX . 'users`.`id` DESC;';
			$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
			if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
			$resource = $db->query($sql);
			for ($result = array(); $tmp = $resource->fetch_array(MYSQLI_ASSOC);) $result[] = $tmp;
			$db->close();
			$smarty->assign('USERS',	$result);
			break;
		case 'add':
			if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			if($token != $_SESSION['csrf']) {
				session::destroy();
				$error = 'CSRF Attack Detected';
			}

			if(empty($error)) {
				if($_POST) {
					$username	= trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$password	= trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$acl		= trim(filter_input(INPUT_POST, 'acl', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$name		= trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$email		= trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

					if(!empty($username) && !empty($password) && (!empty($acl) || $acl == 0)) {
						if($acl >= $userinfo['acl']) {
							$error = 'Insufficient permission to grant this access level';
						} else {
							$id = users::add($username, $password, $acl);
							if(!empty($id)) {
								if(!empty($name) || !empty($email)) {
									$db = new db();

									$params = '';
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

									$db->add($id, 'i');
									$db->add($username, 's');
									$result = $db->query("UPDATE `" . SQL_PREFIX . "users` SET " . $params . " WHERE `" . SQL_PREFIX . "users`.`id` = ? AND `" . SQL_PREFIX . "users`.`username` = ? LIMIT 1;");
								}
							} else {
								$error = 'Username already exists';
							}
						}
						if(empty($error)) {
							// $smarty->assign('SUCCESS', "Added [" . $id . "] " . $username);
							$token = session::csrf(true);
							session::regenerate();
							redirect('accounts?show=added&id=' . md5($id) . '&csrf=' . $token);
						}
					} else {
						$error = 'Missing required fields';
					}
				}

			} else {
				redirect('error');
			}
			break;
		case 'remove':
			if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			if($token != $_SESSION['csrf']) {
				session::destroy();
				$error = 'CSRF Attack Detected';
			}

			$id = isset($_REQUEST['id']) ? trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))) : '';
			$confirm = isset($_REQUEST['confirm']) ? trim(filter_input(INPUT_GET, 'confirm', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))) : false;
			if($confirm == '1') { $confirm = true; }

			if(empty($error)) {
				if($confirm === true) {
					if(!empty(trim($id))) {
						$db = new db();
						$db->add($userinfo['acl'], 'i');
						$db->add($id, 's');
						if($result = $db->query(
							'DELETE FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`acl` < ? AND md5(`' . SQL_PREFIX . 'users`.`id`) = ? LIMIT 1'
						)) {
							redirect('accounts?show=removed');
							// $success = 'Record was removed';
						} else {
							$error = 'Record could not be removed';
						}
					} else {
						$error = 'Invalid ID';
					}
				} else {
					$db = new db();
					$sql = 'SELECT `' . SQL_PREFIX . 'users`.`username`,  `' . SQL_PREFIX . 'users`.`email`, `' . SQL_PREFIX . 'users`.`name`, `' . SQL_PREFIX . 'users`.`acl` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`acl` < ? AND md5(`' . SQL_PREFIX . 'users`.`id`) = ? LIMIT 1;';
					$db->add($userinfo['acl'], 'i');
					$db->add($id, 's');

					if($result = $db->query($sql)) {
						$smarty->assign('RUSER', $result[0]);
					}
				}
			} else {
				redirect('error');
			}
			break;
		case 'added':
			if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			if($token != $_SESSION['csrf']) {
				session::destroy();
				$error = 'CSRF Attack Detected';
			}
			$id = isset($_REQUEST['id']) ? trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))) : '';
			if(!empty(trim($id)) && empty($error) ) {
				$db = new db();
				$sql = 'SELECT `' . SQL_PREFIX . 'users`.`username`,  `' . SQL_PREFIX . 'users`.`email`, `' . SQL_PREFIX . 'users`.`name`, `' . SQL_PREFIX . 'users`.`acl` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`acl` < ? AND md5(`' . SQL_PREFIX . 'users`.`id`) = ? LIMIT 1;';
				$db->add($userinfo['acl'], 'i');
				$db->add($id, 's');
				if($result = $db->query($sql)) {
					$smarty->assign('RUSER', $result[0]);
				}
			} else {
				redirect('error');
			}
			break;
		case 'removed':
			break;
		case 'modify':
			if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
			if($token != $_SESSION['csrf']) {
				session::destroy();
				$error = 'CSRF Attack Detected';
			}
			$id = isset($_REQUEST['id']) ? trim(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))) : '';
			if(!empty(trim($id)) && empty($error) ) {
				if($_POST) {
					$username	= trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$password	= trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$acl		= trim(filter_input(INPUT_POST, 'acl', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$name		= trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
					$email		= trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

					if($acl >= $userinfo['acl']) {
						$error = 'Insufficient permission to grant this access level';
					} else {
						if(!empty($acl) || $acl == 0) {
							$db = new db();

							$params = '';
							if(!empty($username)) {
								$db->add($name, 's');
								if(!empty($params)) { $params .= ', '; }
								$params .= '`' . SQL_PREFIX . 'users`.`username` = ? ';
							}
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

							if(!empty($acl)) {
								$db->add($acl, 'i');
								if(!empty($params)) { $params .= ', '; }
								$params .= '`' . SQL_PREFIX . 'users`.`acl` = ? ';
							}

							if(!empty($params)) {
								$db->add($userinfo['acl'], 'i');
								$db->add($id, 's');
								if($result = $db->query('UPDATE `' . SQL_PREFIX . 'users` SET ' . $params . '  WHERE `' . SQL_PREFIX . 'users`.`acl` < ? AND md5(`' . SQL_PREFIX . 'users`.`id`) = ? LIMIT 1;')) {
									$smarty->assign('SUCCESS', 'Record updated');
								} else {
									$error = 'Record was not updated';
								}

							} else {
								$error = 'No fields to update';
							}

						}
					}

				}

				$db = new db();
				$sql = 'SELECT `' . SQL_PREFIX . 'users`.`username`,  `' . SQL_PREFIX . 'users`.`email`, `' . SQL_PREFIX . 'users`.`name`, `' . SQL_PREFIX . 'users`.`acl` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`acl` < ? AND md5(`' . SQL_PREFIX . 'users`.`id`) = ? LIMIT 1;';
				$db->add($userinfo['acl'], 'i');
				$db->add($id, 's');
				if($result = $db->query($sql)) {
					$smarty->assign('RUSER', $result[0]);
				}
			} else {
				redirect('error');
			}
			break;
		default:
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
		->assign('TITLE',				'Accounts')
		->assign('USER',				$userinfo)
		->assign('SHOW',				$show)
		->assign('ADMIN',				($userinfo['acl'] > ACL_ADMIN_MAX || $userinfo['acl'] < ACL_ADMIN_MIN) ? false : true )
		->assign('NOTIFICATIONS',		users::notifications($_SESSION['user']))

		->display(APP_THEME . '/accounts.tpl');

} else {

	// this page requires the user to login
	redirect('login');
}