<style>
.ui-dialog .ui-dialog-titlebar-close {display: none;}

table.gridtable {
	table-layout: fixed;
  	width: 100%;
  	white-space: nowrap;
	font-size:11px;
	color:#333333;
	border-width: 1px;
	border-color: #cccccc;
	border-collapse: collapse;
}
table.gridtable th {
	white-space: nowrap;
 	overflow: hidden;
  	text-overflow: ellipsis;
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #cccccc;
	background-color: #dedede;
}
table.gridtable td {
	white-space: nowrap;
  	overflow: hidden;
  	text-overflow: ellipsis;
	border-width: 1px;
	padding: 8px;
	border-style: solid;
	border-color: #cccccc;
	background-color: #ffffff;
}
</style>
<div class="header">
    <h2 style="font-size:20px; margin-bottom: 10px">Settings > Customs</h2>
</div>
<div class="ym-grid mailbox">
    <form id="searchCountryForm" action="<?php echo base_url() ?>settings/customs" method="post">
        <div class="ym-g70 ym-gl">
            <div class="ym-grid input-item">
                <div class="ym-g20 ym-gl" style="width: 120px; text-align: left;">
                    <label style="text-align: left;">From Country:</label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    
                        echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key" => 'from_country',
                            "label_key" => 'from_country',
                            "value" => $from_country,
                            "name" => 'from_country',
                            "id" => 'from_country',
                            "clazz" => 'input-width',
                            "style" => '',
                            "has_empty" => true
                        ));
                    ?>
                </div>
                <div class="ym-g20 ym-gl" style="width: 100px; text-align: left;margin: 0px 0px 0px 35px;">
                    <label style="text-align: left;">To Country:</label>
                </div>
                <div class="ym-g30 ym-gl">
                    <?php
                    
                        echo my_form_dropdown(array(
                            "data" => $list_country,
                            "value_key" => 'from_country',
                            "label_key" => 'from_country',
                            "value" => $to_country,
                            "name" => 'to_country',
                            "id" => 'to_country',
                            "clazz" => 'input-width',
                            "style" => '',
                            "has_empty" => true
                        ));
                    ?>
                    <button id="searchCountryButton" class="admin-button">Search</button>
                </div>
            </div>
        </div>
    </form>
</div>
<?php if (!empty($map_country_customs) && count($map_country_customs) > 0) { ?>
<div id="searchTableResult" style="margin: 10px;">
    <table id="tblCustomResultId" class="gridtable" style="width: auto">
        <thead>
            <tr>
                <th style="color:blue;">CUSTOMS</th>
                <?php
                	foreach ($to_countries as $to_country_horizontal) {
                 ?>		
                <th  style="width: 20%;">
                  <?php print ($to_country_horizontal->to_country); ?> 
                </th>
                 <?php }?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($from_countries as $from_country_vertical) { ?>
                <tr> 
                    <td style="font-weight:bold;"> <?php print($from_country_vertical->from_country); ?> </td>
                    <?php
                    foreach ($to_countries as $to_country_horizontal) {
                        $key = $from_country_vertical->from_country.'_'.$to_country_horizontal->to_country;
                        $custom_flag = 0;
                        $edit_action = 1;
                        if (array_key_exists($key, $map_country_customs)) {
                            $custom_flag = $map_country_customs[$key]; 
                        }
                        if ($from_country_vertical->from_country == $to_country_horizontal->to_country) {
                            $custom_flag = 0;
                            $edit_action = 0;
                        }
                        ?>
                        <td class="td1" 
                                <?php if ($edit_action == 1) { ?>
                                style="cursor: pointer; width: 15px; background-color: #f2f2f2" 
                                <?php } else { ?>
                                style="width: 15px; background-color: #999999"
                                <?php }?>
                                data-edit_action="<?php echo $edit_action; ?>"  
                                data-from = "<?php echo $from_country_vertical->from_country ?>" 
                                data-to ="<?php echo $to_country_horizontal->to_country ?>" data-custom ="<?php echo $custom_flag ?>">
                            <?php
                            if ($custom_flag == 1) {
                                echo '<center style="color: red;">yes</center>';
                            } elseif ($custom_flag == 0) {
                                echo '';
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
            <?php } ?>

        </tbody>
    </table>
</div>
<?php } ?>
<div class="clear-height"></div>
<!-- Content for dialog -->
<div class="hide">
    <div id="editCustom" title="Edit Custom" class="input-form dialog-form"></div>
</div>
<script type="text/javascript">
    // When click to search button
    $('#searchCountryButton').button();
    $('#searchCountryButton').live('click', function() {
        var from_country = $('#from_country').val();
        var to_country = $('#to_country').val();
        if (from_country === '' && to_country === '') {
            $.displayError('Please select from country or to country.');
            return false;
        }
        $('#searchCountryForm').submit();
        return false;
    });
    $(document).ready(function () {
        var baseUrl = '<?php echo base_url(); ?>';
        var ajaxUrls = baseUrl + 'settings/customs/edit';

        $(".td1").click(function (e) {
            var editAction = $(this).data('edit_action');
            if (editAction == 0) {
                return false;
            }
            var from_country = encodeURIComponent($(this).data('from'));
            var to_country = encodeURIComponent($(this).data('to'));
            var custom_flag = $(this).data('custom');

            // Clear control of all dialog form
            $('.dialog-form').html('');
            $('#editCustom').attr('title', 'Edit Custom: ' + $(this).data('from') + ' to ' + $(this).data('to'));
            // Open new dialog
            $('#editCustom').openDialog({
                autoOpen: false,
                height: 180,
                width: 481,
                modal: true,
                open: function () {
                    var submitUrl = ajaxUrls + '?flag=' + custom_flag + '&from=' + from_country + '&to=' + to_country;
                    $(this).load(submitUrl, function () {
                        $('#custom_flag').focus();
                    });
                },
                buttons: {
                    'Save': function () {
                        saveCustom();
                    },
                    'Cancel': function () {
                        $(this).dialog('close');
                    }
                }
            });
            $('#editCustom').dialog('option', 'position', 'center');
            $('#editCustom').dialog('open');
        });

        function saveCustom() {
            var submitUrl = $('#addEditCustomForm').attr('action');
            var action_type = $('#h_action_type').val();
            var data = $('#addEditCustomForm').serializeObject();
            $.ajaxExec({
                url: submitUrl,
                data: data,
                success: function (data) {
                    if (data.status) {
                        if (action_type == 'edit') {
                            $('#editCustom').dialog('close');
                        }
                        $.displayInfor(data.message, null, function () {
                            // Reload data 
                            window.location.reload();
                            $('searchCountryForm').submit();
                        });

                    } else {
                        $.displayError(data.message);
                    }
                }
            });
        }
    });
</script>


