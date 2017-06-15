var ShippingAPIForm = {

    /*
     * Constants
     */
    modes: {
        add: 'add',
        edit: 'edit'
    },

    /*
     *  Initialize interface
     */
    init: function (mode) {
        this.initScreen(mode);
        this.initEventListeners();
    },

    initScreen: function (mode) {
        if (mode == this.modes.add) {
        } else {
        }
    },

    initEventListeners: function () {
    }
}