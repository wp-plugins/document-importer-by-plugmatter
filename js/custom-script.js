jQuery(document).ready(function($) {
    $("body").append("<form name='docx_uploader' target='upload_iframe' id='frm_uploader' method='post' action='admin-ajax.php?action=upload_docx' enctype='multipart/form-data'>");
    $("#frm_uploader").append("<div style='position:absolute;top:-100px;'><input type='file' id='plugmatter_browse_docx' name='plugmatter_browse_docx'></div>");
    $("#frm_uploader").append("<div id='upload'></div>");

    $("#plugmatter_browse_docx").change(function(event) {
        $("#loading").show();
        var import_count = $("#pmdi_import_count").text();
        import_count = parseInt(import_count, 10) + 1;
        $("#pmdi_import_count").text(import_count);
        $("body").append("<iframe id='upload_iframe' name='upload_iframe' width='0' height='0' border='0'>");
        $("#frm_uploader").submit(); 

        $("#upload_iframe").load(function() {
            var result = $(this).contents().find('body').html();
            if(result.substring(0,6) == "Error:") {
                $("#pmdi_message").text(result.split(":")[1]);
                $("#loading").hide();
            } else {
                if($("#content_ifr").length == 0) {
                    $("#content").html(result);
                } else {
                    $("#content_ifr").contents().find('body').html(result); 
                }
                
                $("#pmdi_hidden_field").val("true");
                $("#loading").hide();
            }
            $(this).remove();
        });               
    });

   $("#plugmatter_import_docx").click(function(){
        $('.switch-tmce').trigger('click');
        $("#plugmatter_browse_docx").click();
    });

    $("#dropbox_btn a").click(function() {
        $('.switch-tmce').trigger('click');
    });
});


jQuery("#dropbox_btn").click(function(){
    var dropbox_api_key = jQuery.trim(scriptParams.dropbox_app_key);
    if(dropbox_api_key != '') {
        Dropbox.choose({
                // Required. Called when a user selects an item in the Chooser.
            success: function(files) {
                var dropbox_url = encodeURIComponent(files[0].link);
                    jQuery("#loading").show();
                    jQuery.ajax({
                            type: "POST",
                            url: 'admin-ajax.php?action=dropbox_callback',
                            data: { 'dropbox_url':dropbox_url },
                            success: function(data){ // any action to be performed after function.php returns a value.
                                jQuery("#content_ifr").contents().find('body').html(data);
                                var import_count = $("#pmdi_import_count").text();
                                import_count = parseInt(import_count, 10) + 1;
                                $("#pmdi_import_count").text(import_count);
                                jQuery("#loading").hide();
                            },
                            dataType: 'text'
                    });
            },

            // Optional. Called when the user closes the dialog without selecting a file
            // and does not include any parameters.
            cancel: function() {

            },

            // Optional. "preview" (default) is a preview link to the document for sharing,
            // "direct" is an expiring link to download the contents of the file. For more
            // information about link types, see Link types below.
            linkType: "direct", // or "direct"

            // Optional. A value of false (default) limits selection to a single file, while
            // true enables multiple file selection.
            multiselect: false, // or true

            // Optional. This is a list of file extensions. If specified, the user will
            // only be able to select files with these extensions. You may also specify
            // file types, such as "video" or "images" in the list. For more information,
            // see File types below. By default, all extensions are allowed.
            extensions: ['.docx'],
        });
    } else {
        if (confirm("Please configure Dropbox API by going to Settings > Document Importer by Plugmatter")) { 
                location.href =  pmdi_site_url+'/wp-admin/admin.php?page=pmdi_settings';
        }
        return false;
        //alert("Please Enter the Dropbox Api Key in Plugmatter DocImporter Settings and then click on this button");
    }
});

    function pgwdi_google_file(file_content) {
        jQuery("#content_ifr").contents().find('body').html(file_content);
    }


    function initPicker() {
         var google_api_key = jQuery.trim(scriptParams.google_api_key);
         var google_client_id = scriptParams.google_client_id;    

            var picker = new FilePicker({
                apiKey: google_api_key,
                clientId: google_client_id,
                buttonEl: document.getElementById('google_btn'),
                onSelect: function(file) {
                console.log(file);
                    jQuery.ajax ({
                        type: "POST",
                        url: 'admin-ajax.php?action=google_callback',
                        dataType: 'text',
                        data: { 'file.id':file.id },
                            success: function(data){ 
                                var popup = window.open(data, '_blank', 'width=350,height=550');                          
                            }  
                    });             
                }
            }); 
    }