<?php
define('LIVE', true);
include('config.inc.php');

$admin_user = 'admin';	// the default username for admin
$admin_pass = 'admin';	// the default password for admin

// create the database if it doesn't exist
$sql = "CREATE TABLE IF NOT EXIST `" . SQL_PREFIX . "users` ( `id` int(11) NOT NULL AUTO_INCREMENT, `username` varchar(255) NOT NULL, `password` varchar(255) NOT NULL, `enabled` int(1) NOT NULL DEFAULT '0' COMMENT '0 = disabled, 1 = enabled', `otp_key` varchar(32) DEFAULT '', `otp_last_time` int(11) NOT NULL DEFAULT '0', `otp_last_time_count` int(1) NOT NULL DEFAULT '0', `acl` int(11) NOT NULL DEFAULT '0', `otp_scratch_codes` text, PRIMARY KEY (`id`), UNIQUE KEY `username` (`username`) ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;";
$result = false;
$db = new mysqli(SQL_SERVER, SQL_USERNAME, SQL_PASSWORD, SQL_DATA);
if($db->connect_errno > 0){ throw new Exception("Connection Failed: " . $db->connect_error); }
if($db->query($sql) === true) {
	$result = true;
}

// add admin user to table if it doesn't exist
$sql = "INSERT IGNORE INTO `" . SQL_PREFIX . "users` VALUES ('1', ?, ?, '1', null, '0', '0', '9999', null);";
$db_stmt = $db->prepare($sql);
$db_stmt->bind_params('ss',
	$admin_user,
	password_hash($admin_pass, PASSWORD_DEFAULT)
);
$db_stmt->execute();
if($db_stmt->errno) {
	$result = false;
	throw new Exception('Database Error: ' . $db_stmt->error);
}
$db_stmt->close();

$db->close();

