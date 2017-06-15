<!-- inline css  -->
<?php 
    $list_color = APContext::getListColors();
?>
<link rel="stylesheet" href="<?php echo APContext::getAssetPath() ?>/system/virtualpost/themes/assets/css/font-awesome.min.css" />
<style type="text/css">
    .top-nav-right a{
        color: #<?php echo $list_color['COLOR_033'] ?>;
        border-left: solid 1px #<?php echo $list_color['COLOR_024'] ?>;
        border-bottom: solid 1px #<?php echo $list_color['COLOR_024'] ?>;
        border-top: solid 1px #<?php echo $list_color['COLOR_024'] ?>;
        background-color: #<?php echo $list_color['COLOR_021'] ?>;
    }
    .top-nav-right a:hover {
        color: #<?php echo $list_color['COLOR_034'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_022'] ?>;
    }
    .top-nav-right a.selected {
        color: #<?php echo $list_color['COLOR_035'] ?> !important;
        border: solid 1px #<?php echo $list_color['COLOR_023'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_023'] ?> !important;
    }
    .bottom-nav-right a {
        color: #<?php echo $list_color['COLOR_042'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_021'] ?> !important;
    }
    .bottom-nav-right a.selected,.bottom-nav-right a:active {
        color: #<?php echo $list_color['COLOR_044'] ?>;
        background-color: #<?php echo $list_color['COLOR_064'] ?> !important;
    }
    .bottom-nav-right a:hover {
        color: #<?php echo $list_color['COLOR_043'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_065'] ?> !important;
    }
    
    #top-search, #top-search-postbox {
        background-color: #<?php echo $list_color['COLOR_051'] ?>;
        border-radius: 20px;
    }
    #top-search input[type="text"], #top-search-postbox input[type="text"]{
        color: #<?php echo $list_color['COLOR_053'] ?> ;
    }
    .icon-search{
        color:#<?php echo $list_color['COLOR_052'] ?>;
        float: right;
        margin-right: 12px;
        font-size: 14px;
        top: -17px;
        position: relative;
    }

    #freshwidget-button a{
        color: #<?php echo $list_color['COLOR_062'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_061'] ?> !important;
    }
    .left-nav a{
        color: #<?php echo $list_color['COLOR_059'] ?> !important;
    }
    #content-left-wrapper ul.left-nav li.act{
        background-color: #<?php echo $list_color['COLOR_058'] ?> !important;
    }
    #content-left-wrapper ul.left-nav li:hover{
        background-color: #<?php echo $list_color['COLOR_058'] ?>;
    }
    #content-left-wrapper ul.left-nav li.header{
        background-color: #<?php echo $list_color['COLOR_054'] ?> !important;
    }
    #content-left-wrapper ul.left-nav li.header:hover{
        background-color: #<?php echo $list_color['COLOR_054'] ?>;
    }
    #left-account h2,#account-body-wrapper h3, #account-body-wrapper h2, #cloud-body-wrapper h2, #invoice-body-wrapper h2{
        color: #<?php echo $list_color['COLOR_063'] ?>;
        font-size: 20px;
    }
    .COLOR_063{
        color: #<?php echo $list_color['COLOR_063'] ?>;
        font-size: 18px;
    }
    
    #cloud-body-wrapper th, #cloud-body-wrapper tfoot td, #invoice-body-wrapper th, #invoice-body-wrapper tfoot td, 
    .ui-jqgrid .ui-jqgrid-hdiv, .ui-jqgrid .ui-jqgrid-hbox, .ui-jqgrid .ui-pg-table td, .ui-jqgrid .ui-jqgrid-htable th.ui-th-ltr{
        background-color: #<?php echo $list_color['COLOR_054'] ?>;
        color: #<?php echo $list_color['COLOR_055'] ?>;
    }
    .ui-state-hover, .ui-widget-content .ui-state-hover, .ui-widget-header .ui-state-hover, .ui-state-focus, .ui-widget-content .ui-state-focus, .ui-widget-header .ui-state-focus { 
        color: #<?php echo $list_color['COLOR_056'] ?>; 
        background: #<?php echo $list_color['COLOR_057'] ?> 
    }
    .menu_title_background, .menu_title_background h3{
        color: #<?php echo $list_color['COLOR_017'] ?>;
        background-color: #<?php echo $list_color['COLOR_020'] ?>;
    }
    .ui-dialog .ui-dialog-buttonpane button,.btn-yellow, input.input-btn,
    yes_sendmail, .forward_address_radio, .collect_forward_address_radio, #changeForwardAddressWindow ul li a.yes,
    .popup-button a{
        background-image: none;
        background: none;
        color: #<?php echo $list_color['COLOR_062'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_029'] ?> !important;
        border-color: #<?php echo $list_color['COLOR_032'] ?> !important;
    }
    .btn-yellow:hover, yes_sendmail:hover, .forward_address_radio:hover, .collect_forward_address_radio:hover, #changeForwardAddressWindow ul li a.yes:hover, .popup-button a:hover{
        background-image: none;
        background: none;
        color: #<?php echo $list_color['COLOR_062'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_030'] ?> !important;
        border-color: #<?php echo $list_color['COLOR_032'] ?> !important;
    }
    .btn-yellow:active, yes_sendmail:active, .forward_address_radio:active, .collect_forward_address_radio:active, #changeForwardAddressWindow ul li a.yes:active, .popup-button a:active{
        background-image: none;
        background: none;
        color: #<?php echo $list_color['COLOR_062'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_031'] ?> !important;
        border-color: #<?php echo $list_color['COLOR_031'] ?> !important;
    }
    header {
        background: #<?php echo $list_color['COLOR_001'] ?>;
    }
    .COLOR_002{
        background: #<?php echo $list_color['COLOR_002'] ?>;
    }
    .items tbody tr:nth-child(even) td{
        color: #<?php echo $list_color['COLOR_019'] ?>;
        background: #<?php echo $list_color['COLOR_016'] ?>;
    }
    .items tbody tr:nth-child(odd) td{
        color: #<?php echo $list_color['COLOR_018'] ?>;
        background: #<?php echo $list_color['COLOR_015'] ?>;
    }
    .pagination ul li a.current, .pagination ul li a:hover{
        color: #<?php echo $list_color['COLOR_014'] ?>;
        background-color: #<?php echo $list_color['COLOR_010'] ?>;
    }
    .ui-layout-pane-east .box-item, .ui-layout-pane-center{
        background-color: #<?php echo $list_color['COLOR_007'] ?>;
    }
    .left-menu-item-header{
        color: #<?php echo $list_color['COLOR_006'] ?>;
        background-color: #<?php echo $list_color['COLOR_003'] ?>;
    }
    .postbox_title, .postbox_title a, .postbox_title:hover{
        color: #<?php echo $list_color['COLOR_013'] ?> !important;
        background-color: #<?php echo $list_color['COLOR_009'] ?>  !important;
    }
    #mailbox_envelope_image, #mailbox_document_image{
        background-color: #<?php echo $list_color['COLOR_008'] ?>  !important;
    }
    .fa-pencil-square-o, .fa-times, .fa-lock{
        font-size: 20px;
        margin-left: 10px;
        color: #<?php echo $list_color['COLOR_060'] ?>;
    }
    
    
    
    a.main_link_color, .xx .input-btn span{
        color: #<?php echo $list_color['COLOR_059'] ?>;
    }
    a.main_link_color:hover, a.main_link_color:active, .xx .input-btn span{
        color: #<?php echo $list_color['COLOR_059'] ?>;
    }
    #account-body-wrapper a{
        color: #<?php echo $list_color['COLOR_048'] ?>;
    }
    #account-body-wrapper a:hover{
        color: #<?php echo $list_color['COLOR_049'] ?>;
    }
    #account-body-wrapper a:active{
        color: #<?php echo $list_color['COLOR_050'] ?>;
    }
    
    .hide{
        display:none;
    }
    #logo{
        margin-bottom: 20px;
    }
</style>