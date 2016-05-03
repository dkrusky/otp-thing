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