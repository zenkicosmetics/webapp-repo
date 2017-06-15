//--------------------------------------------------------------------------------------
//************************** Import Image*****************************
//--------------------------------------------------------------------------------------
/*-----------------select source---------------------*/
function source_onchange() {
    var iSelectedIndex = document.getElementById(_divICSSourceContainerID).selectedIndex;
    if (iSelectedIndex < 0) {
        DWObject.CloseSource();
        document.getElementById("MediaType").length = 0;
        document.getElementById("ResolutionWebcam").length = 0;
        return;
    }
    
    /**
    var iTwainType = DWObject.GetSourceType(iSelectedIndex);
    if (iTwainType == 0) {
    	
        document.getElementById("divTwainType").style.display = "";
        document.getElementById("btnScan").value = "Scan";
        document.getElementById("divWebcamType").style.display = "none";
    }
    else {
        document.getElementById("divTwainType").style.display = "none";
        document.getElementById("divWebcamType").style.display = "";
        document.getElementById("btnScan").value = "Grab";

        DWObject.SelectSourceByIndex(document.getElementById(_divICSSourceContainerID).selectedIndex);

        DWObject.OpenSource();
        var MediaType = document.getElementById("MediaType");
        MediaType.options.length = 0;
        var count = DWObject.MediaTypeListCount;
        var i;
        var value;
        for (i = 0; i < count; i++) {
            value = DWObject.GetMediaTypeByIndex(i);
            MediaType.options.add(new Option(value, value));
        }

        var ResolutionWebcam = document.getElementById("ResolutionWebcam");
        ResolutionWebcam.options.length = 0;
        count = DWObject.ResolutionForCamListCount;
        for (i = 0; i < count; i++) {
            value = DWObject.GetResolutionForCamByIndex(i);
            ResolutionWebcam.options.add(new Option(value, value));
        }
    }
    */
}


/*-----------------Acquire Image---------------------*/
function acquireImage() {
    if (_divICSSourceContainerID == "")
        DWObject.SelectSource();
    else
        DWObject.SelectSourceByIndex(document.getElementById(_divICSSourceContainerID).selectedIndex);
    DWObject.CloseSource();
    DWObject.OpenSource();
    var iSelectedIndex = document.getElementById(_divICSSourceContainerID).selectedIndex;
    var iTwainType = DWObject.GetSourceType(iSelectedIndex);

    if (iTwainType == 0) {
        DWObject.IfShowUI = document.getElementById("ShowUI").checked;

        var i;
        for (i = 0; i < 3; i++) {
            if (document.getElementsByName("PixelType").item(i).checked == true)
                DWObject.PixelType = i;
        }
        DWObject.Resolution = document.getElementById("Resolution").value;
        DWObject.IfFeederEnabled = document.getElementById("ADF").checked;
        DWObject.IfDuplexEnabled = document.getElementById("Duplex").checked;
        appendMessage("Pixel Type: " + DWObject.PixelType + "<br />Resolution: " + DWObject.Resolution + "<br />");
    }
    else {
        DWObject.IfShowUI = document.getElementById("ShowUIForWebcam").checked;

        if (_bInWindows) {
            DWObject.SelectMediaTypeByIndex(document.getElementById("MediaType").selectedIndex);
            DWObject.SelectResolutionForCamByIndex(document.getElementById("ResolutionWebcam").selectedIndex);

            appendMessage("MediaType: " + DWObject.MediaType + "<br />Resolution: " + DWObject.ResolutionForCam + "<br />");
        }
    }
    DWObject.IfDisableSourceAfterAcquire = true;
    DWObject.AcquireImage();
}

/*-----------------Load Image---------------------*/
function btnLoad_onclick() {
    DWObject.IfShowFileDialog = true;
    DWObject.LoadImageEx("", 5);
    if (checkErrorString()) {
        appendMessage("Loaded an image successfully.<br/>");
    }
}
function loadSampleImage(nIndex) {
    var ImgArr;

    switch (nIndex) {
        case 1:
            ImgArr = "/Images/twain_associate1.png";
            break;
        case 2:
            ImgArr = "/Images/twain_associate2.png";
            break;
        case 3:
            ImgArr = "/Images/twain_associate3.png";
            break;
    }

    if (location.hostname != "") {
        DWObject.HTTPPort = location.port == "" ? 80 : location.port;
        DWObject.HTTPDownload(location.hostname, location.pathname.substring(0, location.pathname.lastIndexOf('/')) + ImgArr);
    }
    else {
        DWObject.IfShowFileDialog = false;
        if (location.pathname.lastIndexOf('\\') > 1) {
            var ImgArr_replaced = ImgArr.replace(new RegExp("/", 'g'), "\\\\");
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('\\')).replace(/%20/g, " ") + ImgArr_replaced);
        }
        else
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('/')).replace(/%20/g, " ") + ImgArr);
    }

    updatePageInfo();
    if (checkErrorString()) {
        appendMessage("Added a demo image successfully.<br/>");
    }
}

