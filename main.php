<?php
/*
Plugin Name: Plugmatter Document Importer Lite
Plugin URI: http://plugmatter.com/
Description: The simplest and quickest way to import your docx files into WordPress Editor without losing the document formatting. Spend more time writing and not formatting it in WordPress editor.
Author: Plugmatter
Version: 1.4.3
Author URI: http://plugmatter.com/document-importer
*/

//--- Global values---
define('Plugmatter_DI_PACKAGE', 'plugmatter_documentimporter_lite');
//--------------------
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'pmdi_setup' );
add_action( 'load-post-new.php', 'pmdi_setup' );
add_action( 'admin_menu', 'pmdi_plugin_settings');
add_action( 'wp_ajax_upload_docx', 'local_file_upload' );
add_action( 'wp_ajax_dropbox_callback', 'pmdi_dropbox_file_download' );
add_action( 'wp_ajax_google_callback', 'pmdi_google_file_download' );
add_action( 'content_save_pre', 'remove_empty_lines' );
add_filter ('the_content', 'pmdi_signature');
//update_option('Plugmatter_di_License', '');
register_uninstall_hook(__FILE__ , 'plugmatter_documentimporter_uninstall' );

function plugmatter_documentimporter_uninstall() {
	delete_option('wpmdi_client_id');
	delete_option('wpmdi_client_secret');
	delete_option('google_api_key');
	delete_option('wpmdi_dropbox_app_key');
	delete_option('Plugmatter_DI_PACKAGE');
	delete_option('Plugmatter_di_License');
}

function remove_empty_lines( $content ){
  // replace empty lines
  $content = preg_replace("/&nbsp;/", "", $content);
  return $content;
}

add_action( 'post_updated', 'pmdi_save_postdata' );
function pmdi_save_postdata( $post_id ) {
	$pmdi_hidden_field = $_POST['pmdi_hidden_field'];
	if($pmdi_hidden_field == "true") {
		add_post_meta($post_id, 'pmdi_docimporter', true);
	}
}


function pmdi_signature($content) {
   if((is_single() && get_post_meta( get_the_ID(), 'pmdi_docimporter', true )) && Plugmatter_DI_PACKAGE == "plugmatter_documentimporter_lite") {
      $content.= '<p style="color:#CF1626;">Content imported by <em>Document Importer by <a style="color:#CF1626;" href="http://plugmatter.com/document-importer" target="_blank">Plugmatter</a></em></p>';
   }
   return $content;
}


function pmdi_plugin_settings() {
	add_options_page('Document Importer Settings', 'Document Importer by Plugmatter', 'manage_options', 'pmdi_settings','pmdi_settings_page');
}

function pmdi_settings_page(){
	if(get_option('Plugmatter_di_License') == "") {
   		require_once( plugin_dir_path( __FILE__ ) . 'license.php');
   	} else {   		
   		require_once( plugin_dir_path( __FILE__ ) . 'pmdi_settings.php');
   	}
}

/* Meta box setup function. */
function pmdi_setup() {
	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'pmdi_meta_box' );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function pmdi_meta_box() {
	wp_register_style('pmdi_metabox_css', plugins_url('/css/meta_box.css', __FILE__));
	wp_enqueue_style('pmdi_metabox_css');
	add_meta_box(
		'pmdi_post_class',			// Unique ID
		'Document Importer',		// Title
		'pmdi_post_class_meta_box',		// Callback function
		'post',					// Admin page (or post type)
		'side',					// Context
		'high'					// Priority
	);
	add_meta_box(
		'pmdi_post_class',			// Unique ID
		'Document Importer',		// Title
		'pmdi_post_class_meta_box',		// Callback function
		'page',					// Admin page (or post type)
		'side',					// Context
		'high'					// Priority
	);    
}

