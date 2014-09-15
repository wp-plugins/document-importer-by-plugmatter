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
		$xml = new DOMDocument();
		$xml->load($xmlFile);

		$xsl = new DOMDocument();
        //include( plugin_dir_path( __FILE__ ) . '/class.DOCX-HTML.php');
		$xsl->load(plugin_dir_path( __FILE__ ) .'DocX2Html.xslt');
		$proc = new XSLTProcessor();
		$proc->importStyleSheet($xsl); 
    	$htmloutput = $proc->transformToXML($xml);   	
    	
		$img_patern = "'src\s*=\s*([\"\'])?(?(1) (.*?)\\1 | ([^\s\>]+))'isx";    	
    	preg_match_all($img_patern, $htmloutput, $docimgs);
    	    
  /*  	$idImgs = array();
        foreach ($docimgs[0] as $dats) {
            $datsFiltered = explode('"', $dats);
            if (preg_match('/^\?image=rId/', $datsFiltered[1])) {
                $datFiltered = explode('?image=', $dats);
                $idImgs[] = substr($datFiltered[1], 0, -1);
            }
        }


  
   		$xmlrels = $this->tempDir."/word/_rels/document.xml.rels";
   		$rels_val = file_get_contents($xmlrels);
   		$relationsImgs = simplexml_load_string($rels_val);
        $pathImgs = array();
        $img_type = array();
        foreach ($relationsImgs->Relationship as $relImg) {        	
            if ($relImg["Type"] == "http://schemas.openxmlformats.org/officeDocument/2006/relationships/image") {            	
                $pathImgs[(string) $relImg["Id"]] = (string) $relImg["Target"];
                $img_name = explode("/",$pathImgs[(string) $relImg["Id"]]);
                $img_type[(string) $relImg["Id"]] = $img_name[1];                       
            }
        } 
        
   		$file_path = wp_upload_dir();
       
        foreach ($idImgs as $datsIdImgs) {
                	$htmloutput = str_replace(
                		      "src=\"?image=$datsIdImgs\"",
                		      "src=\"" .
                		      $file_path['url']."/".$this->time."_".$img_type[$datsIdImgs]."\"",         		
                		      $htmloutput
            	   		  );                   
        } 

        */
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
        
        include( plugin_dir_path( __FILE__ ) . '/simple_html_dom.php');
        $html = str_get_html($htmloutput);

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