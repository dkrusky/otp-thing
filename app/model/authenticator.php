<?php
if( session::verify() === true ) {
	// user is authorized, regenerate session
	session::regenerate();

	// you should verify the token on each request through post or get.
	// if posting as part of a form
	$token = '';
	$error = '';
	if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
	elseif( isset($_REQUEST['csrf']) ) { $token = trim(filter_input(INPUT_GET, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
	if($token != $_SESSION['csrf']) {
		session::destroy();
		$error = 'CSRF Attack Detected';
	}

	if(empty($error)) {
		// regenerate csrf token
		$newtoken = session::csrf(true);

		// get information about logged in user
		$userinfo = users::info($_SESSION['user']);

		if(empty($userinfo['otp'])) {
			// display add authenticator form

			// OTP Stuff
			otp::$algorithm = OTP_ALGORITHM;
			otp::$digits	= OTP_LENGTH;
			otp::$company	= OTP_COMPANY;
			$otp = isset($_SESSION['otp']) ? $_SESSION['otp'] : otp::GenerateSecret( $_SESSION['user'] );
			$_SESSION['otp'] = $otp;
			otp::SetSecret($_SESSION['user'], $otp);

			// regenerate and save session data
			session::regenerate();

			// check if form was submitted, and if so, grab values
			$scratch_codes = '';
			if(!empty($_POST)) {
				$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
				$otpcode = trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

				// get scratch codes
				$scratch_codes = users::addotp( $_SESSION['user'], $password, $otp, $otpcode );
			}

			if(empty($scratch_codes)) {
				$qrcode = otp::GenerateQRCode();

				if(!empty($_POST)) { $error = 'Invalid Credentials'; }

				// display authenticator form
				$smarty
					->assign('QRCODE',				$qrcode['image'])
					->assign('TITLE',				'Authenticator | Add')
					->assign('FORM',				'add');
			} else {
				// show scratch codes
				$smarty
					->assign('CODES',				$scratch_codes)
					->assign('TITLE',				'Authenticator | Scratch Codes')
					->assign('FORM',				'scratch');
			}

		} else {
			// display remove authenticator form
			$success = false;
			if(!empty($_POST)) {
				$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
				$otpcode  = trim(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

				if(!empty($password) && !empty($otpcode)) {
					otp::$algorithm = OTP_ALGORITHM;
					otp::$digits	= OTP_LENGTH;
					otp::$company	= OTP_COMPANY;
					otp::SetSecret($_SESSION['user'], $userinfo['otp']);
					$code = otp::GetCode();

					if($code->code == $otpcode) {
						// remove authenticator
						if(users::remove_otp($userinfo['username'], $password)) {
							$success = true;
						}
					} else {
						// will automatically remove authenticator if code is scratch code
						if(users::validate($userinfo['username'], $password, $otpcode) === false) {
							$error = 'Invalid Credentials';
						} else {
							$success = true;
						}
					}
				}
			}

			if($success === true) {
				$smarty
					->assign('TITLE',				'Authenticator | Removed')
					->assign('FORM',				'removed');
			} else {
				$smarty
					->assign('TITLE',				'Authenticator | Remove')
					->assign('FORM',				'remove');
			}


		}

		$smarty
			->assign('THEME',				APP_THEME)
			->assign('SMARTY_TEMPLATES',	SMARTY_DIR_TEMPLATES)
			->assign('CSRF',				$newtoken,	true)
			->assign('ERROR',				$error)
			->assign('USER',				$userinfo)
			->assign('ADMIN',				($userinfo['acl'] > ACL_ADMIN_MAX || $userinfo['acl'] < ACL_ADMIN_MIN) ? false : true )
			->assign('NOTIFICATIONS',		users::notifications($_SESSION['user']))

			->display(APP_THEME . '/authenticator.tpl');
		exit(0);

	} else {
		echo $error;
	}

} else {
	// this page requires the user to login
	redirect('login');
}