//--------------------------------------------------------------------------------------
//************************** Edit Image ******************************

//--------------------------------------------------------------------------------------
function btnShowImageEditor_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.ShowImageEditor();
    _strTempStr = _strTempStr + "<b>Show Image Editor: </b>";
    if (checkErrorString()) {
        return;
    }
}

function btnRotateRight_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RotateRight(DWObject.CurrentImageIndexInBuffer);
    _strTempStr = _strTempStr + "<b>Rotate right: </b>";
    if (checkErrorString()) {
        return;
    }
}
function btnRotateLeft_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RotateLeft(DWObject.CurrentImageIndexInBuffer);
    _strTempStr = _strTempStr + "<b>Rotate left: </b>";
    if (checkErrorString()) {
        return;
    }
}

function btnMirror_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.Mirror(DWObject.CurrentImageIndexInBuffer);
    _strTempStr = _strTempStr + "<b>Mirror: </b>";
    if (checkErrorString()) {
        return;
    }
}
function btnFlip_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.Flip(DWObject.CurrentImageIndexInBuffer);
    _strTempStr = _strTempStr + "<b>Flip: </b>";
    if (checkErrorString()) {
        return;
    }
}

/*----------------------Crop Method---------------------*/
function btnCrop_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    if (_iLeft != 0 || _iTop != 0 || _iRight != 0 || _iBottom != 0) {
        DWObject.Crop(
            DWObject.CurrentImageIndexInBuffer,
            _iLeft, _iTop, _iRight, _iBottom
        );
        _iLeft = 0;
        _iTop = 0;
        _iRight = 0;
        _iBottom = 0;
        _strTempStr = _strTempStr + "<b>Crop: </b>";
        if (checkErrorString()) {
            return;
        }
        return;
    }
    switch (document.getElementById("Crop").style.visibility) {
        case "visible": document.getElementById("Crop").style.visibility = "hidden"; break;
        case "hidden": document.getElementById("Crop").style.visibility = "visible"; break;
        default: break;
    }
    document.getElementById("Crop").style.top = ds_gettop(document.getElementById("btnCrop")) + document.getElementById("btnCrop").offsetHeight + "px";
    document.getElementById("Crop").style.left = ds_getleft(document.getElementById("btnCrop")) - 80 + "px";
}

function btnCropCancel_onclick() {
    document.getElementById("Crop").style.visibility = "hidden";
}
function btnCropOK_onclick() {
    document.getElementById("img_left").className = "";
    document.getElementById("img_top").className = "";
    document.getElementById("img_right").className = "";
    document.getElementById("img_bottom").className = "";
    if (!re.test(document.getElementById("img_left").value)) {
        document.getElementById("img_left").className += " invalid";
        document.getElementById("img_left").focus();
        appendMessage("Please input a valid <b>left</b> value.<br />");
        return;
    }
    if (!re.test(document.getElementById("img_top").value)) {
        document.getElementById("img_top").className += " invalid";
        document.getElementById("img_top").focus();
        appendMessage("Please input a valid <b>top</top> value.<br />");
        return;
    }
    if (!re.test(document.getElementById("img_right").value)) {
        document.getElementById("img_right").className += " invalid";
        document.getElementById("img_right").focus();
        appendMessage("Please input a valid <b>right</b> value.<br />");
        return;
    }
    if (!re.test(document.getElementById("img_bottom").value)) {
        document.getElementById("img_bottom").className += " invalid";
        document.getElementById("img_bottom").focus();
        appendMessage("Please input a valid <b>bottom</b> value.<br />");
        return;
    }
    DWObject.Crop(
        DWObject.CurrentImageIndexInBuffer,
        document.getElementById("img_left").value,
        document.getElementById("img_top").value,
        document.getElementById("img_right").value,
        document.getElementById("img_bottom").value
    );
    _strTempStr = _strTempStr + "<b>Crop: </b>";
    if (checkErrorString()) {
        document.getElementById("Crop").style.visibility = "hidden";
        return;
    }
}

/*----------------Change Image Size--------------------*/
function btnChangeImageSize_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    switch (document.getElementById("ImgSizeEditor").style.visibility) {
        case "visible": document.getElementById("ImgSizeEditor").style.visibility = "hidden"; break;
        case "hidden": document.getElementById("ImgSizeEditor").style.visibility = "visible"; break;
        default: break;
    }
    document.getElementById("ImgSizeEditor").style.top = ds_gettop(document.getElementById("btnChangeImageSize")) + document.getElementById("btnChangeImageSize").offsetHeight + "px";
    document.getElementById("ImgSizeEditor").style.left = ds_getleft(document.getElementById("btnChangeImageSize")) - 30 + "px";
}
function btnCancelChange_onclick() {
    document.getElementById("ImgSizeEditor").style.visibility = "hidden";
}

