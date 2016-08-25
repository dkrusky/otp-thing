<?php
$msg = '';
$errors = Array();

if(!file_exists('config.inc.php')) {
	$msg = 'You need to copy <b>config.inc.php.distro</b> to <b>config.inc.php</b> and set the values for the variables.';
} else {
	require 'config.inc.php';

	$installsql = Array(
		"CREATE TABLE IF NOT EXIST `" . SQL_PREFIX . "users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `enabled` int(1) NOT NULL DEFAULT '0' COMMENT '0 = disabled, 1 = enabled', `otp_key` varchar(32) DEFAULT '', `otp_last_time` int(11) NOT NULL DEFAULT '0', `otp_last_time_count` int(1) NOT NULL DEFAULT '0', `acl` int(11) NOT NULL DEFAULT '0', `name` varchar(255) DEFAULT NULL,`email` varchar(255) DEFAULT NULL, PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;",
		"CREATE TABLE IF NOT EXIST `" . SQL_PREFIX . "notifications` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(255) NOT NULL, `name` varchar(255) DEFAULT NULL, `email` varchar(255) DEFAULT NULL, `message` varchar(255) NOT NULL, `time` datetime DEFAULT NULL, `read` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;",
		"INSERT INTO `" . SQL_PREFIX . "users` VALUES (null, 'admin', '" . password_hash('admin', PASSWORD_DEFAULT) . "', '1', null, '0', '0', 9999, null, null, null);"
	);

	$defines = Array(
		'APP_ROOT'	=> Array('type' => 'string'),
		'APP_THEME'	=> Array('type' => 'string'),
		'APP_NAME'	=> Array('type' => 'string', 'optional' => true),
		'OTP_COMPANY'	=> Array('type' => 'string'),
		'OTP_MAX_TRIES'	=> Array('type' => 'int', 'min' => 2, 'max'=> 10 ),
		'OTP_LENGTH'	=> Array('type' => 'int', 'min' => 4, 'max'=> 10 ),
		'OTP_ALGORITHM'	=> Array('type' => 'string', 'values'=>Array('sha1','sha256','sha512') ),
		'SESSION_TIMEOUT'	=> Array('type' => 'int', 'min' => 0, 'max'=> 525600 ),
		'SQL_SERVER'	=> Array('type' => 'string'),
		'SQL_USERNAME'	=> Array('type' => 'string'),
		'SQL_PASSWORD'	=> Array('type' => 'string', 'optional' => true),
		'SQL_DATA'	=> Array('type' => 'string'),
		'SQL_PREFIX'	=> Array('type' => 'string', 'optional' => true),
		'SMARTY_DEBUGGING'	=> Array('type' => 'bool'),
		'SMARTY_CACHING'	=> Array('type' => 'bool'),
		'SMARTY_CACHE_LIFETIME'	=> Array('type' => 'int', 'min' => 0, 'max'=> 525600 ),
		'SMARTY_FORCE_COMPILE'	=> Array('type' => 'bool'),
		'SMARTY_DIR_TEMPLATES'	=> Array('type' => 'path'),
		'SMARTY_DIR_TEMPLATES_C'	=> Array('type' => 'path'),
		'SMARTY_DIR_CACHE'	=> Array('type' => 'path'),
		'MAX_NOTIFICATIONS'	=> Array('type' => 'int', 'min' => 1, 'max' => 5000)
	);

	$functions = Array(
		'mysqli'	=> 'mysqli_connect',
		'gz'		=> 'gzdecode',
		'GD'		=> 'ImageJPEG',
		'openssl'	=> 'openssl_random_pseudo_bytes',
		'iconv'		=> 'iconv',
		'PHP 5.5+'	=> 'password_hash',
		'sessions'	=> 'session_regenerate_id',
		'unpack'	=> 'unpack'
	);

	$classes = Array(
		'SQLite3'
	);

	// check defines/constants
	$errors = array_merge($errors, install::check_defines($defines));

	// check functions
	$errors = array_merge($errors, install::check_functions($functions));

	// check classes
	$errors = array_merge($errors, install::check_classes($classes));

	if(defined('SQL_SERVER') && defined('SQL_USERNAME') && defined('SQL_PASSWORD') && defined('SQL_DATA')) {
		$dbtest = install::testdb();
		if(!empty($dbtest)) {
			$errors[] = 'Database connection failed: ' . $dbtest;
		}
	}

	if(empty($errors)) {
		// installer can continue
		foreach($installsql as $sql) {
			install::createdb($sql);
		}
	}
}



class install {
	public static function display_error($msg) {
		echo $msg;
		exit(0);
	}

	public static function check_defines($defines) {
		$errors = Array();
		foreach($defines as $define=>$values) {
			if(!defined($define)) {
				$errors[] = '<b>' . $define . '</b> is not defined';
			} else {
				$value = constant($define);
				switch($values['type']) {
					case 'string':
						if(empty($value) && !array_key_exists('optional', $values)) {
							$errors[] = '<b>' . $define . '</b> must have a value set';
						} else {
							if(array_key_exists('values', $values)) {
								if(!in_array($value, $values['values'])) {
									$errors[] = '<b>' . $define . '</b> must be one of : <i>' . implode(', ', $values['values']) . '</i>';
								}
							}
						}
						break;
					case 'int':
						if(!is_int($value)) {
							$errors[] = '<b>' . $define . '</b> must be an integer between <i>' . $values['min'] . '</i> and <i>' . $values['max'] . '</i>';
						} else {
							if($value < $values['min'] || $value > $values['max']) {
								$errors[] = '<b>' . $define . '</b> must be an integer between <i>' . $values['min'] . '</i> and <i>' . $values['max'] . '</i>';
							}
						}
						break;
					case 'bool':
						if(!is_bool($value)) {
							$errors[] = '<b>' . $define . '</b> must be set to either <i>true</i> or <i>false</i>';
						}
						break;
					case 'path':
						if(!is_dir($value)) {
							$errors[] = '<b>' . $define . '</b> must be set to a valid local directory';
						} elseif(!is_writable($value)) {
							$errors[] = '<b>' . $define . '</b> must is set to a path which is not writeable';
						}
						break;
				}
			}
		}
		return $errors;
	}

	public static function check_classes($classes) {
		$errors = Array();
		foreach($classes as $class) {
			if(!class_exists($class)) {
				$errors[] = '<b>' . $class . '</b> is not installed';
			}
		}
		return $errors;
	}

	public static function check_functions($functioms) {
		$errors = Array();
		foreach($functions as $function=>$value) {
			if(!function_exists($value)) {
				$errors[] = '<b>' . $function . '</b> is not installed';
			}
		}
		return $errors;
	}

	public static function testdb() {
		$error = false;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ $error = $db->connect_error; }
		return $error;
	}

	public static function createdb($sql) {
		$result = false;
		$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
		if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
		if($db->query($sql) === true) {
			$result = true;
		}
		$db->close();
		return $result;
	}
}

?><html>
	<head>
		<title>Application Installer</title>
	</head>
	<body style="background: #000000; color: #c0c0c0;">
		<h2 align="center" style="color: #c0c0c0;">
			Installer
		</h2>
		<table style="width: 400px; margin-left:auto; margin-right:auto; background: #333333; border: 1px solid #eeeeee;">
		<?php
		if(!empty($errors)) {
			foreach($errors as $error) {
				echo '<tr><td style="color: #ffff00;">' . $error . '</td></tr>';
			}
		} else {
			echo '<tr><td align="center" style="color: #00ff00;">Installation Completed</td></tr>';
		}
		?>
		</table>
	</body>
</html>
