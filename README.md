# otp-thing
One time password generator, validator, and qrcode generator that has no web dependencies (self-contained) in PHP

## Introduction

OTP-Thing is a drop in class that makes it easy to implement 2FA with all the bells and whistles. The current implementation supports QR code generation from versions 1 to 40, as well as the various parity levels and quiet zone adjustments. This class is self-contained with the exception that it does require the `qrdata.db` file in order to successfuly generate QR codes. This file contains a compressed and encoded version of all the various QR versions and specs required.

Due to the nature of how this class works, it *can* be used in an evironment with *no internet* access.

## Requirements

This was tested on PHP 5.6, while every effort was made to ensure backwards compatability, using an earlier version is not recommended.

* SQLite3
* GD/GD2

## Warnings

If properly implemented, adding 2FA to your accounts can add a layer of protection, however if not implemented properly it can lead to disasterous results. It is therefore recommended that when using this to add levels of protection that care is taken during implementation. Following are some ideas to check :

* Integrate the OTP box as part of the login process instead of on a separate form. Do not let attackers guess that an account they may have the credentials for is using a OTP token.
* When any part of the login process fails, display a generic message to the user such as 'Invalid credentials supplied'. Do not give specific messages indicating that a specific part failed such as 'Invalid username' or 'Invalid code' as this assures the attacker of the portions they got correct.
* Once a OTP code has been used on an account, prevent that code from being used again for at least 1 day.  This should happen prior to setting the login session as valid.
* Always use CSRF tokens embedded in the page which can be regenerated fairly easily (will be releasing a login class project to demonstrate this). By Always, I do mean, Always. Not just the front end where the user logs in, but also after they are logged in on every page.
* Where possible, bust out of all frames where X-Origin is not your site .
* After X tries and failures, lock out access to the account for a specified period of time. 30 minute lockout after 5 attempts is not unreasonable. Keep in mind the more attempts you allow, and/or the shorter the lockout period is the more chances an attacker has to breach the account.
* Do not store passwords insecurely. A good method to store passwords, is to sign them using a private key, and validate the signature using a public key. You do not need to store the password, only the signature.  The problem is, the stronger the encryption, the slower the process, so there is a trade-off between security, and speed.  The minimum should be a sha256 hash as this is a current balance between something that hasn't been breached (yet), and speed. (in my humble optinion)
 
There are plent of other things related to security when protecting your users data, however it is in your hands to do the research into everything required for your project. (if requested, I am not opposed to expanding this list while keeping it as generic as possible)

## What is in the code ?

The file `otp.class.php` consists of 3 classes as follows:

|class|purpose|
|---|---|
|otp|The main (static) class. This is the only class you will need to call.|
|qrcodedb|This is a helper class for accessing the SQLite3 database.|
|sread|This is a helper class for seeking data in a string in a similar fashion to `fread()`|

## Methods

GenerateSecret ( *user* `string` ) - returns `string` { random 16 base32 character string }

SetSecret ( *user* `string`, *secret* `string` )

GetTime() - returns `int` { current block of time }

GetCode() - returns `array` [ *timeblock* `int` { current block of time }, *code* `string` { current valid code based on user and secret } ]

ValidateCode( *code* `string` ) - returns `bool` { true if code is valid for current block of time, using the current secret }

GenerateQRCode() - returns `array` [ *image* `string` { base64 encoded image string prepared to set as the src value for an `<img>` html tag }, *url* `string` { the full otpauth url used in the qr code image }, *secret* `string` { the current secret used } ]


*Everything except GetTime() and ValidateCode() is inside the demo.php for a demonstration on how to use*
