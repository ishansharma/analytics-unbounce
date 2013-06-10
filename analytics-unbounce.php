<?php
/*
Plugin Name: Analytics Unbounce
Pluign URI: http://wpblogexperts.com/plugins
Description: Adds Google Analytics tracking code to WordPress and fixes bounce rate measurement
Version: 1.0
Author: Ishan
Author URI: http://ishan.co
License: GPL2
*/

function unbounce_tracking_code()
{
	$tracking_script = <<<_TRACKING_CODE_
	<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', 'UA-XXXXXXX-1']);
	_gaq.push(['_trackPageview']);

	setTimeout("_gaq.push(['_trackEvent', '15_seconds', 'read'])", 15000);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

	</script>
_TRACKING_CODE_;

	if (!is_admin())
	{
		echo $tracking_script;
	}
	else
	{
		echo "<!--- Analytics Unbounce Plugin is working but not tracking you as you are admin-->";
	}
}

add_action('wp_footer', 'unbounce_tracking_code');
?>