function btnChangeImageSizeOK_onclick() {
    document.getElementById("img_height").className = "";
    document.getElementById("img_width").className = "";
    if (!re.test(document.getElementById("img_height").value)) {
        document.getElementById("img_height").className += " invalid";
        document.getElementById("img_height").focus();
        appendMessage("Please input a valid <b>height</b>.<br />");
        return;
    }
    if (!re.test(document.getElementById("img_width").value)) {
        document.getElementById("img_width").className += " invalid";
        document.getElementById("img_width").focus();
        appendMessage("Please input a valid <b>width</b>.<br />");
        return;
    }
    DWObject.ChangeImageSize(
        DWObject.CurrentImageIndexInBuffer,
        document.getElementById("img_width").value,
        document.getElementById("img_height").value,
        document.getElementById("InterpolationMethod").selectedIndex + 1
    );
    _strTempStr = _strTempStr + "<b>Change Image Size: </b>";
    if (checkErrorString()) {
        document.getElementById("ImgSizeEditor").style.visibility = "hidden";
        return;
    }
}
//--------------------------------------------------------------------------------------
//************************** Save Image***********************************
//--------------------------------------------------------------------------------------
function btnSave_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    var i, strimgType_save;
    var NM_imgType_save = document.getElementsByName("imgType_save");
    for (i = 0; i < 5; i++) {
        if (NM_imgType_save.item(i).checked == true) {
            strimgType_save = NM_imgType_save.item(i).value;
            break;
        }
    }
    DWObject.IfShowFileDialog = true;
    _txtFileNameforSave.className = "";
    var bSave = false;
    if (!strre.test(_txtFileNameforSave.value)) {
        _txtFileNameforSave.className += " invalid";
        _txtFileNameforSave.focus();
        appendMessage("Please input <b>file name</b>.<br />Currently only English names are allowed.<br />");
        return;
    }
    var strFilePath = "C:\\" + _txtFileNameforSave.value + "." + strimgType_save;
    if (strimgType_save == "tif" && _chkMultiPageTIFF_save.checked) {
        if ((DWObject.SelectedImagesCount == 1) || (DWObject.SelectedImagesCount == DWObject.HowManyImagesInBuffer)) {
            bSave = DWObject.SaveAllAsMultiPageTIFF(strFilePath);
        }
        else {
            bSave = DWObject.SaveSelectedImagesAsMultiPageTIFF(strFilePath);
        }
    }
    else if (strimgType_save == "pdf" && document.getElementById("MultiPagePDF_save").checked) {
        if ((DWObject.SelectedImagesCount == 1) || (DWObject.SelectedImagesCount == DWObject.HowManyImagesInBuffer)) {
            bSave = DWObject.SaveAllAsPDF(strFilePath);
        }
        else {
            bSave = DWObject.SaveSelectedImagesAsMultiPagePDF(strFilePath);
        }
    }
    else {
        switch (i) {
            case 0: bSave = DWObject.SaveAsBMP(strFilePath, DWObject.CurrentImageIndexInBuffer); break;
            case 1: bSave = DWObject.SaveAsJPEG(strFilePath, DWObject.CurrentImageIndexInBuffer); break;
            case 2: bSave = DWObject.SaveAsTIFF(strFilePath, DWObject.CurrentImageIndexInBuffer); break;
            case 3: bSave = DWObject.SaveAsPNG(strFilePath, DWObject.CurrentImageIndexInBuffer); break;
            case 4: bSave = DWObject.SaveAsPDF(strFilePath, DWObject.CurrentImageIndexInBuffer); break;
        }
    }

    if (bSave)
        _strTempStr = _strTempStr + "<b>Save Image: </b>";
    if (checkErrorString()) {
        return;
    }
}
//--------------------------------------------------------------------------------------
//************************** Upload Image***********************************
// DungNT Customize this method to upload more data
//--------------------------------------------------------------------------------------
function GetOCRProInfoInner(result) {  
	if (result == null)
		return null;
		
	var pageCount = result.GetPageCount();
	if (pageCount == 0) {
		alert("OCR result is Null.");
		return;
	} else {

		var bRet = "";
		for (var i = 0; i < pageCount; i++) {
			var page = result.GetPageContent(i);
			var letterCount = page.GetLettersCount();
			for (var n = 0; n < letterCount; n++) {
				var letter = page.GetLetterContent(n);
				bRet += letter.GetText();

			}
		}
	   //console.log(bRet);  //Get OCR result.
	}
        
        var octTempTextFile = OCRTessDataPath + "OCRDirectly.txt";

	if(octTempTextFile.length > 1)
		result.Save(octTempTextFile);
}

