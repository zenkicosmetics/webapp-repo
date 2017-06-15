//
// Dynamsoft JavaScript Library for Basic Initiation of Dynamic Web TWAIN
// More info on DWT: http://www.dynamsoft.com/Products/WebTWAIN_Overview.aspx
//
// Copyright 2017, Dynamsoft Corporation 
// Author: Dynamsoft Team
// Version: 12.3
//
/// <reference path="dynamsoft.webtwain.initiate.js" />
var Dynamsoft = Dynamsoft || { WebTwainEnv: {} };

Dynamsoft.WebTwainEnv.AutoLoad = true;
///
Dynamsoft.WebTwainEnv.Containers = [{ContainerId:'dwtcontrolContainer', Width:650, Height:420}];
///
Dynamsoft.WebTwainEnv.ProductKey = '14270772CCFBEF35A8AF2FBB932459CDC3F3B0D7E7C1AA213A44198EA3E8C55C364EC7B8A64E4F89BFB4BAE104928D1C19F40CA99FE1367B7CA53E1D5BEBB6F1AE04FE3BCE2AE260B2E70C5299EF198E9A815B1D464CD06058ED2FC2C9E2B58A8F382BFBF5237E760AC697D5A19835CCF76F1A37DF55BFB200369214FD';
///
Dynamsoft.WebTwainEnv.Trial = false;
///
Dynamsoft.WebTwainEnv.ActiveXInstallWithCAB = false;
///
Dynamsoft.WebTwainEnv.ResourcesPath = 'system/virtualpost/themes/new_admin2/resources/scans';

/// All callbacks are defined in the dynamsoft.webtwain.install.js file, you can customize them.

// Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', function(){
// 		// webtwain has been inited
// });

