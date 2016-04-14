<?php
// prevent this file from being called directly
if(!defined('LIVE')) { exit(); };

// define the public application root
define('APP_ROOT',		'/app/');

// OTP Configuration
define('OTP_COMPANY',	'ACME');	// company name
define('OTP_MAX_TRIES',	3);			// maximum attempts before preventing use of the current timeblock
define('OTP_LENGTH',	6);			// length of digits a code should be
define('OTP_ALGORITHM',	'sha256');	// the hashing algorithm used


// Database Credentials used for user authentication
define('SQL_SERVER',	'localhost');
define('SQL_USERNAME',	'root');
define('SQL_PASSWORD',	'');
define('SQL_DATA',		'test');
define('SQL_PREFIX',	'auth_');
