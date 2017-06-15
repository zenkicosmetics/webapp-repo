<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2>Targets</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
    </div>
</div>
<div class="clearfix"></div>
<div id="account-body-wrapper" style="margin:20px 0 0 40px">
    <div class="ym-grid">
        <div class="ym-gl"><button type="button" id="btnAddNewPhone" class="input-btn btn-yellow">Add New Target</button></div>
    </div>

    <br />
    <div class="clearfix"></div>
    <div class="ym-grid">
        <div class="ym-gl">
            <div id="gridwraper">
                <div id="searchTableResult">
                    <table id="dataGridResult"></table>
                    <div id="dataGridPager"></div>
                </div>
            </div>
            <div class="clear-height"></div>
        </div>
    </div>
</div>

<!-- Content for dialog -->
<div class="hide">
    <div id="addTargetWindow" title="Add Target" class="input-form dialog-form"></div>
</div>

<script type="text/javascript" src="<?php echo $this->config->item('asset_url'); ?>system/virtualpost/modules/account/js/Target.js"></script>
<script>
    jQuery(document).ready(function ($) {
        $('input:checkbox.customCheckbox').checkbox({cls: 'jquery-safari-checkbox'});
        $('.jquery-safari-checkbox').tipsy({gravity: 'sw', html: true, live: true});

        Target.init('<?php echo base_url(); ?>', 10, '<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>');
    });

</script>