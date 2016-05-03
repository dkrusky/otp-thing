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
	</head>

	<body style="background:#F7F7F7;">

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
								<input type="text" name="username" class="form-control" placeholder="Username" required="">
							</div>
							<div>
								<input type="password" name="password" class="form-control" placeholder="Password" required="">
							</div>
							<div>
								<input type="password" name="code" class="form-control" placeholder="Authenticator Code">
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