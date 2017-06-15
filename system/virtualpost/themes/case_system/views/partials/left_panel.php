<!-- section -->
<section>
    <?php
    $active_service = '';
    $active_bank_account = '';
    $active_account_book = '';
    $active_traslate_service = '';
    $your_case = '';
    $active_verification_service = '';
    
    switch ($controller) {
        case 'company':
            $active_service = "act";
            break;
        case 'services':
            $active_bank_account = "act";
            break;
        case 'books':
            $active_account_book = "act";
            break;
        case 'translate':
            $active_traslate_service = "act";
            break;
        case 'verification':
            $active_verification_service = "act";
            break;
        default:
            $your_case = "act";
    }
    
    ?>
    <div class="ym-clearfix" style="height: 10px;"></div>
    <ul class="left-nav">
        <li class="<?php echo $your_case?>"><a
            href="<?php echo base_url()?>cases">Your cases</a></li>
        <!-- Company registration -->
        <?php if (CaseUtils::is_enable_link_cases(2)) {?>
        <li class="<?php echo $active_service;?>"><a
            href="<?php echo base_url()?>cases/under_construction">Company
                registration</a></li>
        <?php }?>
        <!-- Bank account -->
        <?php if (CaseUtils::is_enable_link_cases(1)) {?>
        <li class="<?php echo $active_bank_account;?>"><a
            href="<?php echo base_url()?>cases/services">Bank account</a></li>
        <?php }?>
        <!-- Verification -->
        <!-- ?php if (!CaseUtils::is_enable_link_verification()) {?-->
        <?php if (CaseUtils::is_enable_link_cases(5)) {?>
        <li class="<?php echo $active_verification_service;?>"><a
            href="<?php echo base_url()?>cases/verification">Verification</a></li>
        <?php }?>
        <!-- Accounting/Books -->
        <?php if (CaseUtils::is_enable_link_cases(3)) {?>
        <li class="<?php echo $active_account_book;?>"><a
            href="<?php echo base_url()?>cases/under_construction">Accounting/Books</a></li>
        <?php }?>
        <!-- Translating services -->
        <?php if (CaseUtils::is_enable_link_cases(4)) {?>
        <li class="<?php echo $active_traslate_service;?>"><a
            href="<?php echo base_url()?>cases/under_construction">Translating
                services</a></li>
        <?php }?>
    </ul>
    <div class="clearfix"></div>
</section>
<script type="text/javascript">
$(document).ready( function() {
    /**
     * Process when user click to logout button.
     */
    $('#verification_link').click(function() {
        // Show confirm dialog
        $.confirm({
            message: 'Do you want to start the verification?',
            yes: function() {
                document.location = '<?php echo base_url()?>cases?product_id=5';
            }
        });
        return false;
    });
});
</script>