function GetErrorInfo(errorcode, errorstring, result) { //This is the function called when OCR fails
	alert(errorstring);
	var strErrorDetail = "";
	var aryErrorDetailList = result.GetErrorDetailList();
	for (var i = 0; i < aryErrorDetailList.length; i++) {
		if (i > 0)
			strErrorDetail += ";";
		strErrorDetail += aryErrorDetailList[i].GetMessage();
	}
	alert(strErrorDetail);
}

function OnOCRSelectedImagesSuccess(result) {
	return GetOCRProInfoInner(result);
}


function btnUpload_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.IfShowFileDialog = false;
    var i, strHTTPServer, strActionPage, strImageType, numberPages;
    
    // If scan document, will convert before upload
    // numberPages = 0;
    var octTempFile = OCRTessDataPath + "OCRDirectly.pdf";
    var octTempTextFile = OCRTessDataPath + "OCRDirectly.txt";
    // var UseOCRFlag = document.getElementById('scanForm_UseOCRFlag').value;
    var UseOCRFlag = 0;
    var numberPages = DWObject.HowManyImagesInBuffer;
    
    //Save all image as PDF to upload late    
    DWObject.SaveAllAsPDF(octTempFile);
    // If client support OCR, create OCR for this PDF
//    if (UseOCRFlag == '1') {
//		var tempFile = "";
//		for (i=0; i < numberPages; i++) {
//			DWObject.SaveAsPNG(OCRTessDataPath + "ImageData" + i + ".png",i);
//			tempFile += "|" + OCRTessDataPath + "ImageData" + i + ".png";
//		}
//		tempFile = tempFile.substring(1);
//                DWObject.OCRTessDataPath = OCRTessDataPath;
//                DWObject.OCRLanguage = "eng";
//		DWObject.OCRResultFormat = 2; //Image over text PDF
//                
//		var convertResult = DWObject.OCRDirectly(tempFile, octTempFile);
//		if (!convertResult) {
//			return;
//		}
//		
//		DWObject.OCRResultFormat = 0; // Text format only (using for search document)
//		DWObject.OCRDirectly(tempFile, octTempTextFile);
                
        //Create OCR for PDF file           
//        DWObject.SelectedImagesCount = numberPages;
//        for(var i = 0; i < numberPages;i++) {
//                 DWObject.SetSelectedImageIndex(i,i);
//        }
//        DWObject.Addon.OCRPro.RecognizeSelectedImages(OnOCRSelectedImagesSuccess, GetErrorInfo);
//    } 
	// For this case client does not support OCR
//	else {
//		DWObject.SaveAllAsPDF(octTempFile);
//	}
    
    //DWObject.MaxInternetTransferThreads = 5;
    strHTTPServer = _strServerName;
    strActionPage = _strActionPage;
    DWObject.HTTPPort = _strPort;
    
    // Get envelope id and token key
    DWObject.ClearAllHTTPFormField();
    DWObject.SetHTTPFormField("envelope_id", envelope_id);
    DWObject.SetHTTPFormField("customer_token_key", token_key);
    DWObject.SetHTTPFormField("scan_type", scan_type);
    DWObject.SetHTTPFormField("number_page", numberPages);

    strActionPage = _strActionPage;
    strImageType = 3;
    for(i=0; i<4; i++){
        if(document.getElementsByName("ImageType").item(i).checked == true){
            strImageType  = i + 1;
            break;
        }
    }
    
    var uploadfilename = '';
    console.log(uploadfilename);
    // Upload file (scan image)
    uploadfilename = envelope_id + ".pdf";
    DWObject.HTTPPort = 443; 
    DWObject.IfSSL = true; // if 443 is the port number of secure port
    console.log('ServerURL:' + strHTTPServer + strActionPage);
    DWObject.HTTPUploadThroughPostDirectly(strHTTPServer, octTempFile, strActionPage, uploadfilename);

    // Using OCR Flag
    if (UseOCRFlag == '1') {
        if (scan_type == '2') {
            uploadtextfilename = envelope_id + ".txt";
            DWObject.HTTPUploadThroughPostDirectly(strHTTPServer, octTempTextFile, strActionPage, uploadtextfilename);
        }
    }
    // Item scan image
    /**
    else if (scan_type == '1') {
    	uploadfilename = envelope_id + ".png";
        DWObject.HTTPUploadThroughPostEx(
            strHTTPServer,
            DWObject.CurrentImageIndexInBuffer,
            strActionPage,
            uploadfilename,
            strImageType
        );
    }
    */
    
}



