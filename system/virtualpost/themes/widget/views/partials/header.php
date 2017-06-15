<header style="background-color: #<?php echo Settings::get(APConstants::MAIN_COLOR_CODE)?>"> 
	<div class="ym-grid">
    	<div class="ym-g50 ym-gl">
    		<div id="logo">
    		    <a href="<?php echo base_url()?>">
    			<?php $logo_url = Settings::get(APConstants::SITE_LOGO_CODE);
    		        if (empty($logo_url)) {
                        $logo_url = APContext::getImagePath().'/logo_white_beta.png';
                    } else {
                        $logo_url = APContext::getAssetPath().$logo_url;
                    }
    		    ?>
    			<img src="<?php echo $logo_url?>" height="50" />
    			</a>
    		</div>
    	</div>
    	
    	<div class="ym-g50 ym-gr">
    		<div id="top-nav">
    		<div id="user-logout" style="float: right;">
    				<a href="#" id="customerLogoutButton001"><?php language_e('them_widg_view_part_head_Logout')?></a>
    			</div>
    			<div id="user-nav" style="float: right;">
    				<a href="<?php echo base_url()?>account" id="myAccountButton"><?php language_e('them_widg_view_part_head_MyAcco')?></a>
    			</div>
    			<div id="user-info" style="float: right;">
    				<a href="<?php echo base_url()?>info/view_pricing_inline" id="customerInfoButton"><?php language_e('them_widg_view_part_head_Info')?></a>
    			</div>
    			
    			<div id="top-search">
    				<input type="text" id="mainSearchTextbox" placeholder="<?php language_e('them_widg_view_part_head_Seac')?>" />
    				<input type="submit" value="Search" />
    			</div>
    			
    		</div>
    	</div>
    </div>
</header>
<div class="ym-grid" id="header_banner" style="height: 10px; background: #<?php echo Settings::get(APConstants::SECOND_COLOR_CODE)?>">
</div>
<div style="display: none;">
    <form ID="mainSearchForm" action="<?php echo base_url()?>mailbox/index" method="POST">
        <input type="hidden" name="p" value="<?php echo $current_postbox?>" />
        <input type="hidden" name="fullTextSearchFlag" value="1" />
        <input type="hidden" id="mainSearchForm_fullTextSearchValue" name="fullTextSearchValue" value="" />
    </form>
</div>
<div style="display: none;">
    <input type="hidden" id="hiddenCheckCurrentMainScreen" value="1"/>
</div>
<script type="text/javascript">
$(document).ready( function() {
    /**
     * Process when user click to logout button.
     */
    $('#customerLogoutButton001').live('click', function() {
    	// Show confirm dialog
		$.confirm({
			message: 'Are you sure you want to logout?',
			yes: function() {
				document.location = '<?php echo base_url()?>customers/logout';
			}
		});
		return false;
    });

    /**
     * Process when user input text in search textbox and press enter key.
     */
    $('#mainSearchTextbox').keydown(function(e){
        if(e.keyCode == 13) {
            var searchText = $('#mainSearchTextbox').val();
            if ($.isEmpty(searchText)) {
                return false;
            }
            
            // Submit search form
            $('#mainSearchForm_fullTextSearchValue').val($('#mainSearchTextbox').val());
            $('#mainSearchForm').submit();
        }
    });
});
</script>