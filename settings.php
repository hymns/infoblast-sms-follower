<?php
/**
 * settings.php
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
 * sms_settings_page
 *
 * setting page function for dashboard
 *
 * @access public
 * @return none
 */ 
function sms_settings_page() 
{
	// get plugin option
	$sms_api_user = get_option( "sms_user" );
	$sms_api_pass = get_option( "sms_password" );
	$sms_signature = get_option( "sms_signature" );
	$sms_header = get_option( "sms_header" );
	$sms_footer = get_option( "sms_footer" );
?>
	<div class="wrap">
		<h2>Infoblast SMS Follower Settings</h2>
		
		<br/>
		<p>Get your FREE account at <a href="http://www.infoblast.com.my" target="_blank">www.infoblast.com.my</a>.</p>
		<br/>
		<form name='sms_update_settings' id='sms_update_settings' method='POST' action='<?php echo "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'] ?>'>
			<table>
				<tr>
					<td>Infoblast API Username</td>
					<td><input type="text" name="sms_api_user" value="<?php echo $sms_api_user;?>"/></td>
				</tr>
				<tr>
					<td>Infoblast API Password</td>
					<td><input type="text" name="sms_api_pass" value="<?php echo $sms_api_pass;?>"/></td>
				</tr>
				<tr>
					<td>SMS Signature</td>
					<td><input type="text" name="sms_signature" value="<?php echo $sms_signature;?>"/></td>
				</tr>
				<tr>
					<td>Widget Header</td>
					<td><input type="text" name="sms_header" size="60" value="<?php echo stripslashes($sms_header);?>"/></td>
				</tr>
				<tr>
					<td>Widget Footer</td>
					<td><input type="text" name="sms_footer" size="60" value="<?php echo stripslashes($sms_footer);?>"/></td>
				</tr>
			</table><br/>
			<span class="submit"><input type="submit" value="Update" name="sms_settings"/></span>
		</form>
	</div>
	<br/>
	<div>
		Plugin by <a href="http://www.hamizi.net/" title="Hamizi Jaminan">Muhammad Hamizi Jaminan</a>
	</div>
<?php
}

/**
 * sms_main_page
 *
 * main page section on dashboard for plugin
 *
 * @access public
 * @return none
 */
function sms_main_page() 
{
	global $smssuccfail;
?>
	<div class="wrap">
		<h2><?php _e('SMS Follower Dashboard') ?></h2>
	</div>
<?php
	add_meta_box("sms_send", "Send SMS Messages", "sms_meta_box_send", "sms");
	add_meta_box("sms_stats", "Follower Statistics", "sms_meta_box_stats", "smsstats");
?>
	<div id="dashboard-widgets-wrap">
		<div class="metabox-holder">
			<div style="float:left; width:50%;" class="inner-sidebar1">
<?php
	do_meta_boxes('sms','advanced','');
?>
			</div>
			<div style="float:right; width:49%;" class="inner-sidebar2">
<?php
	do_meta_boxes('smsstats','advanced','');
?>	
			</div>
		</div>
	</div>
<?php
}


/**
 * sms_meta_box_send
 *
 * box layer for sms composer to follower
 *
 * @access public
 * @return none
 */
function sms_meta_box_send()
{
	global $smssuccfail;

	$sms_maxlen = "1600";
?>
	<div style="padding: 10px;">
		<form name='send_sms_form' id='send_sms_form' method='POST'>
			Send an SMS to your followers:
			<br/>
			<br/>
			<table>
				<tr>
					<td>Message:</td>
				</tr>
				<tr>
					<td>
						<textarea maxlength="<?php echo $sms_maxlen; ?>" name="sms_message" id="sms_message"></textarea>
					</td>
				</tr>
				<tr>
					<td><input size=5 value="<?php echo $sms_maxlen; ?>" name="sms_left" id="sms_left" readonly="true"> Characters Left</td>
				</tr>
				<tr>
					<td><br /><b>Send To:</b> Registered Followers <input type="checkbox" name="sms_send_users" checked="checked"/> &nbsp; &nbsp; SMS Followers <input type="checkbox" name="sms_send_readers" checked="checked"/></td>
				</tr>
			</table>
			<br />
			<span class="submit"><input type="submit" value="Send Messages" /></span>
		</form>
		<p>Get your FREE account at <a href="http://www.infoblast.com.my" target="_blank">www.infoblast.com.my</a>.</p>
<?php 
		echo $smssuccfail;
		$smssuccfail = '';?>
	</div>
<?php
}

