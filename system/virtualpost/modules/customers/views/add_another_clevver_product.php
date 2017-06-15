<style type="text/css">
    #addClevverProduct_addPostbox:hover, #addClevverProduct_addPhoneNumber:hover{
        text-decoration: none;
        color: #000;
    }
    #addClevverProduct_addPostbox, #addClevverProduct_addPhoneNumber{
        color: #000;
    }
    .add-postbox-text{
        width: 50px;
        position: relative;
        display: inline-block;
        top: 17px;
    }
    .add-phone-text{
        width: 50px;
        position: relative;
        display: inline-block;
        top: 12px;
    }
</style>
<div class="ym-grid" style="font-size: 16px">Your current selection</div>
<br />
<div class="clearfix"></div>
<div class="ym-grid">
    <div class="ym-gl">
        <div id="gridwraper">
            <div id="searchTableResult">
                <table id="dataGridResult"></table>
                <div id="dataGridPager"></div>
            </div>
        </div>
        <div class="clear-height"></div>
    </div>
</div>
<div class="clear-height"></div>


<div class="ym-grid" style="font-size: 16px; margin-top: 15px;">Add a product</div>

<div class="ym-grid" style="margin-top: 10px;">
    <div class="ym-gl ym-g25" style="text-align: center;">
        <a href="#" id="addClevverProduct_addPostbox">
            <span class="clevvermail-product">
            <!--<span class="add-postbox-text">Add a postbox</span>-->
            </span>
        </a>
        <br/> <h3 style="font-size: 16px">ClevverMail</h3>
        <br/> Your digital postbox at worldwide locations
    </div>

    <div class="ym-gl  ym-g25"  style="text-align: center;">
        <span class="clevverphone-product"></span>
        <br/> <h3 style="font-size: 16px">ClevverPhone</h3>
        <br/>International Phone Numbers
    </div>
    
    <div class="ym-gl  ym-g25"  style="text-align: center" title="Coming soon..."> 
        <span class="clevvercompany-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverCompany</h3>
        <br/>Incorporate a new international company
    </div>
    
    <div class="ym-gl  ym-g25"  style="text-align: center" title="Coming soon..."> 
        <span class="clevverbanking-product">&nbsp;</span>
        <br/> <h3 style="font-size: 18px">ClevverBank</h3>
        <br/>Open a Business Bank Account
    </div>
</div>

<input type='hidden' id='addClevverProduct_addPostbox_postbox_count' value='<?php echo $postbox_count; ?>' />
<br />
<br /><br />
<div class="ym-clearfix"></div>
<div class="ym-grid ym-text-center asyougo-message" style="">
    *Account fee is 0,00 EUR for 6 months. Activities are charged extra. Account fee is 0,95 EUR / month after the 6 month period.
</div>
<script type="text/javascript">
$(document).ready(function(){
    $("#dataGridResult").jqGrid('GridUnload');
    $("#dataGridResult").jqGrid({
        url: '<?php echo base_url() ?>customers/add_another_clevver_product',
        mtype: 'POST',
        datatype: "json",
        width: 750,
        height: 80,
        rowNum: 100,
        rowList: [<?php echo Settings::get(APConstants::DROPDOWN_LIST_CODE);?>],
        pager: "#dataGridPager",
        sortname: 'created_date',
        sortorder: 'desc',
        viewrecords: true,
        shrinkToFit: false,
        multiselect: true,
        multiselectWidth: 40,
        captions: '',
        colNames: ['', 'Product', 'Description', 'Contract term', 'Price'],
        colModel: [
            {name: 'id', index: 'id', hidden: true, sortable: false},
            {name: 'phone_number', index: 'phone_number', width: 150, sortable: false},
            {name: 'country', index: 'country', width: 250, sortable: false},
            {name: 'area', index: 'area', width: 100, sortable: false},
            {name: 'end_point', index: 'end_point', width: 180, sortable: false}
        ],
        loadComplete: function () {
        }
    });
    
    $(".clevverphone-product, .clevvercompany-product, .clevverbanking-product").click(function(){
        $.displayInfor("This product will be coming soon.", null, function () {
            // do nothing.
        });
    });
    
    $("#addClevverProduct_addPostbox").bind('click', function(e){
        e.preventDefault();
        if($("#addClevverProduct_addPostbox_postbox_count").val() == 0){
            openPostboxNameWindow();
        }else{
            var loadUrl = '<?php echo base_url()?>account/add_postbox/0/1/0';
            $.openDialog('#addPostboxWindow', {
                height: 450,
                width: 600,
                openUrl: loadUrl,
                title: "Add Postbox",
                closeButtonLabel: "Cancel",
                callback: function(){
                    location.reload();
                },
                buttons:[
                    {
                        id: "saveBtn",
                        text: "Submit"
                    }
                ]
            });
        }
        return false;
    });

    $("#addClevverProduct_addPhoneNumber").bind('click', function(e){
        e.preventDefault();
        $('#addAnotherClevverProductWindow').dialog('destroy');
        addNewPhoneNumberWindow();
        return false;
    });
});
</script>
