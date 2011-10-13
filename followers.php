<?php
/**
 * followers.php
 *
 * Dashboard panel to manage follower such as import & export follower numbers
 *
 * @package			infoblast-sms-follower
 * @author			Muhammad Hamizi Jaminan, hymns [at] time [dot] net [dot] my
 * @license			LGPL2, see included license file
 * @link			http://www.hamizi.net/
 * @since			Version 1.0.0
 */

// checking permission 
if (is_admin()) 
{
	// export section
	if ($_GET['mode'] == 'export')
	{
		// set header download
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"WP_SMS_Followers.csv\"");
		
		global $wpdb;
		
		// get follower from db
		$table_name = $wpdb->prefix . "sms_followers";
		$results = $wpdb->get_results("SELECT * FROM " . $table_name);
		
		// prepare data
		foreach ($results as $result) 
			$data .= $result->number."\n";

		// write out
		echo $data;
		exit;
	}
	
	// import section
	if (isset($_POST['sms_import'])) 
	{
		// read upload file
		$data = file_get_contents($_FILES['sms_import_file']['tmp_name']);
		$numbers = explode("\n",$data);

		// process number		
		foreach($numbers as $number) 
		{
			$number = ereg_replace("[^0-9]", "", $number);
			
			// validate length
			if ((strlen($number) <= 15) && (strlen($number) >= 10)) 
			{
				global $wpdb;
				
				// get data follower number
				$table_name = $wpdb->prefix . "sms_followers";
				$exists = $wpdb->get_results("SELECT number FROM " . $table_name . " WHERE number = '".$wpdb->escape($number)."'");
				
				// not exists ? save
				if ($exists[0]->number != $number) 
				{
					$insert = "INSERT INTO " . $table_name . " (number, ip, date) VALUES ('" . $wpdb->escape($number) . "', 'IMPORT', NOW())";
					$results = $wpdb->query($wpdb->prepare( $insert ));
				}
			}
		}
	}
}

/**
 * sms_followers_page
 *
 * follower dashboard panel
 *
 * @access public
 * @return print
 */
function sms_followers_page() 
{
	GLOBAL $wpdb;

	$current_url = $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
?>
	<div class="wrap">
			<h2>SMS Followers</h2>
			<p><a href="#" id="sms_import_link">Import from CSV</a>&nbsp;&nbsp;<a href="http://<?php echo $current_url; ?>&amp;mode=export">Export to CSV</a></p>
			<div id="sms_upload" style="display:none;">
				<form method="post" enctype="multipart/form-data">
					<p>
						Please select a csv file containing 10-11 digit numbers to upload
					</p>
					<table>
						<tr>
							<td>File</td>
							<td><input type="file" name="sms_import_file"/></td>
						</tr>
					</table>
					<input type="submit" value="Import Followers" name="sms_import"/>
				</form>
				<br/>
			</div>
			<table class="sortable widefat" cellspacing="0">
				<thead>
				<tr>
					<th scope="col">ID</th>
					<th scope="col">Phone Number</th>
					<th scope="col">IP Address</th>
					<th scope="col">Since</th>
					<th scope="col">Action</th>
				</tr>
				</thead>
				<tbody>
<?php
	$table_name = $wpdb->prefix . "sms_followers";
	
	// delete ?
	if (isset($_GET['id']) && ($_GET['mode'] == 'delete'))
	{
		$quer = "DELETE FROM " . $table_name . " WHERE id = " . $wpdb->escape($_GET['id']); 
		$wpdb->query($quer);

		echo "<div style='color:red'>" . $_GET['id'] . " removed</div>";
	}
	
	$result = $wpdb->get_results("SELECT * FROM " . $table_name);

	// got result ?
	if ($result)
	{
		// loop over results
		foreach ($result as $results) 
		{
			$tablenum = $results->number;
			$tableip = $results->ip;
			$tabledate = $results->date;
			$tableid = $results->id;
?>
					<tr onmouseover="this.style.backgroundColor='lightblue';" onmouseout="this.style.backgroundColor='white';">
						<td><?php echo $tableid; ?></td>
						<td><?php echo $tablenum; ?></td>
						<td><?php echo $tableip; ?></td>
						<td><?php echo $tabledate; ?></td>
						<td><a href="http://<?php echo $current_url; ?>&amp;mode=delete&amp;id=<?php echo $tableid; ?>" onclick="javascript:check=confirm( '<?php echo "Delete this follower?"?>');if(check==false) return false;"><?php _e('Delete') ?></a></td>
					</tr>
<?php
		}
	} 
	else
	{
		echo '<tr><td colspan="7" align="center">'. '- No Data -' .'</td></tr>';
	}
?>
				</tbody>
			</table>
	
			<p>Get your FREE account at <a href="http://www.infoblast.com.my" target="_blank">www.infoblast.com.my</a>.</p>
			<br/>
			<div>
				SMS Follower Plugin by <a href="http://www.hamizi.net/" title="Hamizi.net">Muhammad Hamizi Jaminan</a>
			</div>
		</div>
<?php
}
?>