function btnUploadToFTP_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }

    _txtFileName.className = "";

    var strFTPServer = "192.168.1.20";
    DWObject.FTPPort = 21;
    DWObject.FTPUserName = "ICS";
    DWObject.FTPPassword = "ICS";
    var remoteDirectory = "/images/";

    for (i = 0; i < 4; i++) {
        if (document.getElementsByName("ImageType").item(i).checked == true) {
            strImageType = i + 1;
            break;
        }
    }
    var uploadfilename = _txtFileName.value + "." + document.getElementsByName("ImageType").item(i).value;
    var uploadFullPath = remoteDirectory + uploadfilename;

    if (strImageType == 2 && _chkMultiPageTIFF.checked) {
        if ((DWObject.SelectedImagesCount == 1) || (DWObject.SelectedImagesCount == DWObject.HowManyImagesInBuffer)) {
            DWObject.FTPUploadAllAsMultiPageTIFF(strFTPServer, uploadFullPath);
        }
        else {
            DWObject.FTPUploadAsMultiPageTIFF(strFTPServer, uploadFullPath);
        }
    }
    else if (strImageType == 4 && MultiPagePDF.checked) {
        if ((DWObject.SelectedImagesCount == 1) || (DWObject.SelectedImagesCount == DWObject.HowManyImagesInBuffer)) {
            DWObject.FTPUploadAllAsPDF(strFTPServer, uploadFullPath);
        }
        else {
            DWObject.FTPUploadAsMultiPagePDF(strFTPServer, uploadFullPath);
        }
    }
    else {
        DWObject.FTPUploadEx(
            strFTPServer,
            DWObject.CurrentImageIndexInBuffer,
            uploadFullPath,
            strImageType
        );
    }
    _strTempStr = _strTempStr + "<b>Upload: </b>";
    checkErrorString();
}


//--------------------------------------------------------------------------------------
//************************** Navigator functions***********************************
//--------------------------------------------------------------------------------------

function btnFirstImage_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = 0;
    updatePageInfo();
}

function btnPreImage_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    else if (DWObject.CurrentImageIndexInBuffer == 0) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = DWObject.CurrentImageIndexInBuffer - 1;
    updatePageInfo();
}
function btnNextImage_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    else if (DWObject.CurrentImageIndexInBuffer == DWObject.HowManyImagesInBuffer - 1) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = DWObject.CurrentImageIndexInBuffer + 1;
    updatePageInfo();
}


function btnLastImage_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.CurrentImageIndexInBuffer = DWObject.HowManyImagesInBuffer - 1;
    updatePageInfo();
}

function btnRemoveCurrentImage_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RemoveAllSelectedImages();
    if (DWObject.HowManyImagesInBuffer == 0) {
        //document.getElementById("DW_TotalImage").value = DWObject.HowManyImagesInBuffer;
        //document.getElementById("DW_CurrentImage").value = "";
        return;
    }
    else {
        updatePageInfo();
    }
}


function btnRemoveAllImages_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    DWObject.RemoveAllImages();
    document.getElementById("DW_TotalImage").value = "0";
    document.getElementById("DW_CurrentImage").value = "";
}
function setlPreviewMode() {
    DWObject.SetViewMode(parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1), parseInt(document.getElementById("DW_PreviewMode").selectedIndex + 1));
    if (!_bInWindows) {
        return;
    }
    else if (document.getElementById("DW_PreviewMode").selectedIndex != 0) {
        DWObject.MouseShape = true;
    }
    else {
        DWObject.MouseShape = false;
    }
}

//--------------------------------------------------------------------------------------
//*********************************radio response***************************************
//--------------------------------------------------------------------------------------
function rdTIFFsave_onclick() {
    _chkMultiPageTIFF_save.disabled = false;

    _chkMultiPageTIFF_save.checked = false;
    _chkMultiPagePDF_save.checked = false;
    _chkMultiPagePDF_save.disabled = true;
}
function rdPDFsave_onclick() {
    _chkMultiPagePDF_save.disabled = false;

    _chkMultiPageTIFF_save.checked = false;
    _chkMultiPagePDF_save.checked = false;
    _chkMultiPageTIFF_save.disabled = true;
}
function rdsave_onclick() {
    _chkMultiPageTIFF_save.checked = false;
    _chkMultiPagePDF_save.checked = false;

    _chkMultiPageTIFF_save.disabled = true;
    _chkMultiPagePDF_save.disabled = true;
}
function rdTIFF_onclick() {
    _chkMultiPageTIFF.disabled = false;

    _chkMultiPageTIFF.checked = false;
    _chkMultiPagePDF.checked = false;
    _chkMultiPagePDF.disabled = true;
}
function rdPDF_onclick() {
    _chkMultiPagePDF.disabled = false;

    _chkMultiPageTIFF.checked = false;
    _chkMultiPagePDF.checked = false;
    _chkMultiPageTIFF.disabled = true;
}
function rd_onclick() {
    _chkMultiPageTIFF.checked = false;
    _chkMultiPagePDF.checked = false;

    _chkMultiPageTIFF.disabled = true;
    _chkMultiPagePDF.disabled = true;
}



//--------------------------------------------------------------------------------------
//************************** Dynamic Web TWAIN Events***********************************
//--------------------------------------------------------------------------------------

