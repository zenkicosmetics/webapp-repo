<?php 
$style_table = "border: solid 1px #ccc; padding: 6px 8px;text-align: center;";
$j=0;
$style='';
foreach ($location_users as $location) { $j++;                 
    $list_new_customer     = $this->customer_m->get_new_and_delete_customers_by_location($location->id);
    $list_deleted_customer = $this->customer_m->get_new_and_delete_customers_by_location($location->id,1);

    echo "<p style='margin-top: 50px;'><strong>Location name: ".$location->location_name."</strong></p>";
    echo  "<p>New Customer:</p>";
    if(count($list_new_customer)){

        
        echo  "<table cellpadding='4' cellspacing='0'><tr>"
                      ."<th style='".$style_table."'>#</th><th style='".$style_table."'>Customer ID</th>"
                      . "<th style='".$style_table."'>Customer Email</th>"
                      . "<th style='".$style_table."'>Postbox Code</th>"
                      . "<th style='".$style_table."'>Name</th>"
                      . "<th style='".$style_table."'>Company</th>"
                      . "<th style='".$style_table."'>Created Date</th></tr>";
        $i=0;
        foreach($list_new_customer as $new_customer){ 
            $i++;
            echo  "<tr><td style='".$style_table."'>".$i."</td>"
                          . "<td style='".$style_table."'>".$new_customer->customer_id."</td>"
                          . "<td style='".$style_table."'>".$new_customer->email."</td>"
                          . "<td style='".$style_table."'>".$new_customer->postbox_code."</td>"
                          . "<td style='".$style_table."'>".$new_customer->name."</td>"
                          . "<td style='".$style_table."'>".$new_customer->company."</td>"
                          . "<td style='".$style_table."'>".date("m.d.Y H:i",$new_customer->created_date)."</td>"
                    ."</tr>";
        }
        echo "</table>";
    }else{
        echo "<p>There is no new customer in this location.</p>";
    }

    echo "<p>Deleted Customer:</p>";
    if(count($list_deleted_customer)){
        echo "<table cellpadding='4' cellspacing='0'><tr>"
                      . "<th style='".$style_table."'>#</th><th style='".$style_table."'>Customer ID</th>"
                      . "<th style='".$style_table."'>Customer Email</th>"
                      . "<th style='".$style_table."'>Postbox Code</th>"
                      . "<th style='".$style_table."'>Name</th>"
                      . "<th style='".$style_table."'>Company</th>"
                      . "<th style='".$style_table."'>Deleted Date</th></tr>";  
        $i=0;
        foreach($list_deleted_customer as $deleted_customer){ 
            $i++;
            echo "<tr><td style='".$style_table."'>".$i."</td>"
                          . "<td style='".$style_table."'>".$deleted_customer->customer_id."</td>"
                          . "<td style='".$style_table."'>".$deleted_customer->email."</td>"
                          . "<td style='".$style_table."'>".$deleted_customer->postbox_code."</td>"
                          . "<td style='".$style_table."'>".$deleted_customer->name."</td>"
                          . "<td style='".$style_table."'>".$deleted_customer->company."</td>"
                          . "<td style='".$style_table."'>".date("m.d.Y H:i",$deleted_customer->deleted_date)."</td>"
                    . "</tr>";
        }
        echo "</table>";
    }   else{
        echo "<p>There is no customer deleted in this location.</p>";
    }       
}
?>