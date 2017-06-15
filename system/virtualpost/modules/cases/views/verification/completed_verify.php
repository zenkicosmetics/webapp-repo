<style>
.xx .input-btn {
    padding: 5px 15px;
    font-size: 17px;
    color: #fff;
    background: #569bdb;
    border: 1px solid #569bdb;
    border-radius: 4px;
    cursor: pointer;
    text-align: center;
    width: 300px;
}

.xx .input-btn span {
    text-decoration: underline;
}

.xx .ym-grid {
    margin-bottom: 12px !important;
    margin-top: 0px !important;
}

.xx a:HOVER {
    text-decoration: none;
}

.xx .bd {
    border: 1px solid #a5a5a5;
    padding: 20px !important;
    max-height: 460px;
    height: 460px;
    overflow-y: auto;
}

.xx .bd-header {
    border-bottom: 1px solid #a5a5a5;
    padding-bottom: 12px !important;
    font-size: 1.2em;
}

.xx .description strong {
    margin-right: 10px;
}
</style>
<script type="text/javascript">
$(function(){
    $("#passport_verification_btn").click(function(event){
        $("#passport_verification_file").click();
        //$('#passport_verification_txt').val('');
    });

    $('#passport_verification_file').change(function(click) {
        $('#passport_verification_txt').val(this.value);
      });
});

</script>
<div class="ym-grid content" id="case-body-wrapper">
    <div class="cloud-body-wrapper xx">
        <div class="ym-grid">
            <h2 style="font-size: 20px; margin-bottom: 10px"><?php language_e('cases_view_verification_completed_verify_VerificationsRequired'); ?></h2>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g80"></div>
        </div>
        <div class="ym-grid">
            <div class="ym-gl ym-g60">
                <strong style="text-indent: 1.3em;"><?php language_e('cases_view_verification_completed_verify_ThankYouForUploadingTheDocument', ['url' => base_url()]); ?></a>
                </strong>
            </div>
        </div>
    </div>
</div>