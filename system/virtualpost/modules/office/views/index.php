<style>
.button_contact, .button_contact:hover {
    border: 1px solid #d39e00;
    border-radius: 10px;
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    margin: 0 auto;
    padding-bottom: 10px;
    padding-top: 10px;
    text-decoration: none;
    width: 100%;
}

.popup_change{
    border: 2px solid #ffcc00;
    border-radius: 6px;
    display: none;
    left: -39px;
    padding: 10px;
    position: relative;
    text-align: left;
    text-indent: 0;
    bottom: 115px;
    width: 250px;
    z-index: 999999;
    height: 65px;
    background-color: rgb(250, 250, 250);
}

.popup_change:before{
    content: "";
    width: 0;
    height: 0;
    border-left: 8px solid transparent;
    border-right: 8px solid transparent;
    border-top: 8px solid #ffcc00;
    position: Absolute;
    bottom: -10px;
    left: 34px;
}
.feature-office {
    height: 20px;
    cursor: pointer;
}
</style>
<!-- inline css for design scheme -->
<?php include 'system/virtualpost/themes/assets/css/change_style_enterprise.php';?>
<div style="margin: 0 auto; width: 940px;">
<?php
$cnt = 0;
// var_dump($list_location); die();
for ($i = 0; $i < count($list_location); $i ++) {
    $cnt ++;
    $lc = $list_location[$i];
?>
        <?php if ($cnt == 1) { ?>
        <div class="ym-grid">
            <div class="ym-g33 ym-gl"
                 style="background-color: #ffffff; height: 460px; width: 300px; border: 2px solid #dadada; margin-top: 10px;">
                <div style="padding: 10px 5px">
                    <strong style="font-size: 13px;">
                        <?php
                            //echo APUtils::autoHidenText($lc->location_name.' - '.$lc->country->country_name, 30);
                            if(is_object($lc) && is_object($lc->country)){
                                echo APUtils::autoHidenText($lc->location_name.' - '.$lc->country->country_name, 30);
                            }
                            else if(is_object(is_object($lc) && !empty($lc->location_name))){
                                echo APUtils::autoHidenText($lc->location_name, 30);
                            }

                        ?></strong>
                </div>
                <div>
                    <?php if (empty($lc->shared_office_image_path)) { ?>
                        <img src="<?php echo APContext::getAssetPath() ?>uploads/images/location/default_location.png" style="width: 100%; height: 150px;">
                    <?php } else { ?>
                        <?php
                        if (substr($lc->shared_office_image_path, 0, 1) == "/") {
                            $image_path = substr($lc->shared_office_image_path, 1, strlen($lc->shared_office_image_path));
                        } else {
                            $image_path = $lc->shared_office_image_path;
                        }
                        ?>
                        <img src="<?php echo APContext::getAssetPath() . $image_path; ?>" style="width: 100%; height: 150px;">
                    <?php } ?>
                </div>
                <div style="padding: 10px 5px 2px 25px; height: 35px; font-weight: bold;">
                    <?php
                        echo $lc->street.', '.$lc->postcode. ', '.$lc->region;
                    ?>
                </div>

                <div style="padding: 2px 5px; height: 60px;">
                <?php if (!empty($lc->location_office)) {?>

                    <ul style="margin-top: 5px;">
                        <?php if ($lc->location_office->business_concierge_flag == '1') { ?>
                        <li class="business-concierge">Business Concierge</li>
                        <?php } ?>
                        <?php if ($lc->location_office->video_conference_flag == '1') { ?>
                        <li class="video-conference">Video Conference</li>
                        <?php } ?>
                        <?php if ($lc->location_office->meeting_rooms_flag == '1') { ?>
                        <li class="meeting-room">Meeting Rooms </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                </div>
                <hr style="height: 1px; width: 260px; color: #003bb3; margin: 10px auto 0; border: 0;border-top: 1px solid #003bb3 ;" />
                <span style="font-weight: bold;padding-left: 20px;">
                <?php if (!empty($lc->list_location_office_feature) && count($lc->list_location_office_feature) > 0) {?>
                Features
                <?php } ?>
                </span>
                <div style="padding: 2px 5px; height: 50px; margin-top: 10px">
                <?php if (!empty($lc->list_location_office_feature) && count($lc->list_location_office_feature) > 0) {?>
                    <ul style="margin-top: 0px; float: left; height: 50px; width: 130px;">
                        <?php $number = 0; ?>
                        <?php foreach ($lc->list_location_office_feature as $item) {
                            if (empty($item->feature_name)) {
                                continue;
                            }
                            $number++;
                            ?>
                            <?php if ($number % 2 == 1) { ?>
                                <li class="feature-office">
                                    <?php echo APUtils::autoHidenText($item->feature_name, 16, false); ?>
                                    <div class="popup_change">
                                        <?php  echo $item->feature_name; ?>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                    <ul style="margin-top: 0px; float: left; height: 50px; width: 130px;">
                        <?php $number = 0; ?>
                        <?php foreach ($lc->list_location_office_feature as $item) {
                            if (empty($item->feature_name)) {
                                continue;
                            }
                            $number++; ?>
                            <?php if ($number % 2 == 0) { ?>
                                <li class="feature-office">
                                    <?php echo APUtils::autoHidenText($item->feature_name, 16, false); ?>
                                    <div class="popup_change">
                                        <?php  echo $item->feature_name; ?>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    </ul>
                <?php } ?>
                </div>

                <div style="padding: 10px 5px">
                    <div style="margin: 0 auto; width: 80%">
                        <a class="button_contact btn-yellow" type="button" data-location_id ="<?php echo $lc->id ?>" style="text-align: center;">Book it/Contact</a>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="ym-g33 ym-gl" style="background-color: #ffffff; height: 460px; width: 300px; border: 2px solid #dadada; margin-top: 10px; margin-left: 10px;">
                <div style="padding: 10px 5px">
                    <strong style="font-size: 13px;">

                    <?php

                        if(is_object($lc) && is_object($lc->country)){
                            echo APUtils::autoHidenText($lc->location_name.' - '.$lc->country->country_name, 30);
                        }
                        else if(is_object(is_object($lc) && !empty($lc->location_name))){
                            echo APUtils::autoHidenText($lc->location_name, 30);
                        }
                    ?>

                    </strong>
                </div>
                <div>
                    <?php if (empty($lc->shared_office_image_path)) { ?>
                        <img src="<?php echo APContext::getAssetPath() ?>uploads/images/location/default_location.png" style="width: 100%; height: 150px;">
                    <?php } else { ?>
                        <?php
                        if (substr($lc->shared_office_image_path, 0, 1) == "/") {
                            $image_path = substr($lc->shared_office_image_path, 1, strlen($lc->shared_office_image_path));
                        } else {
                            $image_path = $lc->shared_office_image_path;
                        }
                        ?>
                        <img src="<?php echo APContext::getAssetPath() . $image_path; ?>" style="width: 100%; height: 150px;">
                    <?php } ?>
                </div>
                <div style="padding: 10px 5px 2px 25px; height: 35px; font-weight: bold;">
                    <?php
                        echo $lc->street.', '.$lc->postcode. ', '.$lc->region;
                    ?>
                </div>
                <div style="padding: 2px 5px; height: 60px;">
                <?php if (!empty($lc->location_office)) {?>
                    <ul style="margin-top: 5px;">
                        <?php if ($lc->location_office->business_concierge_flag == '1') { ?>
                        <li class="business-concierge">Business Concierge</li>
                        <?php } ?>
                        <?php if ($lc->location_office->video_conference_flag == '1') { ?>
                        <li class="video-conference">Video Conference</li>
                        <?php } ?>
                        <?php if ($lc->location_office->meeting_rooms_flag == '1') { ?>
                        <li class="meeting-room">Meeting Rooms </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                </div>
                <hr style="height: 1px; width: 260px; color: #003bb3; margin: 10px auto 0; border: 0;border-top: 1px solid #003bb3 ;" />
                <span style="font-weight: bold;padding-left: 20px;">
                <?php if (!empty($lc->list_location_office_feature) && count($lc->list_location_office_feature) > 0) {?>
                Features
                <?php } ?>
                </span>
                <div style="padding: 2px 5px; height: 50px; margin-top: 10px">
                    <?php if (!empty($lc->list_location_office_feature) && count($lc->list_location_office_feature) > 0) {?>
                        <ul style="margin-top: 0px; float: left; height: 50px; width: 130px;">
                            <?php $number = 0; ?>
                            <?php foreach ($lc->list_location_office_feature as $item) {
                                if (empty($item->feature_name)) {
                                    continue;
                                }
                                $number++;
                            ?>
                            <?php if ($number % 2 == 1) { ?>
                                <li class="feature-office">
                                    <?php echo APUtils::autoHidenText($item->feature_name, 16, false); ?>
                                    <div class="popup_change">
                                        <?php  echo $item->feature_name; ?>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        </ul>
                        <ul style="margin-top: 0px; float: left; height: 50px; width: 130px;">
                            <?php $number = 0; ?>
                            <?php foreach ($lc->list_location_office_feature as $item) {
                                if (!$item->feature_name) {
                                    continue;
                                }
                                $number++;
                            ?>
                            <?php if ($number % 2 == 0) { ?>
                                <li class="feature-office">
                                    <?php echo APUtils::autoHidenText($item->feature_name, 16, false); ?>
                                    <div class="popup_change">
                                        <?php  echo $item->feature_name; ?>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                        </ul>


                    <?php } ?>
                </div>
                <div style="padding: 10px 5px">
                    <div style="margin: 0 auto; width: 80%">
                        <a class="button_contact btn-yellow" type="button" data-location_id ="<?php echo $lc->id ?>" style="text-align: center;">Book it/Contact</a>
                    </div>
                </div>
            </div>

        <?php } ?>

    <?php
        // reset $cnt
        if ($cnt == 3) {
            $cnt = 0;
    ?>
            <!-- close row -->
        </div>
        <?php
    }
    ?>
<?php } ?>
</div>
<script type="text/javascript">
$(document).ready(function () {
/**
 * Click to booking button
 */
$('.button_contact').live('click', function() {
    var location_id = $(this).attr('data-location_id');
    openBookingRequestForm(location_id);
});

$('.feature-office').hover(function() {
    $(".popup_change", this).show();
}, function() {
    $('.popup_change', this).hide();
});

/**
 * Create direct charge
 */
function openBookingRequestForm(location_id) {
    // Clear control of all dialog form
    if (! $('#bookingRequestWindow').length) {
        // Append div to document
        var newDialogHtml = '<div id="bookingRequestWindow" title="Your booking request" class="input-form dialog-form"></div>';
        $('#content-center-wrapper').prepend(newDialogHtml);
    }
    $('#bookingRequestWindow').html('');

    // Open new dialog
    $('#bookingRequestWindow').openDialog({
            autoOpen: false,
            height: 540,
            width: 600,
            modal: true,
            open: function() {
                $(this).load("<?php echo base_url() ?>office/book_request_form?location_id=" + location_id, function() {
                });
            },
            buttons: {
                'Submit': function () {
                    saveBookingRequest();
                }
            }
    });

    $('#bookingRequestWindow').dialog('option', 'position', 'center');
    $('#bookingRequestWindow').dialog('open');
};

/**
 * Save external payment
 */
function saveBookingRequest() {
    var submitUrl = "<?php echo base_url() ?>office/book_request_form";
    $.ajaxSubmit({
        url: submitUrl,
        formId: 'addBookRequestForm',
        success: function(data) {
            if (data.status) {
                $('#bookingRequestWindow').dialog('close');
                $.displayInfor(data.message, null,  function() {

                });
            } else {
                $.displayError(data.message);
            }
        }
    });
}
});
</script>