<?php

// Function to make GET request using cURL
function curlGet($url) {
	$ch = curl_init();	// Initialising cURL session
	// Setting cURL options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);		
	$results = curl_exec($ch);	// Executing cURL session
	curl_close($ch);	// Closing cURL session
	return $results;	// Return the results
}

// Declaring arrays
$mixedEmails = array();
$validEmails = array();
$mixedLinks = array();
$validLinks = array();

// Function to return XPath object
function returnXPathObject($item) {
	$xmlPageDom = new DomDocument();	// Instantiating a new DomDocument object
	@$xmlPageDom->loadHTML($item);	// Loading the HTML from downloaded page
	$xmlPageXPath = new DOMXPath($xmlPageDom);	// Instantiating new XPath DOM object
	return $xmlPageXPath;	// Returning XPath object
}

$packtPage = curlGet('http://www.packtpub.com/contact');	// Calling function curlGet and storing returned results in $packtPage variable

$packtPageXpath = returnXPathObject($packtPage);

$scrapedEmails = $packtPageXpath->query('//a');	// Querying for all link anchors

// If results exist
if ($scrapedEmails->length > 0) {
	// For each result
	for ($i = 0; $i < $scrapedEmails->length; $i++) {
		$mixedEmails[] = $scrapedEmails->item($i)->nodeValue;	// Add result to $mixedEmails array
	}
}

// For each result in $mixedEmails array
foreach ($mixedEmails as $key => $email) {
	// If result is a valid email address
	if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$validEmails[] = $email;	// Add email to $validEmails array
	}
}

$scrapedLinks = $packtPageXpath->query('//a/@href');	// Querying for href attribute of all link anchors

// If results exist
if ($scrapedLinks->length > 0) {
	// For each result
	for ($j = 0; $j < $scrapedLinks->length; $j++){
		$mixedLinks[] = $scrapedLinks->item($j)->nodeValue;	// Add result to $mixedLinks array
	}
}

// For each result in $mixedLinks array
foreach ($mixedLinks as $key => $link) {
	// If result is a valid link
	if (filter_var($link, FILTER_VALIDATE_URL)) {
		$validLinks[] = $link;	// Add link to $validLinks array
	}
}

print_r($validLinks);	// Printing array of validated links

print_r($validEmails);	// Printing array of validated emails

?>