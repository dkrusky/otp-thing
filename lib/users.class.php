<?php
if(!defined('LIVE')) { exit(); };
/*
#
# Authentication class library for PHP5
#
# This class works closely with my otp class and requires MySQL/MariaDB
#
# This class simplifies the creation of OTP qr codes and their keys
*/

class users {
	public static function createdb() {
		$sql = "CREATE TABLE IF NOT EXIST `" . SQL_PREFIX . "users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `enabled` int(1) NOT NULL DEFAULT '0' COMMENT '0 = disabled, 1 = enabled', `otp_key` varchar(32) DEFAULT '', `otp_last_time` int(11) NOT NULL DEFAULT '0', `otp_last_time_count` int(1) NOT NULL DEFAULT '0', `acl` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
		$result = false;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		if($db->query($sql) === true) {
			$result = true;
		}
		$db->close();
		return $result;
	}

	// add a new user
	public static function add($username, $password, $acl=0) {
		$result = true;

		// add admin user to table if it doesn't exist
		$sql = "INSERT IGNORE INTO `" . SQL_PREFIX . "users` VALUES (null, ?, ?, '1', null, '0', '0', ?);";
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		$db_stmt = $db->prepare($sql);
		$db_stmt->bind_param('ssi',
			$admin_user,
			password_hash($admin_pass, PASSWORD_DEFAULT),
			$acl
		);
		$db_stmt->execute();
		if($db_stmt->errno) { $result = false; }
		$db_stmt->close();
		$db->close();

		return $result;
	}

	// add authenticator and generate scratch codes AND check otp code (recommended)
	public static function addotp($username, $password, $otp, $code) {
		$result = false;

		$info = self::info($username);
		if(password_verify($password, $info['password'])) {
			otp::SetSecret($username, $otp);
			otp::$algorithm = OTP_ALGORITHM;
			otp::$digits	= OTP_LENGTH;
			otp::$company	= OTP_COMPANY;
			$otpcode = otp::GetCode();
			if(str_pad(intval($code), OTP_LENGTH, '0', STR_PAD_LEFT) == $otpcode->code) {
				// generate 3 scratch codes
				$code1 = self::random_scratch_code();
				$code2 = self::random_scratch_code();
				$code3 = self::random_scratch_code();

				$scratch_code_list = password_hash($code1, PASSWORD_DEFAULT) . "\n"
								   . password_hash($code2, PASSWORD_DEFAULT) . "\n"
								   . password_hash($code3, PASSWORD_DEFAULT);

				// add admin user to table if it doesn't exist
				$sql = "UPDATE `" . SQL_PREFIX . "users` SET `" . SQL_PREFIX . "users`.`otp_key` = ? , `" . SQL_PREFIX . "users`.`otp_scratch_codes` = ?  WHERE `" . SQL_PREFIX . "users`.`id` = ? AND `" . SQL_PREFIX . "users`.`username` = ? LIMIT 1;";
				$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
				if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
				$db_stmt = $db->prepare($sql);
				$db_stmt->bind_param('ssis',
					$otp,
					$scratch_code_list,
					$info['id'],
					$username
				);
				$db_stmt->execute();
				if(!$db_stmt->errno) {
					$result = Array(
						'code1'	=>	$code1,
						'code2'	=>	$code2,
						'code3'	=>	$code3
					);
				}
				$db_stmt->close();
				$db->close();
			}
		}
		return $result;
	}

	// remove authenticator from account
	private static function remove_otp_internal($username) {
		$result = false;

		$info = self::info($username);

		// add admin user to table if it doesn't exist
		$sql = "UPDATE `" . SQL_PREFIX . "users` SET `" . SQL_PREFIX . "users`.`otp_key` = null , `" . SQL_PREFIX . "users`.`otp_scratch_codes` = null , `" . SQL_PREFIX . "users`.`otp_last_time` = 0 , `" . SQL_PREFIX . "users`.`otp_last_time_count` = 0  WHERE `" . SQL_PREFIX . "users`.`id` = ? AND `" . SQL_PREFIX . "users`.`username` = ? LIMIT 1;";
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		$db_stmt = $db->prepare($sql);
		$db_stmt->bind_param('is',
			$info['id'],
			$username
		);
		$db_stmt->execute();
		if(!$db_stmt->errno) {
			$result = true;
		}
		$db_stmt->close();
		$db->close();

		return $result;
	}

