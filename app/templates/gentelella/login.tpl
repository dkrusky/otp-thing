<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<!-- Meta, title, CSS, favicons, etc. -->
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<title>{$NAME} | {$TITLE}</title>

		<!-- Bootstrap core CSS -->

		<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/bootstrap.min.css" rel="stylesheet">

		<link href="{$SMARTY_TEMPLATES}/{$THEME}/fonts/css/font-awesome.min.css" rel="stylesheet">
		<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/animate.min.css" rel="stylesheet">

		<!-- Custom styling plus plugins -->
		<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/custom.css" rel="stylesheet">
		<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/icheck/flat/green.css" rel="stylesheet">


		<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/jquery.min.js"></script>

		<!--[if lt IE 9]>
			<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/ie8-responsive-file-warning.js"></script>
		<![endif]-->

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		

		<style>
			.form-control[name=code] {
				display: inline-block !important;
				width: calc(100% - 50px) !important;
				float: left;
			}
			
			.help {
				display: absolute;
				right: 0;
				width: 35px;
				margin-top: 0px;
				font-size: 18px;
				text-align: left;
				float: right;
			}

			div.help-dialog {
				background-color:#75B6CF;
				width:100%;
				height:100%;
				z-index:9999;
				top:0;
				left:0;
				position:fixed;
				display: none;
				color: #ffffff;
				padding: 20px;
			}

			div.help-dialog div p {
				text-align: justify;
				color: #ffffff !important;
				font-size: 14px;
				font-weight: bold;
			}

			div.help-dialog div p:first-of-type {
				color: #ffffff !important;
				font-size: 16px;
				background-color: #D58512;
				width: 100%;
				padding: 4px;
			}

			div.help-dialog div {
				width: 50%;
				margin: 0 auto;
			}
			
			div.help-dialog div p a {
				color: yellow;
				font-size: 14px;
			}
		</style>

		<script>
			$(document).ready(function() {
				$(".help").click(function() {
					$(".help-dialog").show();
				});

				$("#help-close").click(function() {
					$(".help-dialog").hide();
				});
			});
		</script>
		
	</head>

	<body style="background:#F7F7F7;">
		<!-- Authenticator Help Dialog -->
		<div class="help-dialog">
			<div>
				<h1>What is an Authenticator ?</h1>
				<p><b>NOTE:</b> If you do not know what this field means, then you can safely leave it empty.</p>
				<p>
					An authenticator is a device or program like <a href="https://support.google.com/accounts/answer/1066447?hl=en" target="_blank">Google Authenticator</a> which adds a pseudo-random element to your login process by providing a 6-8 digit number each time you login.
					<br /><br />
					This portal takes advantage of this capability by providing a way (once logged in) that you can attach an Authenticator to your account. This technology was invented as it is very common for people to use weak passwords.  When using an authenticator this helps to ensure that your account remains secure from brute-force or simply guesisng your password based on information someone may know about you from social media, or other online places.
					<br /><br />
					When your account is created, no authenticator is added as this is not a process that we can do for you. Authenticators are essentially your secret between you and the authentication system and are unique.
				</p>
				<button id="help-close" class="btn btn-large btn-success" style="float: right;" type="button">Close this Window</button>
			</div>
		</div>

		<!-- The login form -->
		<div class="">
			<div id="wrapper">
				<div id="login" class="animate form">
					<section class="login_content">
						<form method="post">
							<h1>Login Form</h1>
							{if !empty($ERROR)}
							<h4>{$ERROR}</h4>
							{/if}
							<div>
								<input type="text" name="username" class="form-control" placeholder="* Username" required="">
							</div>
							<div>
								<input type="password" name="password" class="form-control" placeholder="* Password" required="">
							</div>
							<div>
								<input type="password" name="code" class="form-control" placeholder="Authenticator Code">
								<button class="btn btn-warning fa fa-question help" type="button"></button>
							</div>
							<div>
								<input type="hidden" name="csrf" value="{$CSRF}">
								<button class="btn btn-default submit" type="submit">Log in</button>
							</div>
							<div class="clearfix"></div>

						</form>
						<!-- form -->
					</section>
					<!-- content -->
				</div>
			</div>
		</div>

	</body>

</html>