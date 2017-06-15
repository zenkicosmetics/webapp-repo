<?php $cloud_id_arr = []; ?>
<div class="ym-grid">
    <div id="cloud-body-wrapper">
        <h2 class="title"><?php echo lang("cloud.title_label")?></h2>
        <p class="interface-short-intro">
        <?php language_e('cloud_view_index_short_intro') ?>
        </p>
        <div class="ym-clearfix" style="height:35px;"></div>
        <table class="border">
            <thead>
                <tr>
                    <th><?php echo lang("cloud.type")?></th>
                    <th><?php echo lang("cloud.interface_id")?></th>
                    <th>Customer code</th>
                    <th><?php echo lang("cloud.delete")?></th>
                    <th><?php echo lang("cloud.autu_save")?></th>
                    <th><?php echo lang("cloud.settings")?></th>
                </tr>
            </thead>
            <tbody>
            <?php if (!empty($customer_cloud_service)) {
                foreach ($customer_cloud_service as $cloud) {
                    if (!in_array($cloud->cloud_id, $cloud_id_arr))
                        $cloud_id_arr[] = $cloud->cloud_id;
                    ?>
                <tr>
                    <td style="text-align: center"><?php echo $cloud->interface_type?></td>
                    <td  class="left-align"><?php echo $cloud->cloud_name?></td>
                    <td  class="left-align"><?php echo $cloud->customer_code?></td>
                    <td><a class="delete delete_cloud_service" title="Delete" data-cloud_id="<?php echo $cloud->cloud_id?>" data-postbox_id="<?php  echo (!empty($cloud->postbox_id) ? $cloud->postbox_id : null) ?>">&nbsp;</a></td>
                    <td class="center-align"><input type="checkbox" disabled="disabled" class="customCheckbox" <?php if ($cloud->auto_save_flag === '1') {?> checked="checked" <?php }?> /></td>
                    <td><a class="setting cloud_setting" title="Settings" data-cloud_id="<?php echo $cloud->cloud_id?>" data-postbox_id="<?php  echo (!empty($cloud->postbox_id) ? $cloud->postbox_id : null) ?>">&nbsp;</a></td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <div class="ym-clearfix" style="height:15px;"></div>

        <a id="addNewCustomerCloud" class="main_link_color"><?php echo lang("cloud.add_new_service")?></a>
    </div>
</div>
<div class="hide" style="display: none">
        <div id="addNewService" title="<?php language_e('cloud_view_index_AddNewService'); ?>" class="input-form dialog-form">
    </div>
    <div id="editCloudSetting" title="<?php language_e('cloud_view_index_EditDropboxSetting'); ?>" class="input-form dialog-form">
    </div>
    <div id="selectDropboxFolder" title="<?php language_e('cloud_view_index_SelectDropboxFolder'); ?>" class="input-form dialog-form">
    </div>
    <div id="newDropboxFolder" title="<?php language_e('cloud_view_index_CreateNewDropboxFolder'); ?>" class="input-form dialog-form">
    </div>
        <div id="accountingEmailWindow" class="input-form dialog-form">
    </div>
</div>
<script type="text/javascript">
var selected_node = {};
selected_node.data = {};
selected_node.data.key = '';
$(document).ready( function() {
    $('input:checkbox.customCheckbox').checkbox({cls:'jquery-safari-checkbox'});
    /**
     * Process when user click to add new service.
     */
    $('#addNewCustomerCloud').click(function() {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        // Open new dialog
        $('#addNewService').openDialog({
            autoOpen: false,
            height: 220,
            width: 500,
            modal: true,
            open: function() {
                    $(this).load("<?php echo base_url() ?>cloud/add", function() {
                            $('#addCloudServiceForm_cloud_id').focus();
                    });
            },
            buttons: {
                    'Next': function() {

                                switch($('select#addCloudServiceForm_cloud_id').val()) {
                                    case '<?php echo APConstants::CLOUD_DROPBOX_CODE ?>':
                                    <?php
                                    if (!in_array(APConstants::CLOUD_DROPBOX_CODE, $cloud_id_arr)){
                                        echo "window.location = '" . base_url() . "mailbox/request_dropbox';";
                                    }
                                    else{
                                        echo "$.displayError('" . language('cloud_views_index_CloudExist') ."');";
                                    }
                                    ?>
                                        break;
                                    case '<?php echo APConstants::CLOUD_ACCOUNTING_EMAIL_CODE ?>':
                                         $('#addNewService').dialog('destroy');
                                        openAccountingWindow('Add an accounting interface','');
                                        break;
                                }
                    },
                    'Cancel': function () {
                            $(this).dialog('close');
                    },
            }
        });
        $('#addNewService').dialog('option', 'position', 'center');
        $('#addNewService').dialog('open');
        return false;
    });

    /**
     * Save cloud service
     */
    function saveCloudService() {
        var submitUrl = $('#addCloudServiceForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'addCloudServiceForm',
            success: function(data) {
                if (data.status) {
                    $('#addNewService').dialog('close');
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        document.location.href = '<?php echo base_url()?>cloud';
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * Process when user click to delete icon.
     */
    $('.delete_cloud_service').live('click', function() {
        var cloud_id = $(this).attr('data-cloud_id');
            var postbox_id = $(this).attr('data-postbox_id');
            // Show confirm dialog
            $.confirm({
                message: '<?php language_e('cloud_view_index_AreYouSureYouWantToDelete'); ?>',
                yes: function() {
                    var submitUrl = '<?php echo base_url()?>cloud/delete';
                    $.ajaxExec({
                         url: submitUrl,
                         data: {
                             cloud_id : cloud_id,
                             postbox_id : postbox_id
                         },
                         success: function(data) {
                             if (data.status) {
                                    // Reload data grid
                                    document.location.href = '<?php echo base_url()?>cloud';
                             } else {
                                    $.displayError(data.message);
                             }
                         }
                     });
                }
            });
    });

    /**
     * Process when user click to delete icon.
     */
    $('.cloud_setting').live('click', function() {
        var cloud_id = $(this).attr('data-cloud_id');
            var postbox_id = $(this).attr('data-postbox_id');
            switch(cloud_id) {
                case '<?php echo APConstants::CLOUD_DROPBOX_CODE?>':
                    editDropboxSetting(cloud_id);
                    break;
                case '<?php echo APConstants::CLOUD_ACCOUNTING_EMAIL_CODE?>':
                    openAccountingWindow('<?php language_e('cloud_view_index_EditAccountingInterface'); ?>', postbox_id);
                    break;
            }
    });

    /**
     * Edit dropbox setting.
     */
    function editDropboxSetting(cloud_id) {
        // Clear control of all dialog form
        $('.dialog-form').html('');

        var submit_url = "<?php echo base_url() ?>cloud/dropbox_setting?cloud_id=" + cloud_id;
        // Open new dialog
        $('#editCloudSetting').openDialog({
            autoOpen: false,
            height: 300,
            width: 500,
            modal: true,
            open: function() {
                $(this).load(submit_url, function() {
                    $('#editCloudSettingForm_login').focus();
                });
            },
            buttons: {
                'Save': function() {
                    saveDropboxSetting();
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#editCloudSetting').dialog('option', 'position', 'center');
        $('#editCloudSetting').dialog('open');
        return false;
    }

    /**
     * Save cloud service
     */
    function saveDropboxSetting() {
        var submitUrl = $('#editCloudSettingForm').attr('action');
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'editCloudSettingForm',
            success: function(data) {
                if (data.status) {
                    $('#editCloudSetting').dialog('close');
                    $.displayInfor(data.message, null,  function() {
                        // Reload data grid
                        document.location.href = '<?php echo base_url()?>cloud';
                    });

                } else {
                    $.displayError(data.message);
                }
            }
        });
    }

    /**
     * Process when user click to add new service.
     */
    $('#editCloudSettingForm_folder_name').live('click', function() {
        // Open new dialog
        $('#selectDropboxFolder').openDialog({
            autoOpen: false,
            height: 500,
            width: 500,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>cloud/select_folder", function() {

                });
            },
            buttons: {
                'New Folder': function() {
                    newDropboxFolder();
                },
                'Select': function() {
                    $('#editCloudSettingForm_folder_name').val(selected_node.data.key);
                    $(this).dialog('close');
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#selectDropboxFolder').dialog('option', 'position', 'center');
        $('#selectDropboxFolder').dialog('open');
        return false;
    });

    /**
     * Create new folder
     */
    function newDropboxFolder() {
        // Open new dialog
        $('#newDropboxFolder').openDialog({
            autoOpen: false,
            height: 200,
            width: 500,
            modal: true,
            open: function() {
                var new_folder_url = "<?php echo base_url() ?>cloud/new_folder";
                new_folder_url = new_folder_url + '?parent_folder_name=' + selected_node.data.key;
                $(this).load(new_folder_url, function() {

                });
            },
            buttons: {
                'Save': function() {
                    var submitUrl = $('#newDropboxFolderForm').attr('action');
                    $.ajaxSubmit({
                        url: submitUrl,
                        formId: 'newDropboxFolderForm',
                        success: function(data) {
                            if (data.status) {
                                $('#newDropboxFolder').dialog('close');
                                // Get the DynaTree object instance:
                                var tree = $("#tree").dynatree("getTree");
                                tree.reload();
                            } else {
                                $.displayError(data.message);
                            }
                        }
                    });
                    $(this).dialog('close');
                },
                'Cancel': function () {
                    $(this).dialog('close');
                }
            }
        });
        $('#newDropboxFolder').dialog('option', 'position', 'center');
        $('#newDropboxFolder').dialog('open');
        return false;
    };

        function openAccountingWindow(title, id) {
            // Open new dialog
            $('#accountingEmailWindow').openDialog({
                    title: typeof title !== 'undefined' ? title : '',
                    autoOpen: false,
                    height: 440,
                    width: 550,
                    modal: true,
                    closeOnEscape: false,
                    open: function() {
                            $(this).load("<?php echo base_url() ?>cloud/accounting_email?postbox_id=" + id, function() {});
                    },
                    buttons: {
                            'Confirm': function() {
                                    var submitUrl = $('form#accountingEmailForm').attr('action');
                                    $.ajaxSubmit({
                                        url: submitUrl,
                                        formId: 'accountingEmailForm',
                                        success: function(response) {
                                            if (response.status) {
                                                $('#accountingEmailWindow').dialog('destroy');
                                                // Reload data grid
                                                document.location.href = '<?php echo base_url()?>cloud';
                                            } else {
                                                $.displayError(response.message);
                                            }
                                        }
                                    });
                            }
                    }
            });

            $('#accountingEmailWindow').dialog('option', 'position', 'center');
            $('#accountingEmailWindow').dialog('open');
        }
});
</script>