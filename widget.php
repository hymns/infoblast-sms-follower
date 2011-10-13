<?php
/**
 * widget.php
 *
 * user defined function for dashboard plugin settings panel
 *
 * @package			infoblast-sms-follower
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @license			LGPL2, see included license file
 * @link			http://www.hamizi.net/
 * @since			Version 1.0.0
 */


/**
 * widget_sms
 *
 * widget for follower registration
 *
 * @access public
 * @params array $args
 * @return none
 */  
function widget_sms($args) 
{
	require_once "functions.php";

	global $sms_submitted, $wpdb;
	extract($args);

	echo "\n <!--Infoblast SMS Follower Widget by Muhammad Hamizi Jaminan http://www.hamizi.net/-->\n";
	echo $before_widget;
	echo $before_title . get_option( "sms_header" ) . $after_title;
	echo "<div id=\"sms_submitted\" style=\"text-align:center;\"></div>";
	echo "<div id=\"sms_loading\" style=\"display:none; text-align:center;\"><img src=\"". get_bloginfo('url') . "/wp-content/plugins/infoblast-sms-follower/img/load.gif\"/></div>";
?>
	<form name='sms_sub_form' id='sms_sub_form' style="padding:3px;text-align:center;" method='POST'>
		<p>Phone Number<br/>
		+<label name="sms_country_code" id="sms_country_code">60</label><input type="text" name="sms_number" id="sms_number"/>
		</p>
		<input type="button" value="Follow" name="sms_follow" id="sms_follower"/> <input type="checkbox" name="sms_unfollow" id="sms_unfollow"> Unfollow
	</form>
	<input type="hidden" value="<?php bloginfo('url'); ?>" id="sms_url"/>
<?php
	echo "<h6><em>" . get_option( "sms_footer" ) . "</em></h6>";
	echo $after_widget;
	echo "\n <!--End of Infoblast SMS Follower-->";
}

/**
 * sms_widget_init
 *
 * the initialing function for plugin widget
 *
 * @access public
 * @return none
 */ 
function sms_widget_init()
{
	register_sidebar_widget(__('SMS Follower Widget'), 'widget_sms');
}
?>