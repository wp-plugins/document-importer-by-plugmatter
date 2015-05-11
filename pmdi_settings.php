<?php 

	wp_register_style('wpmdi_css', plugins_url('/css/settings_page.css', __FILE__));
	wp_enqueue_style('wpmdi_css');	

	$clientid_err = $client_secret_err = $redirecturi_err = $api_key_err = $dropbox_appkey_err = $google_details = $dropbox_details = "";
	
	if(!empty($_POST["google-submit"])) {
		$client_id = trim($_POST["wpmdi_client_id"]);
		$client_secret = trim($_POST["wpmdi_client_secret"]);
		$google_api = trim($_POST["google_api_key"]);

		if($client_id != "" && $client_secret != "" && $google_api != "") {
			$google_details = "true";
		}

		if(empty($client_id)){
			$clientid_err = "Client id is required";
			update_option('wpmdi_client_id', $client_id);
		} else {
			update_option('wpmdi_client_id', $client_id);
		}

		if(empty($client_secret)){
			$client_secret_err = "Google client secret key is required";
			update_option('wpmdi_client_secret', $client_secret);
		} else {
			update_option('wpmdi_client_secret', $client_secret);
		}		

		if(empty($google_api)){
			$api_key_err = "Api Key is required";
			update_option('google_api_key', $google_api);
		} else {
			update_option('google_api_key', $google_api);
		}
	}

	if(!empty($_POST["dropbox-submit"])) {
		$dropbox_key = trim($_POST["wpmdi_dropbox_app_key"]);

		if($dropbox_key != "") {
			$dropbox_details = "true";
		}
		
		if(empty($dropbox_key)){
			$dropbox_appkey_err = "Dropbox app key is required";
			update_option('wpmdi_dropbox_app_key', $dropbox_key);
		} else {
			update_option('wpmdi_dropbox_app_key', $dropbox_key);
		} 
	}

	if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_lite") { 
?>
<div class='pmdi_wrap'>
	<div class='pmdi_headbar'>
		<div class='pmdi_pagetitle'><h2>General Settings</h2></div>
	    <div class='pmdi_logodiv'><img src='<?php echo plugins_url("/images/logo.png", __FILE__);?>' height='35'></div>
	</div>
	


	<div class='pmdi_body'  style="position:relative">

		<div class="pmdi_head_img" id='pmdi_com_cont'>
    		<div id="pmdi_com_set">
	        	<img src="<?php echo plugins_url('/images/localdrive.png', __FILE__); ?>"> 
	        	<div class="pmdi_google_head" id="pmdi_head_title">Import Documents from Computer</div>
	        </div>

        	<div class="pm_settings_submit">
				<input id="submit" class="pm_primary_buttons" type="button" value="   Get started here   " onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php'" name="submit">
			</div>
			<div class="pmdi_clear_float"></div>
			<hr class="pmdi_hr_set">
    	</div>
    	<div style='margin:15px 20px 10px 40px;color:gray;background:#fff;padding:15px; width:650px;'>
    		<img src="<?php echo plugins_url('/images/support.png', __FILE__); ?>" align='left' style='margin-right:10px;position:relative;'>
    		<a href='http://plugmatter.com/document-importer#plans&pricing'>Upgrade</a> and start importing documents from Google Docs and Dropbox.
        	If you need any help, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to help you out.
		</div>
		<div id="wrap_stngs" style="position:relative;margin-top:30px; padding-top: 1px; padding-bottom:10px;">
		
			<div class="pmdi_head_img">
				<img src="<?php echo plugins_url('/images/gdrive.png', __FILE__); ?>"> 
				<div class='pmdi_google_head'>Google API Settings</div>
		    	
			</div>

			<table class='pmdi_settings_table'>
				<tr>
					<td><p>Redirect URI</p></td>
					<td></td>
					<td><input readonly type='text' name='wpmdi_redirect_uri' size=45 value = "<?php echo site_url().'/wp-admin/admin-ajax.php?action=google_callback'; ?>" ></td>
					<td><?php// echo $redirecturi_err; ?></td>
				</tr>
				<tr style='padding-bottom: 20px;'>
					<td><p>Client Id</p></td>
					<td></td>
					<td><input type='text' name='wpmdi_client_id' size=45 value = "<?php echo get_option('wpmdi_client_id'); ?>" ></td>
					<td><?php echo $clientid_err; ?></td>
				</tr>
				<tr>
					<td><p>Client Secret</p></td>
					<td></td>
					<td><input type='text' name='wpmdi_client_secret' size=45 value = "<?php echo get_option('wpmdi_client_secret'); ?>" ></td>
					<td><?php echo $client_secret_err; ?></td>
				</tr>			
				<tr>
					<td><p>API Key</p></td>
					<td></td>
					<td><input type='text' name='google_api_key' size=45 value = "<?php echo get_option('google_api_key'); ?>" ></td>
					<td><?php echo $api_key_err; ?></td>
				</tr>
				<td colspan='4' >
					<input style='background-color: #ccc; background-image: none;' id='pmdi_google_submit' class='pmdi_settings_btn' name='google-submit' type='submit' value=' Save Settings '>
				</td>
			</table>
		
			<div class="pmdi_head_img">
				<hr class="pmdi_hr_set">
				<img src="<?php echo plugins_url('/images/dropbox.png', __FILE__); ?>"> 		
				<div class='pmdi_google_head'>DropBox API Settings</div>
			</div>
		
			
			<table class='pmdi_settings_table'>
				<tr>
					<td><p>App Key</p></td>
					<td style='width:125px;'></td>
					<td><input type='text' name='wpmdi_dropbox_app_key' size=20 value = "<?php echo get_option('wpmdi_dropbox_app_key'); ?>" ></td>
					<td><?php echo $dropbox_appkey_err; ?></td>
				</tr>
				<tr>
					<td colspan='4' >
						<input style='background-color: #ccc; background-image: none;' id='pmdi_google_submit' class='pmdi_settings_btn' name='dropbox-submit' type='submit' value=' Save Settings '>
					</td>
				</tr>
			</table>
			<div id="wrap_stngs_layer" style="position:absolute;width:750px;left:-12px;top:0;bottom:0;background-color:rgba(0, 0, 0, 0.15);">	
			</div>
		</div>
	</div>



	<div id="pmdi_xsl_set" class="pmdi_head_img">
		<hr class="pmdi_hr_set">
		<img src="<?php echo plugins_url('/images/xsl.png', __FILE__); ?>"> 
		<div class='plug_di_enable_lable' style='width:400px;'>Using Remote XSLT Engine</div>

		<div class='plug_di_tgl_btn'>
			<input type="checkbox" id="" name="" class="switch" value='1' />
			<label for="plug_di_xsl">&nbsp;</label>
		</div>
		<div style='clear:both'>&nbsp;</div>

		<div style='margin:15px 20px 10px 40px;color:gray;background:#fff;padding:15px; width:650px;'>
			<b>Document Importer LITE uses XSLT parsing engine hosted on our server to convert .DOCX to HTML, which costs us. Hence it restricts the number of imports to 8 per month. 