	// remove authenticator from account after verification
	public static function remove_otp($username, $password) {
		$result = false;

		$info = self::info($username);
		if(password_verify($password, $info['password'])) {
			// add admin user to table if it doesn't exist
			$sql = "UPDATE `" . SQL_PREFIX . "users` SET `" . SQL_PREFIX . "users`.`otp_key` = null , `" . SQL_PREFIX . "users`.`otp_scratch_codes` = null , `" . SQL_PREFIX . "users`.`otp_last_time` = 0 , `" . SQL_PREFIX . "users`.`otp_last_time_count` = 0  WHERE `" . SQL_PREFIX . "users`.`id` = ? AND `" . SQL_PREFIX . "users`.`username` = ? LIMIT 1;";
			$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
			if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
			$db_stmt = $db->prepare($sql);
			$db_stmt->bind_param('is',
								 $info['id'],
								 $username
								);
			$db_stmt->execute();
			if(!$db_stmt->errno) {
				$result = true;
			}
			$db_stmt->close();
			$db->close();

		}

		return $result;
	}

	// generate a secure random 6 digit number
	private static function random_scratch_code() { return str_pad(intval(hexdec(bin2hex(openssl_random_pseudo_bytes ( 1, $cstrong )))), 3, '0', STR_PAD_LEFT) . str_pad(intval(hexdec(bin2hex(openssl_random_pseudo_bytes ( 1, $cstrong )))), 3, '0', STR_PAD_LEFT); }
	
	// change the password for an existing user
	public static function change_password($username, $password, $newpassword) {
		$result = false;
		// get user information
		$info = self::info($username);
		if($info) {
			// ensure password matches existing
			if(password_verify($password, $info['password'])) {
				$sql = "UPDATE `" . SQL_PREFIX . "users` SET `" . SQL_PREFIX . "users`.`password` = ? WHERE `" . SQL_PREFIX . "users`.`id` = ? AND `" . SQL_PREFIX . "users`.`username` = ? LIMIT 1;";
				$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
				if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
				
				// update database with new password
				$db_stmt = $db->prepare($sql);
				$db_stmt->bind_params('sis',
					password_hash($newpassword, PASSWORD_DEFAULT),
					$info['id'],
					$username
				);
				$db_stmt->execute();
				if($db_stmt->errno) {
					$result = false;
					throw new Exception('Database Error: ' . $db_stmt->error);
				}
				$db_stmt->close();
				$db->close();
				$result = true;
				
			}
		}
		return $result;
	}

	// get user information
	public static function info($username) {
		$sql = 'SELECT `' . SQL_PREFIX . 'users`.`id`,  `' . SQL_PREFIX . 'users`.`password`, `' . SQL_PREFIX . 'users`.`otp_key`, `' . SQL_PREFIX . 'users`.`otp_last_time`, `' . SQL_PREFIX . 'users`.`otp_last_time_count`, `' . SQL_PREFIX . 'users`.`acl`, `' . SQL_PREFIX . 'users`.`otp_scratch_codes` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`enabled` = 1 AND `' . SQL_PREFIX . 'users`.`username` = ? LIMIT 1;';
		$result = false;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		$db_stmt = $db->prepare($sql);
		$db_stmt->bind_param("s", $username);
		$db_stmt->execute();
		$db_stmt->store_result();
		$db_stmt->bind_result($user_id, $user_password, $user_otp_key, $user_otp_last_time, $user_otp_last_time_count, $user_acl, $user_otp_scratch_codes);
		if($db_stmt->fetch()) {
			$result = Array(
				'id'		=>	$user_id,
				'username'	=>	$username,
				'acl'		=>	$user_acl,
				'otp'		=>	$user_otp_key,
				'password'	=>	$user_password
			);
		}
		$db_stmt->free_result();
		$db_stmt->close();
		$db->close();
		return $result;
	}

