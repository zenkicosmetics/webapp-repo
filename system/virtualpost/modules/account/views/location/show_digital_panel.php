<div>
    <table style="border: none">
        <tr>
            <td>
                <div>
                    <img src="" style="width: 350px; height: 150px;" />
                </div>
            </td>
            <td rowspan="2">
                <p>
                    This is our digital touch panel, that you can order for your location. 
                    With Wifi connection it is connected to the database and will always show the current postboxes at your location. 
                    Both information, the customer name and the company name are shown. 
                    This way, you can make it easy for the postal services to check if they can deliver postal items at your location to one of the customers. 
                    If this device is correctly placed, 
                    it can make your location to an officially legal and summonable address, which might be needed to officially register companies to it.
                </p>
                
                <br />
                <br />
                <br />
                <div style="text-align: center;">
                    <strong>Location : XXXX</strong>
                    <br />
                    <button class="input-btn btn-yellow" type="button" id="showDigitalPanelWindow_sendEmailNotificationButton">Order your digital panel now for 49,95 EUR / month...</button>
                </div>
                
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    Details: 
                    <br />
                    <ul>
                        <li>15'' digital panel in metal frame</li>
                        <li>IP65 weatherproof</li>
                        <li>Wall mount</li>
                        <li>Touch panel with dim function</li>
                        <li>230V and Wifi required</li>
                        <li>Automatic updates from server</li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#showDigitalPanelWindow_sendEmailNotificationButton").button();
    $("#showDigitalPanelWindow_sendEmailNotificationButton").click(function(){
        var location_id = $("#h_location_id").val();
        $.ajaxExec({
            url: '<?php echo base_url() ?>account/location/add_digital_panel',
            data: {location_id: location_id},
            success: function (data) {
                if (data.status) {
                    // do nothing and close this dialog
                    $("#showDigitalPanelWindow").parent().find('.ui-dialog-titlebar-close').click();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    });
});
</script>