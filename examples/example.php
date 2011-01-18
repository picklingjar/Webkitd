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
$globalerrcode = null;
$globalerrstr = null;

//connect to webkitd
$fd = webkitd_connect('127.0.0.1', 3817);
if($fd == false){
	die('Error: webkitd couldn\'t connect'."\n");
}

/*
stat - check webkitd is alive
$res = webkitd_stat($fd);
if($res == false){
        die('Error: webkitd couldn\'t close'."\n");
}
else {
	echo "STAT: ".$res;
}
*/

//$res = webkitd_help($fd);
//echo $res;

//set url
$res = webkitd_url($fd, 'http://127.0.0.1/wkd/index.php/');
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

/*
//turn ssl errors on (off by default)
$res = webkitd_sslerrorson($fd);
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t turn ssl errors on'."\n");
}
*/

/*
//turn ssl errors off (off by default)
$res = webkitd_sslerrorsoff($fd);
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t turn ssl errors off'."\n");
}
*/


/*
//set htaccess username
$res = webkitd_htaccessusername($fd, 'admin');
if($res == false){
	echo 'Error: webkitd couldn\'t set htaccess username'."\n";
	webkitd_close($fd);
	die(1);
}
*/

/*
//set htaccess pasword
$res = webkitd_htaccesspassword($fd, 'password');
if($res == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t set htaccess password'."\n");
}
*/

//execute request - i.e. download page
$res = webkitd_execute($fd);
if($res == false){
	echo ('Error: webkitd couldn\'t execute - errcode: '.$globalerrcode.' errstr: '.$globalerrstr."\n");
	webkitd_close($fd);
	die(1);
}

//echo webkitd_gethtml($fd);

//check for iframes
$res = webkitd_iframecount($fd);
if($res == false || $res == 0){
	echo "Iframe Count: 0";
}
else {
	echo "Iframe Count: ".$res;
	for($i = 0; $i<$res; $i++){
		echo "IFrame $i: ".webkitd_iframesourceurl($fd,$i)."\n";
	}
	//webkitd_iframeselect 0 = mainFrame, so +1 onto which frame you want to select
	webkitd_iframeselect($fd, 0 + 1);

	/*
	//iframe in an iframe
	$res = webkitd_iframecount($fd);
	echo "Iframe Count: ".$res;
	for($i = 0; $i<$res; $i++){
		echo "IFrame $i: ".webkitd_iframesourceurl($fd,$i)."\n";
	}
	*/

	echo "IFRAME HTML: ".webkitd_gethtml($fd)."\n";
	$res = webkitd_runjs($fd, "document.getElementById('ppp').innerHTML = 'basic js'");
	echo "IFRAME HTML: ".webkitd_gethtml($fd)."\n";
	$res = webkitd_runjs($fd, "(function(){ _jQuery('#ppp').html('jquery text')})();");
	echo "IFRAME HTML: ".webkitd_gethtml($fd)."\n";
	webkitd_iframeselect($fd, 0); //mainframe
	webkitd_iframeselect($fd, 0 + 1); //first iframe
	echo "IFRAME HTML: ".webkitd_gethtml($fd)."\n";
	$res = webkitd_runjs($fd, "(function(){ _jQuery('#ppp').html('jquery text 2')})();");
	echo "IFRAME HTML: ".webkitd_gethtml($fd)."\n";

}

/*
//back to mainframe
//webkitd_iframeselect($fd, 0);
*/
//webkitd_runjs($fd, "_jQuery('#content').html('jquery text 2');");
//echo "html: ".webkitd_gethtml($fd);



//webkitd_showbrowser($fd, 10);
/*
//return url as it might not be the one we set due to 301s/302s etc
$url = webkitd_returnurl($fd);
if($url == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t return url'."\n");
}
echo "url: ".$url."\n";
*/

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
$regex = "/" . ".*testimage.*" . "/i";
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
*/

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
$selector = 'select[name=company] option[value=ThePicklingJar.com]';
$res = webkitd_inputselect($fd, $selector);
if($res == true){
	echo ('inputselect ok'."\n");
}
else {
	echo ('inputselect failed, probably due to incorrect selector'."\n");
}
*/

/*
//select radio button
$selector = 'input[name=group]:radio';
$value = 'notagree';
$res = webkitd_inputchoose($fd, $selector, $value);
if($res == true){
	echo ('inputchoose ok'."\n");
}
else {
	echo ('inputchoose failed, probably due to incorrect selector'."\n");
}
*/

//$res = webkitd_runjs($fd, "var firebug = document.createElement('script'); firebug.setAttribute('src','https://getfirebug.com/firebug-lite.js'); document.body.appendChild(firebug); (function(){ if(window.firebug.version){firebug.init();} else {setTimeout(arguments.callee);}})(); void(firebug);");

//webkitd_showbrowser($fd, 5);

/* 
//click link
$selector = 'input[name=Submit]';
$timeout = 30;
$res = webkitd_clicklink($fd, $selector, $timeout);
if($res == true){
	echo ('ok'."\n");
}
else {
	echo ('click link failed or timed out'."\n");
}
*/

/*$html = webkitd_gethtml($fd);
if($html == false){
	webkitd_close($fd);
	die('Error: webkitd couldn\'t get html'."\n");
}
echo "HTML: ".$html."\n";
*/


/*
$res = webkitd_getcookies($fd);
echo "COOKIES";
echo $res;
*/

/*
//XXX Setcookies sets all cookies - not just one
$res = webkitd_setcookies($fd, webkitd_getcookies($fd).".firefox.com\tTRUE\t/\tFALSE\t946684799\tMOZILLA_ID\t100103");
//echo $res;
*/

//close the connection to be nice to the server
$res = webkitd_close($fd);
if($res == false){
	die('Error: webkitd couldn\'t close'."\n");
}
?>
