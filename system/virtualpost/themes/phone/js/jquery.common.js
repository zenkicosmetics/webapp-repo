var commonContext = "";

(function($) {
	$.fn.hasVerticalScrollbar = function() {
        // This will return true, when the div has vertical scrollbar
        return this.get(0).scrollHeight > this.height();
    };
    $.fn.hasHorizontalScrollbar = function() {
        // This will return true, when the div has horizontal scrollbar
        return this.get(0).scrollWidth > this.width();
    };
    
	/****************************************************************
     * Function name              : $.openDialog
     * Function overview          : Fixed show/hide select option in IE
     * Return                     : N/A
     ****************************************************************/
	$.fn.openDialog = function(config) {
		// Override config
        config = jQuery.extend({
        },config);
		$(this).dialog(config);
	};
	
	/****************************************************************
     * Function name              : $.serializeObject
     * Function overview          : Convert Form to JSON object
     * Return                     : N/A
     ****************************************************************/
	$.fn.serializeObject = function()
	{
	    var o = {};
	    var a = this.serializeArray();
	    $.each(a, function() {
	        if (o[this.name] !== undefined) {
	            if (!o[this.name].push) {
	                o[this.name] = [o[this.name]];
	            }
	            o[this.name].push(this.value || '');
	        } else {
	            o[this.name] = this.value || '';
	        }
	    });
	    return o;
	};

    /****************************************************************
     * Function name              : $.pageBlock
     * Function overview          : Block the page and displays the processing icon.
     * Parameter                  : message : Message (default: "processing...")
     * Return                     : N/A
     ****************************************************************/
    $.pageBlock = function(config) {

        // Override config
        config = jQuery.extend({
                message: "Processing..."
        },config);

        // Block page
        $.blockUI({
            message: "<span class='icon icon-process'></span>&nbsp;" + config.message,
            css: {
                border: 'none',
                width: '200px',
                padding: '5px',
                backgroundColor: '#FFFFFF',
                '-webkit-border-radius': '5px',
                '-moz-border-radius': '5px',
                'border-radius' : '5px',
                opacity: .6,
                color: '#000000',
                textAlign: 'center',
                top: ($(window).height() - 40) /2 + 'px',
                left: ($(window).width() - 210) /2 + 'px'
            },
            overlayCSS: {
                backgroundColor: "#ffffff",
                opacity: 0.0
            },
            baseZ: 9999,
            fadeIn: 0,
            showOverlay: true
        });

    };

    /****************************************************************
     * Function name      : $.pageUnblock
     * Function overview  : To unblock the page
     * Parameter          : N/A
     * Return             : N/A
     ****************************************************************/
    $.pageUnblock = function(config) {
    	$.unblockUI();
    };

    /****************************************************************
     * Function name        : $.ajaxExec
     * Function overview    : Load html data by HTTP communication
     *                        Response is assumed to be HTML.
     * Parameter            : url    : 	Request URL
     *                        data   : The values to send to the server (default: "")
     * Return               : N/A
     ****************************************************************/
    $.ajaxExec = function(config) {
        // The default value of the argument
        config = jQuery.extend({
            data: "",
            success:{}
        },config);

        if ($.isEmpty(config.url)) {
            return;
        }
        
        // To block page
        $.pageBlock();

        // Send ajax request
        $.ajax({
            type: "POST",
            url: config.url,
            data: config.data,
            dataType: "json",
            timeout: 60000,
            async: true,
            // When success response
            success: function(obj) {
            	// Check session time out
            	checkSessionTimeout(obj);
            	
                // Call success method
            	config.success(obj);
            },
            // When error response
            error: function(XMLHttpRequest, textStatus, errorThrown) {
            	// Show error message
    			$.error({message: "System error occurs. Please contact System Administrator."});
            },
            complete: function(XMLHttpRequest, textStatus) {
                // To unblock page
                $.pageUnblock();
            }
        });
    };
    
    /****************************************************************
     * Function name        : $.ajaxSubmit
     * Function overview    : Load html data by HTTP communication
     *                        Response is assumed to be HTML.
     * Parameter            : formId    : The form id
     *                        url		: The target server address
     * Return               : N/A
     ****************************************************************/
    $.ajaxSubmit = function(config) {
        // The default value of the argument
        config = jQuery.extend({
            formId: "",
            url: "",
            success:{}
        },config);
        
        if ($.isEmpty(config.url)) {
            return;
        }
        
        // To block page
        $.pageBlock();

        // Send ajax request
        $.ajax({
            type: "POST",
            url: config.url,
            data: $("#" + config.formId).serializeArray(),
            dataType: "json",
            timeout: 60000,
            async: true,
            // When success response
            success: function(obj) {
            	// Check session time out
            	checkSessionTimeout(obj);
            	
                // Call success method
            	config.success(obj);
            },
            // When error response
            error: function(XMLHttpRequest, textStatus, errorThrown) {
            	// Show error message
    			$.error({message: "System error occurs. Please contact System Administrator."});
            },
            complete: function(XMLHttpRequest, textStatus) {
                // To unblock page
                $.pageUnblock();
            }
        });
    };
    
    /****************************************************************
     * Function name        : $.ajaxLoadHtml
     * Function overview    : Send ajax request by HTTP
     *                        Response is assumed to be HTML.
     * Parameter            : url    : 	Request URL
     *                        data   : The values to send to the server (default: "")
     *                        successCallback: The method will be execute when success response(default: {})
     *                        failCallback: The method will be execute when fail response(default: {})
     * Return               : N/A
     ****************************************************************/
    $.ajaxLoadHtml = function(config) {

        var responseText;

        // The default value of the argument
        config = jQuery.extend({
            data: ""
        },config);
        
        if ($.isEmpty(config.url)) {
            return;
        }

        // To block page
        $.pageBlock();

        // Send ajax request
        responseText = $.ajax({
            type: "POST",
            url: config.url,
            data: config.data,
            dataType: "html",
            timeout: 60000,
            async: false,
            // When success response
            success: function(obj) {
            	// Check session time out
            	checkSessionTimeout(obj);
                // N/A
            },
            // When error response
            error: function(XMLHttpRequest, textStatus, errorThrown) {
            	$.error({message: 'System error ocurr. Please contact with administrator.'});
            },
            complete: function(XMLHttpRequest, textStatus) {
                // To unblock page
                $.pageUnblock();
            }
        }).responseText;

        return responseText;
    };

    /****************************************************************
     * Function name     : $.infor
     * Function overview  : Display dialog show information
     * Paramter: 	titile  : Title of dialog (Default: "Information")
     *               message : Message content (Default: "")
     *               width   : The width of dialog (Default:400)
     *               ok      : Function callback when click OK button
     * Return      : N/A
     ****************************************************************/
    $.infor = function(config) {
        config = jQuery.extend({
            title: "Information",
            message: "",
            width: 400,
            ok: function() {}
        },config);

        $("<div title='" + config.title + "' style='font-weight: bold; color: #0089C8; padding: 20px 10px;'><p>" + config.message + "</p></div>").dialog({
            close: function(event, ui) { $(this).parents(".ui-dialog").remove(); },
            bgiframe: true,
            resizable: false,
            width: config.width,
            modal: true,
            position: 'center',
            buttons: [
                {
                	text: 'OK',
                	"class": 'sub-button-dialog button-dialog-width-80',
	                click: function() {
	                    config.ok();
	                    $(this).dialog("close").dialog("destroy");
	                }
                }
            ]
        });
        
    };
    
    /****************************************************************
     * Function name     : $.confirm
     * Function overview  : Display dialog confirmation
     * Paramter: 	titile  : Title of dialog (Default: "Confirmation")
     *               message : Message content (Default: "")
     *               width   : The width of dialog (Default:400)
     *               yes      : Function callback when click YES button
     * Return      : N/A
     ****************************************************************/
    $.confirm = function(config) {
        config = jQuery.extend({
            title: "Confirmation",
            message: "",
            width: 400,
            modal: true,
            yes: function() {},
            okText: 'Yes',
            class: ''
        },config);

        $("<div title='" + config.title + "' style='font-weight: bold; color: #0089C8; padding: 20px 10px;'><p>" + config.message + "</p></div>").dialog({
            close: function(event, ui) { $(this).parents(".ui-dialog").remove(); },
            bgiframe: true,
            resizable: false,
            width: config.width,
            modal: true,
            position: 'center',
            buttons: [
                {
                	text: config.okText,
                	"class": 'sub-button-dialog button-dialog-width-80 understand',
	                click: function() {
	                    config.yes();
	                    $(this).dialog("close").dialog("destroy");
	                }
                },
                {
                	text: 'Cancel',
                	"class": 'sub-button-dialog button-dialog-width-80',
                	click: function() {
                        $(this).dialog("close").dialog("destroy");
                    }
                }
            ]
        });
        
    };
    
    /****************************************************************
     * Function name     : $.confirmPayment
     * Function overview  : Display dialog confirmation for payment method.
     * Paramter: 	titile  : Title of dialog (Default: "Confirmation")
     *               message : Message content (Default: "")
     *               width   : The width of dialog (Default:400)
     *               yes      : Function callback when click YES button
     * Return      : N/A
     ****************************************************************/
    $.confirmPayment = function(config) {
        config = jQuery.extend({
            title: "Confirmation",
            message: "",
            width: 400,
            modal: true,
            yes: function() {},
            okText: 'Yes',
            class: ''
        },config);

        $("<div title='" + config.title + "' style='font-weight: bold; color: #0089C8; padding: 20px 10px;'><p>" + config.message + "</p></div>").dialog({
            close: function(event, ui) { $(this).parents(".ui-dialog").remove(); },
            bgiframe: true,
            resizable: false,
            width: config.width,
            modal: true,
            position: 'center',
            buttons: [
                {
                	text: config.okText,
                	"class": 'sub-button-dialog button-dialog-width-80 understand',
	                click: function() {
	                    config.yes();
	                    $(this).dialog("close").dialog("destroy");
	                }
                }
            ]
        });
        
    };

    /****************************************************************
     * Function name     : $.error
     * Function overview  : Show dialog contains error message
     * Paramter: 	titile  : Title of dialog (Default: "Error")
     *               message : Message content (Default: "")
     *               width   : The width of dialog (Default:400)
     *               ok      : Function callback when click OK button
     * Return      : N/A
     ****************************************************************/
    $.error = function(config) {
        // Setting default config
        config = jQuery.extend({
                title: "Error",
                message: "",
                width: 400,
                minHeight: 50,
                modal: true,
                ok: function() {}
            },config);

        // Create and show dialog
        var dialog = $("<div title='" + config.title + "' style='font-weight: bold; color: #d14b4b; padding: 20px 10px;'><p>" + config.message + "</p></div>").dialog({
            close: function(event, ui) { $(this).parents(".ui-dialog").remove(); },
            bgiframe: true,
            resizable: false,
            width: config.width,
            modal: config.modal,
            autoOpen: false,
            minHeight: config.minHeight,
            buttons: [
	            {
	            	text: 'OK',
	            	"class": 'sub-button-dialog button-dialog-width-80',
	                click: function() {
	                	if (config.focusItem) {
	                		$(config.focusItem).focus();
	                	}
	                    config.ok();
	                    $(this).dialog("close").dialog("destroy");
	                }
	            }
            ]
        });
        
        $(dialog).dialog('option', 'position', 'center');
		$(dialog).dialog('open');
    };
    
    /****************************************************************
     * Function name        : $.ajaxFileUpload
     * Function overview    : Upload file by HTTP
     *                        Response is assumed to be HTML.
     * Parameter            : id: The id of upload form 
	 *						  url    : 	Request URL
     *                        data   : The values to send to the server (default: "")
     *                        success: The method will be execute when success response(default: {})
     *                        error: The method will be execute when fail response(default: {})
     * Return               : N/A
     ****************************************************************/
	$.ajaxFileUpload = function(config) {
        // Override config
        config = jQuery.extend({
            data: "",
            success: function(obj) {},
            error: function(obj) {}
        },config);
        
        if ($.isEmpty(config.url)) {
            return;
        }

        // Block page
        $.pageBlock();
		
        $("#" + config.id).upload(
            config.url,
            config.data,
            function(text) {
                var obj ={};
                try {
                    obj = eval("(" + text + ")");
                } catch (e) {
                    // Display system error
					$.error({message: "System error occur. Please contact with administrator."});
                }

                // Unblock
                $.pageUnblock();
                var status = obj.status;                
                if (status) {                    
                    config.success(obj);
                } else {
					$.error({message:  obj.message});
					config.error(obj);
                }
				
            },
            "text"
        );
    };
    
    /****************************************************************
     * Function name     : $.initPage
     * Function overview  : Initial page when loading complete.
     * Paramter: 	value  : The value input
     * Return      : True if value input is empty and false in other case.
     ****************************************************************/
    $.initPage = function() {
    	$.trimInputText();
    	$('button').button();
    	$('button.main-button-dialog').button({
            icons: {
                primary: "check_mark"
            }
        });
    	$('.input_date').datepicker();
    };
    
    /****************************************************************
     * Function name     : $.contains
     * Function overview  : Initial page when loading complete.
     * Paramter: 	value  : The value input
     * Return      : True if value input is empty and false in other case.
     ****************************************************************/
    $.containsObject = function(arr, member_vehicle_id) {
    	if (arr == null || arr == 'undefined') {
    		return false;
    	}
    	for (var i = 0; i < arr.length; i++) {
    		if (arr[i] == member_vehicle_id) {
    			return true;
    		}
    	}
    	return false;
    };
    
    /****************************************************************
     * Function name     : $.isEmpty
     * Function overview  : Check value input is empty or not.
     * Paramter: 	value  : The value input
     * Return      : True if value input is empty and false in other case.
     ****************************************************************/
    $.isEmpty = function(value) {
        if (value != void 0 && value != null && value != "") {
            return false;
        }
        return true;
    };
    
    /****************************************************************
     * Function name     : $.bindSelect
     * Function overview  : Bind data of select box by Ajax
     * Paramter: 	url  : The url of remmote server
     * 				param : The json data pass to server
     * 				select_control_id: The target select control id
     * Return      : N/A
     ****************************************************************/
    $.bindSelect = function(url, param, select_control_id, label_default, value_default, callback) {
    	$.post(url, param, function(data) {
		    var sel = $('#' + select_control_id);
		    sel.empty();
		    if ($.isNotEmpty(label_default)) {
		    	sel.append('<option value="" disabled>' + label_default + '</option>');
		    }
		    for (var i=0; i<data.length; i++) {
		    	sel.append('<option value="' + data[i].key + '">' + data[i].label + '</option>');
		    }
		    sel.val(value_default);
		    if (callback) {
		    	callback();
		    }
    	}, "json");
    };
    /****************************************************************
     * Function name     : $.isNotEmpty
     * Function overview  : Check value input is empty or not.
     * Paramter: 	value  : The value input
     * Return      : True if value input is not empty and false in other case.
     ****************************************************************/
    $.isNotEmpty = function(value) {
        return !($.isEmpty(value));
    };
    
    /****************************************************************
     * Function name    	: $.trimInputText
     * Function overview  	: type of input to trim leading and trailing whitespace from a string in the field of text.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.trimInputText = function() {
        // To trim the blank text box when the focus moves away from
        $("input[type='text'], textarea").blur(function() {
            $(this).val($(this).val().replace(/^[\s　]+|[\s　]+$/g, ""));
        });
    };
    
    /****************************************************************
     * Function name    	: $.isValidEmail
     * Function overview  	: Check value input is valid email or not.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.isValidEmail = function(value) {
    	return /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i.test(value);
    }
    
    /****************************************************************
     * Function name    	: $.isValidUrl
     * Function overview  	: Check value input is valid url or not.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.isValidUrl = function(value) {
    	return /^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(value);
    };
    
    /****************************************************************
     * Function name    	: $.isValidNumber
     * Function overview  	: Check value input is valid number or not.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.isValidNumber = function(value) {
    	var result =  /^[0-9]+([,\.][0-9]+)*$/.test(value);
    	return result;
    };
    
    /****************************************************************
     * Function name    	: $.isValidNumber
     * Function overview  	: Check value input is valid number or not.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.isValidInt = function(value) {
    	if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
    		return true;
    	} else { 
    		return false;
    	} 
    };
    
    /****************************************************************
     * Function name    	: $.isValidDigits
     * Function overview  	: Check value input is valid digit or not.
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.isValidDigits = function(value) {
    	return /^\d+$/.test(value);
    };
    
     /****************************************************************
     * Function name    	: $.toTitleCase
     * Function overview  	: Convert text to title case ('foo bar' ==> 'Foo Bar')
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.toTitleCase = function(str) {
	    return str.replace(/(?:^|\s)\w/g, function(match) {
	        return match.toUpperCase();
	    });
    }
    
    $.setContext = function(context) {
    	commonContext = context;
    }
    
    /****************************************************************
     * Function name    	: $.displayQtip
     * Function overview  	: Display tool tip when hover object
     * Paramter         	: N/A
     * Return         		: N/A
     ****************************************************************/
    $.displayQtip = function(event, obj, module_name) {
	    var control_id = obj.attr('id');
	    var data_url = commonContext + '/help/' + module_name + '/' + control_id + ".html";
		obj.qtip({
			overwrite: false,
			content: {
				text: 'Loading...',
				ajax: {
					url: data_url,
					type: 'GET'
				}
			},
			show: {
				event: event.type,
				ready: true
			},
			hide : { 
				delay : 0,
				leave: false
			},
			position: {
				my: 'left center', 
				at: 'right center',
				target: obj
			},
			style: {
				classes: 'ui-tooltip-red ui-tooltip-shadow'
			}
		}, event);
    };
    
    $.checkRegexp = function( o, regexp, message ) {
		if ( !( regexp.test( o.val() ) ) ) {
			$.displayError( message, o);
			o.focus();
			return false;
		} else {
			return true;
		}
	}
	
    $.checkRequired = function(o, message) {
		if ( $.isEmpty(o.val())) {
			$.displayError( message, o);
			o.focus();
			return false;
		} else {
			return true;
		}
	}
    
    $.checkEmail = function(o, message) {
		if (!$.isValidEmail(o.val())) {
			$.displayError( message, o);
			o.focus();
			return false;
		} else {
			return true;
		}
	}
    
    $.checkMatch = function (o1, o2, message) {
    	if (o1.val() != o2.val()) {
    		$.displayError( message, o1);
			o1.val('');
			o2.val('');
    		return false;
    	}
    	return true;
    }
	
	$.checkLength = function ( o, n, min, max ) {
		if ( o.val().length > max || o.val().length < min ) {
			$.displayError( "Length of " + n + " must be between " +
				min + " and " + max + ".", o);
			return false;
		} else {
			return true;
		}
	}
	
	// START DuNT added
	$.checkUrl = function(o, message){
		if(!$.isValidUrl(o.val())){
			$.displayError(message);
			o.focus();
			return false;
		}
		
		return true;
	};
	
	$.checkValidNumber = function (o, message){
		if(!$.isValidNumber(o.val())){
			$.displayError(message);
			o.focus();
			return false;
		}
		
		return true;
	};
	
	$.checkValidDigit = function (o, message){
		if(!$.isValidDigits(o.val())){
			$.displayError(message);
			o.focus();
			return false;
		}
		
		return true;
	};
	
	$.validPasswordStrength = function (o, message){
		strength = 0;
		password = o.val();
		
		if(password.length > 7){
			strength +=1;
		}
		
		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) {
			strength += 1;
		}
		
		if(strength < 2){
			$.displayError(message);
			return false;
		}
		return true;
	};
	// END DuNT added
	
	$.displayError = function(message, o) {
		$.error({message: message, focusItem: o});
	};
	
	$.displaySuccess = function(message, o) {
		$.infor({message: message, focusItem: o});
	};
    
    $.displayError = function(message, o, ok) {
		$.error({message: message, focusItem: o, ok: ok});
	};
	
	$.displayInfor = function(message, o, ok) {
		$.infor({message: message, focusItem: o, ok: ok});
	};
    
	/****************************************************************
     * Function name     : delayTime
     * Function overview  : Delay time (minisecond)
     * Paramter: 	value  : The time system delay
     * Return      : N/A
     ****************************************************************/
	$.delayTime = function(time) {
		var d1 = new Date();
		var d2 = new Date();
		while (d2.valueOf() < d1.valueOf() + time) {
		    d2 = new Date();
		}
	};
	
	/****************************************************************
     * Function name     : checkSessionTimeout
     * Function overview  : Check ajax request have session time out or not.
     * Paramter: 	value  : The response object return from server
     * Return      : True if value input is empty and false in other case.
     ****************************************************************/
    function checkSessionTimeout(response) {
    	if (response && response.data && response.data.code === '999') {
    		document.location = commonContext + 'customers/logout';
    	}
    };
    
    /****************************************************************
     * Function name     : isChecked
     * Function overview  : Check status of current checkbox
     * Paramter: 	checkboxId  : The check box id control
     * Return      : True if checkbox is checked and false in other case
     ****************************************************************/
    $.isChecked = function(checkboxId) {
        var id = '#' + checkboxId;
        return $(id).is(":checked");
    };
    
    /****************************************************************
     * Function name     : getAllSelectedCheckboxValue
     * Function overview  : Get all selected checkbox
     * Paramter: 	selectedClass  : The selected class value
     * 				attrName	   : The attribute name will use to get value
     * Return      : Array of atrribute value.
     ****************************************************************/
    $.getAllSelectedCheckboxValue = function(selectedClass, attrName) {
    	var selectedArray = [];
    	$('.' + selectedClass).each(function () {
    		if ($(this).is(":checked")) {
    			var selectedVal = $(this).attr(attrName);
    			selectedArray.push(selectedVal);
    		}
    	});
    	return selectedArray;
    };
    
    /****************************************************************
     * Function name     : getAllSelectedCheckboxValue
     * Function overview  : Get all selected checkbox
     * Paramter: 	selectedClass  : The selected class value
     * 				attrName	   : The attribute name will use to get value
     * Return      : Array of atrribute value.
     ****************************************************************/
    $.uncheckedAll = function(selectedClass, checkedId) {
    	$('.' + selectedClass).each(function () {
    		var envelope_id = $(this).data('id');
    		$('#row_' + envelope_id).removeClass('selected');
    		if ($(this).attr('id') != checkedId) {
    			$(this).prop("checked", false);
    		}
    	});
    };
    
    /****************************************************************
     * Function name     : openDialog
     * Function overview  : common function open Jquery Dialog.
     * Paramter: 	value  : The response object return from server
     * Return      : True if value input is empty and false in other case.
     ****************************************************************/
    $.openDialog = function(divId, config){
        var divObj = document.getElementById(divId);
        
        if(divObj === null || divObj === undefined){
            // create new element with this object.
            var tmpDiv = $("<div id='"+divId.substring(1)+"' class='input-form dialog-form'></div>");
            $("body").append(tmpDiv);
            divObj = $(divId);
        }

        // get title.
        var title = $(divObj).attr("title");
        if(!$.isEmpty(config.title)){
            title = config.title;
            if($.isEmpty(title)){
                title = divId;
            }
        }

        // Clear control of all dialog form
        $(divObj).html('');

        // Open new dialog
        $(divObj).openDialog({
            autoOpen: false,
            height: config.height,
            width: config.width,
            title: title,
            modal: true,
            open: function () {
                if(!$.isEmpty(config.openUrl)){
                    $(divObj).load(config.openUrl, function () {
                    });
                }
            },
            close: function(){
                var callback = config.callback;
                // callback function before close.
                if (typeof callback == "function") {
                    callback();
                }
            }
        });

        var myButtons = [];
        if(!$.isEmpty(config.buttons)){
            for(i=0; i< config.buttons.length ; i++){
                var tmp = {
                    text: config.buttons[i].text,
                    click: function(){}
                };
                myButtons.push(tmp);
            }
        }
        
        if((!$.isEmpty(config.show_only_close_button) && config.show_only_close_button == true)|| !$.isEmpty(config.buttons) ){
            cancelButtonLabel = "Cancel";
            if(!$.isEmpty(config.closeButtonLabel)){
                cancelButtonLabel = config.closeButtonLabel;
            }
            var cancelButton = {
                text: cancelButtonLabel,
                click: function(){
                    $(divObj).dialog("close");
                }
            };
            myButtons.push(cancelButton);
        }
        
        if(myButtons.length > 0){
            // append button
            $(divObj).dialog('option', 'buttons', myButtons);

            if(!$.isEmpty(config.buttons)){
                for(i=0; i< config.buttons.length ; i++){
                    $(divObj).parent().find('.ui-dialog-buttonpane button:contains("'+config.buttons[i].text+'")').attr("id", divId.substring(1) + "_" +config.buttons[i].id);
                }
            }
        }

        $(divObj).dialog('option', 'position', 'center');
        $(divObj).dialog('open');
    };
})(jQuery);
