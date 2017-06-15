<!-- Term of service-->
<div>
    <form id="addTermAndConditionForm" method="post" class="dialog-form"  action="<?php echo base_url() ?>account/setting/add_term_condition_enterprise">
        <table border="0px">
            <tr>
                <td>
                    <?php echo form_textarea(array('id' => 'content_temp', 'name' => 'content_temp', 'value' => $content, 'rows' => 10)); ?>
                </td>
            </tr>
        </table>

        <input type="hidden" id="content" name="content" value="" />
    </form>
</div>
<?php Asset::js('ckeditor/ckeditor.js'); ?>
<?php Asset::js('ckeditor/adapters/jquery.js'); ?>
<script type="text/javascript">
    $(document).ready(function () {
        var editor = CKEDITOR.instances['content_temp'];
        if (editor) {
            editor.destroy(true);
        }
        CKEDITOR.config.height = "250";
        CKEDITOR.replace('content_temp');

        $('button').button();
    });
</script>