/**
 * sms_meta_box_stats
 *
 * box layer for follower statistics
 *
 * @access public
 * @return none
 */
function sms_meta_box_stats() 
{
	global $wpdb;

	$table_name = $wpdb->prefix . "sms_followers";
	$result = $wpdb->get_results("SELECT count(*) as totalsubs FROM " . $table_name);
?>
	<div style="padding: 10px;">
	<table>
		<tr>
			<td><b>Total SMS Followers:</b></td>
			<td><?php echo $result[0]->totalsubs; ?></td>
		</tr>
<?php
	$usercount = 0;
	$aUsersID = $wpdb->get_col( $wpdb->prepare("SELECT $wpdb->users.ID FROM $wpdb->users"));
	
	foreach ( $aUsersID as $iUserID )
		 if ( get_the_author_meta( 'sms_profile_number', $iUserID ) != "")
			$usercount++;
?>
		<tr>
			<td><b>Total Registered Followers:</b></td>
			<td><?php echo $usercount; ?></td>
		</tr>
	</table>
	</div>
<?php
}

/**
 * sms_metabox_post_sidebar
 *
 * box layer post option for plugin
 *
 * @access public
 * @return none
 */
function sms_metabox_post_sidebar() 
{
	global $wpdb,$post,$smssuccfail;
	$sendsms = get_post_meta($post->ID, 'sms_send_sms', true);

	echo '<p>'.__('Send Post via SMS?').'&nbsp;';
	echo '<input type="radio" name="sms_send_sms" id="sms_send_sms_yes" value="yes" '.checked('yes', $sendsms, false).' /> <label for="sms_send_sms_yes">'.__('Yes').'</label> &nbsp;&nbsp;';
	echo '<input type="radio" name="sms_send_sms" id="sms_send_sms_no" value="no" '.checked('no', $sendsms, false).' /> <label for="sms_send_sms_no">'.__('No').'</label>';
	echo '</p>';
	$table_name = $wpdb->prefix . "sms_followers";
	$result = $wpdb->get_results("SELECT count(*) as totalsubs FROM " . $table_name);
	echo '<p><b>Total Followers</b>: '.$result[0]->totalsubs.'</p>';
	echo $smssuccfail;
	$smssuccfail = '';
}

/**
 * sms_profile_fields
 *
 * additional setting for profile user form
 *
 * @access public
 * @params object $user
 * @return none
 */
function sms_profile_fields($user) 
{
	echo "<h3>SMS Follower Settings</h3>";
	echo "<table class=\"form-table\">";
	echo "	<tr>";
	echo "		<th><label for=\"sms_profile_number\">Phone Number</label></th>";
	echo "		<td>";
	echo "			+60<input type=\"text\" name=\"sms_profile_number\" id=\"sms_profile_number\" value=\"". esc_attr( get_the_author_meta( 'sms_profile_number', $user->ID ) ) ."\" class=\"regular-text\" /><br />";
	echo "			<span class=\"description\">Please enter your phone number</span>";
	echo "		</td>";
	echo "	</tr>";
	echo "</table>";
}

/**
 * sms_save_profile_fields
 *
 * udf processing custom profile form for plugin
 *
 * @access public
 * @params int $user_id
 * @return bool
 */
function sms_save_profile_fields($user_id) 
{
	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
		
	$sms_number = $_POST['sms_profile_number'];
	$sms_number = preg_replace("[^0-9]", "", $sms_number);
	
	//Check if its a valid cellphone number or blank.
	if(strlen($sms_number) >= 11 or $sms_number == "")
		update_usermeta( $user_id, 'sms_profile_number', $sms_number);
	else
		return false;
}
?>
