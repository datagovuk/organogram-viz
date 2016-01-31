<?php ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml"
	lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<link type="image/x-icon"
	href="http://organogram.data.gov.uk/images/datagovuk_favicon.png"
	rel="shortcut icon">
<link type="text/css" href="../css/interface.css" rel="stylesheet">
<link type="text/css" href="../css/organogram.css" rel="stylesheet">

<script language="javascript" type="text/javascript"
	src="../scripts/jquery.min.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/jquery-ui.min.js"></script>
<!--script language="javascript" type="text/javascript" src="../scripts/Jit/jit-yc.js"></script-->
<script language="javascript" type="text/javascript"
	src="../scripts/Jit/jit-2.0.1.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/jquery.cookie.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/jquery.color.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/jquery.jgrowl.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/ui.selectmenu.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/organogram.js"></script>


<!--[if lt IE 9]>
<script language="javascript" type="text/javascript" src="../scripts/json2.js"></script>
<![endif]-->
<!--[if IE]>
<script language="javascript" type="text/javascript" src="../scripts/Jit/Extras/excanvas.js"></script>
<script language="javascript" type="text/javascript" src="../scripts/jquery.corner.js"></script>
<![endif]-->

<title>Organogram Data | Organogram Viewer</title>

</head>

<!--[if lt IE 7]>
	<body class="IE6">
<![endif]-->

<!--[if IE 7]>
	<body class="IE7" onload="Orgvis.init(false, false);">
<![endif]-->

<!--[if IE 8]>
	<body class="IE8" onload="Orgvis.init(false, false);">
<![endif]-->

<!--[if IE 9]>
	<body class="IE9" onload="Orgvis.init(false, false);">
<![endif]-->

<![if !IE]>
<body
	onload="Orgvis.init(false, false);">
<![endif]>

	<div id="apiCalls"></div>

	<!--[if lt IE 7]>
	<div class="ieBanner">
		<p>This application isn't going to work as it makes use of plugins and animation techniques that require a relatively modern browser.</p>
		<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
			<img src="http://www.theie6countdown.com/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
		</a>
	</div>
	<![endif]-->

	<div id="infovis">

		<div id="infobox">
			<a class="close">x</a>
		</div>
		<div id="categorybox">
			<a class="close">x</a>
		</div>

		<div class="sources-tip tip"></div>
	</div>

	<noscript>
		<div class="noscript">
			<p>It looks like you have JavaScript disabled.</p>
			<p>
				Please turn JavaScript <strong>ON</strong> and reload the page in
				order to use this application.
			</p>
		</div>
	</noscript>

</body>
</html>
