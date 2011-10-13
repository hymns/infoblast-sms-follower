<?php
/**
 * ajax_widget.php
 *
 * process phone registration for following
 *
 * @package			infoblast-sms-follower
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @license			LGPL2, see included license file
 * @link			http://www.hamizi.net/
 * @since			Version 1.0.0
 */
 
include_once("../../../wp-config.php");
include_once("../../../wp-load.php");
include_once("../../../wp-includes/wp-db.php");

if (!empty($_POST['action'])) 
{
	$sms_number = $_POST['sms_number'];
	$sms_number = preg_replace("[^0-9]", "", $sms_number);
	$sms_country = "0";

	if ((strlen($sms_country.$sms_number) <= 10) && strlen($sms_number) >= 9) 
	{
		$sms_number = $sms_country.$sms_number;
		global $wpdb;
		$table_name = $wpdb->prefix . "sms_followers";
		
		$exists = $wpdb->get_results("SELECT number FROM " . $table_name . " WHERE number = '".$wpdb->escape($sms_number)."'");

		if ($_POST['sms_unfollow'] == 'true')
		{
	
			if ($exists[0]->number != $sms_number)
			{
				$sms_submitted = "<font color='red'>Your number does not exist.</font>";
			}
			else
			{			
				$delete = "DELETE FROM " . $table_name . " WHERE number = '".$wpdb->escape($sms_number)."'";
				$results = $wpdb->query($wpdb->prepare( $delete ));
				
				$sms_submitted = "<font color='green'>You now are successfully unfollowed.</font>";			
			}
		}
		else
		{
			if ($exists[0]->number != $sms_number)
			{
				$insert = "INSERT INTO " . $table_name . " (number, ip, date) VALUES ('" . $wpdb->escape($sms_number) . "', '" . get_ip() . "', NOW())";
				$results = $wpdb->query($wpdb->prepare( $insert ));
				$sms_submitted = "<font color='green'>Thank you for following.</font>";
			}
			else
			{
				$sms_submitted = "<font color='red'>You have already followed.</font>";			
			}		
		}
	} 
	else 
	{
		$sms_submitted = "<font color='red'>Please enter a valid phone number.</font>";
	}

	echo $sms_submitted;
}
?>