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
    <h2>The list all of customers had sent email notify list incomming items </h2>    
   
<table id="notify_locaton_admin" style=" border-collapse: collapse;" cellpadding="10px" cellspacing="0">
    <thead>
        <tr style="">
            <th style="">#</th>
            <th style="">Customer ID</th>
            <th style="">Email</th>
            <th style="">Total Envelope</th>
            <th style="">Notify Type</th>
        </tr>
    </thead>
    <tbody>
    <?php 
        $i=0;
        if(count($list_notify_daily)){
        foreach ($list_notify_daily as $row) { $i++;
    ?>          
        <tr>
            <td style=""><?php echo $i; ?></td>
            <td style=""><?php echo $row->customer_id; ?> </td>
            <td style=""><?php echo $row->email; ?></td>
            <td style=""><?php echo $row->total_envelopes; ?></td>
            <td style="">Notify daily</td>
        </tr>
    <?php } } ?>
    
    <?php  
        if(count($list_notify_weekly)){
        foreach ($list_notify_weekly as $row) { $i++;
    ?>          
        <tr>
            <td style=""><?php echo $i; ?></td>
            <td style=""><?php echo $row->customer_id; ?> </td>
            <td style=""><?php echo $row->email; ?></td>
            <td style=""><?php echo $row->total_envelopes; ?></td>
            <td style="">Notify weekly</td>
        </tr>
    <?php } } ?>
        
    <?php  
        if(count($list_notify_monthly)){
        
        foreach ($list_notify_monthly as $row) { $i++;
    ?>          
        <tr>
            <td style=""><?php echo $i; ?></td>
            <td style=""><?php echo $row->customer_id; ?> </td>
            <td style=""><?php echo $row->email; ?></td>
            <td style=""><?php echo $row->total_envelopes; ?></td>
            <td style="">Notify monthly</td>
        </tr>
    <?php } } ?>    
        
        
    </tbody>
</table>
</body>
</html>