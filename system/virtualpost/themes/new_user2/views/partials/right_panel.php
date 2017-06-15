<section>
	<div class="ym-gbox">
		<h3><?php language_e('them_user_view_part_right_panel_mailbox_ScanEnvelopeImg') ?></h3>
		<div class="ym-clearfix"></div>	
		<div class="box-item" id="mailbox_envelope_image"></div>
		
		<div class="ym-clearfix"></div>	
		
		<h3><?php language_e('them_user_view_part_right_panel_mailbox_ScanDocumentImg') ?></h3>
		<div class="ym-clearfix"></div>	
		<div class="box-item" id="mailbox_document_image">
		    
		</div>
	</div>
	<div style="width: 157px; margin: 0 auto">
    	<form id="mailSaveAsForm" action="<?php echo base_url()?>mailbox/saveas" method="post">
    	    <input type="hidden" id="mailSaveAsForm_envelope_id" name="envelope_id" />
    	    <input type="button" id="saveAsButton" style="margin-top: 10px;width: 157px; margin-bottom: 0px;position: absolute;bottom: 15px;" 
                   value="<?php language_e('them_user_view_part_right_panel_mailbox_SaveAsBtn') ?>" class="input-btn c btn-yellow" >
    	</form>
	</div>
</section>
<div class="ym-clearfix"></div>	