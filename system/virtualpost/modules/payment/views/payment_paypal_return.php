<!DOCTYPE HTML>
<html lang="en-US">
    <head>
        <meta charset="UTF-8">
        <title>ClevverMail - Page Redirection</title>
        <script src="https://code.jquery.com/jquery-1.7.1.min.js"></script>
    </head>
    <body>
        
        <div style="width: 600px; margin: 0px auto; text-align: center; color: #888888;font-family: arial ">
            <form id="hiddenSubmitForm" name="hiddenSubmitForm" action="<?php echo base_url()?>invoices" method="post">
                <input type="hidden" name="paypal_status" value="1">
            </form>
            <!-- 
            <h2>Your payment request process successfully.</h2>
            <b>This page will redirect to <?php echo base_url()?>invoices after 1s</b>
             -->
        </div>
    </body>
    <script type="text/javascript">
    jQuery(document).ready(function($){
            setTimeout(function(){
              	document.location = '<?php echo base_url()?>invoices?paypal_status=1';
    		},100);
    });
    </script>
</html>