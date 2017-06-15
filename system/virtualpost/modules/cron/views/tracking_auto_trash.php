<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    table#notify_locaton_admin th, table#notify_locaton_admin td{
        border: solid 1px #ccc;padding: 6px 8px;text-align: center;
    }
    table#notify_locaton_admin_content th, table#notify_locaton_admin_content td{
        border-bottom: solid 1px #ccc !important;
        border-top: none !important;
        padding: 6px 8px;text-align: center;
    }
    table#notify_locaton_admin_content {width: 100%;height: 100%;}
</style>
</head>

<body>
    <h2>Tracking list of envelopes have been process when run cron job auo-trash </h2>    
   
    <table width="100%" id="notify_locaton_admin" cellpadding="10px" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>Envelope ID</th>
            <th>Envelope Code</th>
            <th>Postbox ID</th>
            <th>Customer ID</th>
        </tr>
    </thead>
    <tbody>
    <?php     
        $i=0;
        foreach ($data_process as $row) {
    ?>          
        <tr>
            <td><?php echo $i+1; ?></td>
            <td><?php echo $row['envelope_id']; ?> </td>
            <td><?php echo $row['envelope_code']; ?> </td>
            <td><?php echo $row['postbox_id']; ?> </td>
            <td><?php echo $row['customer_id']; ?> </td>
        </tr>
    <?php $i++; } ?>    
    </tbody>
</table>
</body>
</html>