<?php
// prevent this file from being called directly
if(!defined('LIVE')) { exit(); };

// define the public application root
define('APP_ROOT',				'/app/');
define('APP_THEME',				'gentelella');
define('APP_NAME',				'Secure Area');

// OTP Configuration
define('OTP_COMPANY',	'ACME');	// company name
define('OTP_MAX_TRIES',	3);			// maximum attempts before preventing use of the current timeblock
define('OTP_LENGTH',	6);			// length of digits a code should be
define('OTP_ALGORITHM',	'sha256');	// the hashing algorithm used (sha256, sha1, sha512)

// session control
define('SESSION_TIMEOUT',	30);	// how many minutes a logged in session should last

// user control. acl's between these values are granted control to modify
// users that are lower acl. for example, a user with acl 9999 can modify
// users with acl 0 to 9998, but can not modify users with acl of 9997, and
// can't add a user with an acl value higher than 9998
define('ACL_ADMIN_MAX',		9999);	// maximum acl to grant control over modifying users with lower acl
define('ACL_ADMIN_MIN',		9994);	// users with an acl below this value do not get control over other users

// maximum notifications to show on Notifications page
define('MAX_NOTIFICATIONS',	50);

// Database Credentials used for user authentication
define('SQL_SERVER',	'localhost');
define('SQL_USERNAME',	'root');
define('SQL_PASSWORD',	'');
define('SQL_DATA',		'test');
define('SQL_PREFIX',	'');

// Smarty
define('SMARTY_DEBUGGING',	false);
define('SMARTY_CACHING',	false);
define('SMARTY_CACHE_LIFETIME',	120);
define('SMARTY_FORCE_COMPILE',	false);
define('SMARTY_DIR_TEMPLATES',	'./templates');
define('SMARTY_DIR_TEMPLATES_C','./templates_c');
define('SMARTY_DIR_CACHE',	'./cache');