function pmdi_google_file_download() {
	$file_id = $_POST['file_id'];
	require_once( plugin_dir_path( __FILE__ ) . 'google_auth/src/Google_Client.php');
	require_once( plugin_dir_path( __FILE__ ) . 'google_auth/src/contrib/Google_DriveService.php');
	require_once( plugin_dir_path( __FILE__ ) . 'google_auth/src/contrib/Google_Oauth2Service.php');
	$google_client_id = get_option('wpmdi_client_id');
	$google_secret_id = get_option('wpmdi_client_secret');
	//$google_redirect_uri = get_option('wpmdi_redirect_uri');
	$google_redirect_uri = site_url().'/wp-admin/admin-ajax.php?action=google_callback';
	$client = new Google_Client();
	$client->setApprovalPrompt('auto');
	$client->setClientId($google_client_id);
	$client->setClientSecret($google_secret_id);
	$client->setRedirectUri($google_redirect_uri);
	$client->setScopes(array(
  		'https://www.googleapis.com/auth/drive'));
	$client->setUseObjects(true);
	$service = new Google_DriveService($client);
	session_start();
	if ($_SESSION['token']) {
		unset($_SESSION["token"]);
	}
	
	$auth_url = $client->createAuthUrl(array('https://www.googleapis.com/auth/drive.file')) . "&state=".$file_id;
	if (!$_GET['code']) {			
			echo $auth_url;			
			exit;			
		}	
	$_SESSION['token'] = $client->authenticate();	

	if(isset($_GET['code'])) {
		echo "<div style='margin:auto;margin: 40% auto auto; font-family: arial ,sans-serif; text-align: center;font-size:12px;color:gray;'><p>PROCESSING DOCUMENT</p><img id='' src='". plugins_url('/images/loading.GIF', __FILE__) ."' /></div>";       	       			
		$client->setAccessToken($client->getAccessToken($_SESSION['token']));	
		require_once( plugin_dir_path( __FILE__ ) . '/simple_html_dom.php');
		$html_str = downloadFile($service, $_GET['state']);		
		$html = str_get_html($html_str);
		$i = 1;
		$time = time();
		// Find all images
		if(!empty($html)) {
			foreach($html->find('a') as $a) {
			 	
			 	$link = $a->href;
				$pattern = 'http://www.google.com/url?q=';
				
				if(strcmp($pattern, $link)) {
					$query = parse_url($link, PHP_URL_QUERY);
				 	parse_str($query, $params);
					$parsed_link = $params['q'];
					if(isset($params['q']))	{
						$html = str_replace(
			                		      "href=\"$link\"",
			                		      "href=\"$parsed_link\"",         		
			                		      $html
			            	   		  );
					} else {
						$html = str_replace(
			                		      "href=\"$link\"",
			                		      "href=\"$link\"",         		
			                		      $html
			            	   		  );
					}			
						
				} 		 	
			}
		}
		
		$html = str_get_html($html);
		if(!empty($html)) {
			foreach($html->find('img') as $element) {
				$url = $element->src;
				$content = wp_remote_get($url);
				$response = wp_remote_retrieve_body( $content );
				$content_type = $content["headers"]["content-type"];
				$image_type = explode("/", $content_type); 
				$destination = wp_upload_dir();
				$type = "image/".$image_type[1];
				if($type == $content_type) {
					$img_path = $destination['path']."/".$time."_image".$i.".".$image_type[1];
					$fp = fopen($img_path, "w");
					fwrite($fp, $response);
					fclose($fp);
					chmod($img_path, 0777);	

					$file = $time."_image".$i.".".$image_type[1];
					$file_path = $destination['path']."/".$file;          
	                $file_url = $destination['url']."/".$file;
	                $wp_filetype = wp_check_filetype($file, null);
	                $attachment = array(
	                     'guid'           => $file_url,
	                     'post_mime_type' => $wp_filetype['type'],
	                     'post_title'     => $file,
	                     'post_status'    => 'inherit',
	                     'post_date'      => date('Y-m-d H:i:s')
	                );
	                $attachment_id = wp_insert_attachment($attachment, $file_path);
	                $attachment_data = wp_generate_attachment_metadata($attachment_id, $file_path);
	                wp_update_attachment_metadata($attachment_id, $attachment_data);

					$html = str_replace(
	                		      "src=\"$url\"",
	                		      "src=\"" .
	                		      $destination['url']."/".$time."_image".$i.".".$image_type[1]."\"",         		
	                		      $html
	            	   		  );

					$i++;
				} else {
					//echo "file not downloaded<br>";
				}
			}
		} 
		preg_match('/<style.*?>(.*?)<\/style>/', $html, $styles);
		$html_styles = $styles[1];
		
       	preg_match('/<body(.*)<\/body>/s', $html, $matches);
       	$html_body = $matches[0];
       	add_filter('the_content', 'remove_empty_tags_recursive', 20, 1);
       	$html = clean_html_format($html_body, $html_styles);
       	$html = remove_empty_tags_recursive ($html);
       	
		echo "<div id='hidden_google_file' style='border:1px solid #dddddd;width:850px;margin:auto;padding:25px;display:none'>".$html."</div>";
		echo "<script>
				var file_content = document.getElementById('hidden_google_file').innerHTML;
				window.opener.pgwdi_google_file(file_content);
				window.close();
			  </script>"; 
	}
	die(1);
}

