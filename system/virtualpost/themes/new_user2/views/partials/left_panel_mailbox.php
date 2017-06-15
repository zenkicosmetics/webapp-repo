<?php $i = 0;?>
<?php 
$list_color = APContext::getListColors();
if(!isset($current_postbox)){
    $current_postbox = "";
}
$postboxs = array();
$user_name = "";
if(!empty($customer_users)){
    $customer_user = $customer_users[0];
    $postboxs = $customer_user->list_postbox;
    $user_name = $customer_user->user_name;
}
?>
<!-- search postboxes -->
<?php if(count($postboxs) >= 5){ ?>
<div id="top-search-postbox">
    <input type="text" id="myInput" onkeyup="search_postbox()" placeholder="Search for postbox .." /><i class="fa fa-search icon-search" aria-hidden="true"></i>
</div>
<div class="ym-clearfix">&nbsp;</div>
<?php }?>

<!-- postboxes -->
<div class="bd-content">
    <!-- section -->
    <section id="mySection">
        <?php if(count($postboxs) > 0) {?>
        <?php if(count($postboxs) > 1) {?>
            <!--Start all part-->   
            <div style="line-height: 30px; width: 250px;" class="left-menu-item-header">
                <div style="margin-left: 5px;">
                    <a class="left-menu-item-header" href="<?php echo base_url()?>mailbox/index?p=0" style="font-weight: bold;text-decoration: none;">
                        <?php language_e('them_user_view_part_left_panel_mailbox_All') ?></a>
                </div>
            </div>    
            <ul class="left-nav" >
                <li <?php if (0 === $current_postbox && $method === 'news') {?> class="act" <?php }?>><a href="<?php echo base_url()?>mailbox/news?p=0"
                        id="new_leftmenu_0"><?php language_e('them_user_view_part_left_panel_mailbox_New') ?></a> 
                </li>
                <li <?php if (0 === $current_postbox && $method === 'scans') {?> class="act" <?php }?>><a href="<?php echo base_url()?>mailbox/scans?p=0"
                        id="scan_leftmenu_0"><?php language_e('them_user_view_part_left_panel_mailbox_Scan') ?></a>
                </li>
                <li <?php if (0 === $current_postbox && $method === 'instore') {?> class="act" <?php }?>><a href="<?php echo base_url()?>mailbox/instore?p=0"
                        id="items_instore_leftmenu_0"><?php language_e('them_user_view_part_left_panel_mailbox_InStor') ?></a>
                </li>
                <li <?php if (0 === $current_postbox && $method === 'trash') {?> class="act" <?php }?>><a href="<?php echo base_url()?>mailbox/trash?p=0"
                        id="trash_leftmenu_0"><?php language_e('them_user_view_part_left_panel_mailbox_Trash30') ?></a>
                </li>
            </ul>    
            <div class="ym-clearfix">&nbsp;</div>
            <?php } ?>
            <!--End all part-->    
            <div style="line-height: 30px; width: 250px;" class="left-menu-item-header">
                <div style="margin-left: 5px;">
                    <?php echo APUtils::autoHidenTextByLabel( $user_name, 30);?>
                </div>
            </div>
            <?php } ?>
            <?php foreach ($postboxs as $postbox) { $i++;?>
                <ul class="left-nav" >
                    <li style="display: none;"><?php echo $postbox->location_name?></li>
                    <li style="display: none;"><?php echo $postbox->postbox_code?></li>
                    <li style="display: none;"><?php echo $postbox->postbox_name?></li>
                    <li style="display: none;"><?php echo $postbox->name?></li>
                    <li style="display: none;"><?php echo $postbox->company?></li>
                    <li class="postbox_title <?php if ( $postbox->postbox_id === $current_postbox && $method === 'index') {?>act<?php }?>">
                        <a href="<?php echo base_url()?>mailbox/index?p=<?php echo $postbox->postbox_id?>" style="font-weight: bold;"
                                    id="envelope_leftmenu_<?php echo $postbox->postbox_id?>">
                                <?php echo APUtils::autoHidenTextByLabel( $postbox->postbox_name, 17);?>
                                <span class="toggle_control" id="menu_content_<?php echo $postbox->postbox_id?>">Title</span></a></li>
                        <div id="postbox_menu_content_<?php echo $postbox->postbox_id?>" class="hide_title" style="display: none;">
                        <li <?php if ($postbox->postbox_id === $current_postbox && $method === 'news') {?> class="act" <?php }?>><a
                                href="<?php echo base_url()?>mailbox/news?p=<?php echo $postbox->postbox_id?>"
                                id="new_leftmenu_<?php echo $postbox->postbox_id?>"><?php language_e('them_user_view_part_left_panel_mailbox_New') ?></a> 

                                <?php if ($postbox->number_new_item > 0) {?>
                                <span class="new-count"><?php echo $postbox->number_new_item?></span>
                            <?php }?>
                        </li>
                        <li <?php if ($postbox->postbox_id === $current_postbox && $method === 'scans') {?> class="act" <?php }?>><a
                                href="<?php echo base_url()?>mailbox/scans?p=<?php echo $postbox->postbox_id?>"
                                id="scan_leftmenu_<?php echo $postbox->postbox_id?>"><?php language_e('them_user_view_part_left_panel_mailbox_Scan') ?></a></li>
                        <li <?php if ($postbox->postbox_id === $current_postbox && $method === 'instore') {?> class="act" <?php }?>><a
                                href="<?php echo base_url()?>mailbox/instore?p=<?php echo $postbox->postbox_id?>"
                                id="items_instore_leftmenu_<?php echo $postbox->postbox_id?>"><?php language_e('them_user_view_part_left_panel_mailbox_InStor') ?></a></li>
                        <li <?php if ($postbox->postbox_id === $current_postbox && $method === 'trash') {?> class="act" <?php }?>><a
                                href="<?php echo base_url()?>mailbox/trash?p=<?php echo $postbox->postbox_id?>"
                                id="trash_leftmenu_<?php echo $postbox->postbox_id?>"><?php language_e('them_user_view_part_left_panel_mailbox_Trash30') ?></a></li>

                        </div>
                </ul>
                <div class="ym-clearfix">&nbsp;</div>
            <?php } ?>
    </section>
