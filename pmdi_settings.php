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
	<div class='pmadmin_body'  style="position:relative">
		<br>
		<br>
        <h3>Congratulations! You're now good to go.</h3>
		<h3>Start Importing ".docx" Word Documents from your computer <a href="<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php">Go to New Post</a> </h3>
        If you need any help, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to help you out.
		<br><br>
	</div>
</div>
<?php 
	}

	if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_pro" || Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_single") { 
?>

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
	<div class='pmdi_google_head'>Google API Settings</div>	
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
			<div class='pmdi_dropbox_head'>DropBox API Settings</div>
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
</div>
<?php } ?>