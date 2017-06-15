<?php 
$style_table = "border: solid 1px #ccc; padding: 6px 8px;text-align: center;";
    echo  "<table cellpadding='4' cellspacing='0'><tr>"
                  ."<th style='".$style_table."'>Total accounts deactivated because of failed setup process</th>"
                  . "<th style='".$style_table."'>Total accounts deactivated failed payment</th>"
                  . "<th style='".$style_table."'>Total accounts manually deactivated</th>"
                  . "<th style='".$style_table."'>Total accounts older 3 month deleted</th>"
                  . "<th style='".$style_table."'>Total accounts younger 3 month deleted</th>"
                  . "<th style='".$style_table."'>Total accounts deleted manually</th></tr>";
  
           echo "<tr>"
                  . "<td style='".$style_table."'>".$total_accounts_deactivated."</td>"
                  . "<td style='".$style_table."'>".$total_accounts_deactivated_failed_payment."</td>"
                  . "<td style='".$style_table."'>".$total_accounts_manually_deactivated."</td>"
                  . "<td style='".$style_table."'>".$total_accounts_older_3_month_deleted."</td>"
                  . "<td style='".$style_table."'>".$total_accounts_younger_3_month_deleted."</td>"
                  . "<td style='".$style_table."'>".$total_accounts_deleted_manually."</td>"
                ."</tr>";
    
    echo "</table>";
?>