function Dynamsoft_OnPostTransfer() {
    if (_bDiscardBlankImage) {
        var NewlyScannedImage = DWObject.CurrentImageIndexInBuffer;
        if (DWObject.IsBlankImage(NewlyScannedImage)) {
            DWObject.RemoveImage(NewlyScannedImage);
        }
        _strTempStr += "<b>Blank Discard (On PostTransfer): </b>";

        if (checkErrorString()) {
            updatePageInfo();
        }
    }
    updatePageInfo();
}

function Dynamsoft_OnPostLoadfunction(path, name, type) {
    if (_bDiscardBlankImage) {
        var NewlyScannedImage = DWObject.CurrentImageIndexInBuffer;
        if (DWObject.IsBlankImage(NewlyScannedImage)) {
            DWObject.RemoveImage(NewlyScannedImage);
        }
        _strTempStr += "<b>Blank Discard (On PostLoad): </b>";
        if (checkErrorString()) {
            updatePageInfo();
        }
    }
    updatePageInfo();
}

function Dynamsoft_OnPostAllTransfers() {
    DWObject.CloseSource();
    updatePageInfo();
    checkErrorString();
}

function Dynamsoft_OnMouseClick(index) {
    updatePageInfo();
}

function Dynamsoft_OnMouseRightClick(index) {
    // To add
}


function Dynamsoft_OnImageAreaSelected(index, left, top, right, bottom) {
    _iLeft = left;
    _iTop = top;
    _iRight = right;
    _iBottom = bottom;
}

function Dynamsoft_OnImageAreaDeselected(index) {
    _iLeft = 0;
    _iTop = 0;
    _iRight = 0;
    _iBottom = 0;
}

function Dynamsoft_OnMouseDoubleClick() {
    return;
}


function Dynamsoft_OnTopImageInTheViewChanged(index) {
    DWObject.CurrentImageIndexInBuffer = index;
    updatePageInfo();
}

function Dynamsoft_OnGetFilePath(bSave, count, index, path, name) {
    fileName = path + "\\" + name;
}

//---------------------------- Barcode and OCR---------------------------------------
function LoadBarcodeDemoImage(nIndex) {
    var ImgArr;

    switch (nIndex) {
        case 1:
            ImgArr = "/Images/code-39.png";
            break;
        case 2:
            ImgArr = "/Images/code-128.png";
            break;
        case 3:
            ImgArr = "/Images/qrcode.png";
            break;
        case 4:
            ImgArr = "/Images/UPC-A.png";
            break;
    }

    if (location.hostname != "") {
        DWObject.HTTPPort = location.port == "" ? 80 : location.port;
        DWObject.HTTPDownload(location.hostname, location.pathname.substring(0, location.pathname.lastIndexOf('/')) + ImgArr);
    }
    else {
        DWObject.IfShowFileDialog = false;
        if (location.pathname.lastIndexOf('\\') > 1) {
            var ImgArr_replaced = ImgArr.replace(new RegExp("/", 'g'), "\\\\");
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('\\')).replace(/%20/g, " ") + ImgArr_replaced);
        }
        else
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('/')).replace(/%20/g, " ") + ImgArr);
    }

    updatePageInfo();
    if (DWObject.ErrorCode != 0) {
        //alert(DWObject.ErrorString);
        showErrorInMessageBox(DWObject.ErrorString);
    }
    appendMessage("Added a demo image successfully.<br/>");
}

function LoadOCRDemoImage(nIndex) {
    var ImgArr;

    switch (nIndex) {
        case 1:
            ImgArr = "/Images/Demo_OCR1.png";
            break;
        case 2:
            ImgArr = "/Images/Demo_OCR2.png";
            break;
        case 3:
            ImgArr = "/Images/Demo_OCR3.png";
            break;
        case 4:
            ImgArr = "/Images/Demo_OCR4.png";
            break;
    }

    // DWObject.LoadImageFromBytes(nLen, ImgArr, 1);
    if (location.hostname != "") {
        DWObject.HTTPPort = location.port == "" ? 80 : location.port;
        DWObject.HTTPDownload(location.hostname, location.pathname.substring(0, location.pathname.lastIndexOf('/')) + ImgArr);
    }
    else {
        DWObject.IfShowFileDialog = false;
        if (location.pathname.lastIndexOf('\\') > 1) {
            var ImgArr_replaced = ImgArr.replace(new RegExp("/", 'g'), "\\\\");
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('\\')).replace(/%20/g, " ") + ImgArr_replaced);
        }
        else
            DWObject.LoadImage(location.pathname.substring(1, location.pathname.lastIndexOf('/')).replace(/%20/g, " ") + ImgArr);
    }

    updatePageInfo();
    if (DWObject.ErrorCode != 0) {
        //alert(DWObject.ErrorString);
        showErrorInMessageBox(DWObject.ErrorString);
    }
    appendMessage("Added a demo image successfully.<br/>");
}

