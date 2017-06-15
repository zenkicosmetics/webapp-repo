
<!DOCTYPE html>
<html dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

<?php Asset::css("pdf.js/viewer.css"); ?>
<!--#if FIREFOX || MOZCENTRAL-->
<!--#include viewer-snippet-firefox-extension.html-->
<!--#endif-->

<link href="" rel="stylesheet" type="text/css" />
<!--#if !(FIREFOX || MOZCENTRAL || CHROME)-->
<?php Asset::js("pdf.js/compatibility.js"); ?>
<!--#endif-->
<?php Asset::js("pdf.js/l10n.js"); ?>
<?php Asset::js("pdf.js/core.js"); ?>
<?php Asset::js("pdf.js/util.js"); ?>
<?php Asset::js("pdf.js/api.js"); ?>
<?php Asset::js("pdf.js/metadata.js"); ?>
<?php Asset::js("pdf.js/canvas.js"); ?>
<?php Asset::js("pdf.js/obj.js"); ?>
<?php Asset::js("pdf.js/function.js"); ?>
<?php Asset::js("pdf.js/charsets.js"); ?>
<?php Asset::js("pdf.js/cidmaps.js"); ?>
<?php Asset::js("pdf.js/colorspace.js"); ?>
<?php Asset::js("pdf.js/crypto.js"); ?>
<?php Asset::js("pdf.js/evaluator.js"); ?>
<?php Asset::js("pdf.js/fonts.js"); ?>
<?php Asset::js("pdf.js/glyphlist.js"); ?>
<?php Asset::js("pdf.js/image.js"); ?>
<?php Asset::js("pdf.js/metrics.js"); ?>
<?php Asset::js("pdf.js/parser.js"); ?>
<?php Asset::js("pdf.js/pattern.js"); ?>
<?php Asset::js("pdf.js/stream.js"); ?>
<?php Asset::js("pdf.js/worker.js"); ?>
<?php Asset::js("pdf.js/jpg.js"); ?>
<?php Asset::js("pdf.js/jpx.js"); ?>
<?php Asset::js("pdf.js/jbig2.js"); ?>
<?php Asset::js("pdf.js/bidi.js"); ?>
<?php echo Asset::render(); ?>
<?php 
$frontend_theme = Settings::get(APConstants::FRONTEND_THEMES_CODE);
$web_path = APPPATH . 'themes/' . $frontend_theme . '/';
?>
<script type="text/javascript">
    //Now we assign the path of the file on server which we stored in our ViewBag to the THEFILEPATH variable
    //which is on line 2 of viewer.js, alternatively we could have returned a querystring with a file param
    //which would point to the file, e.g: "Viewer?file=/MyPDFs/Pdf1.pdf"
    var THEFILEPATH = '<?php echo $pdf_file_url?>';
    var THEFILESCALE = 1.0;
    var mode = 'full';
    // instantiate the PDF.JS web worker.
    var url = '<?php echo APContext::getAssetPath().$web_path?>js/pdf.js/worker_loader.js';
    PDFJS.workerSrc = url;
</script>
<!--#if GENERIC || CHROME-->
<!--#include viewer-snippet.html-->
<!--#endif-->
<script src="<?php echo APContext::getAssetPath().$web_path."js/pdf.js/viewer.js?r=".uniqid() ?>" type="text/javascript"></script>
<script src="<?php echo APContext::getAssetPath().$web_path."js/pdf.js/debugger.js?r=".uniqid() ?>" type="text/javascript"></script>

  </head>  
 <body>
