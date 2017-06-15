<form id="ownDomainWidgetSettingForm" action="<?php echo base_url() . 'account/setting/generate_own_domain_widget'; ?>" method="post" >
    <table style="margin-top: 0px;">
        <tr>
            <td style="width: 460px">
                <table style="width: 460px;">
                    <tr>
                        <th colspan="2">Your widget code:</th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <textarea id="ownDomainWidgetSettingForm_widget" style="width: 400px; height: 150px"><?php echo $html_widget?></textarea>
                        </th>
                    </tr>
                    <tr>
                        <td style="width:90px">Title <span class="required">*</span></td>
                        <td style="width:360px"><input type="text" class="input-txt" style="width: 310px" name="title_login" id="ownDomainWidgetSettingForm_title_login"
                           value="<?php echo $title_login?>" /></td>
                    </tr>
                    <tr>
                        <td>Button Text <span class="required">*</span></td>
                        <td><input type="text" class="input-txt" style="width: 310px" name="button_text"  id="ownDomainWidgetSettingForm_button_text"
                                   value="<?php echo $button_text?>" /></td>
                    </tr>
                </table>
            </td>
            <td style="width: 400px" id="testWidgetContainer">
                <?php echo $html_widget?>
            </td>
        </tr>
    </table>
    
</form>
<?php Asset::js('clipboard.min.js'); ?>
<?php echo Asset::render(); ?>
<script type="text/javascript">
$(document).ready(function(){
    $('#ownDomainWidgetSettingForm_title_login').change(function(){
        var title_login = $('#ownDomainWidgetSettingForm_title_login').val();
        if (title_login == '') {
            $.displayError('Title is required input');
            return;
        }
        reGenerateWidgetCode();
    });
    
    $('#ownDomainWidgetSettingForm_button_text').change(function(){
        var button_text = $('#ownDomainWidgetSettingForm_button_text').val();
        if (button_text == '') {
            $.displayError('Button text is required input');
            return;
        }
        reGenerateWidgetCode();
    });
    
    function reGenerateWidgetCode() {
        var submitUrl = $('#ownDomainWidgetSettingForm').attr('action');
        if ($.isEmpty(submitUrl)) {
            return;
        }
        $.ajaxSubmit({
            url: submitUrl,
            formId: 'ownDomainWidgetSettingForm',
            success: function (response) {
                if (response.status) {
                    var html_widget = response.data.html_widget;
                    $('#ownDomainWidgetSettingForm_widget').html(html_widget);
                    $('#testWidgetContainer').html(html_widget);
                } else {
                    $.displayError(response.message);
                }
            }
        });
    }
    
    var clipboard = new Clipboard('#OwnDomainWidgetSettingWindow_copyCodeToClipboardButton', {
        target: function() {
            return document.querySelector('#ownDomainWidgetSettingForm_widget');
        }
    });

    clipboard.on('success', function(e) {
        $.displayInfor('Copy to clipboard successfully. ');
    });
});
</script>