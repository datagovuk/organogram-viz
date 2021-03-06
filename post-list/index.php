<?php 
$versions = array('2012-03-31' => 'http://w8www186254:9999/openrdf-sesame/repositories/organogram-2012-03-31', '2011-09-30' => 'http://w8www186254:9999/openrdf-sesame/repositories/organogram-2011-09-30-fixed-parents', '2011-03-31' => 'http://rhapp015244:1433/sparql/gov-organograms-new2');
$thisPubbod = $_GET['pubbod'];
$thisDept = $_GET['dept'];

include '..\organogram\functions.php';

if(isset($_GET['obscured'])){
	//not the live system so decrypt the pubbod and dept
	$thisPubbod = base64_decode($thisPubbod);
	$thisDept = base64_decode($thisDept);
}

$deptUri = "http://reference.data.gov.uk/id/department/" . $thisDept;
$pubbodyUri = "http://reference.data.gov.uk/id/public-body/" . $thisPubbod;

$thisVersion = $_GET['version'];
$arrVersions = getVersions($versions, $deptUri, $pubbodyUri);
if($thisVersion==''){
	$thisVersion = $arrVersions[0]["version_value"];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">

<link type="image/x-icon" href="http://organogram.data.gov.uk/images/datagovuk_favicon.png" rel="shortcut icon">
<link type="text/css" href="../css/interface.css" rel="stylesheet">
<link type="text/css" href="../css/post-list.css" rel="stylesheet">
<title>Organogram Data | Post Listing</title>

<!-- Scripts -->
<script language="javascript" type="text/javascript" src="../scripts/jquery-latest.min.js"></script>
<!--script language="javascript" type="text/javascript" src="js/jquery-ui-1.8.8.custom.min.js"></script-->	
<script language="javascript" type="text/javascript" src="../scripts/jquery-ui-1.8.7.min.js"></script>		
<script language="javascript" type="text/javascript" src="../scripts/jquery.quicksand.js"></script>		
<script language="javascript" type="text/javascript" src="../scripts/jquery.cookie.js"></script>
<script language="javascript" type="text/javascript" src="../scripts/jquery.jgrowl.js"></script>
<script language="javascript" type="text/javascript" src="../scripts/ui.selectmenu.js"></script>
<script language="javascript" type="text/javascript" src="../scripts/post-list.js"></script>
<script language="javascript" type="text/javascript">
//<!--
var strDateFolder = "<?php echo $thisVersion?>";

//-->
 

</script>

<!--[if IE]>
<script language="javascript" type="text/javascript" src="../scripts/jquery.corner.js"></script>		
<![endif]-->

</head>

<!--[if IE 7]> 
	<body class="IE7" onload="PostList.init('<?php print $_GET['dept'];?>','<?php print $_GET['pubbod'];?>','<?php print $_GET['unit'];?>','<?php print $_GET['property'];?>','<?php print $_GET['value'];?>','<?php print $_GET['preview'];?>');"> 
<![endif]--> 
<!--[if IE 8]> 
	<body class="IE8" onload="PostList.init('<?php print $_GET['dept'];?>','<?php print $_GET['pubbod'];?>','<?php print $_GET['unit'];?>','<?php print $_GET['property'];?>','<?php print $_GET['value'];?>','<?php print $_GET['preview'];?>');"> 
<![endif]--> 
<![if !IE]> 
<body onload="PostList.init('<?php print $_GET['dept'];?>','<?php print $_GET['pubbod'];?>','<?php print $_GET['unit'];?>','<?php print $_GET['property'];?>','<?php print $_GET['value'];?>','<?php print $_GET['preview'];?>');">
<![endif]> 

<h1 class="title breadcrumbs">
	<button id="gov" rel="government">HM Government</button><button id="dept">Department</button><button id="unit">Unit</button>

	<select id="loadBy">
	
		
		<option value="--">--</option>
	
		<!--<optgroup label="Type">
			<option value="Permanent Secretary" data-type="type">Permanent Secretary</option>
			<option value="Director General" data-type="type">Director General</option>
			<option value="Director" data-type="type">Director</option>
			<option value="Deputy Director" data-type="type">Deputy Director</option>
		</optgroup-->
	
		  <optgroup label="Civil Service Grade">
			<option value="SCS4" data-type="grade">SCS4</option>		
  			<option value="SCS3" data-type="grade">SCS3</option>
  			<option value="SCS2" data-type="grade">SCS2</option>
  			<option value="SCS1A" data-type="grade">SCS1A</option>
  			<option value="SCS1" data-type="grade">SCS1</option>
		  </optgroup>
		  <optgroup label="Military Grade">
  			<option value="OF-9" data-type="grade">OF-9</option>
  			<option value="OF-8" data-type="grade">OF-8</option>
  			<option value="OF-7" data-type="grade">OF-7</option>
  			<option value="OF-6" data-type="grade">OF-6</option>
		  </optgroup>
		
		<optgroup label="Other">
			<option value="Top posts" data-type="topposts">Top posts</option>
		</optgroup>
	</select>
	<label for="loadBy" class="loadBy">Load</label>
	
	<select id="sortBy">
		<option value="--">--</option>
		<option value="name">Name</option>
		<option value="title">Title</option>
		<option value="unit">Unit</option>
		<!--option value="grade">Grade</option-->
		<option value="salaryRange">Salary Range</option>
	</select>
	<label for="sortBy" class="sortBy">Sort by</label>
	
	<select id="filterBy">
		<option value="none" data-type="none">None</option>
	</select>
	<label for="filterBy" class="filterBy">Filter by</label>
		
</h1>


<!--[if lt IE 7]>
<div class="ieBanner">
	<p>This application isn't going to work as it makes use of plugins and animation techniques that require a relatively modern browser.</p>
	<a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
		<img src="http://www.theie6countdown.com/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
	</a>
</div>
<![endif]--> 

<div id="infovis">
<div class="postHolder">
<!-- -->
</div>
<div class="clear padder"><!-- --></div>
</div>

<div id="right">

		<a class="aboutToggle" href="#">About</a>
		
		<div class="about-tip tip">
			
			<p>This is a list of posts of a specific type within a department. Clicking on a post will load it's organogram. </p>
			<p>Please note: this application is still in development.</p>
			
			<p><i>Powered by <a href="http://code.google.com/p/puelia-php/" target="_blank">Puelia v0.1</a>, an implementation of the <a href="http://code.google.com/p/linked-data-api" target="_blank">Linked Data API</a></i></p>
		</div> <!-- end tip -->
				
	<div id="apiCalls">
		<p class="label">Data sources</p>
		<!-- information about API calls goes here -->
	</div>

</div> <!-- end right -->

<div id="nodeTip" class="tooltip"></div>

<div class="noPosts">
<!-- -->
</div>

<div class="qs-overlay ui-widget-overlay"><!-- --></div>
<div class="qs-overlay-div">
	<p class="text">Filtering...</p>
	<img src="../images/loading.gif" />
</div>
	
<span id="previewModeSign">
	<span>Preview Mode</span>
</span>	
	
	
<noscript>
<div class="noscript">
<p>It looks like you have JavaScript disabled.</p>
<p>Please turn JavaScript <strong>ON</strong> and reload the page in order to use this application.</p>
</div>
</noscript>

</body>

</html>
