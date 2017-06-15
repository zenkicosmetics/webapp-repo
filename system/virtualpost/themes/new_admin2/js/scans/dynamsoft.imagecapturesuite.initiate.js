﻿/*!
* Dynamsoft JavaScript Library for Basic Initiation of ImageCapture Suite
* More info on ICS: http://www.dynamsoft.com/Products/image-acquisition-library.aspx
*
* Copyright 2013, Dynamsoft Corporation 
* Author: Dynamsoft Support Team
* Date: August 8 2013 
* Version: 9.1
*/

// ICS Properties

var Dynamsoft = (function () {
	// Get Browser Agent Value
	var ua = (navigator.userAgent.toLowerCase()),
		_path = CONTEXT_PATH + 'system/virtualpost/themes/new_admin/resources/',
		_ret = {
		ICSProduct : {
			//--------------------------------------------------------------------------------------
			//****** <Required> You must specify it before using ICS
			//--------------------------------------------------------------------------------------
			_strProductKey: '',

			//------------------------------------------
			//++++++ <optional>
			//------------------------------------------
			_bIsTrial: true,     					// Whether it is using the trial version.
			_strProductName: 'ImageCapture Suite', // The Product Name of ICS.
			_strVersionCode: '9,1', 				// The version of ICS. ActiveX will use this to determin if it is necessary to upgrade the client. Use ',' to separate the numbers.

			_strLPKPath: _path + 'ImageCaptureSuite.lpk',     					// The relative path of the LPK file.
			_strPKGPath: _path + 'ImageCaptureSuiteMacEdition.pkg',     	//The relative path of the PKG file.
			_strMSIPath: _path + 'ImageCaptureSuitePlugIn.msi',         		//The relative path of the MSI file.
			_strCABX86Path: _path + 'ImageCaptureSuite.cab',         			//The relative path of the x86 cab file.
			_strCABX64Path: _path + 'ImageCaptureSuitex64.cab',      			//The relative path of the x64 cab file.

			_strMIMETYPE: 'Application/ImageCaptureSuite-Plugin',
			_strPROCLASSID: '5220cb21-c88d-11cf-b347-00aa00a28331',
			_strFULLCLASSID: 'D6D6D32A-E059-4174-88F3-5853EB11DE3F',
			_strTRAILCLASSID: 'E61B84D6-979B-4864-91B7-B8C140B58D54'


		},
		Env: {
		
			// Set the Explorer Type
		_bInIE: (ua.indexOf('msie') != -1 || ua.indexOf('trident') != -1),                 

			// Set the Operating System Type
			// NOTE: only support Mac & Windows
			_bInWindows : (ua.indexOf('macintosh') == -1),
			
			// Set the x86 and x64 type
			_bInWindowsX64: (ua.indexOf('win64') != -1 || ua.indexOf('x64') != -1),

			_iPluginLength: (this._bInIE) ? 0: navigator.plugins.length,
						
			_varSeed : '',               // The seed used to detect the control.
			_bFirstSImageCapture : true,
			_aryAllSImageCapture : []
        },		
	    mix:function (d, s) {
			for (var i in s) {
				d[i] = s[i];
			}
		}
	};
	return _ret;
})();

