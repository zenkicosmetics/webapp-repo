<?php
$submit_url = base_url() . 'cases/create';
$customer_id = APContext::getCustomerCodeLoggedIn();
?>
<form id="addEditCaseForm" method="post" class="dialog-form"
    action="<?php echo $submit_url?>">
    <table>
        <tr>
            <th class="label-case-l">Product <span class="required">*</span></th>
            <td><select id="product_id" name="product_id"
                class="input-txt" required="required">
                <?php foreach ( $products as $product ) :?>
                <?php if (CaseUtils::isEnableCreateCase($product->id, $customer_id)) {?>
                <option
                        value="<?php echo $product->id?>"
                        <?php if ($product_id == $product->id) {?>
                        selected="selected" <?php }?>> <?php echo $product->product_name?>
                </option>
                <?php }?>
                <?php endforeach;?>
             </select></td>
        </tr>
    </table>
</form>
<script type="text/javascript">
jQuery(document).ready(function($){

});
</script>
