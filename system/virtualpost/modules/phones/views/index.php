<div class="items">
    <table id="mainMailboxTable" style="width: 100%">
        <thead>
            <tr>
                <th class="left-align">Activity</th>
                <th style="width:150px;">Number</th>
                <th style="width:200px;">System Response</th>
                <th style="width:150px;">Date-Time</th>
                <th style="width:125px;">Duration</th>
                <th style="width:125px;">Cost (EUR)</th>
                <th style="width:100px;">File</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list_phone_call as $phone_call) {?>
            <tr>
                <td><?php echo $phone_call->activity;?></td>
                <td><?php echo $phone_call->target_phone_number;?></td>
                <td><?php echo $phone_call->system_response;?></td>
                <td><?php echo APUtils::viewDateFormat($phone_call->call_start_time, 'd.m.y-h:i:s');?></td>
                <td><?php echo DateTimeUtils::convertNumberToTimeFormat($phone_call->duration);?></td>
                <td><?php echo APUtils::number_format($phone_call->cost, 2);?></td>
                <td>&nbsp;</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <div class="ym-clearfix"></div>	
    <div id="paginationContainer" class="pagination" style="border: 0px solid #CDCDCD; margin-top: 5px;">
        <div class="wrap">
            <?php echo $page_link;?>
        </div>
    </div>
</div>