<style>
    table#notify_locaton_admin_content th, table#notify_locaton_admin_content td{
        border: solid 1px #ccc;
        padding: 6px 8px;text-align: center;
    }
    table#notify_locaton_admin_content {width: 100%;height: 100%;}
</style>
<table id="notify_locaton_admin_content" cellpadding="10px" cellspacing="0">
    <thead>
        <tr>
            <th>Location Name</th>
            <th>Number Of Open Activity</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    if(!empty($user_location)){
        foreach ($user_location as $row_ul) {
    ?>          
        <tr>
            <td><?php echo $row_ul->location_name; ?> </td>
            <td><?php echo $arr_data[$row_ul->location_id]; ?></td>
    <?php } } ?>
           
    </tbody>
</table>