</div>

<?php 
if(!isset($p)){
    $p = "";
}
?>
<script type="text/javascript">
$(document).ready( function() {
    // Style panel mailbox
    if(document.getElementById("left_notification_bar") !== null){
        $(".bd-content").slimScroll({height:($(window).height() - 465)+'px',railVisible: true, alwaysVisible: true, color: "#<?php echo $list_color['COLOR_010']; ?>"});
    }else if (document.getElementById("collect_shipping_container") !== null){
        $(".bd-content").slimScroll({height:($(window).height() - 220)+'px',railVisible: true, alwaysVisible: true, color: "#<?php echo $list_color['COLOR_010']; ?>"});
    }else{
        $(".bd-content").slimScroll({height:($(window).height() - 195)+'px',railVisible: true, alwaysVisible: true, color: "#<?php echo $list_color['COLOR_010']; ?>"});
    }
 
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
   
    window.onbeforeunload = function() {
        sessionStorage.setItem('name', $('#myInput').val());
        if($('#myInput').val() === null){
            sessionStorage.removeItem('name');
        }
    }

    window.onload = function() {
        var name = sessionStorage.getItem('name');
        if (name != null && name !== "undefined"){
            $('#myInput').val(name);
            search_postbox();
        }
    }
    
});

 /**
    * Process when user input text (Location/Postbox ID/Postbox Name/Postbox Company Name) in  filter/search textbox and press enter key.
    */
   function search_postbox() {
        var input;
        var filter;
        var section;
        var ul;
        var li_locaion;
        var li_postbox_code;
        var li_postbox_name;
        var li_name;
        var li_company ;
        var i;
        input = $("#myInput");
        if(input == null || input == undefined || input.value == null || input.value == undefined){
            return true;
        }

        filter = input.value.toUpperCase();
        section = document.getElementById("mySection");
        ul = section.getElementsByTagName("ul");
        for (i = 1; i < ul.length; i++) {
            li_locaion = ul[i].getElementsByTagName("li")[0];
            li_postbox_code = ul[i].getElementsByTagName("li")[1];
            li_postbox_name = ul[i].getElementsByTagName("li")[2];
            li_name = ul[i].getElementsByTagName("li")[3];
            li_company = ul[i].getElementsByTagName("li")[4];

            if (li_locaion || li_postbox_code || li_postbox_name || li_name ||li_company) {
              if (li_locaion.innerHTML.toUpperCase().indexOf(filter) > -1 || li_postbox_code.innerHTML.toUpperCase().indexOf(filter) > -1 || 
                  li_postbox_name.innerHTML.toUpperCase().indexOf(filter) > -1 || li_name.innerHTML.toUpperCase().indexOf(filter) > -1 ||
                  li_company.innerHTML.toUpperCase().indexOf(filter) > -1) {

                ul[i].style.display = "";
              } else {
                ul[i].style.display = "none";
              }
            } 
        }
    }
</script>