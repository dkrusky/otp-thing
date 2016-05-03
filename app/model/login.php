<?php

if( session::verify() === true ) {

	redirect();

} else {

	$error = '';
	$token = $_SESSION['csrf'];

	if ( !empty($_POST) ) {
		if ( isset($_POST['csrf']) ) {
			// check CSRF token and if match ti token stored in session
			$csrf 		=	trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
			if($csrf != $_SESSION['csrf']) { session::destroy(); $error = 'CSRF Attack Detected'; }

			if( isset($_POST['username']) && isset($_POST['password']) && empty($error) ) {
				// sanitize username and password
				$username	=	trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
				$password	=	trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

				$otp = -1;
				if( isset($_POST['code']) ) {
					// sanitize otp code
					$otp	=	intval(filter_input(INPUT_POST, 'code', FILTER_SANITIZE_NUMBER_INT, array('default' => -1)) );
				}

				// username or password can not be empty
				if( !empty($username) && !(empty($password)) ) {
					// verify user. if account has OTP enabled, and
					// $otp has been filled out in the form, it will validate
					// using the OTP settings as defined in config.inc.php
					// additionally, it will automatically lock out used timeblocks
					// as well as limit attempts (returning false if past) to the
					// value set for OTP_MAX_TRIES
					$auth = users::validate($username, $password, $otp);
					if($auth) {
						// regenerate csrf token
						$token = session::csrf(true);
						session::create( $username );

						// redirect back to application root
						redirect();
					}
				}
				$error = 'Invalid credentials';
			} else {
				if(empty($error)) { $error = 'Invalid credentials'; }
			}

			// user was not authenticated, regenerate csrf token to prevent form spam
			$token = session::csrf(true);

		} else {
			// CSRF token did not match stored token in session
			$error = 'CSRF Attack Detected';
		}
	}


	$smarty
		->assign('THEME',				APP_THEME)
		->assign('SMARTY_TEMPLATES',	SMARTY_DIR_TEMPLATES)
		->assign('CSRF',				$token,	true)
		->assign('ERROR',				$error)
		->assign('TITLE',				'Login')

		->display(APP_THEME . '/login.tpl');

}