function btnScanReadBarcode_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }
    J_SetBtnProcessingAndText("btnReadBarcode", true, "Processing...");
    setTimeout(J_Barcoding, 100);
}
function J_Barcoding() {
    var barcodeVerStr = DWObject.BarcodeVersion;
    if (!barcodeVerStr || barcodeVerStr != _strBarcodeVersion) {
        if (location.hostname != "") {
            var strHostIP = location.hostname;
            DWObject.HTTPPort = location.port == "" ? 80 : location.port;
            var CurrentPathName = unescape(location.pathname); // get current PathName in plain ASCII	
            var CurrentPath = CurrentPathName.substring(0, CurrentPathName.lastIndexOf("/") + 1);
            var strBarcodepath = CurrentPath + "Resources/barcode.zip";
            DWObject.HTTPDownloadResource(strHostIP, strBarcodepath, "barcode.zip");
        }
    }

    var strLength = DWObject.GetImageSize(DWObject.CurrentImageIndexInBuffer, DWObject.GetImageWidth(DWObject.CurrentImageIndexInBuffer), DWObject.GetImageHeight(DWObject.CurrentImageIndexInBuffer));
    if (strLength > 300000)
        DWObject.IfShowProgressBar = true;
    else
        DWObject.IfShowProgressBar = false;

    DWObject.IfShowProgressBar = true;
    
    var barcodeformat;
    barcodeformat = document.getElementById("ddl_barcodeFormat").value;
    if (_iLeft != 0 || _iTop != 0 || _iRight != 0 || _iBottom != 0) {
        DWObject.ReadBarcodeEx(
                    DWObject.CurrentImageIndexInBuffer,
                    _iLeft, _iTop, _iRight, _iBottom, barcodeformat
                    );
    }
    else {
        DWObject.ReadBarcode(DWObject.CurrentImageIndexInBuffer, barcodeformat);
    }
    
    var barcodeText = "";
    barcodeText += "ReadBarcode : " + DWObject.ErrorString + "<br/>";

    var count = DWObject.BarcodeCount;
    barcodeText += "BarcodeCount: " + count + "<br/>";

    for (i = 0; i < count; i++) {
        var text = DWObject.GetBarcodeText(i);
        var x = DWObject.GetBarcodeInfo(0, i);
        var y = DWObject.GetBarcodeInfo(1, i);
        var type = DWObject.GetBarcodeInfo(2, i);
        var len = DWObject.GetBarcodeInfo(5, i);
        barcodeText += ("barcode[" + (i + 1) + "]: " + text + "<br/>");
        barcodeText += ("text len:" + len + "<br/>");
        barcodeText += ("type:" + getBarcodeType(type) + "<br/>");
        barcodeText += ("x: " + x + " y:" + y + "<br/>");

        var strBarcodeString = text + "\r\n" + getBarcodeType(type);
        DWObject.AddText(DWObject.CurrentImageIndexInBuffer, x, y, strBarcodeString, 255, 4894463, 0, 1);
    }
    appendMessage(barcodeText);


    //DWObject.  Barcoding Call XActive.
    J_SetBtnProcessingAndText("btnReadBarcode", false, "Try Barcode");
}

function btnOCRClient_onclick() {
    if (!checkIfImagesInBuffer()) {
        return;
    }

    J_SetBtnProcessingAndText("btnOCRClient", true, "Processing...");
    setTimeout(J_OCRing, 100);


}

var fileName;

