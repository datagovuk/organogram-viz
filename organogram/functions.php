<?php

/*
* Caches API calls to a local file which is updated on a
* given time interval.
*
* http://www.profilepicture.co.uk/caching-api-responses-php/
*/
class API_cache {

  private
      $_update_interval // how often to update
    , $_cache_file
    , $_api_call_string
    , $_api_call; // API call (array, first is function name, others are params)

  public function __construct ($tw, $int = 240) {
  	//$bad_file_characters = array_merge(array_map('chr', range(0,31)), array("<", ">", ".", ":", '"', "/", "\\", "|", "?", "*"));
    $this->_api_call = $tw;
    $this->_api_call_string = implode("_", $tw);
    $this->_update_interval = $int * 60; // minutes to seconds
    // use md5sum of the function call as filename (avoids bad character issues)
    $this->_cache_file = '../cache/' . md5($this->_api_call_string);
  }

  /*
* Updates cache if last modified is greater than
* update interval and returns cache contents
*/
  public function get_api_cache () {
    if (!file_exists($this->_cache_file) ||
        time() - filemtime($this->_cache_file) > $this->_update_interval) {
      $this->_update_cache();
    }
    $handle = fopen($this->_cache_file, "r");

    $first = fgets($handle); // dont return first line ( contains api call string)
    $buffer = "";
	while (!feof($handle)) {
	    $buffer .= fgets($handle);
	}

    return $buffer;
  }

  /*
* Http expires date
*/
  public function get_expires_datetime () {
    if (file_exists($this->_cache_file)) {
      return date (
        'D, d M Y H:i:s \G\M\T',
        filemtime($this->_cache_file) + ($this->_update_interval)
      );
    }
  }

  /*
* Makes the api call and updates the cache
*/
  private function _update_cache () {
    // update from api if past interval time
    $fp = fopen($this->_cache_file, 'w+'); // open or create cache
    if ($fp) {
      if (flock($fp, LOCK_EX)) {
      	$func_name = array_shift($this->_api_call);
        $contents = $this->_api_call_string . "\n" . call_user_func_array($func_name, $this->_api_call);
        fwrite($fp, $contents);
        flock($fp, LOCK_UN);
      }
      fclose($fp);
    }
  }
}

/**
 * Given an array of version dates and URLs, it returns an
 * array of key-value objects with version dates in two formats
 *
 * @param  [type] $versions   array('2014-09-30' => 'http://46.43.41.16/sparql/organogram/query',...)
 * @param  [type] $deptUri    [description]
 * @param  [type] $pubbodyUri [description]
 * @return [type]             array('version_name' => '30/09/2014', 'version_value => '2014-09-30'},...]
 */
function getVersions($versions, $deptUri, $pubbodyUri) {
  // echo '<script>console.log('.json_encode($versions).');</script>';
  // echo '<script>console.log("'.$deptUri.' '.$pubbodyUri.'");</script>';
    $arrVersions = array();
	foreach($versions as $version1 => $endpoint1) {
		$api_cache = new API_cache (array("isInDB", $deptUri,$pubbodyUri, $endpoint1));
		if ($api_cache->get_api_cache()) {
			$arrDateArray = explode("-", $version1);
			$arrVersions[] = array('version_name' => $arrDateArray[2].'/'.$arrDateArray[1].'/'.$arrDateArray[0],'version_value' => $version1);
		}
	}
  // echo '<script>console.log('.json_encode($arrVersions).');</script>';
	return $arrVersions;
}

function getDepartmentsJSON($versions) {
	foreach($versions as $version => $endpoint) {
		$api_cache = new API_cache (array("getDepartments", $endpoint,));
		$json = json_decode($api_cache->get_api_cache());
		foreach($json->results->bindings as $line){
			$uriParts = explode("/", $line->uri->value);
			$type = $uriParts[4];
            $parent = '';
            if (!empty($line->parent))
			    $parent = substr($line->parent->value, strrpos($line->parent->value, "/")  + 1);
			$deptName = str_replace("@en","",$line->dept->value);

			$type = $type == 'department' ? 'dept' : 'pubbod';
			$uri = "/organogram/?$type=" . $uriParts[5];

			$arrDepts[$uriParts[5]] = array('type' => $type, 'label' => $deptName, 'uri' => $uri, 'parent' => $parent);
		}
	}

	$deptsJSON = array();
	foreach ($arrDepts as $dept => $values) {
		$entry =  array('label' => $values['label'], 'uri' => $values['uri']);

		$parent = $values['parent'];
		if ($parent) {
			$index = $parent;
			if (isset($arrDepts[$parent])) {
				$label = $arrDepts[$parent]['label'];
			}
			else {
				// ideally should get here if all parent names are accurate
				$label = $dept;
			}
		}
		else {
			$index = $dept;
			$label = $values['label'];
		}

		if (!isset($deptsJSON[$index])) {
			$deptsJSON[$index] = array('label' => $label, 'departments' => array($entry));
		}
		else {
			$deptsJSON[$index]['departments'][] = $entry;
		}
	}

	$arrDepts = array();

	// remove key
	foreach ($deptsJSON as $entry) {
		$arrDepts[] = $entry;
	}

	return $arrDepts;
}

