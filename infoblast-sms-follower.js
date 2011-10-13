jQuery(function(){
	jQuery('#sms_follower').click(function() {
		jQuery("#sms_submitted").html('');
		if (typeof ajaxurl == "undefined"){
			var ajaxurl = jQuery('#sms_url').val()+'/wp-content/plugins/infoblast-sms-follower/ajax_widget.php';
		}
		var number = jQuery("#sms_number").val();
		var unfollow = jQuery('#sms_unfollow').is(':checked');
		var country = jQuery('#sms_country_code_h').val();
		var data = "action=sms_follow&sms_unfollow="+unfollow+"&sms_number="+number;
		
		jQuery.post(ajaxurl, data, function(response){
			jQuery("#sms_submitted").html(response);
		});
	})
	
	jQuery('#sms_message').keyup(function() {
		var len = jQuery('#sms_message').val().length;
		jQuery('#sms_left').val(1600-len);
	});
	
	jQuery("#sms_loading").ajaxStart(function(){
		jQuery(this).show();
	});
	jQuery("#sms_loading").ajaxStop(function(){
		jQuery(this).hide();
	}); 
	
	jQuery('#sms_import_link').click(function() {
		jQuery('#sms_upload').show();
	});
});