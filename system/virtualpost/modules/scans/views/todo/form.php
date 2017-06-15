<script type="text/javascript">
    var token_key = '<?php echo $token_key?>';
    var envelope_id = '<?php echo $envelope_id?>';
    var scan_type = '<?php echo $scan_type?>';
</script>
<!-- Add css -->
<?php Asset::css('scans/style.css'); ?>
<?php Asset::css('pdf.js/viewer.css'); ?>
<div id="errorMessagePopup" style="display:none; color: red;font-weight: bold;margin-left: 15px; margin-bottom: 10px;"></div>
<div style="clear: both;height: 1px;"></div>
<div id="container" class="ym-grid" style="margin: 0 auto;">
	
   <div id="DwtPluginContainer"  class="ym-g70 ym-gl" style="width: 700px; float: left;">
        <div id="DWTcontainer">
            <div id="outerContainer">
                <div id="dwtcontrolContainer"></div>
                <div id="DWTContainerID" class='divcontrol' ></div>
            </div>

            <div style="clear: both;height: 10px;"></div>
            <div id="DWTemessageContainer"></div>

        </div>
   </div>
    <div id="displayUploadFileContainer" class="ym-g70 ym-gl" style="width: 680px; margin-right: 20px; float: left; display: none;">
    </div>
    
    <div id="ScanWrapper" class="ym-g30 ym-gr" style="width: 300px;">
        <div id="divScanner" class="divinput">
            <div id="div_ScanImage" class="divTableStyle">
                <ul id="ulScaneImageHIDE">
                    <li><b>Select Source:</b></li>
                    <li><select size="1" id="source"
                        style="position: relative; width: 250px;" class="input-txt-none"
                        onchange="source_onchange()">
                            <option value=""></option>
                    </select></li>
                    <li id="divProductDetail"></li>
                    <li style="display: none;" id="pNoScanner"><a
                        href="javascript: void(0)" class="ShowtblLoadImage"
                        style="font-size: 11px; background-color: #f0f0f0; position: relative"
                        id="aNoScanner"><b>What if I don't have a scanner/webcam
                                connected?</b> </a></li>
                    <li id="divProductDetail"></li>
                    <li style="text-align: center;" class="hide"><input id="btnScan"
                        class="bigbutton" style="color: #C0C0C0;" disabled="disabled"
                        type="button" value="Scan" onclick="DW_AcquireImage();" />&nbsp;&nbsp;<a
                        id="showDetail" style="display: none;" href="javascript: void(0)"
                        class="ShowtblCanNotScan">Can't Scan</a></li>
                </ul>
            </div>
        </div>
        <div style="position:relative" class="divinput" id="divEdit">
            <div class="divTableStyle">
            <b>Edit Image:</b>
            <img onclick="btnRotateLeft_onclick()" id="btnRotateL" alt="Rotate Left" title="Rotate Left" src="<?php echo APContext::getImagePath(true)?>/RotateLeft.png">
            <img onclick="btnRotateRight_onclick()" id="btnRotateR" alt="Rotate Right" title="Rotate Right" src="<?php echo APContext::getImagePath(true)?>/RotateRight.png">
            </div>
        </div>
        <div id="divSave" class="divinput" style="position: relative">

            <div class="divTableStyle">
                <ul>
                    <li><b>Select PDF file to upload:</b></li>
                    <li>
                        <button id="buttonUploadPdfFile">Upload</button>
                        <a id="linkViewUploadFile" href="#" class="iframe" style="display: none">View</a>
                        <button id="buttonLoadImageFile">Load Image</button>
                        <div id="loading" class="loadingIcon" style="display:none; height:50px;width:50px; margin-top:45px; margin-left:5px;">
                        </div>
                    </li>
                </ul>
                <div style="display: none;">
                    <label for="txt_fileName">File
                            Name: <input type="text" size="20" id="txt_fileName" />
                    </label>
                    <label for="imgTypejpeg2"> <input type="radio" value="jpg"
                        name="ImageType" id="imgTypejpeg2" onclick="rd_onclick();" />JPEG
                    </label> <label for="imgTypetiff2"> <input type="radio"
                        value="tif" name="ImageType" id="imgTypetiff2"
                        onclick="rdTIFF_onclick();" />TIFF
                    </label> <label for="imgTypepng2"> <input type="radio" value="png"
                        name="ImageType" id="imgTypepng2" onclick="rd_onclick();" <?php if ($scan_type === '1') {?>checked="checked" <?php }?> />PNG
                    </label> <label for="imgTypepdf2"> <input type="radio" value="pdf"
                        name="ImageType" id="imgTypepdf2" <?php if ($scan_type === '2') {?>checked="checked" <?php }?>
                        onclick="rdPDF_onclick();" />PDF
                    </label><label for="MultiPageTIFF"><input type="checkbox"
                        id="MultiPageTIFF" />Multi-Page TIFF</label> <label
                        for="MultiPagePDF"><input type="checkbox" id="MultiPagePDF"
                        <?php if ($scan_type === '2') {?>checked="checked" <?php }?> />Multi-Page PDF </label> <input id="btnUpload"
                        type="button" value="Upload Image" onclick="btnUpload_onclick()" />
                </div>
            </div>
        </div>
    </div>
