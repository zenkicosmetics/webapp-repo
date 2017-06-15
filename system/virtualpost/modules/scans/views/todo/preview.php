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
    <link href="" rel="stylesheet" type="text/css" />
    <!--#if !(FIREFOX || MOZCENTRAL || CHROME)-->
    <?php Asset::js("pdf.js/compatibility.js"); ?>
   
    <?php echo Asset::render(); ?>

    <?php 
    $frontend_theme = Settings::get(APConstants::ADMIN_THEMES_CODE);
    $web_path = APPPATH . 'themes/' . $frontend_theme . '/';
    ?>
    <script type="text/javascript">
        //Now we assign the path of the file on server which we stored in our ViewBag to the THEFILEPATH variable
        //which is on line 2 of viewer.js, alternatively we could have returned a querystring with a file param
        //which would point to the file, e.g: "Viewer?file=/MyPDFs/Pdf1.pdf"
        var THEFILEPATH = '<?php echo $preview_file->file_name?>';
        var THEFILESCALE = 2;
       
        if (THEFILEPATH != '') {
            if ('<?php echo $has_scan_item_type?>' == '1') {
                // Change status
                window.parent.$('#markCompletedButton').addClass('yl');
                window.parent.$('#markCompletedButton').addClass('input-btn');
                window.parent.$('#markCompletedButton').removeClass('input-btn-disable');
                window.parent.$('#markCompletedButton').prop('disabled', false);
                //window.parent.$('#current_scan_type').val('1');
            } else if ('<?php echo $has_scan_item_type?>' == '2') {
                // Change status
                window.parent.$('#markCompletedButton').addClass('yl');
                window.parent.$('#markCompletedButton').addClass('input-btn');
                window.parent.$('#markCompletedButton').removeClass('input-btn-disable');
                window.parent.$('#markCompletedButton').prop('disabled', false);
                //window.parent.$('#current_scan_type').val('2');
            }
        }
    </script>
    <script type="text/javascript">
    jQuery(document).ready(function($){
        var open_preview_envelope_id = '<?php echo $preview_file->envelope_id?>';
        var file_type = '<?php echo $preview_file->type?>';
        $("#mainContainer").live('click', function() {
            open_document_full(open_preview_envelope_id);
        });

        function open_document_full(envelope_id) {
             if (file_type == '2') {
                 var submitUrl = '<?php echo $preview_file->file_name?>';
                 window.parent.$('#display_document_full').attr('href', submitUrl);
                 window.parent.$('#display_document_full').click();
             } else if (file_type == '1') {
                 var submitUrl = '<?php echo $preview_file->file_name?>';
                 window.parent.$('#display_envelope_full').attr('href', submitUrl);
                 window.parent.$('#display_envelope_full').click();
             }
        }
    });
    </script>
    <style type="text/css"> 
    #scan_preview{
        
        overflow: hidden;
        height: 100%;
        width: 406px;
        align-content: center;
        vertical-align: top; 
    }
    
    @-moz-document url-prefix() { 
       #scan_preview{ 
           height: 110%;
       }
   }
   
   /* Css hack chrome */
   @media screen and (-webkit-min-device-pixel-ratio:0) {
        #scan_preview{ 
           height: 110%;
           margin-top: -54px;
           min-height: 200px;
          
       }
    }
   </style>
    </head>  
    
    <body style="height: 100%;">
        <embed id="scan_preview" src="<?php echo $preview_file->file_name?>"></embed>
        <div id="mainContainer" style="width: 5000px;height: 5000px; opacity: 0;"></div>
     </body>
    
    </html>
    <?php } else {?>
    <script type="text/javascript">
        var image_envelope_file = '<?php echo $preview_file->file_name?>';
        if (image_envelope_file != '' && '<?php echo $envelope->envelope_scan_flag?>' != '1') {
            // Change status
            window.parent.$('#markCompletedButton').addClass('yl');
            window.parent.$('#markCompletedButton').addClass('input-btn');
            window.parent.$('#markCompletedButton').removeClass('input-btn-disable');
            window.parent.$('#markCompletedButton').prop('disabled', false);
            //window.parent.$('#current_scan_type').val('1');
        }
    </script>
    <img id="documentItemPreviewFile" style="cursor: pointer;" src="<?php echo $preview_file->file_name?>" width="380" height="190">
    <?php }?>
<?php }else if($envelope->trash_flag == 0 || $envelope->trash_flag == 5){?>
        <script type="text/javascript">
            // Change status
            var trash_flag = "<?php echo $envelope->trash_flag ?>";
                if(trash_flag === "0" || trash_flag === "5"){
                window.parent.$('#markCompletedButton').addClass('yl');
                window.parent.$('#markCompletedButton').addClass('input-btn');
                window.parent.$('#markCompletedButton').removeClass('input-btn-disable');
                window.parent.$('#markCompletedButton').prop('disabled', false);
                //window.parent.$('#current_scan_type').val('1');
            }
        </script>
    <?php
    }
?>