var ShippingCalculator = {
    /*
     * Ajax URLs
     */
    ajaxUrls: {
        shipping_calculator: null,
        getShippingServicesByLocation: null,
        getShippingServiceDescription: null,
        inputParcelsInfo: null,
        convertCurrency: null
    },

    /*
     *  Messages
     */
    messages: {
        has_no_standard_shipment_service: 'Sorry, this location currently has no standard shipment service.'
    },

    /*
     *  Modes
     */
    modes: {
        create: 'create', // Enter parcels' information
        edit: 'edit' // Modify parcels' information
    },

    /*
     *  Decimal separators
     */
    DECIMAL_SEPARATORS: {
        COMMA: ',',
        DOT: '.'
    },
    decimalSeparator: null,

    /*
     *  Intervals (timer in milliseconds)
     */
    intervals: {
        timer: 1000,
        ID_NumberOfParcels: null,
        ID_Weight: null
    },

    /*
     *  For storing parcels' data to sessionStorage
     */
    sessionStorageItemKey: 'parcelsInfoData',

    /*
     *  Initialize interface
     */
    init: function (baseUrl, decimalSeparator) {
        // init data
        ShippingCalculator.initAjaxUrls(baseUrl);
        this.decimalSeparator = decimalSeparator;

        // Event listeners

        // 1. Change location drop-down list
        $("#location_id").change(function () {
            ShippingCalculator.fillShippingServicesByLocation($(this).val());
        });

        // 2. Change standard shipment service drop-down list
        $("#shipment_service_id").change(function () {
            ShippingCalculator.fillServiceDescriptionById($(this).val());
        });

        // 3. Change the input value of "number of parcels"
        ShippingCalculator.changeInputParcelNum();

        // 4. Click on the "edit" link
        $("#editMultiPackagesInfo").click(function (e) {
            e.preventDefault();
            ShippingCalculator.showParcelLightBox(ShippingCalculator.modes.edit);
            return false;
        });
        
        //shippingEnvelopeForm_edit_parcels
        $("#shippingEnvelopeForm_edit_parcels").bind('click', function (e) {
            e.preventDefault();
            ShippingCalculator.showParcelLightBox(ShippingCalculator.modes.edit);
            return false;
        });

        // 5. Click the "Calculate" button
        $("#calculateButton").bind('click', function (e) {
            e.preventDefault();
            ShippingCalculator.resetValue();
            
            ShippingCalculator.calculate();
            return false;
        });
        
        $('#shippingEnvelopeForm_ReCalculateButton').bind('click', function (e) {
            e.preventDefault();
            ShippingCalculator.calculate();
            return false;
        });
    },

    initAjaxUrls: function (baseUrl) {
        this.ajaxUrls.shipping_calculator = baseUrl + 'info/shipping_calculator';
        this.ajaxUrls.getShippingServicesByLocation = baseUrl + 'info/get_shipping_services_by_location';
        this.ajaxUrls.getShippingServiceDescription = baseUrl + 'info/get_shipping_service_description';
        this.ajaxUrls.inputParcelsInfo = baseUrl + 'info/input_parcels_info';
        this.ajaxUrls.convertCurrency = baseUrl + 'info/convert_currency';
    },

    fillShippingServicesByLocation: function (locationId) {
        var $shippingServiceDescription = $("#shipment_service_description"),
            $shippingServiceSelector = $("#shipment_service_id");

        $.ajaxExec({
            url: ShippingCalculator.ajaxUrls.getShippingServicesByLocation,
            data: {
                location_id: locationId
            },
            success: function (response) {
                var shippingServices = response.data;

                $shippingServiceSelector.empty();
                $shippingServiceDescription.text('');

                if (Array.isArray(shippingServices) && shippingServices.length > 0) {
                    ShippingCalculator.buildShipmentServicesSelector($shippingServiceSelector, shippingServices);
                } else {
                    //$.infor({message: ShippingCalculator.messages.has_no_standard_shipment_service});
                    return false;
                }
            }
        });
    },

    buildShipmentServicesSelector: function ($shipmentServiceSelector, shipmentServices) {
        var htmlOptions = '<option value=""></option>';

        $.each(shipmentServices, function (index, shipmentService) {
            htmlOptions += '<option value="' + shipmentService.id + '">' + shipmentService.name + '</option>';
        });
        $shipmentServiceSelector.html(htmlOptions);
    },

    fillServiceDescriptionById: function (shippingServiceId) {
        var $serviceDescriptor = $("#shipment_service_description");
        if (parseInt(shippingServiceId) > 0) {
            $.ajaxExec({
                url: ShippingCalculator.ajaxUrls.getShippingServiceDescription,
                data: {
                    shipment_service_id: shippingServiceId
                },
                success: function (response) {
                    $serviceDescriptor.text(response.data.shipment_service_description);
                }
            });
        } else {
            $serviceDescriptor.text('');
        }
    },

    changeInputParcelNum: function () {
        var $parcelsNum = $("#number_of_parcels"),
            val = $parcelsNum.val();

        if (/^[0-9]+$/.test(val) == true) {
            $parcelsNum.data("value", val);
            if (parseInt(val) > 1) {
                ShippingCalculator.enableMainFormInput(false);
            } else {
                ShippingCalculator.enableMainFormInput(true);
            }
        }
        ShippingCalculator.intervals.ID_NumberOfParcels = setInterval(ShippingCalculator.checkParcelNum, ShippingCalculator.intervals.timer);
    },

    getTotalWeight: function (parcels) {
        var totalWeight = 0;

        $.each(parcels, function (index, parcel) {
            totalWeight += parcel.quantity * parcel.weight;
        });

        return ShippingCalculator.formatFloatValue(totalWeight);
    },

    formatFloatValue: function (priceValue) {
        if (ShippingCalculator.decimalSeparator == ShippingCalculator.DECIMAL_SEPARATORS.COMMA) {
            return priceValue.toFixed(2).replace('.', ',');
        } else if (ShippingCalculator.decimalSeparator == ShippingCalculator.DECIMAL_SEPARATORS.DOT) {
            return priceValue.toFixed(2).replace(',', '.');
        } else {
            return priceValue.toFixed(2).replace('.', ',');
        }
    },

    setMainFormInput: function (length, width, height, weight) {
        var $length = $("#length"), $width = $("#width"), $height = $("#height"), $weight = $("#weight");

        $length.val(length);
        $width.val(width);
        $height.val(height);
        $weight.val(weight);
    },

    enableMainFormInput: function (enable) {
        var $length = $("#length"), $width = $("#width"), $height = $("#height"), $weight = $("#weight");

        if (enable) {
            $length.removeAttr("disabled");
            $width.removeAttr("disabled");
            $height.removeAttr("disabled");
            $weight.removeAttr("disabled");
        } else {
            $length.attr("disabled", "disabled");
            $width.attr("disabled", "disabled");
            $height.attr("disabled", "disabled");
            $weight.attr("disabled", "disabled");
        }
    },

    checkParcelNum: function () {
        var $parcelsNum = $("#number_of_parcels"),
            oldInputValue = $parcelsNum.data("value"),
            newInputValue = $parcelsNum.val();

        if ((newInputValue != '') && (/^[0-9]+$/.test(newInputValue) == true) && (oldInputValue != newInputValue)) {
            $parcelsNum.data("value", newInputValue);
            ShippingCalculator.setMainFormInput('', '', '', '');
            if (parseInt(newInputValue) > 1) {
                ShippingCalculator.enableMainFormInput(false);
                ShippingCalculator.showParcelLightBox(ShippingCalculator.modes.create);
                clearInterval(ShippingCalculator.intervals.ID_NumberOfParcels);
            } else {
                ShippingCalculator.enableMainFormInput(true);
            }
        }
    },
    checkParcelNumTODO: function () {
        var $parcelsNum = $("#number_of_parcels"),
            oldInputValue = $parcelsNum.data("value"),
            newInputValue = $parcelsNum.val();

        if ((newInputValue != '') && (/^[0-9]+$/.test(newInputValue) == true) && (oldInputValue != newInputValue) && oldInputValue != undefined) {
            $parcelsNum.data("value", newInputValue);
            ShippingCalculator.setMainFormInput('', '', '', '');
            if (parseInt(newInputValue) > 1) {
                ShippingCalculator.enableMainFormInput(false);
                ShippingCalculator.showParcelLightBox(ShippingCalculator.modes.create);
                clearInterval(ShippingCalculator.intervals.ID_NumberOfParcels);
            } else {
                ShippingCalculator.enableMainFormInput(true);
            }
        }
    },

    showParcelLightBox: function (mode) {
        var $inputParcelsInfo = $('#inputParcelsInfo'),
            parcelsNum = $("#number_of_parcels").val(),
            parcels = sessionStorage.getItem(ShippingCalculator.sessionStorageItemKey);

        // Clear control of all dialog form
        //$('.dialog-form').html('');

        // Open new dialog
        $inputParcelsInfo.openDialog({
            autoOpen: false,
            height: 370,
            width: 810,
            modal: true,
            open: function () {
                if ((/^[0-9]+$/.test(parcelsNum) == false) || (parcelsNum == 0)) {
                    if (mode == ShippingCalculator.modes.edit) mode = ShippingCalculator.modes.create;
                    parcelsNum = 1;
                }
                $(this).load(ShippingCalculator.ajaxUrls.inputParcelsInfo, {
                    "mode": mode,
                    "lines": parcelsNum,
                    "parcelsData": parcels
                }, function () {
                    ShippingCalculator.fixCssOfApplyButton(parcelsNum);
                    $("input[name='quantity[]']").first().focus();

                    // Attach an event listener for input "weight" on the light-box
                    ShippingCalculator.checkInputWeight();
                });
            },
            close: function () {
                ShippingCalculator.intervals.ID_NumberOfParcels = setInterval(ShippingCalculator.checkParcelNum, ShippingCalculator.intervals.timer);
                clearInterval(ShippingCalculator.intervals.ID_Weight);
            },
            buttons: {
                'Add new line...': function () {
                    ShippingCalculator.addNewLine();
                },
                'Delete marked lines...': function () {
                    ShippingCalculator.deleteMarkedLines();
                },
                'Apply': function () {
                    var $dataRows = $("table.parcels_info_table > tbody > tr.row-data"), $dataRow = null;
                    var parcels = [], parcel = null;
                    var quantity = 0, weight = 0, length = 0, width = 0, height = 0;

                    $.each($dataRows, function (index, dataRow) {
                        $dataRow = $(dataRow);
                        quantity = ShippingCalculator.getParcelProp($dataRow, 'quantity');
                        weight = ShippingCalculator.getParcelProp($dataRow, 'weight');
                        length = ShippingCalculator.getParcelProp($dataRow, 'length');
                        width = ShippingCalculator.getParcelProp($dataRow, 'width');
                        height = ShippingCalculator.getParcelProp($dataRow, 'height');
                        parcel = {quantity: quantity, weight: weight, length: length, width: width, height: height};
                        parcels.push(parcel);
                    });
                    if (ShippingCalculator.validate(parcels)) {
                        sessionStorage.setItem(ShippingCalculator.sessionStorageItemKey, JSON.stringify(parcels));
                        ShippingCalculator.apply(parcels);
                        $(this).dialog('close');
                    }
                }
            }
        });

        $inputParcelsInfo.dialog('option', 'position', 'center');
        $inputParcelsInfo.dialog('open');
    },
    showParcelLightBoxTODO: function (mode) {
        var $inputParcelsInfo = $('#inputParcelsInfoTODO'),
            parcelsNum = $("#number_of_parcels").val(),
            parcels = sessionStorage.getItem(PrepareShipping.sessionStorageItemKey);

        // Clear control of all dialog form
        //$('.dialog-form').html('');

        // Open new dialog
        $inputParcelsInfo.openDialog({
            autoOpen: false,
            height: 370,
            width: 810,
            modal: true,
            open: function () {
                if ((/^[0-9]+$/.test(parcelsNum) == false) || (parcelsNum == 0)) {
                    if (mode == ShippingCalculator.modes.edit) mode = ShippingCalculator.modes.create;
                    parcelsNum = 1;
                }
                $(this).load(PrepareShipping.ajaxUrls.inputParcelsInfo, {
                    "mode": mode,
                    "lines": parcelsNum,
                    "parcelsData": parcels
                }, function () {
                    ShippingCalculator.fixCssOfApplyButton(parcelsNum);
                    $("input[name='quantity[]']").first().focus();

                    // Attach an event listener for input "weight" on the light-box
                    ShippingCalculator.checkInputWeight();
                });
            },
            close: function () {
                ShippingCalculator.intervals.ID_NumberOfParcels = setInterval(ShippingCalculator.checkParcelNumTODO, ShippingCalculator.intervals.timer);
                clearInterval(ShippingCalculator.intervals.ID_Weight);
            },
            buttons: {
                'Add new line...': function () {
                    ShippingCalculator.addNewLine();
                },
                'Delete marked lines...': function () {
                    ShippingCalculator.deleteMarkedLines();
                },
                'Apply': function () {
                    var $dataRows = $("table.parcels_info_table > tbody > tr.row-data"), $dataRow = null;
                    var parcels = [], parcel = null;
                    var quantity = 0, weight = 0, length = 0, width = 0, height = 0;

                    $.each($dataRows, function (index, dataRow) {
                        $dataRow = $(dataRow);
                        quantity = ShippingCalculator.getParcelProp($dataRow, 'quantity');
                        weight = ShippingCalculator.getParcelProp($dataRow, 'weight');
                        length = ShippingCalculator.getParcelProp($dataRow, 'length');
                        width = ShippingCalculator.getParcelProp($dataRow, 'width');
                        height = ShippingCalculator.getParcelProp($dataRow, 'height');
                        parcel = {quantity: quantity, weight: weight, length: length, width: width, height: height};
                        parcels.push(parcel);
                    });
                    if (ShippingCalculator.validate(parcels)) {
                        sessionStorage.setItem(ShippingCalculator.sessionStorageItemKey, JSON.stringify(parcels));
                        ShippingCalculator.apply(parcels);
                        $(this).dialog('close');
                    }
                }
            }
        });

        $inputParcelsInfo.dialog('option', 'position', 'center');
        $inputParcelsInfo.dialog('open');
    },
    /*
     * fix css-related problem of the "Apply" button
     */
    fixCssOfApplyButton: function (parcelsNum) {
        if (parcelsNum > 5) {
            $(".ui-dialog-buttonset button:last-child").attr('style', 'margin-right: 30px !important');
        }
    },

    checkInputWeight: function () {
        ShippingCalculator.intervals.ID_Weight = setInterval(ShippingCalculator.recalculateTotalWeight, ShippingCalculator.intervals.timer);
    },

    /*
     * Update total values for Light-box (Sub-form)
     */
    recalculateTotalWeight: function () {
        var $tbody = $("table.parcels_info_table > tbody"),
            dataRows = $tbody.find('tr.row-data'),
            sumRow = $tbody.find('tr.row-sum').first(),
            $dataRow = null, quantity = 0, weight = 0, totalWeight = 0;

        $.each(dataRows, function (index, dataRow) {
            $dataRow = $(dataRow);
            quantity = ShippingCalculator.getParcelProp($dataRow, 'quantity');
            weight = ShippingCalculator.getParcelProp($dataRow, 'weight');
            totalWeight += quantity * weight;
        });
        //console.log(totalWeight);
        $(sumRow).find('td').eq(3).html(ShippingCalculator.formatFloatValue(totalWeight));
    },

    addNewLine: function () {
        var $tbody = $("table.parcels_info_table > tbody"),
            parcelNum = $tbody.find("tr.row-data").length + 1,
            cssClass = (parcelNum % 2 == 0) ? 'even' : 'odd',
            tr = '<tr class="' + cssClass + ' row-data">';

        tr += '<td class="chk"><input type="checkbox" name="delete[]" class="cbox" role="checkbox"/></td>';
        tr += '<td>' + parcelNum + '</td>';
        tr += '<td><input type="text" name="quantity[]" class="input-width parcel-info" value="1"/></td>';
        tr += '<td><input type="text" name="weight[]" class="input-width parcel-info"/></td>';
        tr += '<td>kg</td>';
        tr += '<td><input type="text" name="length[]" class="input-width parcel-info"/></td>';
        tr += '<td><input type="text" name="width[]" class="input-width parcel-info"/></td>';
        tr += '<td><input type="text" name="height[]" class="input-width parcel-info"/></td>';
        tr += '<td>cm</td>';
        tr += '</tr>';

        $tbody.find('tr.row-sum').before(tr);
        ShippingCalculator.fixCssOfApplyButton(parcelNum);

        // Change the CSS class of the sum row
        ShippingCalculator.changeCssClassForLastRow();
    },

    deleteMarkedLines: function () {
        var $tbody = $("table.parcels_info_table > tbody"),
            markedLines = $tbody.find('tr.row-data').filter(':has(input[type="checkbox"]:checked)'),
            dataRows = $tbody.find('tr.row-data'),
            i = 0, $dataRow = null, cssClass = null;

        $.each(markedLines, function (index, markedLine) {
            markedLine.remove();
        });

        // Reset the CSS class of the remaining rows & the row values of the [Parcel #] column
        $.each(dataRows, function (index, dataRow) {
            ++i;
            $dataRow = $(dataRow);
            cssClass = (i % 2 == 0) ? 'even row-data' : 'odd row-data';
            $dataRow.removeClass();
            $dataRow.addClass(cssClass);
            $dataRow.find('td').eq(1).html(i);
        });

        // Reset the CSS class for the row-sum row
        ShippingCalculator.changeCssClassForLastRow();

        // Recalculate total values (Total of weights)
        ShippingCalculator.recalculateTotalWeight();
    },

    apply: function (parcels) {
        var length = parcels.length, i;
        var multipleQuantity = '', multipleLength = '', multipleWidth = '', multipleHeight = '', multipleWeight = '';

        console.log(parcels);
        for (i = 0; i < length; i++) {
            multipleQuantity += (i == length - 1) ? parcels[i].quantity : parcels[i].quantity + '#';
            multipleWeight += (i == length - 1) ? parcels[i].weight * 1000 : parcels[i].weight * 1000 + '#';
            multipleLength += (i == length - 1) ? parcels[i].length : parcels[i].length + '#';
            multipleWidth += (i == length - 1) ? parcels[i].width : parcels[i].width + '#';
            multipleHeight += (i == length - 1) ? parcels[i].height : parcels[i].height + '#';
        }

        $("#multiple_quantity").val(multipleQuantity);
        $("#multiple_length").val(multipleLength);
        $("#multiple_width").val(multipleWidth);
        $("#multiple_height").val(multipleHeight);
        $("#multiple_weight").val(multipleWeight);

        if (parcels.length > 1) {
            $("#length").val('multiple');
            $("#width").val('multiple');
            $("#height").val('multiple');
            ShippingCalculator.enableMainFormInput(false);
        } else {
            $("#length").val(parcels[0].length);
            $("#width").val(parcels[0].width);
            $("#height").val(parcels[0].height);
            ShippingCalculator.enableMainFormInput(true);
        }

        // Update total weight for both Main form and Light-box
        ShippingCalculator.updateTotalValues(parcels);
    },

    /*
     * Update total values for Main form
     */
    updateTotalValues: function (parcels) {
        var i, lines = parcels.length, parcel, totalQuantity = 0, totalWeight = 0;

        for (i = 0; i < lines; i++) {
            parcel = parcels[i];
            totalQuantity += parcel.quantity;
            totalWeight += parcel.weight * parcel.quantity;
        }

        totalWeight = ShippingCalculator.formatFloatValue(totalWeight);
        $("table.parcels_info_table > tbody > tr.row-sum > td").eq(3).html(totalWeight);

        $("#number_of_parcels").data("value", totalQuantity).val(totalQuantity);
        $("#weight").val(totalWeight);
    },

    calculate: function () {
        var numberOfParcels = parseInt($("#number_of_parcels").val());
        if (numberOfParcels >= 1) {
            $.ajaxSubmit({
                url: ShippingCalculator.ajaxUrls.shipping_calculator,
                formId: 'shippingCalculatorForm',
                success: function (response) {
                    if (response.status) {
                        //console.log(response);
                        $("#service_errors").html('').hide();
                        $("span.currency_short").text(response.data.currency_short);
                        $("#cal_postal_charge").text(response.data.postal_charge);
                        $("#cal_customs_handling").text(response.data.customs_handling);
                        $("#cal_handling_charges").text(response.data.handling_charges);
                        $("#cal_total_VAT").text(response.data.total_vat);
                        $("#cal_total_charge").text(response.data.total_charge);
                    } else {
                        //response.log(response);
                        $("#service_errors").show().html(response.data.errors);
                    }
                }
            });
        }
    },

    /*
     * Change CSS class for the last row (or the sum row)
     */
    changeCssClassForLastRow: function () {
        var $tbody = $("table.parcels_info_table > tbody"),
            dataRows = $tbody.find('tr.row-data'),
            numRows = dataRows.length + 1,
            cssClass = (numRows % 2 == 0) ? 'even row-sum' : 'odd row-sum',
            $dataRow = $($tbody.find('tr.row-sum').first());

        $dataRow.removeClass();
        $dataRow.addClass(cssClass);
    },

    getParcelDataFromMainForm: function () {
        var parcelsNum = parseInt($("#number_of_parcels").val()),
            length = parseInt($("#length").val()),
            width = parseInt($("#width").val()),
            height = parseInt($("#height").val()),
            weight = parseFloat($("#weight").val().replace(',', '.')),
            parcel = {quantity: parcelsNum, weight: _weight, length: _length, width: _width, height: _height},
            parcels = [];

        parcels.push(parcel);

        return parcels;
    },

    /*
     * Get all parcels' information of Light-box (sub-form)
     */
    getParcelsDataFromLightBox: function () {
        var $dataRows = $("table.parcels_info_table > tbody > tr.row-data"),
            parcels = [],
            $dataRow = null, parcelRow = null, quantity = 0, weight = 0, length = 0, width = 0, height = 0;

        $.each($dataRows, function (index, dataRow) {
            $dataRow = $(dataRow);
            quantity = ShippingCalculator.getParcelProp($dataRow, 'quantity');
            weight = ShippingCalculator.getParcelProp($dataRow, 'weight');
            length = ShippingCalculator.getParcelProp($dataRow, 'length');
            width = ShippingCalculator.getParcelProp($dataRow, 'width');
            height = ShippingCalculator.getParcelProp($dataRow, 'height');
            parcelRow = {quantity: quantity, weight: weight, length: length, width: width, height: height};
            parcels.push(parcelRow);
        });

        return parcels;
    },

    /*
     * Get a property value of one parcel entered the Light-box, for example: weight
     */
    getParcelProp: function ($dataRow, propName) {
        var propVal = $dataRow.find('input[name="' + propName + '[]"]').val();

        if (propName == 'weight') {
            if (/^[0-9]+[\.,]?[0-9]*$/.test(propVal) == true) {
                propVal = propVal.replace(',', '.');
                propVal = parseFloat(propVal);
            } else {
                propVal = 0;
            }
        } else {
            propVal = (/^[0-9]+$/.test(propVal) == true) ? parseInt(propVal) : 0;
        }

        return propVal;
    },

    validate: function (parcels) {
        var isValid = true, i, parcel = null, chkQu, chkWe, chkLe, chkWi, chkHe;

        for (i = 0; i < parcels.length; i++) {
            parcel = parcels[i];
            chkQu = ShippingCalculator.validateItem(parcel.quantity, i, 'quantity');
            chkWe = ShippingCalculator.validateItem(parcel.weight, i, 'weight');
            chkLe = ShippingCalculator.validateItem(parcel.length, i, 'length');
            chkWi = ShippingCalculator.validateItem(parcel.width, i, 'width');
            chkHe = ShippingCalculator.validateItem(parcel.height, i, 'height');
            isValid = isValid && chkQu && chkWe && chkLe && chkWi && chkHe;
        }
        if (isValid) {
            $(".error-messages").hide();
        } else {
            $(".error-messages").show();
            $("table.parcels_info_table > tbody > tr.row-data").find('input[style^="border"]').first().focus();
        }

        return isValid;
    },

    validateItem: function (val, i, name) {
        var isValid;

        if (val == 0) {
            isValid = false;
            ShippingCalculator.markInputError(i, name);
        } else {
            isValid = true;
            ShippingCalculator.clearInputError(i, name);
        }

        return isValid;
    },

    markInputError: function (i, name) {
        var $dataRow = $("table.parcels_info_table > tbody > tr.row-data").eq(i),
            $input = $dataRow.find('input[name="' + name + '[]"]').first();

        $input.attr('style', 'border: solid 1px red; background-color: rgb(255, 238, 164);');
    },

    clearInputError: function (i, name) {
        var $dataRow = $("table.parcels_info_table > tbody > tr.row-data").eq(i),
            $input = $dataRow.find('input[name="' + name + '[]"]').first();

        $input.removeAttr('style');
    },

    /*
     * Check if the browser has support for Web Storage
     */
    hasSupportedStorage: function () {
        if (typeof(Storage) !== "undefined") {
            return true;
        } else {
            console.log('Sorry! No Web Storage support.');
            return false;
        }
    },

    convertCurrency: function (currencyId) {
        var $postal_charge = $("#calculate_shipping_rate_detail_form_postal_charge"),
            $customs_handling = $("#calculate_shipping_rate_detail_form_customs_handling"),
            $handling_charges = $("#calculate_shipping_rate_detail_form_handling_charges"),
            $VAT = $("#calculate_VAT"),
            $total_charge = $("#calculate_total_charge"),
            $currency_selector = $("#currency_id"),
            $currency_short = $(".currency_short");

        var currency_short = $currency_selector.find(":selected").text(),
            postal_charge = $postal_charge.text(),
            customs_handling = $customs_handling.text(),
            handling_charges = $handling_charges.text(),
            VAT = $VAT.text(),
            total_charge = $total_charge.text();

        $.ajaxExec({
            url: ShippingCalculator.ajaxUrls.convertCurrency,
            data: {
                converted_currency_id: currencyId,
                base_postal_charge: postal_charge,
                base_customs_handling: customs_handling,
                base_handling_charges: handling_charges,
                base_VAT: VAT,
                base_total_charge: total_charge
            },
            success: function (response) {
                var data = response.data;
                if (data.postal_charge > 0) $postal_charge.text(data.postal_charge);
                if (data.customs_handling > 0) $customs_handling.text(data.customs_handling);
                if (data.handling_charges > 0) $handling_charges.text(data.handling_charges);
                if (data.VAT > 0) $VAT.text(data.VAT);
                if (data.total_charge > 0) $total_charge.text(data.total_charge);
            }
        });
        $currency_short.text(currency_short);
    },
    
    resetValue: function(){
        $("span.currency_short").text("");
        $("#cal_postal_charge").text("");
        $("#cal_customs_handling").text("");
        $("#cal_handling_charges").text("");
        $("#cal_total_VAT").text("");
        $("#cal_total_charge").text("");
    }
}