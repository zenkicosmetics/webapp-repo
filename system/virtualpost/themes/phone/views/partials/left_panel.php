<!-- section -->
<section>
    <?php $i = 0; ?>
    <?php
    if (!isset($current_user_id)) {
        $current_user_id = "";
    }
    if (!isset($current_phone_number)) {
        $current_phone_number = "";
    }
    ?>
    <?php foreach ($phone_users as $user) {
        $i++;?>
        <ul class="left-nav" >
            <li class="postbox_title <?php if ($user->customer_id === $current_user_id && $method === 'index') { ?>act<?php } ?>">
                <a href="<?php echo base_url() ?>phones/index?u=<?php echo $user->customer_id ?>" style="font-weight: bold;"
                   id="envelope_leftmenu_<?php echo $user->customer_id ?>">
                    <?php echo APUtils::autoHidenTextByLabel($user->user_name, 17); ?>
                <span class="toggle_control" id="menu_content_<?php echo $user->customer_id ?>">Title</span></a></li>

                <div id="phonenumber_menu_content_<?php echo $user->customer_id ?>" class="hide_title" style="display: none;">
                    <?php 
                    foreach ($user->list_phonenumber as $phonenumber) {
                    ?>     
                    
                    <li <?php if ($phonenumber->phone_number === $current_phone_number) { ?> class="act" <?php } ?>>
                        <a href="<?php echo base_url() ?>phones/index?u=<?php echo $phonenumber->customer_id ?>&phone_number=<?php echo $phonenumber->phone_number ?>"
                            id="new_leftmenu_<?php echo $phonenumber->customer_id ?>_<?php echo $phonenumber->phone_number ?>">
                            <?php echo $phonenumber->phone_number ?>
                        </a>
                    </li>
                    <?php } ?>
                </div>
        </ul>
        <div class="ym-clearfix">&nbsp;</div>
<?php } ?>
</section>
<?php
if (!isset($p)) {
    $p = "";
}
?>
<script type="text/javascript">
$(document).ready(function () {
    <?php
    foreach ($phone_users as $user) {
        if ($current_user_id == $user->customer_id) {
            ?>
            toggle_control_menu('menu_content_' + <?php echo $current_user_id ?>);
        <?php }
    } ?>

    $('.toggle_control').click(function () {
        var id = $(this).attr('id');
        toggle_control_menu(id);
        return false;
    });

    /**
     * Toggle control menu
     */
    function toggle_control_menu(id) {
        if ($('#phonenumber_' + id).css('display') == 'none') {
            $('#phonenumber_' + id).show();
            $(this).removeClass('hide_title');
        } else {
            $('#phonenumber_' + id).hide();
            $(this).addClass('hide_title');
        }
        return false;
    }
});
</script>