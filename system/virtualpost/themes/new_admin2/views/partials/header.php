<div class="ym-grid">
	<div class="ym-g50 ym-gl">
		<div id="logo">
            <a href="<?php echo base_url('scans/todo')?>">
			    <?php $logo_url = Settings::get(APConstants::SITE_LOGO_CODE);
    		        if (empty($logo_url)) {
                        $logo_url = APContext::getImagePath().'/logo_white_beta.png';
                    }else {
                        $logo_url = APContext::getAssetPath().$logo_url;
                    }
    		    ?>
    			<img src="<?php echo $logo_url?>" height="40" />
			</a>
		</div>
	</div>
	
	<div class="ym-g50 ym-gr">
	    <div style="float: right; margin-right: 15px; color: #c3dbf2;margin-top: 10px">
	        <?php echo date('d.m.Y H:i:s').' '.date_default_timezone_get();?>
	    </div>
	    <div style="clear: both;"></div>
	    <div id="user-logout" style="float: right; margin-right: 10px;">
			<a id="adminLogoutButton" href="#">Logout</a>
		</div>
		<div style="float: right;" id="user-nav">
			<a id="myAccountButton" href="#"><?php echo APContext::getAdminLoggedIn()->display_name;?></a>
		</div>
		<?php if (APContext::isSupperAdminUser()) { ?>
	    <div style="float: right; margin-right: 10px; margin-top: 27px;display:none;">
	        <span style="color: #FFFFFF">Instances:</span>
			<?php echo my_form_dropdown(array(
                 "data" => $list_instances,
                 "value_key" => 'instance_id',
                 "label_key" => 'name',
                 "value" => APContext::getCurrentInstanceId(),
                 "name" => 'instance_id',
                 "id"    => 'instance_id',
                 "clazz" => 'input-txt-none',
                 "style" => 'width: 150px;',
                 "has_empty" => true
             ));?>
		</div>
	    <?php }?>
	</div>
</div>	
<div class="ym-clearfix"></div>	
<div style="display: none;">
    <input type="hidden" id="hiddenCheckCurrentMainScreen" value="1"/>
</div>
<script type="text/javascript">

$(document).ready( function() {
    /**
     * Process when user click to logout button.
     */
    $('#adminLogoutButton').click(function() {
    	// Show confirm dialog
		$.confirm({
			message: 'Are you sure you want to logout?',
			yes: function() {
				document.location = '<?php echo base_url()?>admin/logout';
			}
		});
		return false;
    });
	 
});
</script>