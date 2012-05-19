
<?php 

// A simple script for pushing db entries out via XML.  The queries can be filtered
// by php id.
//
// Copyright (c) 2011, 2012 Christopher Baker <http://christopherbaker.net> 
//
// Permission is hereby granted, free of charge, to any person obtaining a copy of 
// this software and associated documentation files (the "Software"), to deal in 
// the Software without restriction, including without limitation the rights to use, 
// copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the 
// Software, and to permit persons to whom the Software is furnished to do so, 
// subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in all 
// copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
// WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN 
// CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

// connect to db
$link = mysql_connect('SERVER','USERNAME','PASSWORD');
if (!$link) {
     //die('Not connected : ' . mysql_error());
}

if (! mysql_select_db('ipwsteps') ) {
    //die ('Can\'t use foo : ' . mysql_error());
}

$id = "-1";

if (isset($_GET['id'])) {
  $id = (get_magic_quotes_gpc()) ? $_GET['id'] : addslashes($_GET['id']);
}


$queryResult = mysql_query("SELECT * FROM `walkdata` WHERE id > " . $id . " ORDER BY id") or trigger_error(mysql_error());


$numEntries = mysql_num_rows($queryResult);
$walkData= mysql_fetch_assoc($queryResult);

$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";

if($numEntries > 0) {
	
	$xml .= "<walkdata>\n";

	do { 
		$xml .= "\t<entry>\n";
		
			$xml .= "\t\t<id>" . $walkData['id'] . "</id>\n";
			$xml .= "\t\t<steps>" . $walkData['steps'] . "</steps>\n";
			$xml .= "\t\t<lon>" . $walkData['lon'] . "</lon>\n";
			$xml .= "\t\t<lat>" . $walkData['lat'] . "</lat>\n";
			$xml .= "\t\t<timestamp>" . $walkData['datetime'] . "</timestamp>\n";
			
		$xml .= "\t</entry>\n";

	} while ($text = mysql_fetch_assoc($numEntries));
	
	$xml .= "</walkdata>";
} else {
	$xml = "<walkdata />"; // empty
}

  
header("Content-Type: application/xml; charset=UTF-8");
echo $xml;

mysql_free_result($queryResult);
