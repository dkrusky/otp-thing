<?php
define('LIVE', true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('lib/otp.class.php');

// These are just used to format the table output to demonstrate the parameters for the QR code
// it is not required for runtime.
$row = '<tr><td>%s</td><td><input type="text" value="%s"></td></tr>';
$table = '<table><tr><td><b>Field</b></td><td><b>Value</b></td></tr>%s</table>';


// set the location and filename for the qrcode database
otp::$qrcode_database = 'qrdata.db';

// initialize block with user data
$user		=	'test@test.com';
$secret		=	otp::GenerateSecret($user);
otp::SetSecret($user, $secret);

// set the algorithm to use.
// supports : [ sha1, sha256, sha512 ] .  if anything aside from sha1 is selected it uses TOTP automatically
otp::$algorithm = 'sha1';

// set whether the sha1 method is TOTP or HOTP. has no effect when using sha256 or sha512
otp::$totp = false;

// display qr code
$qr = otp::GenerateQRCode();
echo '<img src="' . $qr['image'] . '">';

// get current timeblock and code
$code = otp::GetCode();

// display all aparameters
$o  = sprintf($row, 'Type', (otp::$totp == true || otp::$algorithm != 'sha1' ? 'TOTP' : 'HOTP' ) )
	. sprintf($row, 'Company', otp::$company)
	. sprintf($row, 'User', $user)
	. sprintf($row, 'Secret', $secret)
	. sprintf($row, 'Issuer', otp::$company)
	. sprintf($row, 'Digits', otp::$digits)
	. sprintf($row, 'Period', otp::$period)
	. sprintf($row, 'Algorithm', otp::$algorithm)
	. sprintf($row, 'Current', $code->timeblock)
	. sprintf($row, 'OTP', $code->code);
	
// echo out table
echo '<br>' . sprintf($table, $o);