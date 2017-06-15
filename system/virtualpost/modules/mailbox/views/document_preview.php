<?php if (!empty($preview_file)) { ?>
<?php if (APUtils::endsWith($preview_file->local_file_name, '.pdf') ) {?>
<!DOCTYPE html>
<html dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<?php Asset::css("pdf.js/viewer.css"); ?>
<?php Asset::css('jquery.fancybox-1.3.4.css'); ?>
<!--#if FIREFOX || MOZCENTRAL-->
<!--#include viewer-snippet-firefox-extension.html-->
<!--#endif-->
<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.fancybox-1.3.4.pack.js'); ?>
<!--#if !(FIREFOX || MOZCENTRAL || CHROME)-->
<?php Asset::js("pdf.js/compatibility.js"); ?>
<!--#endif-->
<?php echo Asset::render(); ?>
<?php 
$frontend_theme = Settings::get(APConstants::FRONTEND_THEMES_CODE);
$web_path = APPPATH . 'themes/' . $frontend_theme . '/';
$current_date = date('YmdHis');
$file_path = $preview_file->public_file_name;
if (!APUtils::endsWith($file_path, '.pdf')) {
	$file_path = $file_path."&d=".$current_date;
}
?>
<script type="text/javascript">
    //Now we assign the path of the file on server which we stored in our ViewBag to the THEFILEPATH variable
    //which is on line 2 of viewer.js, alternatively we could have returned a querystring with a file param
    //which would point to the file, e.g: "Viewer?file=/MyPDFs/Pdf1.pdf"
    var THEFILEPATH = '<?php echo $file_path;?>';
    var THEFILESCALE = 1.0;
    var mode = 'preview';
    // instantiate the PDF.JS web worker.
    var url = '<?php echo APContext::getAssetPath().$web_path?>js/pdf.js/worker_loader.js';
    PDFJS.workerSrc = url;
</script>
<script type="text/javascript">
jQuery(document).ready(function($){
	var open_preview_envelope_id = '<?php echo $preview_file->envelope_id?>';
	var enable_saves = '0';
	<?php if (file_exists($preview_file->local_file_name)) { ?>
        enable_saves = '1';
	<?php } ?>
	$("#mainContainer").live('click', function() {
		open_document_full(open_preview_envelope_id);
	});
	
	
    function open_document_full(envelope_id) {
    	
       	 //var submitUrl = '<?php echo base_url()?>mailbox/open_item_scan?id=' + envelope_id;
         var submitUrl = '<?php echo $file_path?>';
         window.parent.$('#display_document_full').css({"width":"90%"}).attr('href', submitUrl);
         window.parent.$('#display_document_full').click();
         
    }
    
});
</script>

<style type="text/css"> 
    #scan_preview{
        
        overflow: hidden;
        height: 100%;
        width: 210px;
        align-content: center;
        vertical-align: top; 
    }
    
 @-moz-document url-prefix() { 
    #scan_preview{ 
        height: 110%;
        margin-top: -54px;
         min-height: 410px;
    }
    
}
/* Css hack chrome */
   @media screen and (-webkit-min-device-pixel-ratio:0) {
        #scan_preview{ 
           height: 110%;
           margin-top: -55px;
           min-height: 410px;
          width: 250px;
       }
    }
</style>

</head>  
<body style="height: 100%;">
    <embed id="scan_preview" src="<?php echo $file_path;?>"></embed>
    <div id="mainContainer" style="width: 5000px;height: 5000px; opacity: 0;"></div>
 </body>
</html>
<?php }?>
<?php }?>

