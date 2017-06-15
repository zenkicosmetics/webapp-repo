<?php

?>
<form method="post" class="dialog-form" action="#">
    <table>
    	<tr>
            <td>
                <textarea style="width: 600px; height: 300px" readonly=""><?php if (!empty($pricing_phone_number)) {echo $pricing_phone_number->remarks;} ?></textarea>
            </td>
        </tr>
        
    </table>
</form>

<script>
jQuery(document).ready(function () {
    
});
</script>
