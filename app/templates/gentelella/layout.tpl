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
	<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/maps/jquery-jvectormap-2.0.3.css" rel="stylesheet" type="text/css">
	<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/icheck/flat/green.css" rel="stylesheet">
	<link href="{$SMARTY_TEMPLATES}/{$THEME}/css/floatexamples.css" rel="stylesheet" type="text/css">

	<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/jquery.min.js"></script>
	<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/nprogress.js"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
		<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/ie8-responsive-file-warning.js"></script>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

</head>


<body class="nav-md">

  <div class="container body">

    <div class="main_container">

      <div class="col-md-3 left_col">
        <div class="left_col scroll-view">

          <div class="navbar nav_title" style="border: 0; height: 1px; overflow: hidden;">
            <a href="{$ROOT}" class="site_title"><i class="fa fa-paw"></i> <span>{$NAME}</span></a>
			<br>
          </div>
          <div class="clearfix"></div>

          <!-- menu prile quick info -->
          <div class="profile">
            <div class="profile_pic">
              <img src="https://secure.gravatar.com/avatar/{$USER['email']|md5}" alt="{$USER['name']}" class="img-circle profile_img">
            </div>
            <div class="profile_info">
              <span>Welcome,</span>
              <h2>{$USER['name']}</h2>
            </div>
			<br>
          </div>
          <!-- /menu prile quick info -->

          <!-- sidebar menu -->
          <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">

            <div class="menu_section">
              <h3>&nbsp;</h3>
              <ul class="nav side-menu">
                <li><a><i class="fa fa-home"></i> Home <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu" style="display: none">
                    <li><a href="{$ROOT}">Action One</a></li>
                    <li><a href="{$ROOT}">Action Two</a></li>
                    <li><a href="{$ROOT}">Action Three</a></li>
                  </ul>
                </li>
                <li><a><i class="fa fa-user"></i> My Account <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu" style="display: none">
                    <li><a href="{$ROOT}authenticator?csrf={$CSRF}">Authenticator</a></li>
                    <li><a href="{$ROOT}notifications">Notifications</a></li>
                    <li><a href="{$ROOT}settings?csrf={$CSRF}">Settings</a></li>
                    <li><a href="{$ROOT}logout">Log Out</a></li>
                  </ul>
                </li>
              </ul>
            </div>

			{if $ADMIN eq true}
            <div class="menu_section">
              <h3>Administration</h3>
              <ul class="nav side-menu">
                <li><a><i class="fa fa-user"></i> Accounts <span class="fa fa-chevron-down"></span></a>
                  <ul class="nav child_menu" style="display: none">
                    <li><a href="{$ROOT}accounts?show=list">List</a></li>
                    <li><a href="{$ROOT}accounts?show=add&csrf={$CSRF}">Add</a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
			{/if}

          </div>
          <!-- /sidebar menu -->

          <!-- /menu footer buttons -->
          <div class="sidebar-footer hidden-small">
            <a data-toggle="tooltip" data-placement="top" title="Settings" href="{$ROOT}settings">
              <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="FullScreen" onclick="fullscreen()" href="javascript:void(0);">
              <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Authenticator" href="{$ROOT}authenticator?csrf={$CSRF}">
              <span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
            </a>
            <a data-toggle="tooltip" data-placement="top" title="Logout" href="{$ROOT}logout">
              <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
            </a>
          </div>
          <!-- /menu footer buttons -->
        </div>
      </div>

      <!-- top navigation -->
      <div class="top_nav">

        <div class="nav_menu">
          <nav class="" role="navigation">
            <div class="nav toggle">
              <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
              <li class="">
                <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                  <img src="https://secure.gravatar.com/avatar/{$USER['email']|md5}" alt="{$USER['name']}">{$USER['name']}
                  <span class=" fa fa-angle-down"></span>
                </a>
                <ul class="dropdown-menu dropdown-usermenu pull-right">
                  <li><a href="{$ROOT}authenticator?csrf={$CSRF}">Authenticator</a></li>
                  <li><a href="{$ROOT}notifications">Notifications</a></li>
                  <li><a href="{$ROOT}settings?csrf={$CSRF}">Settings</a></li>
                  <li><a href="{$ROOT}logout"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
                </ul>
              </li>

				{if isset($NOTIFICATIONS) && !empty($NOTIFICATIONS)}
				<li role="presentation" class="dropdown">
					<a href="javascript:;" class="dropdown-toggle info-number" data-toggle="dropdown" aria-expanded="false">
						<i class="fa fa-envelope-o"></i>
						<span class="badge bg-green">{$NOTIFICATIONS|@count}</span>
					</a>

					<ul id="menu1" class="dropdown-menu list-unstyled msg_list animated fadeInDown" role="menu">
						{foreach from=$NOTIFICATIONS item=notification}
						{if $smarty.foreach.notification.index == 5}
						{break}
						{else}
						<li>
							<a>
								<span class="image">
									<img src="https://secure.gravatar.com/avatar/{$notification['email']|md5}" alt="{$notification['name']}" />
								</span>
								<span>
									<span>{$notification['name']}</span>
									<span class="time">{$notification['time']}</span>
								</span>
								<span class="message">
									{$notification['message']}
								</span>
							</a>
						</li>
						{/if}
						{/foreach}
						<li>
							<div class="text-center">
								<a href="{$ROOT}notifications">
									<strong>See All Notifications</strong>
									<i class="fa fa-angle-right"></i>
								</a>
							</div>
						</li>
					</ul>
				</li>
				{else}
				<li role="presentation">
					<a href="{$ROOT}notifications" alt="See All Notifications">
						<i class="fa fa-envelope-o"></i>
					</a>
				</li>
				{/if}

            </ul>
          </nav>
        </div>

      </div>
      <!-- /top navigation -->

      <!-- page content -->
      <div class="right_col" role="main">
{block name=right_col} {/block}

