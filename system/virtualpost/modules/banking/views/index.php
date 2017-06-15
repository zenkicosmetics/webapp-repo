<div id="phoneFragmentContaincer" style="background-image: url('system/virtualpost/themes/phone/images/background_logo_3000.png')">
    <img src="system/virtualpost/themes/account_setting2/images/holvi-logo.png" alt="phone" class="phone-icon">
    <h2>Get your EU Business Bank Account <span class="right-header">In cooperation with <img src="system/virtualpost/themes/account_setting2/images/holvi-logo.svg" height="30px" /></span></h2>
    <ul>
        <li>Real EU business bank account with Credit Card</li>
        <li>Banking, Invoicing, Bookkeeping</li>
        <li>Online Verification, you can start in minutes
            <span><button id="addBankAccountButton" style="margin-left: 300px;" type="button" class="btn-yellow">Open an account now</button></span></li>
    </ul>
</div>
<div class="bank-content">
    <div class="bank-message" style="color: #336699">
        <span style="font-weight: bold; ">Relax and get on with the good stuff. </span>
        Your Holvi account automates a lot of tedious manual tasks allowing you to focus on building a brilliant business. Everything your business needs in one easy to use solution.
    </div>
    
    <div class="ym-clearfix"></div>
    <div style="width: 100%">
        <div class="ym-gl ym-g33 bank-box" style="width: 575px;" >
            <img src="system/virtualpost/themes/account_setting2/images/holvi-graph.png" width="100%" height="100%" />
        </div>
        <div class="ym-gl ym-g33 bank-box text-center">
            <div class="bank-header-box">Pro Account</div>
            
            <div class="text-center" style="line-height: 30px;">
                € 8,00 / Month <br />
                Business account <br />
                Bookkeeping <br />
                Invoices <br />
                Online store <br />
                Holvi Business Mastercard <br />
            </div>
            
            <button type="button" id="selectProAccountButton" class="btn-yellow" style="top: 30px; position: relative;">Select</button>
        </div>
        <div class="ym-gl ym-g33 bank-box text-center" >
            <div class="bank-header-box">Basic Account</div>
            
            <div class="text-center" style="line-height: 30px;">
                € 0 / Month <br />
                Business account <br />
                € 0,50 per transfer <br />
            </div>
            
            <button id="selectBasicAccountButton" type="button" class="btn-yellow" style="top: 120px; position: relative;">Select</button>
        </div>
    </div>
    <div class="ym-clearfix"></div>
    <div style="margin-left:715px;"><small>Detailed Pricing Information: 
            <a href="https://about.holvi.com/pricing/" class="main_link_color">https://about.holvi.com/pricing/</a></small></div>
</div>
<div class="ym-clearfix"></div>
<br /><br /><br /><br />


<script type="text/javascript">
    $(document).ready(function(){
        $("button").button();
        
        // Set white background
        $("#content-center-wrapper").css("background-color", "#fff");
        
        $("#addBankAccountButton, #selectBasicAccountButton, #selectProAccountButton").click(function(){
            window.open("https://my.holvi.com/register?lang=en&country=DE");
        });
    });
</script>