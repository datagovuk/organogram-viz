<?php
$versions = array(
    //sparql/organogram/query
    '2014-09-30' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2014-03-31' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2013-09-30' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2013-03-31' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2012-09-30' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2012-03-31' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2011-09-30' => 'http://reference.data.gov.uk/sparql/organogram/query',
    '2011-03-31' => 'http://reference.data.gov.uk/sparql/organogram/query');
$thisPubbod = '';
$thisDept = '';

if (!empty( $_GET['pubbod']))
    $thisPubbod = $_GET['pubbod'];
if (!empty( $_GET['dept']))
    $thisDept = $_GET['dept'];

include 'functions.php';

if(isset($_GET['obscured'])){
	//not the live system so decrypt the pubbod and dept
	$thisPubbod = base64_decode($thisPubbod);
	$thisDept = base64_decode($thisDept);
}

$deptUri = "http://reference.data.gov.uk/id/department/" . $thisDept;
$pubbodyUri = "http://reference.data.gov.uk/id/public-body/" . $thisPubbod;

$thisVersion = '';
if (!empty($_GET['version']))
    $thisVersion = $_GET['version'];
$arrVersions = getVersions($versions, $deptUri, $pubbodyUri);
if($thisVersion==''){
	$thisVersion = $arrVersions[0]["version_value"];
}

//create array of ALL departments in ALL triplestores
$arrDepts = getDepartmentsJSON($versions);

$thisPost = "";
$thisendPoint = $versions[$thisVersion];
//echo "{".$thisendPoint."}";
$deptURI = $deptUri;
if($thisDept==""){
    $deptURI = $pubbodyUri;
}
$api_cache = new API_cache (array("getTopDog", $thisendPoint, $deptURI));
$top_dog = json_decode($api_cache->get_api_cache());

if (count($top_dog->results->bindings) > 0) {
    $top_dog = $top_dog->results->bindings[0]->item->value;
    $startPos = strrpos($top_dog, "/");
    $thisPost = substr($top_dog, $startPos + 1);
}
$preview = FALSE;
if (!empty($_GET['preview']))
    $preview = $_GET['preview'];
?>
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
<!-- Scripts -->
<script language="javascript" type="text/javascript">
//<!--
var strDateFolder = "<?php echo $thisVersion?>";
var departmentList = 
<?php echo json_encode($arrDepts);?>

// reverse array to have latest version on right of slider
var versionsList = 
<?php echo json_encode(array_reverse($arrVersions));?>

//-->
 

</script>

<script language="javascript" type="text/javascript"
	src="../scripts/jquery-latest.min.js"></script>
<script language="javascript" type="text/javascript"
	src="../scripts/jquery-ui-1.8.7.min.js"></script>
<!--script language="javascript" type="text/javascript" src="../scripts/Jit/jit-yc.js"></script-->
<script language="javascript" type="text/javascript"
	src="../scripts/Jit/jit.js"></script>
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
	<body class="IE7" onload="Orgvis.init('<?php print $thisDept;?>','<?php print $thisPubbod;?>','<?php print $thisPost;?>',false,'<?php print $preview;?>');">
<![endif]-->

<!--[if IE 8]> 
	<body class="IE8" onload="Orgvis.init('<?php print $thisDept;?>','<?php print $thisPubbod;?>','<?php print $thisPost;?>',false,'<?php print $preview;?>');">
<![endif]-->

<!--[if IE 9]> 
	<body class="IE9" onload="Orgvis.init('<?php print $thisDept;?>','<?php print $thisPubbod;?>','<?php print $thisPost;?>',false,'<?php print $preview;?>');">
<![endif]-->

<![if !IE]>
<body
	onload="Orgvis.init('<?php print $thisDept;?>','<?php print $thisPubbod;?>','<?php print $thisPost;?>',false,'<?php print $preview;?>');">
<![endif]>


	<h1 class="title breadcrumbs">
		<button id="back">Manage</button>
		<button id="category">Category</button>
		<button id="dept">Department</button>
		<button id="unit">Unit</button>
		<button id="post">Post</button>

		<select id="filterBy">
			<option value="none" data-type="none">--</option>
		</select> <label for="filterBy">Highlight</label>
	</h1>
	<div id="live-link"></div>

	<!--[if lt IE 7]>
