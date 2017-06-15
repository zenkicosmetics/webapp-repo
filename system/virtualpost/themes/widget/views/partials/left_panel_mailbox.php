<!-- section -->
<section>
    <?php $i = 0;?>
    <?php foreach ($postboxs as $postbox) { $i++?>
	<ul class="left-nav" >
	    <li class="postbox_title <?php if ($postbox->postbox_id === $current_postbox && $method === 'index') {?>act<?php }?>">
	        <a href="<?php echo base_url()?>mailbox/index?p=<?php echo $postbox->postbox_id?>" style="font-weight: bold;"
			    id="envelope_leftmenu_<?php echo $postbox->postbox_id?>">
			<?php echo APUtils::autoHidenTextByLabel( $postbox->postbox_name, 17);?>
			<span class="toggle_control" id="menu_content_<?php echo $postbox->postbox_id?>">Title</span></a></li>
		<div id="postbox_menu_content_<?php echo $postbox->postbox_id?>" class="hide_title" style="display: none;">
		<li <?php if ($postbox->postbox_id === $current_postbox && $method === 'news') {?> class="act" <?php }?>><a
			href="<?php echo base_url()?>mailbox/news?p=<?php echo $postbox->postbox_id?>"
			id="new_leftmenu_<?php echo $postbox->postbox_id?>">New</a> 
			
			<?php if ($postbox->number_new_item > 0) {?>
			<span class="new-count"><?php echo $postbox->number_new_item?></span>
		    <?php }?>
		</li>
		<li <?php if ($postbox->postbox_id === $current_postbox && $method === 'scans') {?> class="act" <?php }?>><a
			href="<?php echo base_url()?>mailbox/scans?p=<?php echo $postbox->postbox_id?>"
			id="scan_leftmenu_<?php echo $postbox->postbox_id?>">Scanned</a></li>
		<li <?php if ($postbox->postbox_id === $current_postbox && $method === 'instore') {?> class="act" <?php }?>><a
			href="<?php echo base_url()?>mailbox/instore?p=<?php echo $postbox->postbox_id?>"
			id="items_instore_leftmenu_<?php echo $postbox->postbox_id?>">In storage</a></li>
		<li <?php if ($postbox->postbox_id === $current_postbox && $method === 'trash') {?> class="act" <?php }?>><a
			href="<?php echo base_url()?>mailbox/trash?p=<?php echo $postbox->postbox_id?>"
			id="trash_leftmenu_<?php echo $postbox->postbox_id?>">Trash (30d)</a></li>
		
		</div>
	</ul>
	<div class="ym-clearfix">&nbsp;</div>
    <?php } ?>
</section>
<script type="text/javascript">
$(document).ready( function() {
	<?php foreach ($postboxs as $postbox) { 
	    if ($p == $postbox->postbox_id) {
    ?>
    toggle_control_menu('menu_content_' + <?php echo $p?>);
	<?php }}?>
	
    $('.toggle_control').click(function(){
        var id = $(this).attr('id');
        toggle_control_menu(id);
        return false;
    });

    /**
     * Toggle control menu
     */
    function toggle_control_menu(id) {
    	if ($('#postbox_' + id).css('display') == 'none') {
        	$('#postbox_' + id).show();
        	$(this).removeClass('hide_title');
        } else {
        	$('#postbox_' + id).hide();
        	$(this).addClass('hide_title');
        }
        return false;
    }
});
</script>