/**
* This Function Cleans the html from unwanted css.
*/
function clean_html_format ($raw_html, $raw_style) {
	$html = $raw_html;
	$style = $raw_style;
	$class_pttn = '/class="([^"]*)"/';
	while (preg_match($class_pttn, $html)) {
		$styles = '';
		preg_match($class_pttn, $html, $classes);
		$styles = 'style="'.class2style($classes[1], $style).'"';
		$html = str_replace($classes[0], $styles, $html);
	} 
	return $html;
}


/**
* This function Replaces classes with styles
*/
function class2style($classes, $style) {
	$styles = '';
	$classes = explode(" ", $classes);
	foreach ($classes as $class) {
		$class_pttn = "/.$class{(.*?)}/";
		preg_match($class_pttn, $style, $class_style);
		$styleJson = css2json($class_style[1]);

		if(array_key_exists('text-align', $styleJson)) {
	      $styles .= $styleJson['text-align'].";"; 
	    }
	    
	    if(array_key_exists('font-weight', $styleJson)) {
	      $styles .= $styleJson['font-weight'].";";
	    }
	    
	    if(array_key_exists('font-size', $styleJson)) {
	      $styles .= $styleJson['font-size'].";"; 
	    }

	    if(array_key_exists('color', $styleJson)) {
	      $styles .= $styleJson['color'].";";
	    }
	    
	    if(array_key_exists('text-decoration', $styleJson)) {
	      $styles .= $styleJson['text-decoration'].";";
	    }

	    if(array_key_exists('font-style', $styleJson)) {
	      $styles .= $styleJson['font-style'].";";
	    }
	}
	return $styles .= "line-height:auto;";
}


/**
* This function splits the css and returns its properties in form of json
*/
function css2json($css) {
	$css_properties = explode(";", $css);
	foreach ($css_properties as $class_styles) {
		$css_name = explode(":", $class_styles);
		$json[$css_name[0]] = $class_styles;
	}
	return $json;
}


function remove_empty_tags_recursive ($str, $repto = NULL) {
	$str = force_balance_tags($str);
	$pattern = '/<(?!img|IMG)[^\/>][^>]*><\/[^>]+>/'; 
	while(preg_match($pattern, $str)) {
		$str = preg_replace (
	        //** Pattern written by Junaid Atari.
	        ///<[^\/>][^>]*><\/[^>]+>/
	        '/<(?!img|IMG)[^\/>][^>]*><\/[^>]+>/',
	        //** Replace with nothing if string empty.
	        !is_string ($repto) ? '' : $repto,

	        //** Source string
	        $str
	    );
	} 
	return $str;
}

function downloadFile($service, $file) {
  	$file = $service->files->get($file);
  	$export_links = $file->getExportLinks();
  	$downloadUrl = $export_links["text/html"];
  	if ($downloadUrl) {
    	$request = new Google_HttpRequest($downloadUrl, 'GET', null, null);
    	$httpRequest = Google_Client::$io->authenticatedRequest($request);
    	if ($httpRequest->getResponseHttpCode() == 200) {
    		return $httpRequest->getResponseBody();
    	} else {
    		// An error occurred.
    		return null;
    	}
  	} else {
    	// The file doesn't have any content stored on Drive.
    	return null;
  	}
}


