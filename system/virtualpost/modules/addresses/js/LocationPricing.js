var LocationPricing = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        index: null,
        save_location: null,
        change_pricing_template: null
    },

    /*
     * Messages
     */
    messages: {
        change_pricing_template_CONFIRM_1: change_pricing_template_confirm_1,
        change_pricing_template_CONFIRM_2: change_pricing_template_confirm_2,
        change_pricing_template_SUCCESS: change_pricing_template_success
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl) {
        this.initAjaxUrls(baseUrl);
        this.initEventListeners();
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.index = baseUrl + 'addresses/admin/location_pricing';
        this.ajaxUrls.save_location = baseUrl + 'addresses/admin/save_location';
        this.ajaxUrls.change_pricing_template = baseUrl + 'addresses/admin/change_pricing_template';
    },

    initEventListeners: function () {
        $("#saveButtonId").button().click(function () {
            LocationPricing.saveLocation();
            return false;
        });

        $("#location_id").live("change", function () {
            $("#locationForm").submit();
            return false;
        });

        $("#pricing_template_id").live("change", function () {
            LocationPricing.changePricingTemplate(this);
            return false;
        });
        
        $("#enterprise_pricing_template_id").live("change", function () {
            LocationPricing.savePricingTemplate(this);
            return false;
        });
    },

    saveLocation: function () {
        $.ajaxExec({
            url: LocationPricing.ajaxUrls.save_location,
            data: {location_id: $("#location_id").val(), pricing_template_id: $("#pricing_template_id").val()},
            success: function (data) {
                if (data.status) {
                    location.href = LocationPricing.ajaxUrls.index;
                } else {
                    $.displayError(data.message);
                }
            }
        });
    },  
    
    /**
     * View & Save pricing template
     */
    changePricingTemplate: function (elem) {
        var $link = $(elem);
        var name = $( "#pricing_template_id option:selected" ).text();
        // Open new dialog
        $('#confirmPricingTemplate').openDialog({
            autoOpen: false,
            height: 188,
            width: 660,
            modal: true,
            open: function () {
            	$(this).html('<center style="padding:28px;"><span style="color:#3366ff;font-weight:bold; font-size:15px">'+ LocationPricing.messages.change_pricing_template_CONFIRM_1 + name + LocationPricing.messages.change_pricing_template_CONFIRM_2 + '</span></center>');
            },
            buttons: {
                'Save': function () {
                	$(this).dialog('close');
                	LocationPricing.successPricingTemplate($link);
                },
                'Cancel': function () {
                    $(this).dialog('close');
                    location.reload(true);
                }
            }
        });
        $('#confirmPricingTemplate').dialog('option', 'position', 'center');
        $('#confirmPricingTemplate').dialog('open');

        return false;
    },
    
    successPricingTemplate: function ($link) {
        // Open new dialog
        $('#successPricingTemplate').openDialog({
            autoOpen: false,
            height: 188,
            width: 480,
            modal: true,
            closeOnEscape: false,
            open: function (event, ui) {
            	$(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
            	$(this).html('<center style="padding:28px;"><span style="color:#3366ff;font-weight:bold; font-size:15px">'+ LocationPricing.messages.change_pricing_template_SUCCESS + '</span></center>');
            },
            buttons: {
                'OK': function () {
                	LocationPricing.savePricingTemplate($link);
                }
            }
        });
        $('#successPricingTemplate').dialog('option', 'position', 'center');
        $('#successPricingTemplate').dialog('open');

        return false;
    },

    savePricingTemplate: function ($link) {
        var locationID = $("#location_id").val();
        var pricingTemplateID = $('#pricing_template_id').val();
        var enterprisePricingTemplateID = $('#enterprise_pricing_template_id').val();
        $.ajaxExec({
            url: LocationPricing.ajaxUrls.change_pricing_template,
            data: {location_id: locationID, pricing_template_id: pricingTemplateID, enterprise_pricing_template_id: enterprisePricingTemplateID},
            success: function (data) {
                if (data.status) {
                    $("#pricingTemplateForm").submit();
                } else {
                    $.displayError(data.message);
                }
            }
        });
    }
}