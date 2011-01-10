<?php
/*
 * Copyright (c) 2010 The Pickling Jar Ltd <code@thepicklingjar.com>
 *
 * Permission to use, copy, modify, and distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES
 * WITH REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR
 * ANY SPECIAL, DIRECT, INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES
 * WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER IN AN
 * ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT OF
 * OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.
 */
require('webkitdlib.php');

//connect to webkitd
$fd = webkitd_connect('127.0.0.1', 3817);
if($fd == false){
	die('Error: webkitd couldn\'t connect'."\n");
}

//$res = webkitd_help($fd);
//echo $res;

//set url
$res = webkitd_url($fd, 'http://127.0.0.1/wkd/index.php');
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set url'."\n");
}

/*
//set useragent
$res = webkitd_setuseragent($fd, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)');
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set user agent'."\n");
}
*/

/*
//set proxy
$res = webkitd_setproxy($fd, 'http://127.0.0.1:8080');
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set proxy'."\n");
}
*/

/*
//get current proxy
$proxy = webkitd_returnproxy($fd);
print_r($proxy);
*/

/*
//set referrer
$res = webkitd_setreferrer($fd,'http://www.testreferrer.com');
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set referrer'."\n");
}
*/

/*
//set to get request (default)
$res = webkitd_get($fd);
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set http get'."\n");
}
*/

//execute request - download page
$res = webkitd_execute($fd);
if($res == false){
	echo ('Error: webkitd couldn\'t execute - errcode: '.$globalerrcode.' errstr: '.$globalerrstr."\n");
}

//return url might not be the one we set due to 301s/302s etc
$url = webkitd_returnurl($fd);
if($url == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t return url'."\n");
}
echo "url: ".$url."\n";

/*
//broke as returns last header (i.e. could be an image)
$header= webkitd_returnheader($fd);
if($header == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t return header'."\n");
}
echo "header: ".$header."\n";
*/

/*
//run internal jquery, use _jQuery not _$
$res = webkitd_runjs($fd, "_jQuery('#content').html('test text js 1');");
*/

/*
//get html
$html = webkitd_gethtml($fd);
if($html == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t get html'."\n");
}
echo "HTML: ".$html."\n";
*/

/*
//return image based on regex
$regex = "/.*pic.*jpg/i";
$imgdata = webkitd_returnimage($fd, $regex);
if($imgdata == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t return image, possibly due to regex failing'."\n");
}
else {
	$ifd = fopen('./img1.jpg','w');
	if($ifd){ 
		fwrite($ifd,$imgdata);
		fclose($ifd);
		echo ('wrote image'."\n");
	}
	else {
		echo ('didn\'t write image'."\n");
	}
}

/*
//return screenshot
$imgdata = webkitd_screenshot($fd, $regex);
if($imgdata == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t return screenshot'."\n");
}
else {
	$ifd = fopen('./screenshot1.jpg','w');
	if($ifd){ 
		fwrite($ifd,$imgdata);
		fclose($ifd);
		echo ('wrote image'."\n");
	}
	else {
		echo ('didn\'t write image'."\n");
	}
}
*/

/*
//fill in input text box
$selector = 'input[name=surname]';
$value = 'test 123';
$res = webkitd_inputfill($fd, $selector, $value);
if($res == true){
	echo ('inputfill ok'."\n");
}
else {
	echo ('inputfill failed, probably due to incorrect selector'."\n");
}
*/

/*
//check checkbox
$selector = 'input[name=terms]';
$res = webkitd_inputcheck($fd, $selector);
if($res == true){
	echo ('inputcheck ok'."\n");
}
else {
	echo ('inputcheck failed, probably due to incorrect selector'."\n");
}
*/

/*
//select dropdown
$selector = 'select[name=company] option[value=Trainline.com]';
$res = webkitd_inputselect($fd, $selector);
if($res == true){
	echo ('inputselect ok'."\n");
}
else {
	echo ('inputselect failed, probably due to incorrect selector'."\n");
}
*/

$selector = 'input[name=group]:radio';
$value = 'notagree';
$res = webkitd_inputchoose($fd, $selector, $value);
if($res == true){
	echo ('inputchoose ok'."\n");
}
else {
	echo ('inputchoose failed, probably due to incorrect selector'."\n");
}


$selector = 'input[name=Submit]';
$timeout = 30;
$res = webkitd_clicklink($fd, $selector, $timeout);
if($res == true){
	echo ('ok'."\n");
}
else {
	echo ('click link failed or timed out'."\n");
}

$html = webkitd_gethtml($fd);
if($html == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t get html'."\n");
}
echo "HTML: ".$html."\n";

//close the connection to be nice to the server
$res = webkitd_close($fd);
if($res == false){
	die('Error: webkitd couldn\'t close'."\n");
}
?>