// ICS Functions
(function(D) {
    function SImageCapture() {
        var swt = this;
        //--------------------------------------------------------------------------------------
        //****** <Required> You must specify it before using ICS
        //--------------------------------------------------------------------------------------
        swt._strICSControlContainerID = 'dwtcontrolContainer';

        //--------------------------------------------------------------------------------------
        //++++++ <optional> Default value provided. You can change it accordingly
        //--------------------------------------------------------------------------------------
        swt._strObjectName = swt._strICSControlContainerID + '_Obj';
        swt._iWidth = 680;        // The width of the main control.
        swt._iHeight = 400;       // The height of the main control.

        //--------------------------------------------------------------------------------------
        //++++++ <optional> Events
        //--------------------------------------------------------------------------------------
        swt._onPostTransfer = '';
        swt._onPostAllTransfers = '';
        swt._onMouseClick = '';
        swt._onPostLoad = '';
        swt._onImageAreaSelected = '';
        swt._onMouseDoubleClick = '';
        swt._onMouseRightClick = '';
        swt._onTopImageInTheViewChanged = '';
        swt._onImageAreaDeSelected = '';
        swt._onGetFilePath = '';

        //--------------------------------------------------------------------------------------
        //					 Default value provided. YOU CANNOT change it accordingly
        //--------------------------------------------------------------------------------------
        swt._objectImageCapture = null;           // The ICS Object
        swt._strICSInnerContainerID = swt._strICSControlContainerID + '_CID'; // The ID of the container (Usually a DIV) which is used to contain ICS object. User must specify it.
        swt._strICSNonInstallInnerContainerID = swt._strICSControlContainerID + '_NonInstallCID'; // The ID of the container (Usually a DIV) which is used to show a message if ICS is not installed. User must specify it.

    };

    SImageCapture.prototype.getInstance = function() {
        return this._objectImageCapture;
    };

    SImageCapture.prototype._init = function(configs) {
        if (!configs)
            return;

        var swt = this;
        if (configs.width)
            swt._iWidth = configs.width;
        if (configs.height)
            swt._iHeight = configs.height;

        if (configs.containerID) {
            swt._strICSControlContainerID = configs.containerID;
            swt._strICSInnerContainerID = swt._strICSControlContainerID + '_CID';
            swt._strObjectName = swt._strICSControlContainerID + '_Obj';
            swt._strICSNonInstallInnerContainerID = swt._strICSControlContainerID + '_NonInstallCID';
        }

        if (configs.onPostTransfer) {
            swt._onPostTransfer = configs.onPostTransfer;
        }

        if (configs.onPostAllTransfers) {
            swt._onPostAllTransfers = configs.onPostAllTransfers;
        }

        if (configs.onMouseClick) {
            swt._onMouseClick = configs.onMouseClick;
        }

        if (configs.onPostLoad) {
            swt._onPostLoad = configs.onPostLoad;
        }

        if (configs.onImageAreaSelected) {
            swt._onImageAreaSelected = configs.onImageAreaSelected;
        }

        if (configs.onMouseDoubleClick) {
            swt._onMouseDoubleClick = configs.onMouseDoubleClick;
        }

        if (configs.onMouseRightClick) {
            swt._onMouseRightClick = configs.onMouseRightClick;
        }

        if (configs.onTopImageInTheViewChanged) {
            swt._onTopImageInTheViewChanged = configs.onTopImageInTheViewChanged;
        }

        if (configs.onImageAreaDeSelected) {
            swt._onImageAreaDeSelected = configs.onImageAreaDeSelected;
        }

        if (configs.onGetFilePath) {
            swt._onGetFilePath = configs.onGetFilePath;
        }
    };

    SImageCapture.prototype._attachEvents = function() {
        // For IE, attach events
        if (Dynamsoft.Env._bInIE) {
            var wt = this;
            if (wt._onPostTransfer != '')
                wt._objectImageCapture.attachEvent('onPostTransfer', wt._onPostTransfer);
            if (wt._onPostAllTransfers != '')
                wt._objectImageCapture.attachEvent('onPostAllTransfers', wt._onPostAllTransfers);
            if (wt._onMouseClick != '')
                wt._objectImageCapture.attachEvent('onMouseClick', wt._onMouseClick);
            if (wt._onPostLoad != '')
                wt._objectImageCapture.attachEvent('onPostLoad', wt._onPostLoad);
            if (wt._onImageAreaSelected != '')
                wt._objectImageCapture.attachEvent('onImageAreaSelected', wt._onImageAreaSelected);
            if (wt._onMouseDoubleClick != '')
                wt._objectImageCapture.attachEvent('onMouseDoubleClick', wt._onMouseDoubleClick);
            if (wt._onMouseRightClick != '')
                wt._objectImageCapture.attachEvent('onMouseRightClick', wt._onMouseRightClick);
            if (wt._onTopImageInTheViewChanged != '')
                wt._objectImageCapture.attachEvent('onTopImageInTheViewChanged', wt._onTopImageInTheViewChanged);
            if (wt._onImageAreaDeSelected != '')
                wt._objectImageCapture.attachEvent('onImageAreaDeSelected', wt._onImageAreaDeSelected);
            if (wt._onGetFilePath != '')
                wt._objectImageCapture.attachEvent('onGetFilePath', wt._onGetFilePath);
        }
    };

    SImageCapture.prototype._firefoxEvents = function() {

        var eventString = [];

        if (this._onPostTransfer != '')
            eventString.push(' onPostTransfer="' + this._onPostTransfer.name + '"');
        if (this._onPostAllTransfers != '')
            eventString.push(' onPostAllTransfers="' + this._onPostAllTransfers.name + '"');
        if (this._onMouseClick != '')
            eventString.push(' onMouseClick="' + this._onMouseClick.name + '"');
        if (this._onPostLoad != '')
            eventString.push('  onPostLoad="' + this._onPostLoad.name + '"');
        if (this._onImageAreaSelected != '')
            eventString.push(' onImageAreaSelected = "' + this._onImageAreaSelected.name + '"');
        if (this._onImageAreaDeSelected != '')
            eventString.push(' onImageAreaDeSelected = "' + this._onImageAreaDeSelected.name + '"');
        if (this._onMouseDoubleClick != '')
            eventString.push(' onMouseDoubleClick = "' + this._onMouseDoubleClick.name + '"');
        if (this._onMouseRightClick != '')
            eventString.push(' onMouseRightClick = "' + this._onMouseRightClick.name + '"');
        if (this._onTopImageInTheViewChanged != '')
            eventString.push(' onTopImageInTheViewChanged = "' + this._onTopImageInTheViewChanged.name + '"');
        if (this._onGetFilePath != '')
            eventString.push(' onGetFilePath="' + this._onGetFilePath.name + '"');

        return eventString.join('');
    };

    SImageCapture.prototype._createControl = function() {

        var varICSContainer;

        var objString = "<div id ='" + this._strICSInnerContainerID + "' style='position: relative;width:" + this._iWidth + "px; height:" + this._iHeight + "px;'>";

        // For IE, render the ActiveX Object
        if (Dynamsoft.Env._bInIE) {

            ///////////////////////////////////////
            objString += "<object classid='clsid:" + D.ICSProduct._strPROCLASSID + "' style='display:none;'><param name='LPKPath' value='" + D.ICSProduct._strLPKPath + "'/></object>";
            ///////////////////////////////////////

            objString += "<object id='" + this._strObjectName + "' style='width:" + this._iWidth + "px;height:" + this._iHeight + "px'";

            if (Dynamsoft.Env._bInWindowsX64)
                objString += "codebase='" + D.ICSProduct._strCABX64Path + "#version=" + D.ICSProduct._strVersionCode + "' ";
            else
                objString += "codebase='" + D.ICSProduct._strCABX86Path + "#version=" + D.ICSProduct._strVersionCode + "' ";


            var temp = D.ICSProduct._bIsTrial ? D.ICSProduct._strTRAILCLASSID : D.ICSProduct._strFULLCLASSID;
            objString += " classid='clsid:" + temp + "' viewastext>";
            objString += " <param name='Manufacturer' value='DynamSoft Corporation' />";
            objString += " <param name='ProductFamily' value='" + D.ICSProduct._strProductName + "' />";
            objString += " <param name='ProductName' value='" + D.ICSProduct._strProductName + "' />";
            //objString += " <param name='wmode' value='transparent'/>  ";
            objString += " </object>";
        }
        // For non-IE, render the embed object
        else {
            objString += "<embed id='" + this._strObjectName + "' style='display: inline; width:" + this._iWidth + "px;height:" + this._iHeight + "px' type='" + D.ICSProduct._strMIMETYPE + "'";

            objString += this._firefoxEvents();

            if (Dynamsoft.Env._bInWindows)
                objString += " pluginspage='" + D.ICSProduct._strMSIPath + "'></embed>";
            else
                objString += " pluginspage='" + D.ICSProduct._strPKGPath + "'></embed>";
        }
        objString += "</div><div id='" + this._strICSNonInstallInnerContainerID + "' style='width: " + this._iWidth + "px;'></div>";

        varICSContainer = document.getElementById(this._strICSControlContainerID);

        varICSContainer.innerHTML = objString;
        this._objectImageCapture = document.getElementById(this._strObjectName);
    };

    // Check if the control is fully loaded.
    SImageCapture.prototype._controlDetect = function() {
        var cImageCapture = Dynamsoft.Env._aryAllSImageCapture[0];

        var aryICSs = Dynamsoft.Env._aryAllSImageCapture;
        // If the ErrorCode is 0, it means everything is fine for the control. It is fully loaded.
        if (cImageCapture._objectImageCapture.ErrorCode == 0) {
            clearInterval(Dynamsoft.Env._varSeed);

            // Please put product key (since v9.0)
            for (var i = 0; i < aryICSs.length; i++) {
                var o = aryICSs[i];
                o._objectImageCapture.ProductKey = D.ICSProduct._strProductKey;
                o._attachEvents();
            }

        }
        else {
            if (!Dynamsoft.Env._bInIE) {
                navigator.plugins.refresh(false);
                if (Dynamsoft.Env._iPluginLength != navigator.plugins.length) {
                    for (var i = 0; i < navigator.mimeTypes.length; i++) {
                        if (navigator.mimeTypes[i].type.toLowerCase().indexOf(D.ICSProduct._strMIMETYPE.toLowerCase()) > -1) {
                            location.reload();
                        }
                    }
                }
            }

            for (var i = 0; i < aryICSs.length; i++) {
                var o = aryICSs[i];
                o._noControl();
            }
        }
    };

    SImageCapture.prototype._noControl = function() {
        // Display the message and hide the main control
        if (!Dynamsoft.Env._bInIE) {
            this._createNonInstallDivPlugin();
            document.getElementById(this._strICSNonInstallInnerContainerID).style.display = 'inline';
            document.getElementById(this._strICSInnerContainerID).style.display = 'none';
        }
    };

    SImageCapture.prototype._createNonInstallDivPlugin = function() {
        var o = document.getElementById(this._strICSNonInstallInnerContainerID);
        if (o.innerHTML != '')
            return;

        var strHref = '';
        if (Dynamsoft.Env._bInIE) {
            var strObjString = ["<div style='display: block; border:solid black 1px; text-align:center; width:",
				this._iWidth,
				'px;height:',
				this._iHeight,
				"px'>",
				"<ul style='padding-top:100px;'>",
				"<li>The Component is not installed</li>",
				"<li>You need to download and install the ActiveX to use this sample.</li>",
				"<li>Please follow the instructions in the information bar.</li>",
				"</ul></div>"].join('');
        }
        else {

            var _http = ('https:' == location.protocol ? 'https://' : 'http://');

            if (Dynamsoft.Env._bInWindows) {
                if (location.hostname != '')
                    strHref = _http + location.host + location.pathname.substring(0, location.pathname.lastIndexOf('/')) + '/' + D.ICSProduct._strMSIPath;
                else
                    strHref = D.ICSProduct._strMSIPath;
            }
            else {
                if (location.hostname != '')
                    strHref = _http + location.host + location.pathname.substring(0, location.pathname.lastIndexOf('/')) + '/' + D.ICSProduct._strPKGPath;
                else
                    strHref = D.ICSProduct._strPKGPath;
            }

            var strObjString = ["<div style='display: block; border:solid black 1px; text-align:center; width:",
				this._iWidth,
				"px;height:",
				this._iHeight,
				"px'>",
				"<ul style='padding-top:100px;'>",
				"<li>The Component is not installed</li>",
				"<li>You need to download and install the plug-in to use this sample.</li>",
				"<li>Please click the below link to download it.</li>",
				"<li>After the installation, please RESTART your browser.</li>",
				"<li><a href='",
				strHref,
				"'>Download</a></li>",
				"</ul></div>"].join('');
        }

        o.innerHTML = strObjString;
    };

    function _initProduct(configs) {

        if (configs.productKey)
            Dynamsoft.ICSProduct._strProductKey = configs.productKey;
        if (configs.isTrial)
            Dynamsoft.ICSProduct._bIsTrial = configs.isTrial == 'true' ? true : false;
        if (configs.version)
            Dynamsoft.ICSProduct._strVersionCode = configs.version;

        if (configs.resourcesPath) {
            var _path = configs.resourcesPath;
            if (_path.length > 0) {
                if (_path[_path.length - 1] == "/")
                    _path = _path.substring(0, _path.length - 1);
            }
            Dynamsoft.ICSProduct._strLPKPath = _path + '/ImageCaptureSuite.lpk';     // The relative path of the LPK file. User can change it.
            Dynamsoft.ICSProduct._strPKGPath = _path + '/ImageCaptureSuiteMacEdition.pkg';     //The relative path of the PKG file. User need to change it.
            Dynamsoft.ICSProduct._strMSIPath = _path + '/ImageCaptureSuitePlugIn.msi';         //The relative path of the MSI file. User need to change it.
            Dynamsoft.ICSProduct._strCABX86Path = _path + '/ImageCaptureSuite.cab';         //The relative path of the x86 cab file. User need to change it.
            Dynamsoft.ICSProduct._strCABX64Path = _path + '/ImageCaptureSuitex64.cab';      //The relative path of the x64 cab file. User need to change
        }


    };

    D.ImageCapture = function(objectConfigs) {

        var isFirstControl = Dynamsoft.Env._bFirstSImageCapture;

        if (isFirstControl) {
            Dynamsoft.Env._bFirstSImageCapture = false;
            _initProduct(objectConfigs);
        }

        var o = new SImageCapture();
        o._init(objectConfigs);
        o._createControl(); //Create an instance of the component in the DIV assigned by this._strICSContainerID


        Dynamsoft.Env._aryAllSImageCapture.push(o);

        // detect the first instance
        if (isFirstControl) {
            //Set interval to check if the control is fully loaded.
            Dynamsoft.Env._varSeed = setInterval(Dynamsoft.Env._aryAllSImageCapture[0]._controlDetect, 500);
        }

        return o;
    };

})(Dynamsoft);
