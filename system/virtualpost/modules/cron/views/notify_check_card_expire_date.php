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
    <h2>The list customers have been sent email notify about check card expire date </h2>    
   
    <table width="100%" id="notify_locaton_admin" cellpadding="10px" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>Customer ID</th>
            <th>Name</th>
            <th>Email To</th>
            <th>Email slug</th>
        </tr>
    </thead>
    <tbody>
    <?php $i=0; foreach ($arr_data_sent as $rows => $row) { $i++;?>          
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $row['customer_id']; ?> </td>
            <td style="text-align: left;"><?php echo $row['full_name']; ?></td>
            <td style="text-align: left;"><?php echo $row['to_email']; ?></td>
            <td><?php echo $row['email_slug']; ?></td>
        </tr>
    <?php } ?>    
    </tbody>
</table>
</body>
</html>