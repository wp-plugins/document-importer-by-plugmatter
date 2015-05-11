<?php 
$msg = '';
$msg_reg='';
$reg_done = false;

	wp_register_style('wpmdi_css', plugins_url('/css/settings_page.css', __FILE__));
	wp_enqueue_style('wpmdi_css');	

if(isset($_POST["license_key"] ) && ($_POST["license_key"]!="")) {
	$res = wp_remote_post("http://plugmatter.com/activate",
			array('body'=>array(
					"license_key"=>$_POST["license_key"],
					"siteurl"=>get_option('siteurl'),
					"package"=>Plugmatter_DI_PACKAGE,	
					)
				)
			);
	$res_arr = explode(":",$res["body"]);
	if($res_arr[0] == "VERIFIED") {
		update_option('Plugmatter_DI_PACKAGE', $res_arr[1]);
		update_option('Plugmatter_di_License', $_POST["license_key"]);
		$msg="<div class='pm_msg_success'><strong>Plugmatter Document Importer activated successfully</strong></div>";
	} else {
		$msg="<div class='pm_msg_error'><strong>Invalid License Key</strong></div>";
	}
} else if(isset($_POST["email"]) || isset($_POST["first_name"])) {
    if(isset($_POST["first_name"]) && $_POST["first_name"] == "") {
        $msg_reg="<div class='pm_msg_error'><strong>Please enter your First Name</strong></div>";
    } else if(!eregi("^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$", $_POST["email"])) {
        $msg_reg="<div class='pm_msg_error'><strong>Please Enter a Valid Email</strong></div>";
    } else {
	   $res = wp_remote_post("http://plugmatter.com/register_lite",
			array('body'=>array(
					"first_name"=>$_POST["first_name"],
                    "email"=>$_POST["email"],
                    "package"=>Plugmatter_DI_PACKAGE,	
					)
				)
			);
	   $res_arr = explode(":",$res["body"]);
       if($res_arr[0] == "ERROR") {
           $msg_reg="<div class='pm_msg_error'><strong>".$res_arr[1]."</strong></div>";
       } else {
           $res_arr2 = explode("|",$res_arr[1]);
           $msg_reg="<div class='pm_msg_success'><strong>".$res_arr2[0]."</strong></div>";
           $reg_done = true;
           $lc_key = $res_arr2[1];
       }
    }

}
//------------------------------------------------------------------------------------------------------
?>
<div class='pmdi_wrap'>
	<div class='pmdi_headbar'>
		<div class='pmdi_pagetitle'><h2>General Settings</h2></div>
	    <div class='pmdi_logodiv'><img src='<?php echo plugins_url("/images/logo.png", __FILE__);?>' height='35'></div>
	</div>
	<?php 	
	if($msg!='') { 
		echo "$msg";
	}
	if(get_option('Plugmatter_di_License') != "") {	
		if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_lite") {
	?>
	<div class='pmadmin_body'  style="position:relative">
        <br>
		<br>
        <h3>Congratulations! You're now good to go.</h3>
        <h3>Quickly Import ".docx" Word Documents from your computer by going to New <a href="<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php"> Post </a> /
        	<a href="<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php?post_type=page"> Page </a>. </h3>
		<img class="pmdi_license_img" src="<?php echo plugins_url('/images/pmdi_settings.png', __FILE__); ?>">
		<h3></h3>
		<!-- <h3>Start Importing ".docx" Word Documents from your computer <a href="<?php //echo get_option('siteurl'); ?>/wp-admin/post-new.php">Go to New Post</a> </h3> -->
        If you need any help, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to help you out.
		<br><br>

	<?php 
		}
		if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_pro" || Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_single") {
	?>
	<div class='pmadmin_body'  style="position:relative">
        <br>
        <h3>Congratulations! You're now good to go.</h3>
		<h3>Quickly Import ".docx" Word Documents from your computer by going to New Post / Page. </h3>
		<img class="pmdi_license_img" src="<?php echo plugins_url('/images/pmdi_settings.png', __FILE__); ?>">
		<div class="pmadmin_submit">
			<input id="submit" class="pm_primary_buttons" type="submit" value=" Get Started " onclick="location.href='<?php echo get_option('siteurl'); ?>/wp-admin/post-new.php'" name="submit">
		</div>        
        <br><br>
        To configure Google Docs and Dropbox integeration please follow the <a href='http://plugmatter.com/document-importer/user-guide'>User Guide</a>.
        If you need any help, <a href='mailto:support@plugmatter.com' target="_blank"><b>shoot us an email</b></a> and we will be glad to help you out.
		<br><br>
	<?php }
	} else {
	?>
	
	<div class='pmadmin_body'  style="position:relative">
    <div style='padding-bottom:30px;'>
        <p>To get Support for your plugin from Plugmatter, you need to enter your Plugmatter License Key sent to you in your "License Key Email". Please contact <a href='http://plugmatter.com/support' target='_blank'>support</a> if you're unable to find your License Key.</p>
    </div>        
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=pmdi_settings"; ?>" id='pm_settings' method="post">	
		<div>
			<div class='plug_enable_lable' style='width:250px'>Enter Your License Key</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='license_key' size='45' style='padding:6px;' value='<?php echo $lc_key; ?>'>
			</div>
			<div style='clear:both'>&nbsp;</div>
		</div>
		<div class="pmdi_submit">
			<input id="submit" class="pm_primary_buttons" type="submit" value="   Register Plugin   " name="submit">
		</div>
		<br><br>
	</form>
    <?php if(Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_lite") { ?>
    <div style="border-top:1px solid #ddd;padding-bottom:10px;">&nbsp;</div>
    <div class='plug_enable_lable' style='font-weight:bold;width:350px;'>Don't Have a License Key? Register Now!</div>
    <div style='padding-bottom:15px;clear:both;padding-top:10px;'>Your license key will be sent to your registered email.</div>
    <?php
    if($msg_reg!='') { 
		echo $msg_reg;
	} 
    if($reg_done != true) {
    ?><br><br>
	<form action="<?php $siteurl = get_option('siteurl');echo $siteurl."/wp-admin/admin.php?page=pmdi_settings"; ?>" id='pm_settings' method="post">	
		<div>
			<div class='plug_enable_lable' style='width:150px'>First Name</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='first_name' size='30' style='padding:4px;' value='<?php echo isset($_POST["first_name"])?$_POST["first_name"]:""; ?>'>
			</div>
			<div style='clear:both'>&nbsp;</div>
			<div class='plug_enable_lable' style='width:150px'>Email</div>
			<div class='plug_tgl_btn'>
				<input type='text' name='email' size='30' style='padding:4px;' value='<?php echo isset($_POST["email"])?$_POST["email"]:""; ?>'>
			</div>
			<div style='clear:both'>&nbsp;</div>            
		</div>
		<div class="pmadmin_submit" style='margin-top:10px;'>
			<input id="submit" class="pm_primary_buttons" type="submit" value="   Get License key   " name="submit">
		</div>
		<br><br>
	</form>        
    <?php } } ?>
	</div>
	<?php } ?>
</div>