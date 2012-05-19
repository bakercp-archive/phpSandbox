<?php

	// Demo code to get the current Twitter trending topics
	// and carry out a google image search on each of the trending topics
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

	$icon = false;
	
	if(isset($_GET['icon'])) $icon = true;
	

	// $out is what we will eventually echo to the screen
	$out = '<html><body>'; // bare bones

	
	$woeid = 1; // this is for the whole world
	$trend_data = getTwitterTrendingTopics($woeid); // call our custom function

	// add some basic trend data to the output
	$out .= "Location: "   . $trend_data['location']   . "<br/>";
	$out .= "As Of: "      . $trend_data['as_of']      . "<br/>";
	$out .= "Created At: " . $trend_data['created_at'] . "<br/>";
	$out .= "<br/><br/>";

	foreach ($trend_data['trends'] as $trend_item) {
	  	// feed each $item['name'] into your googleimage search 
		$trend = $trend_item['name'];
		
		// do you want to remove the hashtags?
		
		// do the google image search with our function 
		$urls = getGoogleImageSearchResults($trend, $icon);
		
		$out .= "<strong>" . $trend . "</strong><br/>";
	    // drop an <img> tag for each url
	    foreach($urls as $url) {
			$out .=  '<img src="' . $url . '" />';
	 	}
	
	  $out .= "<br/><br/>";
	}

	$out .= '</body></html>'; // bare bones

	// this outputs the correct headers (so unicode chars will be interpreted correctly)
	header('Content-Type: text/html; charset=utf-8');

	echo $out; // send it to the screen in one burst


//=====================================================
	
	// our function
	function getTwitterTrendingTopics($woeid) {
	
		//https://dev.twitter.com/docs/api/1/get/trends/%3Awoeid
		// there is documentation there for choosing other trending regions, etc.
	
		$site_url = 'https://api.twitter.com/1/trends/'. $woeid . '.json';
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt($ch, CURLOPT_URL, $site_url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		
		// this ob_start/ob_end_clean() stuff is more reliable for shared web hosts
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$file_contents = ob_get_contents();
		ob_end_clean();
		
		$json = json_decode( $file_contents, true );
		
		// create a little data structure to return from the function
		$trend_data['location']   = $json[0]['locations'][0]['name'];;
		$trend_data['as_of']      = $json[0]['as_of'];
		$trend_data['created_at'] = $json[0]['created_at'];
		$trend_data['trends']     = $json[0]['trends'];
		
		return $trend_data;
	}

	function getGoogleImageSearchResults($query, $icon) {
		// this may be against the google TOS.
		// it is a straight web scrape.
		// for "demo" purposes only.
		
		$search_url = 'http://images.google.com/images?safe=off';
		$search_url .= '&q=' . urlencode($query); // url encode it (i.e. convert spaces to %20 etc)
		$search_url .= ($icon ? "&imgsz=icon" : ""); // conditionally add the icon
		
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $search_url);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$file_contents = ob_get_contents();
		ob_end_clean();
		
		
		// there are A LOT of ways to get the image urls.
		// this is a nice way that uses xpath.  
		// it could also be done with a clever preg_match / regex.
		// or many other ways.
		
		// parse the html into a DOMDocument
		$dom = new DOMDocument();
		$dom->loadHTML($file_contents);
		
		// grab all the anchors the page
		$xpath = new DOMXPath($dom);
		$hrefs = $xpath->evaluate("/html/body//a");
		
		$urls = array(); // this is the array we will fill and return
		
		for ($i = 0; $i < $hrefs->length; $i++) {
			$href = $hrefs->item($i);
			$url = $href->getAttribute('href');
			$pos = strrpos($url, '/imgres');
			if(strrpos($url,'/imgres') === 0) {
			$start = 15;
			$end = strrpos($url,'&imgrefurl=');
		
			// the image url
			$img_url = substr($url,$start,$end - $start);		
			array_push($urls,$img_url);
			}	
		}
		
		return $urls;
	}

?>
