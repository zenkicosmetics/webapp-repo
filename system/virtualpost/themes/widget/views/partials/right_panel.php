<section>
	<div class="ym-gbox">
		<h3>Envelope</h3>
		<div class="ym-clearfix"></div>	
		<div class="box-item" id="mailbox_envelope_image"></div>
		
		<div class="ym-clearfix"></div>	
		
		<h3>Scan Document</h3>
		<div class="ym-clearfix"></div>	
		<div class="box-item" id="mailbox_document_image">
		    
		</div>
	</div>
	<div style="width: 157px; margin: 0 auto">
    	<form id="mailSaveAsForm" action="<?php echo base_url()?>mailbox/saveas" method="post">
    	    <input type="hidden" id="mailSaveAsForm_envelope_id" name="envelope_id" />
    	    <input type="button" id="saveAsButton" style="margin-top: 10px;width: 157px; margin-bottom: 0px;" value="Save As" class="input-btn c" >
    	</form>
	</div>
</section>
<div class="ym-clearfix"></div>	