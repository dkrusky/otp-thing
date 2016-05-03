<!DOCTYPE html>
<html lang="en"><head>
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


<body class="nav-md">

  <div class="container body">

    <div class="main_container">

      <!-- page content -->
      <div class="col-md-12">
        <div class="col-middle">
          <div class="text-center text-center">
			{if $CODE==410}
            <h1 class="error-number">410</h1>
            <h2>Sorry but we couldnt find this page</h2>
            <p>This page you are looking for does not exist <a href="{$ROOT}">Return to Dashboard</a>
            </p>
			{/if}

			{if $CODE==400}
            <h1 class="error-number">400</h1>
            <h2>Sorry but your request was invalid</h2>
            <p>Try something else as the action your attempted was invalid. <a href="{$ROOT}">Return to Dashboard</a>
            </p>
			{/if}

          </div>
        </div>
      </div>
      <!-- /page content -->

    </div>
    <!-- footer content -->
  </div>

  <div id="custom_notifications" class="custom-notifications dsp_none">
    <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
    </ul>
    <div class="clearfix"></div>
    <div id="notif-group" class="tabbed_notifications"></div>
  </div>

  <script src="{$SMARTY_TEMPLATES}/{$THEME}/js/bootstrap.min.js"></script>

  <!-- bootstrap progress js -->
  <script src="{$SMARTY_TEMPLATES}/{$THEME}/js/progressbar/bootstrap-progressbar.min.js"></script>
  <!-- icheck -->
  <script src="{$SMARTY_TEMPLATES}/{$THEME}/js/icheck/icheck.min.js"></script>

  <script src="{$SMARTY_TEMPLATES}/{$THEME}/js/custom.js"></script>
  <!-- pace -->
  <script src="{$SMARTY_TEMPLATES}/{$THEME}/js/pace/pace.min.js"></script>
  <!-- /footer content -->
</body>

</html>


