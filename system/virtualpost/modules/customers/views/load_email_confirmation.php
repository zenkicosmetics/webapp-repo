<h2 style="font-size: 16px;text-align: center;">Please check your email account and confirm the link</h2>
<br/>
<div style="text-align: center">
    <button type="button" style="text-align: center; width: 100px;" class="input-btn btn-yellow" id="close_confirmation_email">OK</button>
</div>
<br />
<div style="text-align: center">
    <a href="#" id="resendEmailConfirmationButton" class="main_link_color">Resend e-mail confirmation</a>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#close_confirmation_email").bind('click', function(e){
        e.preventDefault();
        
        $("#confirmEmailDialogWindow").dialog('close');
        
        return false;
    });
    
    $("#resendEmailConfirmationButton").bind('click', function(e){
        e.preventDefault();
        
        $.ajaxExec({
            url: "<?php echo base_url() ?>customers/resend_email_confirm",
            success: function (data) {
                if (data.status) {
                    $.displayInfor(data.message);
                } else {
                    $.displayError(data.message);
                }
            }
        });
        
        return false;
    });
});
</script>