	// validate login, and if otp exists verify otp code, etc etc.
	public static function validate($username, $password, $otp) {
		$sql = 'SELECT `' . SQL_PREFIX . 'users`.`id`,  `' . SQL_PREFIX . 'users`.`password`, `' . SQL_PREFIX . 'users`.`otp_key`, `' . SQL_PREFIX . 'users`.`otp_last_time`, `' . SQL_PREFIX . 'users`.`otp_last_time_count`, `' . SQL_PREFIX . 'users`.`acl`, `' . SQL_PREFIX . 'users`.`otp_scratch_codes` FROM `' . SQL_PREFIX . 'users` WHERE `' . SQL_PREFIX . 'users`.`enabled` = 1 AND `' . SQL_PREFIX . 'users`.`username` = ? LIMIT 1;';
		$result = false;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		$db_stmt = $db->prepare($sql);
		$db_stmt->bind_param("s", $username);
		$db_stmt->execute();
		$db_stmt->store_result();
		$db_stmt->bind_result($user_id, $user_password, $user_otp_key, $user_otp_last_time, $user_otp_last_time_count, $user_acl, $user_otp_scratch_codes);
		if($db_stmt->fetch()) {
			if(password_verify($password, $user_password)) {
				// if otp key found
				if($user_otp_key != '') {
					otp::SetSecret($username, $user_otp_key);
					otp::$algorithm = OTP_ALGORITHM;
					otp::$digits	= OTP_LENGTH;
					otp::$company	= OTP_COMPANY;
					$code = otp::GetCode();

					// set timeblock to current timeblock and reset attempts
					if($user_otp_last_time < $code->timeblock) {
						self::otp_reset_count($user_id, $code->timeblock);
						$user_otp_last_time_count = 1;
					}

					// increment attempts and validate otp code
					if($user_otp_last_time_count < OTP_MAX_TRIES) {
						$user_otp_last_time_count++;
						self::otp_count_update($user_id, $user_otp_last_time_count);

						if(str_pad(intval($otp), OTP_LENGTH, '0', STR_PAD_LEFT) == $code->code) {
							$result = Array(
								'id'		=>	$user_id,
								'username'	=>	$username,
								'acl'		=>	$user_acl
							);

							// disable use of this code since it was successful
							self::otp_prevent_last_code($user_id, $code->timeblock);
						} else {
							// check if OTP value is a scratch code
							$otp_scratch_code = explode("\n", $otp_scratch_codes);
							$scratch_valid = false;
							foreach($otp_scratch_code as $key => $value ) {
								if(password_verify($otp, $value)) {
									// scratch code found
									$scratch_valid = true;
									break;
								}
							}
							if($scratch_valid === true) {
								// allow the user to login
								$result = Array(
									'id'		=>	$user_id,
									'username'	=>	$username,
									'acl'		=>	$user_acl
								);
								
								// remove authenticator from account
								self::remove_otp_internal($username);
							}
							
						}
					}
				} else {
					$result = Array(
						'id'		=>	$user_id,
						'username'	=>	$username,
						'acl'		=>	$user_acl
					);
				}
			}
		}
		$db_stmt->free_result();
		$db_stmt->close();
		$db->close();
		return $result;
	}

	// set current otp timeblock to new timeblock and reset attempts
	public static function otp_reset_count($userid, $timeblock) {
		$result = true;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ $result = false; throw new Exception("Connection Failed: " . $db->connect_error); }
		if($result === true) {
			$sql = 'UPDATE `' . SQL_PREFIX . 'users` SET `' . SQL_PREFIX . 'users`.`otp_last_time` = ? , `' . SQL_PREFIX . 'users`.`otp_last_time_count` = 1 WHERE `' . SQL_PREFIX . 'users`.`id` = ? LIMIT 1;';
			$db_stmt = $db->prepare($sql);
			$db_stmt->bind_param('ii', $timeblock, $userid);
			$db_stmt->execute();
			if($db_stmt->errno) {
				$result = false;
				throw new Exception('Database Error: ' . $db_stmt->error);
			}
			$db_stmt->close();
		}
		$db->close();
		return $result;
	}

	// change timeblock to timeblock+1 effectively disabling use of that timeblock
	public static function otp_prevent_last_code($userid, $timeblock) {
		$result = true;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ $result = false; throw new Exception("Connection Failed: " . $db->connect_error); }
		if($result === true) {
			$timeblock++;
			$sql = 'UPDATE `' . SQL_PREFIX . 'users` SET `' . SQL_PREFIX . 'users`.`otp_last_time` = ? , `' . SQL_PREFIX . 'users`.`otp_last_time_count` = 1 WHERE `' . SQL_PREFIX . 'users`.`id` = ? LIMIT 1;';
			$db_stmt = $db->prepare($sql);
			$db_stmt->bind_param('ii', $timeblock, $userid);
			$db_stmt->execute();
			if($db_stmt->errno) {
				$result = false;
				throw new Exception('Database Error: ' . $db_stmt->error);
			}
			$db_stmt->close();
		}
		$db->close();
		return $result;
	}

	// set current otp try in database to specific value
	public static function otp_count_update($userid, $attempts) {
		$result = true;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ $result = false; throw new Exception("Connection Failed: " . $db->connect_error); }
		if($result === true) {
			$sql = 'UPDATE `' . SQL_PREFIX . 'users` SET `' . SQL_PREFIX . 'users`.`otp_last_time_count` = ? WHERE `' . SQL_PREFIX . 'users`.`id` = ? LIMIT 1;';
			$db_stmt = $db->prepare($sql);
			$db_stmt->bind_param('dd', $attempts, $userid);
			$db_stmt->execute();
			if($db_stmt->errno) {
				$result = false;
				throw new Exception('Database Error: ' . $db_stmt->error);
			}
			$db_stmt->close();
		}
		$db->close();
		return $result;
	}

}