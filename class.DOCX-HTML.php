<?php

//include 'xslt/XMLTypeDescription.inc.php';
//include 'xslt/XSLTransformationFilter.inc.php';
//include 'xslt/XSLTransformer.inc.php';

class DOC_CONVERTER {

    function Init() {
       $this->UnZipDocx();
       $this->extractRelXML();
       $this->extractXML(); 
       $file_path = wp_upload_dir();
       $dirPath = $file_path['path']."/doccontents";
        if (file_exists($dirPath)) {                  
            $this->rrmdir($dirPath);            
        }            
    }
   
    function UnZipDocx() {
        WP_Filesystem();
        $file_path = wp_upload_dir();
        $targetDir = $file_path['path'] ."/doccontents";
        $this->tempDir = $targetDir;

        if(file_exists($targetDir)) {
            $this->rrmdir($targetDir); 
        } 

        unzip_file( $this->docxPath, $targetDir);         
        $sourcedir = $file_path['path'] ."/doccontents/word/media/";
        $destdir = $file_path['path']."/";
        if(file_exists($sourcedir)) {
            $file_time = $this->recurse_copy($sourcedir,$destdir);
            $this->time = $file_time; 
        }                      
    }



    function extractRelXML(){
        $xmlFile = $this->tempDir."/word/_rels/document.xml.rels";
        $xml = file_get_contents($xmlFile);
        if($xml == false){
            return false;
        }
        $xml = mb_convert_encoding($xml, 'UTF-8', mb_detect_encoding($xml));
        $parser = xml_parser_create('UTF-8');
        $data = array();
        xml_parse_into_struct($parser, $xml, $data);
        foreach($data as $value){
            if($value['tag']=="RELATIONSHIP"){
                //it is an relationship tag, get the ID attr as well as the TARGET and (if set, the targetmode)set into var.
                if(isset($value['attributes']['TARGETMODE'])){
                    $this->rels[$value['attributes']['ID']] = array(0 => $value['attributes']['TARGET'], 3=> $value['attributes']['TARGETMODE']);
                } else {
                    $this->rels[$value['attributes']['ID']] = array(0 => $value['attributes']['TARGET']);
                }
            }
        }
        return true;
    }



    function recurse_copy($src,$dst) {        
        $dir = opendir($src); 
        $time = time();  
        $img_path = wp_upload_dir(); 

        while(false !== ( $file = readdir($dir)) ) { 

            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 

                    recurse_copy($src . '/' . $file,$dst . '/' . $time .'_'. $file); 
                    chmod($dst."/". $time .'_'. $file ,'0777');

                    $file_path = $dst . '/' . $time .'_'. $file;          
                    $file_url = $img_path['url'] . '/' . $time .'_'. $file;
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

                } else { 
                    copy($src . '/' . $file,$dst . '/' . $time .'_'. $file);
                    chmod($dst."/". $time .'_'. $file  , 0777);

                    $file_path = $dst . '/' . $time .'_'. $file;          
                    $file_url = $img_path['url'] . '/' . $time .'_'. $file;
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
                } 
            } 
        } 
        return $time;      
        closedir($dir); 
    }

      
    function extractXML() {

    	$xmlFile = $this->tempDir."/word/document.xml";  

		$pmdi_key = get_option("Plugmatter_di_License");

        if(Plugmatter_DI_PACKAGE == 'plugmatter_documentimporter_lite' || get_option('pmdi_xsl_enable') == 'yes') {

            $xml = file_get_contents($this->tempDir."/word/document.xml"); 

            $pmdi_key = get_option("Plugmatter_di_License");

            $pmdi_api_count = get_option("pmdi_api_count");


            $parse_html_url = 'http://api.plugmatter.com/pmdi/parsexml.php';

            $response = wp_remote_post( $parse_html_url, array(
                            'method' => 'POST',
                            'timeout' => 45,
                            'redirection' => 5,
                            'httpversion' => '1.0',
                            'blocking' => true,
                            'headers' => array(),
                            'body' => array('file_contents' => $xml, 'pmdi_key' => $pmdi_key, 'package' => Plugmatter_DI_PACKAGE ),
                            'cookies' => array()
                            )
                        );

            if ( is_wp_error( $response ) ) {
               $error_message = $response->get_error_message();
               echo "Something went wrong: $error_message";
            } else {
                $result = json_decode($response['body'], true);
                if($result["status"] == "error") {
                    echo $result["message"];
                    exit;
                } 

                if($result["status"] == "success") {
                    $htmloutput = stripslashes($result["data"]);
                    update_option("pmdi_api_count", $result["count"]);
                }
               
            }
        } else {
            $xml = new DOMDocument();
            $xml->load($xmlFile);

            $xsl = new DOMDocument();

            $xsl->load(plugin_dir_path( __FILE__ ) .'DocX2Html.xslt');

            try {
              register_shutdown_function("catch_fatal_error");
              $proc = new XSLTProcessor;
            } catch (Exception $e) {
                echo "xsl is not enable on your hosting server Please enable Remote XSL Which is under Plugmatter Document Importer Settings Page";
            }

            $proc->importStyleSheet($xsl); 
            $htmloutput = $proc->transformToXML($xml);      
        }
    	
		$img_patern = "'src\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx";    	
    	preg_match_all($img_patern, $htmloutput, $docimgs);
    	    
        $file_path = wp_upload_dir();

        foreach ($docimgs[0] as $dats) {
            $datsFiltered = explode('"', $dats);
            if (preg_match('/^\?image=rId/', $datsFiltered[1])) {
                $datFiltered = explode('?image=', $dats);
                $idImgs = substr($datFiltered[1], 0, -1);
                $path = $this->rels[$idImgs][0];
                $img_name = explode("/",$path);

                $htmloutput = str_replace(
                              "src=\"?image=$idImgs\"",
                              "src=\"" .
                              $file_path['url']."/".$this->time."_".$img_name[1]."\"",                       
                              $htmloutput
                          );

            }
        }        
        
        require_once( plugin_dir_path( __FILE__ ) . '/pmdi_simple_html_dom.php');
        $html = pmdi_str_get_html($htmloutput);

        if($html) {
            foreach($html->find('a') as $a) {    
                $link_rid = $a->href;
                $url = $this->rels[$link_rid][0];
                $html = str_replace(
                                        "href=\"$link_rid\"",
                                        "href=\"$url\"",              
                                          $html
                                      );
            }

            preg_match('/<body(.*)<\/body>/s', $html, $matches);       
        } else {
            preg_match('/<body(.*)<\/body>/s', $htmloutput, $matches);
        }
       // $pattern = "/<p[^>]*><\\/p[^>]*>/";
        //$newstr = preg_replace($pattern, '', $matches[0]);        
        
        $this->output = $matches[0];
    }

    function rrmdir($dir)
    {       
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        $this->rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }     
}