<div class="ieBanner">
	<p>This application isn't going to work as it makes use of plugins and animation techniques that require a relatively modern browser.</p>
	<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
		<img src="http://www.theie6countdown.com/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
	</a>
</div>
<![endif]-->

	<div id="infovis"></div>

	<div id="infobox">
		<a class="close">x</a>
	</div>
	<div id="categorybox">
		<a class="close">x</a>
	</div>

	<div id="right" data-corner="left 10px">

		<a class="aboutToggle" href="#">About</a>

		<div class="about-tip tip">
			<p>This is an organisational chart (organogram) visualisation for the
				structure of 'posts' within the UK government. Government
				departments are comprised of units which contain posts and these
				posts can be held by one or more people.</p>

			<p>This visualisation shows the paths of responsibility in terms of
				who reports to who for the post in question by including it's
				'parent' posts and it's 'children' posts. Clicking on a post in the
				visualisation should load it's children posts if there are any
				present.</p>

			<p>Each post has an information panel that includes information such
				as the name of the person(s) that holds the post, their contact
				details, the name of the departmental unit the post exists in, a
				description of the post's role and there are also links available
				that take you to the information itself - provided by the Linked
				Data API.</p>

			<p>The source code for this visualisation is available from <a class="about-link" href="http://code.google.com/p/linked-data-api/">http://code.google.com/p/linked-data-api/</a></p>
			<p>To view the sources of data the visualisation uses at any time
				while using it, there's information provided about all of the the
				API calls made in the bottom right under "Data sources". Here you
				can grab the data in several different formats and see which
				parameters have been used to tailor the data for the visualisation.</p>

			<p>
				<strong>Controls: </strong>
			</p>
			<ul>
				<li>Adjust: click and hold the organogram to drag it around.</li>
				<li>Zoom: while hovering over the organogram, scroll up/down using a
					mousewheel or trackpad to zoom in/out.</li>
				<li>Alternatively, you can use the buttons provided on right hand
					side.</li>
			</ul>

			<p>Please note: this application is still under development.</p>

			<p>
				<i>Powered by <a href="http://code.google.com/p/puelia-php/"
					target="_blank">Puelia v0.1</a>, an implementation of the <a
					href="http://code.google.com/p/linked-data-api" target="_blank">Linked
						Data API</a> </i>
			</p>

		</div>

		<div class="orientation">
			<p>Orientation</p>
			<form>
				<div id="orientation">
					<input type="radio" id="top" name="orientation" checked="checked" /><label
						for="top">Top</label> <input type="radio" id="left"
						name="orientation" /><label for="left">Left</label>
				</div>
			</form>
		</div>

		<!--<div class="navigate">
			<p>Adjust</p>
            <div id="nav">
                <button id="nav_up">Up</button>
                <button id="nav_down">Down</button>
                <button id="center">Center</button>
                <button id="nav_left">Left</button>
                <button id="nav_right">Right</button>
            </div>
		</div> -->
		<div class="slider">
			<p>
				<label for="versions"
					style="border: 0; color: #FFFFFF; font-weight: bold; background: #9A51C6">Version:</label>
				<span id="versions"
					style="border: 0; color: #FFFFFF; font-weight: bold; background: #9A51C6"></span>
			</p>
			<div id="slider"></div>
		</div>

		<div id="apiCalls">
			<!--<p class="label">Data sources</p>-->
			<!-- information about API calls goes here -->
		</div>
	</div>


	<!--
div id="login">
	<form>
		<fieldset>
			<legend>Login</legend>	
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="text" maxlength="30" />
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="text" maxlength="30" />
		</fieldset>
	</form>
	<p class="logging-in login-message">Logging in...</p>
	<p class="login-success login-message">Login successful!</p>
	<p class="login-failed login-message">Login failed, try again.</p>
	<p class="delHistory login-message">Login details forgotten!</p>
</div
-->

	<span id="previewModeSign"> <span>Preview Mode</span> </span>

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