</div>

<input type="file" id="imagepath_banner" name="imagepath"
	style="display: none; visibility: hidden;" />
<input type="hidden" id="scanForm_envelope_id"
	value="<?php echo $envelope_id?>" />
<input type="hidden" id="scanForm_token_key"
	value="<?php echo $token_key?>" />
<input type="hidden" id="scanForm_UseOCRFlag"
	value="1" />
<input type="hidden" value="<?php echo $check_page_flag?>" name="check_page_flag" id="check_page_flag" />
<script>
if (scan_type == '1') {
    $('#scanEnvelopeWindow').dialog('option', 'title', 'Envelope scan');
} else if (scan_type == '2') {
    $('#scanEnvelopeWindow').dialog('option', 'title', 'Item scan');
}
</script>
<script type="text/javascript">
$(document).ready( function() {
	$('#linkViewUploadFile').fancybox({
		width: 1100,
		height: 700,
		hideOnOverlayClick: false,
		opacity: true
	});
    
    $("#buttonUploadPdfFile").on('click', function (){
    	$('#imagepath_banner').click();
    });
    
    /**
     * When select file
     */
    $('#imagepath_banner').on('change', function(){
    	myfile= $( this ).val();
 	    var ext = myfile.split('.').pop();
 	    if(ext.toUpperCase() != "PDF"){
 	       $('#container').css('visibility', 'hidden');
 	       $.displayError('Please select pdf file to upload.', null, function() {
  	    	  $('#container').css('visibility', '');
               });
 	        return;
 	    }
		$('#loading').show();
		// $('#scanEnvelopeWindow').dialog('close');
		// Upload data here
		$.ajaxFileUpload({
				id: 'imagepath_banner',
				data: {
						customer_token_key: token_key,
						envelope_id: envelope_id,
						scan_type: scan_type,
						action_type: 'upload',
						number_page: 1,
						check_page_flag: $("#check_page_flag").val()
				},
				url: '<?php echo base_url()?>scans/todo/execute_scan',
				success: function(data) {
					$('#loading').hide();
					if (data && data.status) {
						var linkUrl = data.data.private_path;
						$('#displayUploadFileContainer').html("<iframe id='displayUploadFileContainer_iframe' src='" + linkUrl + "' style='height:450px; width:100%'><iframe>");
						$('#displayUploadFileContainer').show();
						$('#DwtPluginContainer').hide();
						$('#DWTemessageContainer').hide();
						
						// $('#linkViewUploadFile').attr('href', linkUrl);
						// $('#linkViewUploadFile').show();
						// $('#linkViewUploadFile').click();
						
						$('#scanItemTemporaryFlag_id').val(scan_type);
						$('#current_scan_type').val(scan_type);
						if($("#check_page_flag").val() != '1'){
						    $('#dataGridResult').jqGrid("setSelection",envelope_id);
						}
						$('#documentType').val('1'); //'1': upload file; '2': scan file
					} else {
						$.displayError(data.message);
					}	
				}
		});
    });

    /**
     * Load image file to scanner
     */
    $('#buttonLoadImageFile').button().live('click', function() {
    	DWObject.IfShowFileDialog = true;
        DWObject.LoadImageEx("", 3);
    });
});
</script>

<!-- Add js -->
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/themes/new_admin2/resources/dynamsoft.webtwain.config.js"></script>
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/themes/new_admin2/resources/dynamsoft.webtwain.initiate.js"></script>
<!--<script src="<?php //echo $this->config->item('asset_url'); ?>system/virtualpost/themes/new_admin2/resources/addon/dynamsoft.webtwain.addon.ocrpro.js"></script>-->
<script src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/themes/new_admin2/resources/addon/dynamsoft.webtwain.addon.pdf.js"></script>
<?php Asset::js('scans/online_demo_operation.js'); ?>
<?php Asset::js('scans/online_demo_initpage.js'); ?>
<?php //Asset::js('jquery.upload-1.0.0.min.js'); ?>
<?php echo Asset::render(); ?>
<script>
$('#DW_btnRemoveCurrentImage, #DW_btnRemoveAllImages').button();
</script>
