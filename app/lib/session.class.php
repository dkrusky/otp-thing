<?php
if(!defined('LIVE')) { exit(); };
session::start();

class session {

	static $duration = 60 * SESSION_TIMEOUT;

	public static function start() {
		session_start();
		self::csrf(false);
		if(self::verify(false) === true) {
			$_SESSION['expires']	=	time() + self::$duration;
		}
		session_regenerate_id(true);
		$id = session_id();
		session_write_close();
		session_id($id);
		session_start();
	}

	public static function csrf($new = false) {
		if(!isset($_SESSION['csrf']) || $new === true) {
			$_SESSION['csrf']		= sha1(openssl_random_pseudo_bytes(mt_rand(16,32)));
		}
		return $_SESSION['csrf'];
	}

	public static function create($user) {
		$_SESSION['nonce']		=	sha1(microtime(true));
		$_SESSION['ip']			=	$_SERVER['REMOTE_ADDR'];
		$_SESSION['agent']		=	sha1($_SERVER['HTTP_USER_AGENT']);
		$_SESSION['expires']	=	time() + self::$duration;
		$_SESSION['user']		=	$user;
		session_regenerate_id(true);
		$id = session_id();
		session_write_close();
		session_id($id);
		session_start();
	}

	public static function regenerate() {
		$_SESSION['nonce']		=	sha1(microtime(true));
		$_SESSION['expires']	=	time() + self::$duration;
		session_regenerate_id(true);
		$id = session_id();
		session_write_close();
		session_id($id);
		session_start();
	}

	public static function destroy() {
		if(isset($_SESSION['nonce'])) { unset($_SESSION['nonce']); }
		if(isset($_SESSION['ip'])) { unset($_SESSION['ip']); }
		if(isset($_SESSION['agent'])) { unset($_SESSION['agent']); }
		if(isset($_SESSION['expires'])) { unset($_SESSION['expires']); }
		if(isset($_SESSION['user'])) { unset($_SESSION['user']); }
		session_unset();
		session_destroy();
	}

	public static function verify($destroy = false) {
		$valid = true;
		try {
			if( !isset($_SESSION['nonce']) ) { $valid = false; }
			if( !isset($_SESSION['user']) ) { $valid = false; }
			if( isset($_SESSION['ip']) ) { if($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) { $valid = false; } } else { $valid = false; }
			if( isset($_SESSION['agent']) ) { if($_SESSION['agent'] != sha1($_SERVER['HTTP_USER_AGENT']) ) { $valid = false; } } else { $valid = false; }
			if( isset($_SESSION['expires']) ) { if($_SESSION['expires'] <= time()) { $valid = false; } } else { $valid = false; }
		} catch (Exception $e) {
			$valid = false;
		}
		if($valid === false) {
			if(isset($_SESSION['nonce'])) { unset($_SESSION['nonce']); }
			if(isset($_SESSION['ip'])) { unset($_SESSION['ip']); }
			if(isset($_SESSION['agent'])) { unset($_SESSION['agent']); }
			if(isset($_SESSION['expires'])) { unset($_SESSION['expires']); }
			if(isset($_SESSION['user'])) { unset($_SESSION['user']); }
			if($destroy === true) {
				session_unset();
				session_destroy();
			}
		}
		return $valid;
	}

}