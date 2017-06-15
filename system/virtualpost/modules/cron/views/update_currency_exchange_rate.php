<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    table#notify_locaton_admin th, table#notify_locaton_admin td{
        border: solid 1px #ccc;padding: 6px 8px;text-align: center;
    }
</style>
</head>

<body>
    <h2>The list of update currency exchange rate </h2>    
   
<table id="notify_locaton_admin" style=" border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="">
            <th style="">#</th>
            <th style="">Currency ID</th>
            <th style="">Currency Rate</th>
            <th style="">Updated date</th>
        </tr>
    </thead>
    <tbody>
    <?php     
        $i=0;
        foreach ($result_update as $row) { $i++;
    ?>          
        <tr>
            <td style=""><?php echo $i; ?></td>
            <td style=""><?php echo $row['currency_id']; ?> </td>
            <td style=""><?php echo $row['currency_rate']; ?></td>
            <td style=""><?php echo date("d-m-Y H:i:s",$row['last_updated_date']); ?></td>
        </tr>
    <?php } ?>    
    </tbody>
</table>
</body>
</html>