function pmdi_dropbox_file_download() {
	$varr = $_POST['dropbox_url'];
	$varr = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($varr)); 
	$varr = html_entity_decode($varr,null,'UTF-8');
	$request = wp_remote_get( $varr, array( 'timeout' => 120, 'httpversion' => '1.1') );	
	$response = wp_remote_retrieve_body( $request );
	$destination = wp_upload_dir();
	$destination_path = $destination['path'];
	$file = $destination_path."/doccontents.docx";
	$fp = fopen($file, "w");
	fwrite($fp, $response);
	fclose($fp);
	chmod($file, 0777);
	if($file) {
			require_once( plugin_dir_path( __FILE__ ) . '/class.DOCX-HTML.php');				
			$extract = new DOC_CONVERTER();
			$extract->docxPath = $file;
    		$extract->Init();
    		$doc_data = $extract->output;
    		if($doc_data){
    			add_filter('the_content', 'remove_empty_tags_recursive', 20, 1);
    			$new_doc_data = remove_empty_tags_recursive ($doc_data);
    			echo $new_doc_data;
    			if (file_exists($file)) {                  
            		unlink($file);            
        		}
    		} else {
    			echo "Error:Error converting file";
    		}
		
	} else {
		echo "does not exist";
	} 
die(1);	
}