<div id="outerContainer">
	<div id="sidebarContainer">
		<div id="toolbarSidebar">
			<div class="splitToolbarButton toggled">
				<button id="viewThumbnail" class="toolbarButton group toggled"
					title="Show Thumbnails" tabindex="1" data-l10n-id="thumbs">
					<span data-l10n-id="thumbs_label">Thumbnails</span>
				</button>
				<button id="viewOutline" class="toolbarButton group"
					title="Show Document Outline" tabindex="2" data-l10n-id="outline">
					<span data-l10n-id="outline_label">Document Outline</span>
				</button>
			</div>
		</div>
		<div id="sidebarContent">
			<div id="thumbnailView"></div>
			<div id="outlineView" class="hidden"></div>
		</div>
	</div>
	<!-- sidebarContainer -->

	<div id="mainContainer">
		<div class="findbar hidden doorHanger" id="findbar">
			<label for="findInput" class="toolbarLabel" data-l10n-id="find_label">Find:</label>
			<input id="findInput" class="toolbarField" tabindex="20">
			<div class="splitToolbarButton">
				<button class="toolbarButton findPrevious" title=""
					id="findPrevious" tabindex="21" data-l10n-id="find_previous">
					<span data-l10n-id="find_previous_label">Previous</span>
				</button>
				<div class="splitToolbarButtonSeparator"></div>
				<button class="toolbarButton findNext" title="" id="findNext"
					tabindex="22" data-l10n-id="find_next">
					<span data-l10n-id="find_next_label">Next</span>
				</button>
			</div>
			<input type="checkbox" id="findHighlightAll" class="toolbarField"> <label
				for="findHighlightAll" class="toolbarLabel" tabindex="23"
				data-l10n-id="find_highlight">Highlight all</label> <input
				type="checkbox" id="findMatchCase" class="toolbarField"> <label
				for="findMatchCase" class="toolbarLabel" tabindex="24"
				data-l10n-id="find_match_case_label">Match case</label> <span
				id="findMsg" class="toolbarLabel"></span>
		</div>
		<div class="toolbar">
			<div id="toolbarContainer">
				<div id="toolbarViewer">
					<div id="toolbarViewerLeft">
						<button id="sidebarToggle" class="toolbarButton"
							title="Toggle Sidebar" tabindex="3" data-l10n-id="toggle_slider">
							<span data-l10n-id="toggle_slider_label">Toggle Sidebar</span>
						</button>
						<div class="toolbarButtonSpacer"></div>
						<button id="viewFind" class="toolbarButton group"
							title="Find in Document" tabindex="4" data-l10n-id="findbar">
							<span data-l10n-id="findbar_label">Find</span>
						</button>
						<div class="splitToolbarButton">
							<button class="toolbarButton pageUp" title="Previous Page"
								id="previous" tabindex="5" data-l10n-id="previous">
								<span data-l10n-id="previous_label">Previous</span>
							</button>
							<div class="splitToolbarButtonSeparator"></div>
							<button class="toolbarButton pageDown" title="Next Page"
								id="next" tabindex="6" data-l10n-id="next">
								<span data-l10n-id="next_label">Next</span>
							</button>
						</div>
						<label id="pageNumberLabel" class="toolbarLabel" for="pageNumber"
							data-l10n-id="page_label">Page: </label> <input type="number"
							id="pageNumber" class="toolbarField pageNumber" value="1"
							size="4" min="1" tabindex="7"> </input> <span id="numPages"
							class="toolbarLabel"></span>
					</div>
					<div id="toolbarViewerRight">
						<input id="fileInput" class="fileInput" type="file"
							oncontextmenu="return false;"
							style="visibility: hidden; position: fixed; right: 0; top: 0" />


						<button id="fullscreen" class="toolbarButton fullscreen"
							title="Switch to Presentation Mode" tabindex="11"
							data-l10n-id="presentation_mode">
							<span data-l10n-id="presentation_mode_label">Presentation Mode</span>
						</button>

						<button id="openFile" class="toolbarButton openFile"
							title="Open File" tabindex="12" data-l10n-id="open_file">
							<span data-l10n-id="open_file_label">Open</span>
						</button>

						<button id="print" class="toolbarButton print" title="Print"
							tabindex="13" data-l10n-id="print">
							<span data-l10n-id="print_label">Print</span>
						</button>

						<button id="download" class="toolbarButton download"
							title="Download" tabindex="14" data-l10n-id="download">
							<span data-l10n-id="download_label">Download</span>
						</button>
						<!-- <div class="toolbarButtonSpacer"></div> -->
						<a href="#" id="viewBookmark" class="toolbarButton bookmark"
							title="Current view (copy or open in new window)" tabindex="15"
							data-l10n-id="bookmark"><span data-l10n-id="bookmark_label">Current
								View</span></a>
					</div>
					<div class="outerCenter">
						<div class="innerCenter" id="toolbarViewerMiddle">
							<div class="splitToolbarButton">
								<button class="toolbarButton zoomOut" title="Zoom Out"
									tabindex="8" data-l10n-id="zoom_out">
									<span data-l10n-id="zoom_out_label">Zoom Out</span>
								</button>
								<div class="splitToolbarButtonSeparator"></div>
								<button class="toolbarButton zoomIn" title="Zoom In"
									tabindex="9" data-l10n-id="zoom_in">
									<span data-l10n-id="zoom_in_label">Zoom In</span>
								</button>
							</div>
							<span id="scaleSelectContainer" class="dropdownToolbarButton"> <select
								id="scaleSelect" title="Zoom" oncontextmenu="return false;"
								tabindex="10" data-l10n-id="zoom">
									<option id="pageAutoOption" value="auto" selected="selected"
										data-l10n-id="page_scale_auto">Automatic Zoom</option>
									<option id="pageActualOption" value="page-actual"
										data-l10n-id="page_scale_actual">Actual Size</option>
									<option id="pageFitOption" value="page-fit"
										data-l10n-id="page_scale_fit">Fit Page</option>
									<option id="pageWidthOption" value="page-width"
										data-l10n-id="page_scale_width">Full Width</option>
									<option id="customScaleOption" value="custom"></option>
									<option value="0.5">50%</option>
									<option value="0.75">75%</option>
									<option value="1">100%</option>
									<option value="1.25">125%</option>
									<option value="1.5">150%</option>
									<option value="2">200%</option>
							</select>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<menu type="context" id="viewerContextMenu">
			<menuitem label="First Page" id="first_page"
				data-l10n-id="first_page"></menuitem>
			<menuitem label="Last Page" id="last_page" data-l10n-id="last_page"></menuitem>
			<menuitem label="Rotate Counter-Clockwise" id="page_rotate_ccw"
				data-l10n-id="page_rotate_ccw"></menuitem>
			<menuitem label="Rotate Clockwise" id="page_rotate_cw"
				data-l10n-id="page_rotate_cw"></menuitem>
		</menu>

		<div id="viewerContainer">
			<div id="viewer" contextmenu="viewerContextMenu"></div>
		</div>

		<div id="loadingBox">
			<div id="loading"></div>
			<div id="loadingBar">
				<div class="progress"></div>
			</div>
		</div>

		<div id="errorWrapper" hidden='true'>
			<div id="errorMessageLeft">
				<span id="errorMessage"></span>
				<button id="errorShowMore" onclick="" oncontextmenu="return false;"
					data-l10n-id="error_more_info">More Information</button>
				<button id="errorShowLess" onclick="" oncontextmenu="return false;"
					data-l10n-id="error_less_info" hidden='true'>Less Information</button>
			</div>
			<div id="errorMessageRight">
				<button id="errorClose" oncontextmenu="return false;"
					data-l10n-id="error_close">Close</button>
			</div>
			<div class="clearBoth"></div>
			<textarea id="errorMoreInfo" hidden='true' readonly="readonly"></textarea>
		</div>
	</div>
	<!-- mainContainer -->

</div>
<!-- outerContainer -->
<div id="printContainer"></div>
</body>
</html>

