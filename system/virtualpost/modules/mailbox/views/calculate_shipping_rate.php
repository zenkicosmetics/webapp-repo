<!--  
<p style="font-style:italic;">
CP=P+[(UVP-P)*F]<br />
EP=(P+[(UVP-P)*F])*(1+HCP)+HCA+CC<br /><br />

CP = postal charge given to customer<br />
EP = End net price given to customer<br />
P = net price that was calculated using the API for CM customer account<br />
UVP = official price calculation without customer account <br />       
CC = customs charge from CM price list<br />
F = Factor A from database<br />
HCP = % handling charge from CM price list<br />
HCA = abs. handling charge from CM price list<br />
</p>
<hr/>
-->
<table class="" style="width: 100%">         
    <thead>
        <tr> 
            <th>Carrier</th> 
            <th>Service</th> 
            <th>Price</th>
            <th colspan="2">Details</th>
        </tr>
    </thead>          
    <tbody> 
    <?php 
    $idx = 0;
    foreach($rates as $rate) { ?>      
        <tr>                  
            <td><img alt="<?php echo $rate->carrierName; ?>" title="<?php echo $rate->carrierName; ?>" src="<?php echo $rate->serviceImage; ?>" width="80" /></td>                
            <td><strong><?php echo $rate->serviceName; ?></strong>&nbsp;&nbsp;<span class="glyphicon glyphicon-info-sign" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Estimated Delivery: <?php echo $rate->estimatedDelivery; ?>"></span></td>
            <td style="text-align:center;">
                <span class="price">
                    <?php
                    //TODO: what type of currency is totalNetCharge->currency displayed in? Basically, it is required to return in EUR from fedex API
                    //echo number_format((float)$rate->rateBusinessLogic['EP'], 2, '.', '') . ' ' . $rate->rateDetails[0]->totalNetCharge->currency;
                    echo APUtils::convert_currency_once($rate->rateBusinessLogic['EP']). ' ' . $rate->rateDetails[0]->totalNetCharge->currency;
                    ?>
                </span>
            </td>
            <td class="select-btn">
                <?php 
                    $postal_charge = $rate->rateBusinessLogic['CP'] * (1 + $rate->rateBusinessLogic['HCP']);
                    $customs_handling = $rate->rateBusinessLogic['CC'];
                    $handling_charges =  $rate->rateBusinessLogic['HCA'];
                ?>
                <input type="button" value="Confirm" 
                        data-envelope_id=<?php echo $envelope->id;?>
                        data-shipping_service_id="<?php echo $rate->shipping_service_id?>"
                        data-F="<?php echo $rate->rateBusinessLogic['F']; ?>"
                        data-CC="<?php echo $rate->rateBusinessLogic['CC']; ?>"
                        data-HCP="<?php echo $rate->rateBusinessLogic['HCP']; ?>"
                        data-HCA="<?php echo $rate->rateBusinessLogic['HCA']; ?>"
                        data-P="<?php echo $rate->rateBusinessLogic['P']; ?>"
                        data-UVP="<?php echo $rate->rateBusinessLogic['UVP']; ?>"
                        data-CP="<?php echo $rate->rateBusinessLogic['CP']; ?>"
                        data-EP="<?php echo $rate->rateBusinessLogic['EP']; ?>"
                        data-postal_charge="<?php echo $postal_charge; ?>"
                        data-customs_handling="<?php echo $customs_handling; ?>"
                        data-handling_charges="<?php echo $handling_charges; ?>"
                        data-shipping_type="<?php echo $shipping_type; ?>"
                        class="input-btn confirm_calculate_shipping_rate_link" 
                        style="background: #ecbd01 none repeat scroll 0 0;color: #FFF"/>
            <td>
            <?php
            if(is_array($rate->rateBusinessLogic)){
               echo 'EP: '.$rate->rateBusinessLogic['EP'];
            }
            ?>
            </td>
        </tr> 
        <?php
        $idx++;       
    } ?> 
    </tbody> 
</table>