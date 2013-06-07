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

	if (is_admin())
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

<?php
	function unbounce_options_validate($input) 
	{
		$newinput['text_string'] = trim($input['text_string']);
		if(!preg_match('/^[a-z0-9]{12}$/i', $newinput['text_string'])) 
		{
			$newinput['text_string'] = '';
		}
		return $newinput;
	}

	function unbounce_admin_init()
	{
		register_setting('unbounce_options', 'unbounce_options', 'unbounce_options_validate');
		add_settings_section('unbounce_main', 'Main Settings', 'unbounce_section_text', 'unbounce');
		add_settings_field('unbounce_text_string', 'Unbounce Text Input', 'unbounce_setting_string', 'unbounce', 'unbounce_main');
	}
	add_action('admin_init', 'unbounce_admin_init');
?>

<?php
function unbounce_admin_add_page()
{
	add_options_page('Analytics Unbounce', 'Analytics Unbounce', 'manage_options', 'analytics-unbounce', 'unbounce_options_page');
}

add_action('admin_menu', 'unbounce_admin_add_page');
?>

<?php
function unbounce_options_page()
{
?>
	<div>
		<h2>Analytics Unbounce: Improve Your Tracking</h2>
		<?php
			$unbounce_dir = get_bloginfo('wpurl') . "/wp-content/plugins/analytics-unbounce/options.php";
		?>
		<form action="<?php echo $_SERVER[PHP_SELF] ?>" method="post">

	<?php settings_fields('unbounce_options'); ?>
	<?php do_settings_sections('unbounce'); ?>
	<input type="submit" name="submit" value="<?php esc_attr_e('Save Settings') ?>" />
	</form></div>
<?php
}
?>
<?php
	function unbounce_section_text()
	{
		echo '<p>Please Enter Your Analytics ID. <strong>Format:</strong> UA-XXXXXXX-1 </p>';
	}
	function unbounce_setting_string()
	{
		$options = get_option('unbounce_options');
		echo "<input id='unbounce_text_string' name='unbounce_options[text_string]' size='40' type='text' value='{$options['text_string']}' />";
	}
?>