function local_file_upload() {	
	if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$uploadedfile = $_FILES['plugmatter_browse_docx'];    			
	
	if (($_FILES["plugmatter_browse_docx"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document")) {
		$upload_overrides = array( 'test_form' => false );
		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
		if ( $movefile ) {
    		if($movefile["error"]) {
    			echo "Error:". $movefile;
    		} else {    						
				require_once( plugin_dir_path( __FILE__ ) . '/class.DOCX-HTML.php');				
				$extract = new DOC_CONVERTER();
    			$extract->docxPath = $movefile["file"];
    			$extract->Init();    			
    			$doc_data = $extract->output;
    			if($doc_data){    				
    				add_filter('the_content', 'remove_empty_tags_recursive', 20, 1);    				
    				$new_doc_data = remove_empty_tags_recursive ($doc_data);
    				echo $new_doc_data;
    				unlink($movefile["file"]);
    			} else {
    				echo "Error:Error converting file";
    			}
    		}
		} else {
    		echo "Possible file upload attack!\n";
		}
	} else {
		echo "Error:upload only word document";
	}
	die(1);
}

/*Drop Box*/
function pmdi_dropbox( $good_protocol_url, $original_url, $_context){
    if ( FALSE === strpos($original_url, 'dropbox') or FALSE === strpos($original_url, '.js')) {
        return $url;
    } else {
    	remove_filter('clean_url','pmdi_dropbox',10,3);
      	$url_parts = parse_url($good_protocol_url);
      	return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' id='dropboxjs' data-app-key='".get_option('wpmdi_dropbox_app_key');
    }
}



/* Display the post meta box. */
function pmdi_post_class_meta_box( $object, $box ) { 
	wp_enqueue_script('jquery');
	wp_register_script('dropboxjs','http://www.dropbox.com/static/api/2/dropins.js');
	wp_enqueue_script('dropboxjs');
	add_filter('clean_url','pmdi_dropbox',10,3);
	wp_register_script( 'custom-script', plugins_url( '/js/custom-script.js', __FILE__ ) );		
	wp_enqueue_script('custom-script');
	wp_register_script( 'filepicker', plugins_url( '/js/filepicker.js', __FILE__ ) );		
	wp_enqueue_script('filepicker');
	$api_key = get_option('google_api_key');
	wp_register_script('googlejs','https://www.google.com/jsapi?key='.$api_key);
	wp_enqueue_script('googlejs');
	wp_register_script('googlejs2','https://apis.google.com/js/client.js?onload=initPicker');
	wp_enqueue_script('googlejs2');
	
	wp_nonce_field( basename( __FILE__ ), 'docimporter_post_class_nonce' );
	$script_params = array(
    	'google_api_key' => get_option('google_api_key'),
    	'google_client_id' => get_option('wpmdi_client_id'),
		'google_secret_id' => get_option('wpmdi_client_secret'),
		'dropbox_app_key' => get_option('wpmdi_dropbox_app_key')
	);
	wp_localize_script( 'googlejs2', 'scriptParams', $script_params );
	echo "<div class='pmdi_inside'><label for=\"pmdi_post_class\">Select a .docx File:</label>";
	echo "<div id='pmdi_message'></div>";
	if(get_option('Plugmatter_di_License') == "" ) {
		echo "<input disabled='disabled' type='button' id='plugmatter_import_docx_dis' style='background: url( ". plugins_url('/images/localdrive.png', __FILE__). ")' value=''>";
		echo "<input disabled='disabled' type='button' id='google_btn_dis' style='background: url( ". plugins_url('/images/gdrive.png', __FILE__). ")' value=''>";
		echo "<input disabled='disabled' type='button' id='dropbox_btn_dis' style='background: url( ". plugins_url('/images/dropbox.png', __FILE__). ")' value=''>";
		echo "<div id='pmdi_names'><p class='pmdi_p_gray'>Computer</p><p class='pmdi_p_gray' id='pmdi_google_p'>Google Drive</p><p class='pmdi_p_gray' id='pmdi_dropbox_p'>Dropbox</p></div>";
		echo "<div id='pmdi_loading'><p ><img id='loading' src='". plugins_url('/images/loading.GIF', __FILE__) ."' /></p></div></div>";
		echo "<p id='pmdi_update_msg'>Enter <a href='". get_option('siteurl') . "/wp-admin/admin.php?page=pmdi_settings'>license key</a> to begin using the plugin</p>";
	}

	if(Plugmatter_DI_PACKAGE == 'plugmatter_documentimporter_pro' || Plugmatter_DI_PACKAGE == 'plugmatter_documentimporter_single') {
		if(get_option('Plugmatter_di_License') != "" ) {
			echo "<input type='button' id='plugmatter_import_docx' style='background: url( ". plugins_url('/images/localdrive.png', __FILE__). ")' value=''>";
			echo "<input type='button' id='google_btn' style='background: url( ". plugins_url('/images/gdrive.png', __FILE__). ")' value=''>";
			echo "<input type='button' id='dropbox_btn' style='background: url( ". plugins_url('/images/dropbox.png', __FILE__). ")' value=''>";
			echo "<div id='pmdi_names'><p>Computer</p><p id='pmdi_google_p'>Google Drive</p><p id='pmdi_dropbox_p'>Dropbox</p></div>";
			echo "<div id='pmdi_loading' class='pmdi_loading_pro'><p ><img id='loading' src='". plugins_url('/images/loading.GIF', __FILE__) ."' /></p></div></div>";
		}
	}

	if(Plugmatter_DI_PACKAGE == 'plugmatter_documentimporter_lite') {
		if(get_option('Plugmatter_di_License') != "" ) {
			echo "<input type='button' id='plugmatter_import_docx' style='background: url( ". plugins_url('/images/localdrive.png', __FILE__). ")' value=''>";
			echo "<input disabled='disabled' type='button' id='google_btn_dis' style='background: url( ". plugins_url('/images/gdrive.png', __FILE__). ")' value=''>";
			echo "<input disabled='disabled' type='button' id='dropbox_btn_dis' style='background: url( ". plugins_url('/images/dropbox.png', __FILE__). ")' value=''>";
			echo "<div id='pmdi_names'><p>Computer</p><p class='pmdi_p_gray' id='pmdi_google_p'>Google Drive</p><p class='pmdi_p_gray' id='pmdi_dropbox_p'>Dropbox</p></div>";
			echo "<div id='pmdi_loading'><p ><img id='loading' src='". plugins_url('/images/loading.GIF', __FILE__) ."' /></p></div></div>";
			echo "<p id='pmdi_update_msg'><a href='http://plugmatter.com/document-importer' target='_blank'>Upgrade</a> to enable Google Drive & Dropbox</p>";
		}
	}
	echo "<img id='pmdi_half_banner' src='http://plugmatter.com/images/pmdi/pmdi_half_banner.png' width='' height=''>";
	echo '<input type="hidden" id="pmdi_hidden_field" name="pmdi_hidden_field" value="false">';
}

?>