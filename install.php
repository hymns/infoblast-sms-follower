<?php
/**
 * install.php
 *
 * plugin installer
 *
 * @package			infoblast-sms-follower
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @license			LGPL2, see included license file
 * @link			http://www.hamizi.net/
 * @since			Version 1.0.0
 */
 

// plugin version
$sms_version = "1.0.0";

/**
 * sms_install
 *
 * plugin installer function
 *
 * @access public
 * @return none
 */
function sms_install() 
{
	global $wpdb, $sms_version;
	
	// set table name
	$table_name = $wpdb->prefix . "sms_followers";

	// table not exists ? create !
	if ($wpdb->get_var("show tables like '$table_name'") != $table_name) 
	{ 
		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			number text NOT NULL,
			ip varchar(100) NOT NULL,
			date datetime NOT NULL,
			UNIQUE KEY id (id)
		);";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option('sms_header', 'Follow updates via SMS');
		add_option('sms_footer', 'Driven by <a href=\"http://wordpress.org/extend/plugins/infoblast-sms-follower/\">SMS Follower Plugin</a>');
		add_option('sms_max','1600');
		add_option('sms_version', $sms_version);
		add_option('sms_signature', '- Sent by SMS Follower Plugin');
		
	} 
	
	// already exist ? check version for upgrading
	else 
	{ 
		$installed_ver = get_option( "sms_version" );
		if ($installed_ver != $sms_version ) 
		{
			$sql = "CREATE TABLE " . $table_name . " (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				number text NOT NULL,
				ip varchar(100) NOT NULL,
				date datetime NOT NULL,
				UNIQUE KEY id (id)
			);";
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			
			dbDelta($sql);
			update_option( "sms_version", $sms_version );
			update_option( "sms_max", '1600' );
		}
	}
}
?>
