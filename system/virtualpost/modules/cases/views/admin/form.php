<?php
   $submit_url = base_url () . 'cases/todo/create_verification_report';
?>
<form id="createReportForm" method="post" action="<?php echo $submit_url?>">
  <table>
      <tr><th>Locations</th>
            <td>
              <?php echo my_form_dropdown(array(
                    "data" => $locations,
                    "value_key" => 'id',
                    "label_key" => 'location_name',
                    "value" => '',
                    "name" => 'location',
                    "id" => 'location_id',
                    "clazz" => 'input-txt-none',
                    "style" => ' width:300px;height:30px',
                    "has_empty" => false,
                    "html_option" => ''
                )); ?>
            </td>
       </tr>
       <tr>
          <th><?php admin_language_e('cases_view_admin_form_StartDate'); ?></th>
          <td>
            <input type="text" style="width:285px" name="startDate" id="createReportForm_startDate" value="" class="input-width datepicker">
          </td>
      </tr>
       <tr>
          <th><?php admin_language_e('cases_view_admin_form_EndDate'); ?></th>
          <td>
            <input type="text" style="width:285px" name="endDate" id="createReportForm_endDate" value="" class="input-width datepicker">
          </td>
      </tr>
   </table>
</form>

<script type="text/javascript">
$(document).ready( function() {

    //$(".datepicker").datepicker();
    var dateFormat = "mm/dd/yy",

        from = $( "#createReportForm_startDate" ).datepicker({

            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1
        }) .on( "change", function() {

            to.datepicker( "option", "minDate", getDate( this ) );

        }),

        to = $( "#createReportForm_endDate" ).datepicker({

            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1
      }).on( "change", function() {

            from.datepicker( "option", "maxDate", getDate( this ) );
      });

    function getDate( element ) {

        var date;
        try {
           date = $.datepicker.parseDate( dateFormat, element.value );
        } catch( error ) {
           date = null;
        }
        return date;
    }


});
</script>