<style>
    form.parcels_info { margin-right: 15px;}
    form.parcels_info .chk { vertical-align: middle; text-align: center; cursor: pointer; -webkit-appearance: none; appearance: none;}
    table.parcels_info_table input.parcel-info { width: 55px; font-size: 95%; font-style: italic; height: 20px !important; padding: 2px 5px !important; margin: 0;}
    table.parcels_info_table { border-collapse: collapse;}
    table.parcels_info_table th, table.multi_packages_info td { line-height: 15px !important; }
    table.parcels_info_table th.title { color: #ffffff; border-left: solid 1px #ffffff; border-right: solid 1px #ffffff; border-bottom: solid 3px #ffffff;}
    table.parcels_info_table td { width: 40px; border: solid 1px #ffffff; margin: 0; padding: 5px; height: 15px;}
    tr.odd { background-color: #e1e1e1; }
    tr.even { background-color: #f0f0f0;}
    div.error-messages { display: none; font-style: italic; font-size: 95%; }
    div.error-message { color: red; }
</style>
<div class="error-messages">
    <div class="error-message">You are required to fill all valid information to those input fields.</div>
</div>
<form id="input_parcels_info_form" action="<?php echo base_url() ?>scans/todo/input_parcels_info" method="post" class="parcels_info">
    <table style="width: 100%;" class="parcels_info_table">
        <thead>
        <tr style="background-color: #a5a5a5;">
            <th class="title"></th>
            <th class="title">Parcel #</th>
            <th class="title">Quantity</th>
            <th class="title">Shipment #</th>
            <th class="title" colspan="2">Weight per parcel</th>
            <th class="title" colspan="4">Dimensions (Length/Width/Height)</th>
        </tr>
        </thead>
        <tbody>
        <?php $total_weight = 0; ?>
        <?php if ($mode == 'create'): ?>
            <?php for($i=1; $i<=$lines; $i++): ?>
                <tr class="<?php echo ($i % 2 == 0)? 'even' : 'odd'; ?> row-data">
                    <td class="chk"><input type="checkbox" name="delete[]" class="cbox" role="checkbox" value="1"/></td>
                    <td><?php echo $i; ?></td>
                    <td><input type="text" name="quantity[]" class="input-width parcel-info" value="1"/></td>
                    <td><input type="text" name="number_shipment[]" class="input-width parcel-info" value="<?php echo $i; ?>"/></td>
                    <td><input type="text" name="weight[]" class="input-width parcel-info"/></td>
                    <td>kg</td>
                    <td><input type="text" name="length[]" class="input-width parcel-info"/></td>
                    <td><input type="text" name="width[]" class="input-width parcel-info"/></td>
                    <td><input type="text" name="height[]" class="input-width parcel-info"/></td>
                    <td>cm</td>
                </tr>
            <?php endfor; ?>
        <?php elseif ($mode == 'edit'): ?>
            <?php if ($parcels): ?>
                <?php foreach($parcels as $index => $parcel): ?>
                    <?php $i = $index + 1; $total_weight += $parcel->weight; ?>
                    <tr class="<?php echo ($i % 2 == 0)? 'even' : 'odd'; ?> row-data">
                        <td class="chk"><input type="checkbox" name="delete[]" class="cbox" role="checkbox" value="1"/></td>
                        <td><?php echo $i; ?></td>
                        <td><input type="text" name="quantity[]" class="input-width parcel-info" value="<?php echo $parcel->quantity; ?>"/></td>
                        <td><input type="text" name="number_shipment[]" class="input-width parcel-info" value="<?php echo $i; ?>"/></td>
                        <td><input type="text" name="weight[]" class="input-width parcel-info" value="<?php echo APUtils::number_format($parcel->weight, 2, "."); ?>"/></td>
                        <td>kg</td>
                        <td><input type="text" name="length[]" class="input-width parcel-info" value="<?php echo $parcel->length; ?>"/></td>
                        <td><input type="text" name="width[]" class="input-width parcel-info" value="<?php echo $parcel->width; ?>"/></td>
                        <td><input type="text" name="height[]" class="input-width parcel-info" value="<?php echo $parcel->height; ?>"/></td>
                        <td>cm</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        <tr class="<?php echo (($lines + 1) % 2 == 0)? 'even' : 'odd'; ?> row-sum">
            <td></td>
            <td>Total</td>
            <td></td>
            <td></td>
            <td><?php echo APUtils::number_format($total_weight, 2, "."); ?></td>
            <td>kg</td>
            <td></td>
            <td></td>
            <td></td>
            <td>cm</td>
        </tr>
        </tbody>
    </table>
</form>
