# otp-thing
One time password generator, validator, and qrcode generator that has no web dependencies (self-contained) in PHP

## Introduction

This started out as a straight drop-in class which you can still [download from here](https://github.com/microvb/otp-thing/tree/f4f10da122a4f83fdc97445ae67df812c5de3791), however it has been developed into a full admin/usercp system.

## Installation

Download the application, and set the appropriate values in `config.inc.php`, then run `/app/install` .  If everything passes, the database will be installed if it doesn't exist, and a new user `admin` withe the password `admin` will be created using ACL `9999` which should give plenty of access levels to play with for lower level users or admins.

Once installed, all users and administrators have access to add an authenticator to their account, and edit the following basic settings for their own account :  Name, Password, Email

----

## Login

If an authenticator is not on the users account, the authenticator code field is disregarded. If any part of the credentials fail including if the authenticator code is invalid or not a valid scratch code, a generic message is displayed stating 'Invalid Credentials' so that no clue is given making it more difficult for an attacker to guess which part of the credential system was correct by messages such as 'Invalid Password'.

![image](https://cloud.githubusercontent.com/assets/11585632/14994589/a755599a-113e-11e6-9246-b93a921349af.png)


## Dashboard
![image](https://cloud.githubusercontent.com/assets/11585632/14994153/4dccb7ee-113c-11e6-9a28-85c95598b441.png)

## Settings
![image](https://cloud.githubusercontent.com/assets/11585632/14994184/7e61ab94-113c-11e6-87f7-787a362ba553.png)

## Authenticator

If no authenticator is on the users account

![image](https://cloud.githubusercontent.com/assets/11585632/14994430/f5bdf098-113d-11e6-9698-3e2fa02ce6ff.png)

![image](https://cloud.githubusercontent.com/assets/11585632/14994471/2610336e-113e-11e6-8ff3-e31a39ee9118.png)

If an authenticator exists on the users account:

![image](https://cloud.githubusercontent.com/assets/11585632/14994494/39c63106-113e-11e6-8922-1c434bec8f49.png)

![image](https://cloud.githubusercontent.com/assets/11585632/14994536/677be3ac-113e-11e6-8b08-596046711c6a.png)

## Accounts : Add
![image](https://cloud.githubusercontent.com/assets/11585632/14994212/9ff2c888-113c-11e6-8c55-812d6bea3a08.png)

## Accounts : Added
![image](https://cloud.githubusercontent.com/assets/11585632/14994249/d4306d08-113c-11e6-8691-467c9b8739c6.png)

## Accounts : List
![image](https://cloud.githubusercontent.com/assets/11585632/14994267/f01f4066-113c-11e6-86cb-fa71282d9037.png)

## Notifications

![image](https://cloud.githubusercontent.com/assets/11585632/14994382/a59141a6-113d-11e6-8564-3461afdd5f39.png)

![image](https://cloud.githubusercontent.com/assets/11585632/14994293/0f21ab3e-113d-11e6-912b-2fbfedfd2e2d.png)

## Generic Error Page handles

410 Gone instead of 404 for pages that do not have a valid modal. This script only checks for the physical existence of resource files (jpg, png, js, etc.) and denies any direct access to physical php/html/etc files. As you can see in the screenshot, if the physical resource file is missing, it will also trigger a 410 gone, showing the missing element and virtual path in the address bar. 
![image](https://cloud.githubusercontent.com/assets/11585632/14994714/6650bbbe-113f-11e6-8e16-61263b335341.png)

400 error message for resources that the user does not have permission to access.
![image](https://cloud.githubusercontent.com/assets/11585632/14994873/08bba396-1140-11e6-92a4-bd0cb0db14e9.png)

