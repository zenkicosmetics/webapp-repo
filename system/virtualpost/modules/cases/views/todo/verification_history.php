<div class="verification-history">
    <div class="ym-grid history-header">
        <div class="ym-gl ym-g20">
            <div class="ym-gbox"><?php admin_language_e('cases_view_todo_verification_hist_Date'); ?></div>
        </div>
        <div class="ym-gl ym-g20">
            <div class="ym-gbox"><?php admin_language_e('cases_view_todo_verification_hist_Activity'); ?></div>
        </div>
        <div class="ym-gl ym-g40">
            <div class="ym-gbox"><?php admin_language_e('cases_view_todo_verification_hist_Comment'); ?></div>
        </div>
        <div class="ym-gl ym-g20">
            <div class="ym-gbox"><?php admin_language_e('cases_view_todo_verification_hist_Worker'); ?></div>
        </div>
    </div>
    <div class="history-content">
        <?php if(!empty($verification_history)) { ?>
            <?php foreach($verification_history as $history_item) {?>
                <div class="ym-grid">
                     <div class="ym-gl ym-g20">
                        <div class="ym-gbox"><?php echo date('d.m.Y', $history_item->activity_date)?></div>
                    </div>
                    <div class="ym-gl ym-g20">
                        <div class="ym-gbox"><?php echo CaseUtils::convertCaseVerificationToString($history_item->activity_type) ?></div>
                    </div>
                    <div class="ym-gl ym-g40">
                        <div class="ym-gbox"><?php echo $history_item->activity_content?></div>
                    </div>
                    <div class="ym-gl ym-g20">
                        <div class="ym-gbox"><?php echo $history_item->activity_by?></div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</div>

<script>
 $( document ).ready(function() {
     $(".history-content").slimScroll({height: '50px'});
});
</script>