<!-- footer content -->
 <footer>
   <div class="pull-right">
     Gentelella - Bootstrap Admin Template by <a href="https://colorlib.com">Colorlib</a>
   </div>
   <div class="clearfix"></div>
 </footer>
 <!-- /footer content -->
</div>
<!-- /page content -->
</div>

</div>

<div id="custom_notifications" class="custom-notifications dsp_none">
<ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
</ul>
<div class="clearfix"></div>
<div id="notif-group" class="tabbed_notifications"></div>
</div>

<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/bootstrap.min.js"></script>

<!-- gauge js -->
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/gauge/gauge.min.js"></script>
<!-- bootstrap progress js -->
<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/progressbar/bootstrap-progressbar.min.js"></script>
<!-- icheck -->
<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/icheck/icheck.min.js"></script>
<!-- daterangepicker -->
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/moment/moment.min.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/datepicker/daterangepicker.js"></script>
<!-- chart js -->
<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/chartjs/chart.min.js"></script>

<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/custom.js"></script>

<!-- flot js -->
<!--[if lte IE 8]><script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/excanvas.min.js"></script><![endif]-->
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.pie.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.orderBars.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.time.min.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/date.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.spline.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.stack.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/curvedLines.js"></script>
<!--  <script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/flot/jquery.flot.resize.js"></script>//-->

<!-- worldmap -->
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/maps/jquery-jvectormap-2.0.3.min.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/maps/gdp-data.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/maps/jquery-jvectormap-world-mill-en.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/maps/jquery-jvectormap-us-aea-en.js"></script>
<!-- pace -->
<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/pace/pace.min.js"></script>
<!-- skycons -->
<script src="{$SMARTY_TEMPLATES}/{$THEME}/js/skycons/skycons.min.js"></script>

<!-- PNotify -->
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/notify/pnotify.core.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/notify/pnotify.buttons.js"></script>
<script type="text/javascript" src="{$SMARTY_TEMPLATES}/{$THEME}/js/notify/pnotify.nonblock.js"></script>

<script>
function fullscreen() {
var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
(document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
(document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
(document.msFullscreenElement && document.msFullscreenElement !== null);

if (!isInFullScreen) {
if (document.documentElement.requestFullscreen) {
 document.documentElement.requestFullscreen();
} else if (document.documentElement.mozRequestFullScreen) {
 document.documentElement.mozRequestFullScreen();
} else if (document.documentElement.webkitRequestFullScreen) {
 document.documentElement.webkitRequestFullScreen();
} else if (document.documentElement.msRequestFullscreen) {
 document.documentElement.msRequestFullscreen();
}
} else {
if (document.exitFullscreen) {
 document.exitFullscreen();
} else if (document.webkitExitFullscreen) {
 document.webkitExitFullscreen();
} else if (document.mozCancelFullScreen) {
 document.mozCancelFullScreen();
} else if (document.msExitFullscreen) {
 document.msExitFullscreen();
}
}
}
{if isset($ERROR) && !empty($ERROR)}
$(document).ready(function(){
new PNotify({
title: 'Error',
text: '{$ERROR|escape}',
type: 'error',
hide: true
});
});
{elseif isset($SUCCESS) && !empty($SUCCESS)}
$(document).ready(function(){
new PNotify({
title: 'Success',
text: '{$SUCCESS|escape}',
type: 'success',
hide: true
});
});
{/if}

NProgress.done();
</script>
<!-- /datepicker -->
<!-- /footer content -->
</body>

</html>