function getTopDog($endpoint,$thisDepartment,$version){

	$getTopDog = <<<GETTOPDOG
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX gov: <http://reference.data.gov.uk/def/central-government/>
PREFIX postStatus: <http://reference.data.gov.uk/def/civil-service-post-status/>
PREFIX org: <http://www.w3.org/ns/org#>

SELECT DISTINCT ?item

WHERE {
  GRAPH <http://reference.data.gov.uk/organogram/graph/$version> {
    ?item gov:postIn <$thisDepartment> .
    {
      ?item postStatus:postStatus postStatus:current .
    }
    UNION
    {
      ?item postStatus:postStatus postStatus:vacant .
    }
    OPTIONAL
    {
      ?item org:reportsTo ?boss .
    }
    FILTER (!BOUND(?boss))
  }
}
ORDER BY DESC(?item)
LIMIT 100

GETTOPDOG;

	$result = query($endpoint, $getTopDog);
	if($result){
		return $result;
		//$thisString = "found something";
	}

}


function getDepartments($endpoint){

	$getDepartments = <<<GETDEPT
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX org: <http://www.w3.org/ns/org#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf-schema: <http://www.w3.org/2000/01/rdf-schema#>

SELECT DISTINCT ?uri ?dept ?parent

 WHERE {
	?uri rdf:type org:Organization;
	rdf-schema:label ?dept
	OPTIONAL {
	?uri <http://reference.data.gov.uk/def/central-government/parentDepartment> ?parent
	}
}
order by (?uri)

GETDEPT;

	$result = query($endpoint, $getDepartments);
	if($result){
		return $result;
		//$thisString = "found something";
	}

}

/**
 * Checks to see if a department exists in the DB through its URI
 * @param  [type]  $deptUri    [description]
 * @param  [type]  $pubbodyUri [description]
 * @param  [type]  $endpoint   [description]
 * @return boolean             [description]
 */
function isInDB($deptUri,$pubbodyUri, $endpoint) {
	$deptExists = <<<DEPTEXISTS
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX org: <http://www.w3.org/ns/org#>

ASK
  {
{<$deptUri> rdf:type org:Organization}
	UNION
	{<$pubbodyUri> rdf:type org:Organization}
}

DEPTEXISTS;

	$result = queryAsk($endpoint, $deptExists);
	// sesame returns true rather than yes
	if($result == 'yes' || $result == 'true'){
		return true;
		//$thisString = "found something";
	}else{
		return false;
		//$thisString = "nothing found";
	}
}


function query ($endpoint, $query) {
	$params = array(
    'http' => array(
      'method' => 'GET',
      'header' => 'Accept: application/sparql-results+json',
      'max_redirects' => 1,
      'ignore_errors' => true
	)
	);
	$ctx = stream_context_create($params);
	$query = array(
    'query' => $query,
    'output' => 'json'
    );
    $queryString = http_build_query($query);
    try {
    	$fp = fopen($endpoint . '?' . $queryString, 'rb', false, $ctx);
    	if (!$fp) {
      header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
      echo "<html><head><title>Error Accessing SPARQL Endpoint</title></head><body><p>Problem accessing $endpoint</p></body></html>";
      return array();
    	} else {
      $response = stream_get_contents($fp);
      if ($response === false) {
      	header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
      	echo "<html><head><title>Error Getting Data</title></head><body><p>Problem reading data from $endpoint</p></body></html>";
      	return array();
      } else {
      	return trim($response);
      }
    	}
    } catch (Exception $e) {
    	header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    	echo "<html><head><title>Error Getting Data</title></head><body><p>Exception " . $e->getMessage() . ".</p></body></html>";
    	return array();
    }

}

function queryAsk ($endpoint, $query) {
	$params = array(
    'http' => array(
      'method' => 'GET',
	//'header' => 'Accept: application/sparql-results+json',
      'max_redirects' => 1,
      'ignore_errors' => true
	)
	);
	$ctx = stream_context_create($params);
	$query = array(
    'query' => $query,
    'output' => 'text'
    );
    $queryString = http_build_query($query);
    try {
    	$fp = fopen($endpoint . '?' . $queryString, 'rb', false, $ctx);
    	if (!$fp) {
      header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
      echo "<html><head><title>Error Accessing SPARQL Endpoint</title></head><body><p>Problem accessing $endpoint</p></body></html>";
      return array();
    	} else {
      $response = stream_get_contents($fp);
      if ($response === false) {
      	header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
      	echo "<html><head><title>Error Getting Data</title></head><body><p>Problem reading data from $endpoint</p></body></html>";
      	return array();
      } else {
      	return trim($response);
      }
    	}
    } catch (Exception $e) {
    	header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error");
    	echo "<html><head><title>Error Getting Data</title></head><body><p>Exception " . $e->getMessage() . ".</p></body></html>";
    	return array();
    }

}

?>
