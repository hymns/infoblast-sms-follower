<?php
/*
Plugin Name: Infoblast SMS Follower
Plugin URI: http://www.hamizi.net/opensource/wp_infobast
Description: Allows users to receive sms notifications and allows blog owners to send sms message directly from dashboard to their followers number.
Author: Muhammad Hamizi Jaminan
Version: 1.0.0
Author URI: http://www.hamizi.net
*/

require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/install.php";
require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/settings.php";
require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/widget.php";
require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/followers.php";
require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/functions.php";

// initialing
add_action("plugins_loaded", "sms_widget_init");

// register install function for new install or update
register_activation_hook(__FILE__, 'sms_install');

// add the admin menu to the dashboad
add_action('admin_menu', 'sms_add_menu');

// add control for new post
add_action('publish_post', 'sms_send_on_post', 99);
add_action('publish_post', 'sms_store_post_meta', 1, 2);
add_action('save_post', 'sms_store_post_meta', 1, 2);

// add profile field
add_action( 'show_user_profile', 'sms_profile_fields' );
add_action( 'edit_user_profile', 'sms_profile_fields' );
add_action( 'personal_options_update', 'sms_save_profile_fields' );
add_action( 'edit_user_profile_update', 'sms_save_profile_fields' );

// add jquery script 
wp_enqueue_script('jquery');
wp_register_script("infoblast-sms-follower", "/wp-content/plugins/infoblast-sms-follower/infoblast-sms-follower.js");
wp_enqueue_script('infoblast-sms-follower');

// set global sms notification
global $smssuccfail;

/**
 * sms_add_menu
 *
 * admin menu for plugin
 *
 * @access public
 * @return none
 */
function sms_add_menu() 
{
	add_menu_page('SMS Follower', 'SMS Follower', 8, __FILE__, 'sms_main_page');
	add_submenu_page(__FILE__, 'Followers', 'Followers', 8, 'infoblast-sms-followers', 'sms_followers_page');
	add_submenu_page(__FILE__, 'Settings', 'Settings', 8, 'infoblast-sms-settings', 'sms_settings_page');
	add_meta_box('sms_post_form', __('Send Notification to SMS Followers'), 'sms_metabox_post_sidebar', 'post', 'side');
}

/**
 * sms_store_post_meta
 *
 * flagging posting data to void redundant
 *
 * @access public
 * @params int $post_id
 * @params bool $post
 * @return none
 */
function sms_store_post_meta($post_id, $post = false) 
{
	$post = get_post($post_id);

	if (!$post || $post->post_type == 'revision') 
		return;
	
	$posted_meta = $_POST['sms_send_sms'];
	
	if (!empty($posted_meta)) 
		$meta = $posted_meta == 'yes' ? 'yes' : 'no';

	else
		$meta = 'no';
	
	update_post_meta($post_id, 'sms_send_sms', $meta);
}

/**
 * sms_send_on_post
 *
 * sending sms for new post / update
 *
 * @access public
 * @params int $post_id
 * @return none
 */
function sms_send_on_post($post_id = 0) 
{
	global $wpdb;
	
	if ($post_id == 0)
		return;
	
	$post = get_post($post_id);

	if ($post->post_status == 'private')
		return;
	
	if ('no' == get_post_meta($post->ID, 'sms_send_sms', true))
		return;
	
	if ('yes' == get_post_meta($post->ID, 'sms_sent',true))
		return;

	// create short url using bit.ly service
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, 'http://api.bit.ly/v3/shorten?login=hamizi&apiKey=R_be891b6d85d5f8901a6d7035bcfc366c&longUrl='.urlencode(get_permalink($post_id))."&format=json"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$res = curl_exec($ch);
	$urldata = json_decode($res);
	curl_close($ch);
	$bitlyurl = $urldata->data->url;
	
	// load library	
	require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/openapi.class.php";
	
	// set config
	$config['username'] = get_option( "sms_user" );
 	$config['password'] = get_option( "sms_password" );
 
	// call openapi class
	$openapi = new openapi();
	$openapi->initialize($config);

	// get signature
	$signature = get_option( "sms_signature" );
		
	// get follower from db
	$table_name = $wpdb->prefix . "sms_followers";
	$results = $wpdb->get_results("SELECT number FROM " . $table_name);
	
	//set post status to sent to avoid resending on updates
	update_post_meta($post->ID, 'sms_sent', 'yes');
	
	// process data results
	$smssent = 0;
	foreach ($results as $result) 
		$to[] = $result->number;
	
	// prepare sms data
	$data['msgtype']   = 'text';	
	$data['to'] = implode(',', $to);
	$data['message'] = "New Blog Post: " . $post->post_title . " " . $bitlyurl . $signature;

	// send to server
	$openapi->send_sms($data);
}


// trigger sms form post
if (!empty($_POST['sms_message'])) 
{
	global $wpdb;

	// load library	
	require_once WP_PLUGIN_DIR . "/infoblast-sms-follower/openapi.class.php";
	
	// set config
	$config['username'] = get_option( "sms_user" );
 	$config['password'] = get_option( "sms_password" );
 
	// call openapi class
	$openapi = new openapi();
	$openapi->initialize($config);

	// get signature
	$signature = get_option( "sms_signature" );

	// send sms to follower readers
	if ('on' == isset($_POST['sms_send_readers'])) 
	{
		$table_name = $wpdb->prefix . "sms_followers";
		$results = $wpdb->get_results("SELECT number FROM " . $table_name);
		
		foreach ($results as $result) 
			$to[] = $result->number;
	}
	
	// send sms to registered users
	if ('on' == isset($_POST['sms_send_users'])) 
	{
		$aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users"));
		
		foreach ( $aUsersID as $iUserID )
		{
			$sms_number = get_user_meta( $iUserID, 'sms_profile_number' );
			$sms_number = $sms_number[0];
			
			if ( $sms_number != "") 
				$to[] = $sms_number;
		}
	}

	// prepare sms data
	$data['msgtype']   = 'text';	
	$data['to'] = implode(',', $to);
	$data['message'] = $_POST['sms_message'] . $signature;
	
	// sent to server
	$response = $openapi->send_sms($data);

	// output response
	if ($response['status'] == 'OK')
		$smssuccfail = "<span style=\"color:'green';\">Sent to ".sizeof($to)." followers</span><br/>";
	else
		$smssuccfail .= "<span style=\"color:'red';\">".$smsfail."</span>";
}

// update plugin settings
if (!empty($_POST['sms_settings'])) 
{
	update_option( "sms_user", $_POST['sms_api_user']);
	update_option( "sms_password", $_POST['sms_api_pass']);
	update_option( "sms_signature", $_POST["sms_signature"]);
	update_option( "sms_header", $_POST['sms_header']);
	update_option( "sms_footer", $_POST['sms_footer']);
}
?>