<?php
define('LIVE', true);

include('config.inc.php');
require('lib/session.class.php');
require('lib/otp.class.php');
require('lib/users.class.php');

try {

	if(isset($_REQUEST['logout'])) {
		// if '?logout=true' supplied, execute logout and redirect to root of application
		session::destroy();
		header('location: ' . APP_ROOT);
		exit(0);
	}

	// if session is still valid (user is still logged in)
	if( session::verify() === true ) {
		// regenerate session
		session::regenerate();

		$page = '';
		if(isset($_REQUEST['page'])) {
			$page = trim(filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
		}

		// you should verify the token on each request through post or get.
		// if posting as part of a form
		$token = "";
		if(isset($_POST['csrf'])) { $token = trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
		elseif( isset($_REQUEST['token']) ) { $token = trim(filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH))); }
		if($token != $_SESSION['csrf']) {
			session::destroy();
			throw new Exception("CSRF Attack Detected");
		}
		$newtoken = session::csrf(true);

		// get information about logged in user
		$userinfo = users::info($_SESSION['user']);

		// write your application logic and handler stuff here
		// case select for which page you are on (simple test for multiple pages)
		switch($page) {
			case 'otp':
				if(trim($userinfo['otp'] . '') == '') {
					// OTP Stuff
					otp::$algorithm = OTP_ALGORITHM;
					otp::$digits	= OTP_LENGTH;
					otp::$company	= OTP_COMPANY;
					$otp = isset($_SESSION['otp']) ? $_SESSION['otp'] : otp::GenerateSecret( $_SESSION['user'] );
					$_SESSION['otp'] = $otp;
					otp::SetSecret($_SESSION['user'], $otp);

					// regenerate and save session data
					session::regenerate();
					if(isset($_SESSION['otp'])) {
						$scratch_codes = false;
						if(!empty($_POST)) {
							$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
							$otpcode = trim(filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
							$scratch_codes = users::addotp( $_SESSION['user'], $password, $otp, $otpcode );
						}
						if($scratch_codes) {
							// display scratch codes
							var_export($scratch_codes);
							echo str_replace(
								Array(
									'URLHOME',
									'CSRFVALUE',
									'CODE1',
									'CODE2',
									'CODE3'
								),
								Array(
									APP_ROOT,
									session::csrf(),
									$scratch_codes['code1'],
									$scratch_codes['code2'],
									$scratch_codes['code3']
								),
								file_get_contents('view/otp_scratch_codes.html')
							);
						} else {
							// display add qr form
							$img = otp::GenerateQRCode();
							echo str_replace(
								Array(
									'URLHOME',
									'CSRFVALUE',
									'QRCODE'
								),
								Array(
									APP_ROOT,
									session::csrf(),
									$img['image']
								),
								file_get_contents('view/otp_verify.html')
							);
						}
					}
				} else {
					$removed = false;
					otp::$algorithm = OTP_ALGORITHM;
					otp::$digits	= OTP_LENGTH;
					otp::$company	= OTP_COMPANY;
					otp::SetSecret($userinfo['username'], $userinfo['otp']);

					if(!empty($_POST)) {
						$password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
						$otpcode  = trim(filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
						$code = otp::GetCode();
						if($code->code == $otpcode) {
							if(users::remove_otp($userinfo['username'], $password)) {
								echo str_replace(
									Array(
										'URLHOME',
										'CSRFVALUE'
									),
									Array(
										APP_ROOT,
										session::csrf()
									),
									file_get_contents('view/otp_removed.html')
								);
								exit(0);
							}
						} else {
							echo 'Invalid Credentials';
						}
					}
					echo str_replace(
						Array(
							'URLHOME',
							'CSRFVALUE'
						),
						Array(
							APP_ROOT,
							session::csrf(),
						),
						file_get_contents('view/otp_remove.html')
					);
				}
				break;

			default:
				// write your application logic and handler stuff here
				echo str_replace(
					Array(
						'URLHOME',
						'CSRFVALUE'
					),
					Array(
						APP_ROOT,
						session::csrf(),
					),
					file_get_contents('view/index.html')
				);
		}

	} else {
		if ( !empty($_POST) ) {
			if ( isset($_POST['csrf']) && isset($_POST['username']) && isset($_POST['password']) ) {
				// check CSRF token and if match ti token stored in session
				$csrf 		=	trim(filter_input(INPUT_POST, 'csrf', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
				if($csrf != $_SESSION['csrf']) { session::destroy(); throw new Exception("CSRF Attack Detected"); }

				// sanitize username and password
				$username	=	trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
				$password	=	trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS, array('flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));

				$otp = -1;
				if( isset($_POST['otp']) ) {
					// sanitize otp code
					$otp	=	intval(filter_input(INPUT_POST, 'otp', FILTER_SANITIZE_NUMBER_INT, array('default' => -1)) );
				}

				if($username == '' || $password == '') {
					// username or password can not be empty
					throw new Exception("Invalid Credentials");
				} else {
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
						header('location: ' . APP_ROOT . '?token=' . $token);
						exit();
					}
				}

				// user was not authenticated, regenerate csrf token to prevent form spam
				session::csrf(true);
				echo '<h3>Invalid credentials</h3>';

			} else {
				// CSRF token did not match stored token in session
				throw new Exception('CSRF Attack Detected');
			}
		}

		// display login form
		echo str_replace('CSRFVALUE', session::csrf(), file_get_contents('view/login.html') );

	}

} catch ( Exception $e ) {
	echo $e->getMessage();
}

function exception_handler($e) {
	echo $e->getMessage();
	exit(0);
}
?>