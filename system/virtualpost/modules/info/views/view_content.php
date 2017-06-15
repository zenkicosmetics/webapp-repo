<div style="line-height: 20px;margin-bottom: 10px;">
    <?php echo (!empty($query_result)&& isset($popup_flag) && $popup_flag == "1") ? $query_result->message_to_customer : "" ?>
</div>
<div style="<?php if( isset($popup_flag) && $popup_flag == "1"){ ?>margin: 0px;padding: 12px; border: solid 2px #41719c;<?php } else { ?>margin: 10px <?php } ?>">
<?php echo $content; ?>
</div>