<br><br>The paid packages comes with the XSLT parsing engine packed with it and hence provide unlimited document imports per month and improves the speed of 
import drastically.
<br><br>
<a target="_blank" href='http://plugmatter.com/my/packages'>Upgrade</a> to enjoy fast and unlimited imports.
			<br><br>

		</div>
		<hr class="pmdi_hr_set">
	</div>	
</div>
<?php 
	}

	if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_pro" || Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_single") { 
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	jQuery("#plug_di_xsl_enable").click(function() {
		var pmdi_xsl_enable;
		if(jQuery(this).attr("checked") == "checked") {
			pmdi_xsl_enable = "yes";
		} else {
			pmdi_xsl_enable = "no";
		}

		jQuery.ajax ({
            type: "POST",
            url: 'admin-ajax.php?action=pmdi_xsl',
            dataType: 'text',
            data: { 'pmdi_xsl_enable':pmdi_xsl_enable },
            success: function(data){ 
                    
            }  
        });
	});
});
</script>
<div class='pmdi_body'  style="position:relative">
	<div class='pmdi_headbar'>
		<div class='pmdi_pagetitle'><h2>General Settings</h2></div>
	    <div class='pmdi_logodiv'><img src='<?php echo plugins_url('/images/logo.png', __FILE__); ?>' height='35'></div>
	</div>
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=pmdi_settings"; ?>" id='pm_settings' method="post">
		<?php 
			if($google_details == "true") {
				echo "<div class='updated pm_success'><p>Google API settings saved successfully</p></div>";
			}

		?>

		<div class="pmdi_head_img" id='pmdi_com_cont'>
    		<div id="pmdi_com_set">
	        	<img src="<?php echo plugins_url('/images/localdrive.png', __FILE__); ?>"> 
	        	<div class="pmdi_google_head" id="pmdi_head_title">Import Documents from Computer</div>
	        </div>

        	<div class="pm_settings_submit">
				<input id="submit" class="pm_primary_buttons" type="button" value="   Get started here   " onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php'" name="submit">
			</div>
			<div class="pmdi_clear_float"></div>
			<hr class="pmdi_hr_set">
    	</div>
    	<div style='margin:15px 20px 10px 40px;color:gray;background:#fff;padding:15px; width:650px;'>
    		<img src="<?php echo plugins_url('/images/support.png', __FILE__); ?>" align='left' style='margin-right:10px;position:relative;'>
    		To configure Google and Dropbox API, please follow our <a href='http://plugmatter.com/document-importer/user-guide'>User Guide</a>.
        	If you need any help, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to help you out.
		</div>
		
		<div class="pmdi_head_img">
			<img src="<?php echo plugins_url('/images/gdrive.png', __FILE__); ?>"> 
			<div class='pmdi_google_head'>Google API Settings</div>
	    	
		</div>

		<table class='pmdi_settings_table'>
			<tr>
				<td><p>Redirect URI</p></td>
				<td></td>
				<td><input readonly type='text' name='wpmdi_redirect_uri' size=45 value = "<?php echo site_url().'/wp-admin/admin-ajax.php?action=google_callback'; ?>" ></td>
				<td><?php// echo $redirecturi_err; ?></td>
			</tr>
			<tr style='padding-bottom: 20px;'>
				<td><p>Client Id</p></td>
				<td></td>
				<td><input type='text' name='wpmdi_client_id' size=45 value = "<?php echo get_option('wpmdi_client_id'); ?>" ></td>
				<td><?php echo $clientid_err; ?></td>
			</tr>
			<tr>
				<td><p>Client Secret</p></td>
				<td></td>
				<td><input type='text' name='wpmdi_client_secret' size=45 value = "<?php echo get_option('wpmdi_client_secret'); ?>" ></td>
				<td><?php echo $client_secret_err; ?></td>
			</tr>			
			<tr>
				<td><p>API Key</p></td>
				<td></td>
				<td><input type='text' name='google_api_key' size=45 value = "<?php echo get_option('google_api_key'); ?>" ></td>
				<td><?php echo $api_key_err; ?></td>
			</tr>
			<td colspan='4' >
				<input id='pmdi_google_submit' class='pmdi_settings_btn' name='google-submit' type='submit' value=' Save Settings '>
			</td>
		</table>
	</form>		
		<div class="pmdi_head_img">
			<hr class="pmdi_hr_set">
			<img src="<?php echo plugins_url('/images/dropbox.png', __FILE__); ?>"> 		
			<div class='pmdi_google_head'>DropBox API Settings</div>
		</div>
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=pmdi_settings"; ?>" id='pm_settings' method="post">	
		<?php 
			if($dropbox_details == "true") {
				echo "<div class='updated pm_success'><p>Dropbox API settings saved successfully</p></div>";
			}

		?>
		<table class='pmdi_settings_table'>
			<tr>
				<td><p>App Key</p></td>
				<td style='width:125px;'></td>
				<td><input type='text' name='wpmdi_dropbox_app_key' size=20 value = "<?php echo get_option('wpmdi_dropbox_app_key'); ?>" ></td>
				<td><?php echo $dropbox_appkey_err; ?></td>
			</tr>
			<tr>
				<td colspan='4' >
					<input id='pmdi_google_submit' class='pmdi_settings_btn' name='dropbox-submit' type='submit' value=' Save Settings '>
				</td>
			</tr>
		</table>
	</form>
	<div id="pmdi_xsl_set" class="pmdi_head_img">
		<hr class="pmdi_hr_set">
		<img src="<?php echo plugins_url('/images/xsl.png', __FILE__); ?>"> 
		<div class='plug_di_enable_lable' style='width:400px;'>Debug Mode (Use Remote XSLT Engine)</div>

		<div class='plug_di_tgl_btn'>
			<input type="hidden" name="plug_di_xsl_enable" value='0'/>
			<input type="checkbox" id="plug_di_xsl_enable" name="plug_di_xsl_enable" class="switch" <?php if(get_option('pmdi_xsl_enable') == 'yes') echo "checked"; ?> value='1' />
			<label for="plug_di_xsl_enable">&nbsp;</label>
		</div>
		<div style='clear:both'>&nbsp;</div>

		<div style='margin:15px 20px 10px 40px;color:gray;background:#fff;padding:15px; width:650px;'>
			<b>Getting XSLT error or blank document?</b> Try enabling our Debug Mode to use our remote XSLT engine for converting .DOCX to HTML.<br><br>
			Note: The debug mode limits number of imports per month, affects import speed. Therefore it should be only used for test purpose.
			<br><br>

		</div>

		<hr class="pmdi_hr_set">
	</div>
</div>
<?php } ?>