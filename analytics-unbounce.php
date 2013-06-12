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

$unbounce_analytics_id = get_option('unbounce_analytics_id');
$unbounce_analytics_input_id = '';

function unbounce_tracking_code()
{
	global $unbounce_analytics_id;
	$tracking_script = <<<_TRACKING_CODE_
	<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '$unbounce_analytics_id']);
	_gaq.push(['_trackPageview']);

	setTimeout("_gaq.push(['_trackEvent', '15_seconds', 'read'])", 15000);

	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();

	</script>
_TRACKING_CODE_;

	if (!current_user_can('edit_published_posts'))
	{
		echo $tracking_script;
	}
	else
	{
		echo "<!--- Analytics Unbounce Plugin is working but not tracking you as you are admin-->";
	}
}

add_action('wp_footer', 'unbounce_tracking_code');
/* Plugin front end work ends here */
?>

<?php
/* Next section adds the admin options. */
add_action ('admin_menu', 'unbounce_admin_menu');

function unbounce_admin_menu()
{
	add_options_page( 'Analytics Unbounce', 'Analytics Unbounce', 'manage_options', 'analytics-unbounce', 'unbounce_options');
}

function unbounce_options()
{
	global $unbounce_analytics_id;
	if ( !current_user_can( 'manage_options' ) )
	{
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
	$unbounce_message = '';
	if (array_key_exists('unbounce_submit', $_POST))
	{
		$unbounce_message = unbounce_check_analytics_key();
		store_unbounce_analytics_key();
	}
	echo <<<_UNBOUNCE_OPTIONS_
	<div id="wrap">
		<h2>Analytics Unbounce Settings</h2> 
		$unbounce_message
		<hr />
		<form action="options-general.php?page=analytics-unbounce" method="post">
			<label for="analytics_id">Enter Your Google Analytics ID</label> <br />
			<input type="text" name="analytics_id" value="$unbounce_analytics_id"/> <br />
			<input type="submit" value="Save" /> <hr />
			<input type="hidden" name="unbounce_submit" />
		</form>
	</div>
_UNBOUNCE_OPTIONS_;
}

function unbounce_check_analytics_key()
{
	if (array_key_exists('analytics_id', $_POST))
	{
		global $unbounce_analytics_input_id;
		$unbounce_analytics_local_id = sanitize_text_field($_POST['analytics_id']);
		$unbounce_input_length = strlen($unbounce_analytics_local_id);
		if ($unbounce_input_length < 9 || $unbounce_input_length > 15) 
		{
			return '<div class="updated settings-error">This seems like wrong Analytics ID. Please enter a valid one.</div>';
		}
		else
		{
			$unbounce_analytics_input_id = $unbounce_analytics_local_id;
			return '<div class="updated settings-error">Analytics ID saved</div>';
		}
	}
}
?>

<?php
/* This is some scary databse stuff!*/
function unbounce_activate()
{
	add_option('unbounce_analytics_id');
}

function store_unbounce_analytics_key()
	{
		global $unbounce_analytics_input_id;
		if ($unbounce_analytics_input_id != '')
		{
			update_option('unbounce_analytics_id', $unbounce_analytics_input_id);
		}
	}
?>

<?php
/* Some work to be done on activation*/
	register_activation_hook(__FILE__ , 'unbounce_activate');
?>