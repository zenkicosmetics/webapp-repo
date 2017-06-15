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
    <h2>The list all email location admin have been sent notify about number open activity </h2>    
   
    <table width="100%" id="notify_locaton_admin" cellpadding="10px" cellspacing="0">
    <thead>
        <tr>
            <th>#</th>
            <th>User Email</th>
            <th>Display Name</th>
            <th>Content</th>
        </tr>
    </thead>
    <tbody>
    <?php     
        $i=0;
        foreach ($message as $row) { $i++;
    ?>          
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php echo $row->email; ?> </td>
            <td><?php echo $row->display_name; ?></td>
            <td style="padding-left: 0px;padding-right: 0px;">
                <?php //echo $row->str_content; ?>
                <style>
                    table#notify_locaton_admin_content th, table#notify_locaton_admin_content td{
                        border-bottom: solid 1px #ccc;
                        padding: 6px 8px;text-align: center;
                    }
                    table#notify_locaton_admin_content {width: 100%;height: 100%;}
                </style>
                    <table width="100%" id="notify_locaton_admin_content" cellpadding="10px" cellspacing="0">
                    <?php if($i==1){?>
                    <thead>
                        <tr>
                            <th style="border-right: none;border-left: none;"  width="50%">Location Name</th>
                            <th>Number Of Open Activity</th>
                        </tr>
                    </thead>
                    <?php } ?>
                    <tbody>
                    <?php 
                    if(!empty($row->user_location)){
                        foreach ($row->user_location as $row_ul) {
                    ?>          
                        <tr>
                            <td style="border-right: none;border-left: none;" width="50%"><?php echo $row_ul->location_name; ?> </td>
                            <td><?php $arr_data = $row->arr_data;  echo $arr_data[$row_ul->location_id]; ?></td>
                    <?php } } ?>

                    </tbody>
                </table>
            </td>
        </tr>
    <?php } ?>    
    </tbody>
</table>
</body>
</html>