function J_OCRing() {

    var OCRVerStr = DWObject.OCRVersion;
    if (!OCRVerStr || OCRVerStr != _strOCRVersion) {
        if (location.hostname != "") {
            var CurrentPathName = unescape(location.pathname); // get current PathName in plain ASCII	
            var CurrentPath = CurrentPathName.substring(0, CurrentPathName.lastIndexOf("/") + 1);
            var strOCRfilepath = CurrentPath + "Resources/OCR.zip";
            var strHostIP = location.hostname;
            DWObject.HTTPPort = location.port == "" ? 80 : location.port;
            DWObject.HTTPDownloadResource(strHostIP, strOCRfilepath, "OCR.zip");
        }
    }

    var aryLanguage = [
    "eng", "English",
    "ara", "Arabic",
    "bul", "Bulgarian",
    "cat", "Catalan",
    "ces", "Czech",
    "chi_sim", "Chinese (Simplified)",
    "chi_tra", "Chinese (Traditional)",
    "chr", "Cherokee",
    "dan-frak", "Danish (Fraktur)",
    "dan", "Danish",
    "nld", "Dutch",
    "deu-frak", "German (Fraktur)",
    "deu", "German",
    "ell", "Greek",
    "fin", "Finnish",
    "fra", "French",
    "heb-ras", "Hebrew",
    "heb-seg", "Hebrew",
    "heb", "Hebrew",
    "hin", "Hindi",
    "hun", "Hungarian",
    "ind", "Indonesian",
    "ita", "Italian",
    "jpn", "Japanese",
    "kor", "Korean",
    "lav", "Latvian",
    "lit", "Lithuanian",
    "nor", "Norwegian",
    "pol", "Polish",
    "por", "Portuguese",
    "ron", "Romanian",
    "rus", "Russian",
    "slk-frak", "Slovakian (Fraktur)",
    "slk", "Slovakian",
    "slv", "Slovenian",
    "spa", "Spanish",
    "srp", "Serbian (Latin)",
    "swe-frak", "Swedish (Fraktur)",
    "swe", "Swedish",
    "tgl", "Tagalog",
    "tha", "Thai",
    "tur", "Turkish",
    "ukr", "Ukrainian",
    "vie", "Vietnamese",
    ];
    var tmpObj = document.getElementById("ddl_language");
    var strlanguage = tmpObj.value;
    var zipName = "";
    for (var i = 0; i < aryLanguage.length; ++i) {
        if (aryLanguage[i] == strlanguage) {
            zipName = aryLanguage[i + 1];
        }
    }
    tmpObj = document.getElementById("ddl_fileType");
    var result;
    var fileType = tmpObj.value;
    if (fileType == 0)
        result = DWObject.ShowFileDialog(true, "*.TXT", 0, "TXT", "OCRResult.txt", true, true, 0);
    else if (fileType == 1)
        result = DWObject.ShowFileDialog(true, "*.PDF", 0, "PDF", "OCRPTResult.pdf", true, true, 0);
    else
        result = DWObject.ShowFileDialog(true, "*.PDF", 0, "PDF", "OCRIOTResult.pdf", true, true, 0);



    if (result == true) {
        var strFileName = fileName;
        //DWObject.  OCR Call XActive.
        DWObject.OCRLanguage = strlanguage;
        DWObject.OCRResultFormat = fileType;
        DWObject.HTTPPort = 80;
        DWObject.HTTPDownloadOCRLangData("www.dynamsoft.com", "/download/OCR Language/" + zipName + ".zip", true);
        DWObject.IfShowProgressBar = true;

        var bResutlOCR = false;
        if (_iLeft != 0 || _iTop != 0 || _iRight != 0 || _iBottom != 0) {
            if (!DWObject.OCR1(
                    DWObject.CurrentImageIndexInBuffer,
                    _iLeft, _iTop, _iRight, _iBottom, strFileName
                    )) {
                ShowErrorInMessageBox(DWObject.ErrorString);
            }
            else {
                bResutlOCR = true;
            }
        }
        else {
            if (!DWObject.OCRSelectedImages(strFileName)) {
                //alert(DWObject.ErrorString);
                ShowErrorInMessageBox(DWObject.ErrorString);
            }
            else
                bResutlOCR = true;
        }
        
        if (bResutlOCR == true) {
            appendMessage("OCR result file has been saved to " + strFileName + ".<br />");
        }
    }

    ////

    ////
    J_SetBtnProcessingAndText("btnOCRClient", false, "OCR");
}
function J_SetBtnProcessingAndText(btnID, isDisbaled, strbtText) {
    var objBtn = document.getElementById(btnID);
    objBtn.disabled = isDisbaled;
    objBtn.value = strbtText;
}

function getBarcodeType(type) {
    var strBarcodeString = type;
    switch (type) {
        case 1:
            strBarcodeString = "AZTEC";
            break;
        case 2:
            strBarcodeString = "CODABAR";
            break;
        case 4:
            strBarcodeString = "CODE_39";
            break;
        case 8:
            strBarcodeString = "CODE_93";
            break;
        case 16:
            strBarcodeString = "CODE_128";
            break;
        case 32:
            strBarcodeString = "DATAMATRIX";
            break;
        case 64:
            strBarcodeString = "EAN_8";
            break;
        case 128:
            strBarcodeString = "EAN_13";
            break;
        case 256:
            strBarcodeString = "ITF";
            break;
        case 512:
            strBarcodeString = "MAXICODE";
            break;
        case 1024:
            strBarcodeString = "PDF417";
            break;
        case 2048:
            strBarcodeString = "QR_CODE";
            break;

        case 4096:
            strBarcodeString = "RSS_14";
            break;
        case 8192:
            strBarcodeString = "RSS_EXPANDED";
            break;
        case 16384:
            strBarcodeString = "UPC_A";
            break;
        case 32768:
            strBarcodeString = "UPC_E";
            break;
        case 65536:
            strBarcodeString = "UPC_EAN_EXTENSION";
            break;
    }

    return strBarcodeString;
}


function showErrorInMessageBox(strErrorString) {
    var emTemp = "<span style='color:#cE5E04'><b>" + strErrorString + "</b></span><br />";
   appendMessage(emTemp);
}
