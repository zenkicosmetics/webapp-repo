var ShippingStandards = {
    /*
     *  Initialize interface
     */
    init: function () {
        // Event listeners
        // Click on the "Submit" button
        this.submitForm();
    },

    submitForm: function () {
        $("#submitButton").button().click(function () {
            $('#locationServiceForm').submit();
            return false;
        });
    }
}