<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('otp.class.php');


// initialize block with user data
$user		=	'test@test.com';
$secret		=	otp::GenerateSecret($user);
otp::SetSecret($user, $secret);


// display qr code
$qr = otp::GenerateQRCode();
echo '<img src="' . $qr['image'] . '">';

// display all aparameters
$row = '<tr><td>%s</td><td><input type="text" value="%s"></td></tr>';
$table = '<table><tr><td><b>Field</b></td><td><b>Value</b></td></tr>%s</table>';
$o = '';
$o .= sprintf($row, 'Company', otp::$company);
$o .= sprintf($row, 'User', $user);
$o .= sprintf($row, 'Secret', $secret);
$o .= sprintf($row, 'Issuer', otp::$company);
$o .= sprintf($row, 'Digits', otp::$digits);
$o .= sprintf($row, 'Period', otp::$period);
$o .= sprintf($row, 'Algorithm', $algorithm);
$o .= sprintf($row, 'Current', $code->timeblock);
$o .= sprintf($row, 'OTP', $code->code);
echo '<br>' . sprintf($table, $o);