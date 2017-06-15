<?php if (!empty($preview_file)) { ?>
<?php if (APUtils::endsWith($preview_file->local_file_name, '.pdf') ) {?>
<!DOCTYPE html>
<html dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="Cache-control" content="public">
<?php Asset::css("pdf.js/viewer.css"); ?>
<?php Asset::css('jquery.fancybox-1.3.4.css'); ?>
<!--#if FIREFOX || MOZCENTRAL-->
<!--#include viewer-snippet-firefox-extension.html-->
<!--#endif-->
<?php Asset::js('jquery-1.7.2.min.js'); ?>
<?php Asset::js('jquery.fancybox-1.3.4.pack.js'); ?>
<!--#if !(FIREFOX || MOZCENTRAL || CHROME)-->
<?php Asset::js("pdf.js/compatibility.js"); ?>

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
    var THEFILEPATH = '<?php echo $file_path?>';
    var THEFILESCALE = 1.0;
    var mode = 'preview';
   
</script>
<script type="text/javascript">
jQuery(document).ready(function($){
	var open_preview_envelope_id = '<?php echo $preview_file->envelope_id?>';
	var enable_saves = '0';
	<?php if (file_exists($preview_file->local_file_name)) { ?>
        enable_saves = '1';
	<?php } ?>
	
	$("#mainContainer").live('click', function() {
		open_envelope_full(open_preview_envelope_id);
	});
	
    function open_envelope_full(envelope_id) {
       	 //var submitUrl = '<?php echo base_url()?>mailbox/open_envelope_scan?id=' + envelope_id;
         var submitUrl = '<?php echo $file_path?>';
         window.parent.$('#display_envelope_full').css({"width":"90%"}).attr('href', submitUrl);
         window.parent.$('#display_envelope_full').click();
    }
    
});
</script>
<style type="text/css"> 
    #envelope_preview{
        
        overflow: hidden;
        height: 100%;
        width: 210px;
        align-content: center;
        vertical-align: top; 
    }
    
 @-moz-document url-prefix() { 
    #envelope_preview{ 
        height: 120%;
        margin-top: -54px;
         min-height: 210px;
    }
}

/* Css hack chrome */
   @media screen and (-webkit-min-device-pixel-ratio:0) {
        #envelope_preview{ 
           height: 110%;
           margin-top: -54px;
           min-height: 210px;
           width: 250px;
          
       }
    }
</style>
</head>  
<body style="height: 100%;">
    <embed id="envelope_preview" src="<?php echo $file_path;?>"></embed>
    <div id="mainContainer" style="width: 5000px;height: 5000px; opacity: 0;"></div>
 </body>
</html>
<?php } else if(is_object($preview_file) && isset($preview_file->envelope_id) ) {?>
<img id="envelopeItemPreviewFile" data-envelope_id="<?php echo $preview_file->envelope_id ?>" style="cursor: pointer; max-width: 250px; max-height: 100px" src="<?php echo $preview_file->public_file_name?>">
<?php }?>
<?php }?>