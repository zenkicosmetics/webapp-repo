<style>
    .input-width{
        margin: 0px;
    }
    #crop_wrapper,#crop_wrapper2 {
        max-height: 350px;
    }
    #crop_wrapper img, #crop_wrapper2 img {
        max-width:600px;
        max-height:350px;
        border: 1px solid #d3d3d3;
    }
    #crop_div{
        width:241px;
        height:50px;
        border:2px dashed #d3d;
        position:absolute;
        top:380px;
        box-sizing:border-box;
    }
    #crop_div2 {
        width:241px;
        height:50px;
        border:2px dashed #d3d;
        position:absolute;
        top:520px;
        box-sizing:border-box;
    }
</style>
<?php
$image_base_url = base_url() . "account/setting/view_file?local_file_path=";
?>
<div class="ym-grid">
    <div id="cloud-body-wrapper" style="width: 1070px">
        <h2>Design Setting</h2>
        <div class="ym-clearfix" style="height:1px;"></div>
    </div>
</div>
<div class="clearfix"></div>
<div id="account-body-wrapper" style="margin:20px 0 0 40px">
    <div class="ym-grid">
        <form id="usesrSearchForm" method="post" action="<?php echo base_url() ?>account/setting/design">
            <button type="submit" class="input-btn btn-yellow" style="margin-left: 12px">Save</button>
            <button type="button" class="input-btn btn-yellow ResetColorDesign" style="margin-left: 12px">Reset</button>
            <br />
            <div class="input-form">
                <table class="settings">
                    <tr>
                        <td style="width: 50%">
                            <?php 
                            $is_admin_site = false;
                            include 'system/virtualpost/modules/account/views/settings/list_colors_setting.php';?>
                        </td>
                        <td style="width: 50%">
                            <table>
                                <tr>
                                    <th class="input-width-100">Site Name</th>
                                    <td>
                                        <input type="text" id="SITE_NAME_CODE" name="SITE_NAME_CODE" value="<?php echo AccountSetting::get($customer_id, APConstants::SITE_NAME_CODE) ?>" class="input-width input-width-400" /><br />
                                        <small>The name of the website for page titles and for use around the site.</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="input-width-100">First letter image</th>
                                    <td>
                                        <input type="text" style="width: 245px" id="FIRST_ENVELOPE_KEY" name="FIRST_ENVELOPE_KEY" value="<?php echo AccountSetting::get($customer_id, APConstants::FIRST_ENVELOPE_KEY) ?>" class="input-width input-width-400 readonly" readonly="readonly" />
                                        <button type="button" class="tooltip input-btn btn-yellow" id="selectEnvelopeFileButton">Browser</button>
                                        <button type="button" class="tooltip input-btn btn-yellow" id="previewEnvelopeFileButton">Preview</button>
                                        <input type="file" id="imagepath02" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
                                        <small>This envelope should appear in the new postbox exactly 24 hours after the creation of the account</small>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="input-width-100">First letter item</th>
                                    <td>
                                        <input type="text" style="width: 245px" id="FIRST_LETTER_KEY" name="FIRST_LETTER_KEY" value="<?php echo AccountSetting::get($customer_id, APConstants::FIRST_LETTER_KEY) ?>" class="input-width input-width-400 readonly" readonly="readonly" />
                                        <button type="button" class="tooltip input-btn btn-yellow" id="selectFileButton">Browser</button>
                                        <button type="button" class="tooltip input-btn btn-yellow" id="previewFileButton">Preview</button>
                                        <input type="file" id="imagepath" name="imagepath" class=""	style="visibility: hidden; display: none;" /> <br />
                                        <small>This letter should appear in the new postbox exactly 24 hours after the creation of the account</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="input-width-100">Logo on main color</th>
                                    <td>
                                        <div id="crop_wrapper">
                                            <?php $site_logo_image_path = AccountSetting::get($customer_id, APConstants::SITE_LOGO_CODE); ?>
                                            <img id="SITE_LOGO_CODE_IMG" src="<?php echo !empty($site_logo_image_path) ? $image_base_url . $site_logo_image_path: "";?>"  />
                                            <div id="crop_div"></div>
                                        </div>
                                        <br />
                                        <input type="text" id="SITE_LOGO_CODE" name="SITE_LOGO_CODE" value="<?php echo AccountSetting::get($customer_id, APConstants::SITE_LOGO_CODE) ?>" class="input-width input-width-400 readonly" readonly="readonly" />
                                        <button type="button" class="tooltip input-btn btn-yellow" id="selectMainLogoButton">Change</button>
                                        <button type="button" class="tooltip input-btn btn-yellow" id="cropMainLogoButton">Crop</button>
                                        <input type="file" id="imagepath03" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
                                        <small>This image will display on the left corner of website</small>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="input-width-100">Logo on white</th>
                                    <td>
                                        <div id="crop_wrapper2">
                                            <?php $site_logo_white_image_path = AccountSetting::get($customer_id, APConstants::SITE_LOGO_WHITE_CODE); ?>
                                            <img id="SITE_LOGO_WHITE_CODE_IMG" src="<?php echo !empty($site_logo_white_image_path) ? $image_base_url . $site_logo_white_image_path: "";?>"  />
                                            <div id="crop_div2"> </div>
                                        </div>
                                        <br />
                                        <input type="text" id="SITE_LOGO_WHITE_CODE" name="SITE_LOGO_WHITE_CODE" value="<?php echo AccountSetting::get($customer_id, APConstants::SITE_LOGO_WHITE_CODE) ?>" class="input-width input-width-400 readonly" readonly="readonly" />
                                        <button type="button" class="tooltip input-btn btn-yellow" id="selectWhiteLogoButton">Change</button>
                                        <button type="button" class="tooltip input-btn btn-yellow" id="cropWhiteLogoButton">Crop</button>
                                        <input type="file" id="imagepath04" name="imagepath" class="" style="visibility: hidden; display: none;" /> <br />
                                        <small>This image will display on the left corner of website</small>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>

            <br /><br />
            <button type="submit" class="input-btn btn-yellow" style="margin-left: 12px">Save</button>
            <button type="button" class="input-btn btn-yellow ResetColorDesign" style="margin-left: 12px">Reset</button>
            <br /><br /><br />
        </form>
        <div class="clear-height"></div>
        <div class="hide" style="display: none;">
            <a id="preview_first_letter" class="iframe" href="<?php echo $image_base_url . AccountSetting::get($customer_id, APConstants::FIRST_LETTER_KEY) ?>">Preview first letter</a>
            <a id="preview_first_letter_envelope" class="iframe" href="<?php echo $image_base_url . AccountSetting::get($customer_id, APConstants::FIRST_ENVELOPE_KEY) ?>">Preview first letter envelope</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var assetPath = '<?php echo $image_base_url ?>';
        $('.input-btn').button();
        $( "#crop_div, #crop_div2" ).draggable({ containment: "parent" });
        $("#cropMainLogoButton").hide();
        $("#cropWhiteLogoButton").hide();
        $( "#crop_div, #crop_div2" ).hide();

        $("#selectFileButton").button().click(function () {
            $('#imagepath').val('');
            $('#imagepath').click();
            return false;
        });
        $("#selectEnvelopeFileButton").button().click(function () {
            $('#imagepath02').val('');
            $('#imagepath02').click();
            return false;
        });

        $("#selectMainLogoButton").button().click(function () {
            $('#imagepath03').val('');
            $('#imagepath03').click();
            
            return false;
        });

        $("#selectWhiteLogoButton").button().click(function () {
            $('#imagepath04').val('');
            $('#imagepath04').click();
            
            return false;
        });

        $('#preview_first_letter').fancybox({
            width: 1000,
            height: 800
        });

        $('#preview_first_letter_envelope').fancybox({
            width: 1000,
            height: 800
        });

        $('#displayTermsServiceLink').fancybox({
            width: 1000,
            height: 800
        });

        $('#displayPrivacyLink').fancybox({
            width: 1000,
            height: 800
        });

        $("#imagepath").change(function () {
            // Upload data here
            $.ajaxFileUpload({
                id: 'imagepath',
                data: {},
                url: '<?php echo base_url() ?>account/setting/upload',
                success: function (data) {
                    var time = new Date().getTime();
                    $('#FIRST_LETTER_KEY').val(data.message);
                    $('#preview_first_letter').attr('href', assetPath + data.message + "&t=" + time);
                }
            });
        });

        $("#imagepath02").change(function () {
            // Upload data here
            $.ajaxFileUpload({
                id: 'imagepath02',
                data: {},
                url: '<?php echo base_url() ?>account/setting/upload',
                success: function (data) {
                    $('#FIRST_ENVELOPE_KEY').val(data.message);
                    var time = new Date().getTime();
                    $('#preview_first_letter_envelope').attr('href', assetPath + data.message + "&t=" + time);
                }
            });
        });

        $("#imagepath03").change(function () {
            // Upload data here
            $.ajaxFileUpload({
                id: 'imagepath03',
                data: {},
                url: '<?php echo base_url() ?>account/setting/upload',
                success: function (data) {
                    $("#cropMainLogoButton").show();
                    $( "#crop_div" ).show();
                    
                    var time = new Date().getTime();
                    $('#SITE_LOGO_CODE_IMG').attr('src', assetPath + data.message + "&t=" + time);
                    $('#SITE_LOGO_CODE').val(data.message);
                }
            });
        });

        $("#cropMainLogoButton").click(function(){
            var posi = document.getElementById('crop_div');
            var pos = $("#crop_wrapper").position();
            var data = {
                image_path: $("#SITE_LOGO_CODE").val(),
                top: posi.offsetTop - pos.top,
                left: posi.offsetLeft - pos.left,
                right: posi.offsetWidth,
                bottom: posi.offsetHeight
            };
            $.ajaxExec({
                url: '<?php echo base_url() ?>account/setting/crop',
                data: data,
                success: function (response) {
                    $( "#crop_div" ).hide();
                    $("#cropMainLogoButton").hide();
                    
                    var time = new Date().getTime();
                    $('#SITE_LOGO_CODE_IMG').attr('src', assetPath + response.message + "&t=" + time);
                    $('#SITE_LOGO_CODE').val(response.message);
                }
            });
        });
        

        $("#imagepath04").change(function () {
            // Upload data here
            $.ajaxFileUpload({
                id: 'imagepath04',
                data: {},
                url: '<?php echo base_url() ?>account/setting/upload',
                success: function (data) {
                    $("#cropWhiteLogoButton").show();
                    
                    var pos = $("#crop_wrapper2").position();
                    $( "#crop_div2" ).css("top", pos.top);
                    $( "#crop_div2" ).show();
                    
                    var time = new Date().getTime();
                    $('#SITE_LOGO_WHITE_CODE_IMG').attr('src', assetPath + data.message + "&t=" + time);
                    $('#SITE_LOGO_WHITE_CODE').val(data.message);
                }
            });
        });
        
        $("#cropWhiteLogoButton").click(function(){
            var posi = document.getElementById('crop_div2');
            var pos = $("#crop_wrapper2").position();
            var data = {
                image_path: $("#SITE_LOGO_WHITE_CODE").val(),
                top: posi.offsetTop - pos.top,
                left: posi.offsetLeft - pos.left,
                right: posi.offsetWidth,
                bottom: posi.offsetHeight
            };
            $.ajaxExec({
                url: '<?php echo base_url() ?>account/setting/crop',
                data: data,
                success: function (response) {
                    $( "#crop_div2" ).hide();
                    $("#cropWhiteLogoButton").hide();
                    
                    var time = new Date().getTime();
                    $('#SITE_LOGO_WHITE_CODE_IMG').attr('src', assetPath + response.message + "&t=" + time);
                    $('#SITE_LOGO_WHITE_CODE').val(response.message);
                }
            });
        });

        $("#previewFileButton").button().click(function () {
            var first_letter_url = $('#FIRST_LETTER_KEY').val();
            if (first_letter_url != '') {
                $('#preview_first_letter').click();
            }
            return false;
        });
        
        // Reset design color to default color.
        $(".ResetColorDesign").click(function(){
            // Show confirm dialog
            $.confirm({
                message: 'Do you want to reset all setting colors to default colors of system?',
                yes: function () {
                    $.ajaxExec({
                        url: '<?php echo base_url() ?>account/setting/reset_design_color',
                        success: function (response) {
                            location.reload();
                        }
                    });
                }
            });
        });

        $("#previewEnvelopeFileButton").button().click(function () {
            var first_letter_url = $('#FIRST_ENVELOPE_KEY').val();
            if (first_letter_url != '') {
                $('#preview_first_letter_envelope').click();
            }
            return false;
        });

        $('.color_code').colorpicker({
            ok: function (event, color) {
                $(this).css("background-color", '#' + color.formatted);
            }
        });
    });
</script>