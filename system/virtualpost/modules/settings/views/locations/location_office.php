
<form id="locationOfficeForm" action="<?php echo base_url() . 'settings/locations/location_office'; ?>"
      method="post">
    <table>
        <tr>
            <th>Business Concierge</th>
            <td>
                <input type="checkbox" id="temp_business_concierge_flag" name="business_concierge_flag" value="0" /> 
            </td>
        </tr>
        <tr>
            <th>Video Conference</th>
            <td>
                <input type="checkbox" id="temp_video_conference_flag" name="video_conference_flag" value="1"  /> 
            </td>
        </tr>
        <tr>
            <th>Meeting Rooms</th>
            <td>
                <input type="checkbox" id="temp_meeting_rooms_flag" name="meeting_rooms_flag" value="1" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 1</th>
            <td>
                <input type="text" id="temp_office_feature_1" name="feature[]" class="input-width" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 2</th>
            <td>
                <input type="text" id="temp_office_feature_2" name="feature[]" class="input-width" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 3</th>
            <td>
                <input type="text" id="temp_office_feature_3" name="feature[]" class="input-width" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 4</th>
            <td>
                <input type="text" id="temp_office_feature_4" name="feature[]" class="input-width" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 5</th>
            <td>
                <input type="text" id="temp_office_feature_5" name="feature[]" class="input-width" /> 
            </td>
        </tr>
        <tr>
            <th>Feature 6</th>
            <td>
                <input type="text" id="temp_office_feature_6" name="feature[]" class="input-width" /> 
            </td>
        </tr>
    </table>
    <input type="hidden" id="location_id" name="location_id" value="<?php echo $location_id; ?>"/>
</form>
<script type="text/javascript">
$(document).ready(function ($) {
    
    $("#temp_business_concierge_flag").val($("#business_concierge_flag").val());
    $("#temp_video_conference_flag").val($("#video_conference_flag").val());
    $("#temp_meeting_rooms_flag").val($("#meeting_rooms_flag").val());
    
    if($("#temp_business_concierge_flag").val() == '1'){
        $("#temp_business_concierge_flag").attr("checked",true);
    }
    if($("#temp_video_conference_flag").val() == '1'){
        $("#temp_video_conference_flag").attr("checked",true);
    }
    if($("#temp_meeting_rooms_flag").val() == '1'){
        $("#temp_meeting_rooms_flag").attr("checked",true);
    }
    
    $("#temp_business_concierge_flag").click(function(){
        if($("#temp_business_concierge_flag").is(':checked')){
            $("#temp_business_concierge_flag").val(1);
        }
        else {
            $("#temp_business_concierge_flag").val(0);
        }
    });
    
    $("#temp_video_conference_flag").click(function(){
        if($("#temp_video_conference_flag").is(':checked')){
            $("#temp_video_conference_flag").val(1);
        }
        else {
            $("#temp_video_conference_flag").val(0);
        }
    });
    
    $("#temp_meeting_rooms_flag").click(function(){
        if($("#temp_meeting_rooms_flag").is(':checked')){
            $("#temp_meeting_rooms_flag").val(1);
        }
        else {
            $("#temp_meeting_rooms_flag").val(0);
        }
    });
    
    
    $("#temp_office_feature_1").val($("#office_feature_1").val());
    $("#temp_office_feature_2").val($("#office_feature_2").val());
    $("#temp_office_feature_3").val($("#office_feature_3").val());
    $("#temp_office_feature_4").val($("#office_feature_4").val());
    $("#temp_office_feature_5").val($("#office_feature_5").val());
    $("#temp_office_feature_6").val($("#office_feature_6").val());
});
</script>