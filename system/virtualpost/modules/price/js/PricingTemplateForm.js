var PricingTemplateForm = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        index: null
    },

    /*
     *  Initialize interface
     */
    init: function (baseUrl) {
        this.initAjaxUrls(baseUrl);
        this.initEventListeners();
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.index = baseUrl + 'price/admin';
    },

    initEventListeners: function () {
        $("#submitButton").button().click(function () {
            $('#priceSettingForm').submit();
            return false;
        });

        $("#cancelButton").button().click(function () {
            location.href = PricingTemplateForm.ajaxUrls.index;
        });
    }
}
