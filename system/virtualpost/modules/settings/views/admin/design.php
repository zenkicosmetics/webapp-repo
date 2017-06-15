<div class="header">
    <h2 style="font-size: 20px; margin-bottom: 10px">Settings > Instances >
        Design</h2>
</div>
<form id="usesrSearchForm" method="post"
      action="<?php echo base_url() ?>admin/settings/design">
    <div class="input-form">
        <?php 
        $is_admin_site = true;
        include 'system/virtualpost/modules/account/views/settings/list_colors_setting.php';?>
        <br />
        <button id="saveDesignButton" class="admin-button">Save</button>
        <br /><br />
    </div>
</form>
<script type="text/javascript">
    $(document).ready(function () {
        $('.admin-button').button();
        $('.color_code').colorpicker({
            ok: function (event, color) {
                $(this).css("background-color", '#' + color.formatted);
            }
        });
    });
</script>