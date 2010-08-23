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


//defines for commands from webkitd.py
define('CMDURL', 1);
define('CMDGET', 2);
define('CMDPOST', 3);
define('CMDSETPOSTVAL', 4);
define('CMDSETCOOKIE', 5);
define('CMDDELCOOKIE', 6);
define('CMDRETURNCOOKIE', 7);
define('CMDSETPROXY', 8);
define('CMDRETURNPROXY', 9);
define('CMDSETREFERRER', 10);
define('CMDSETUSERAGENT', 11);
define('CMDNEWCOOKIEJAR', 12);
define('CMDEXECUTE', 13);
define('CMDQUIT', 14);
define('CMDRETURNHTML', 15);
define('CMDRUNJS', 16);
define('CMDSSLERRORSOFF', 17);
define('CMDSSLERRORSON', 18);
define('CMDHTACCESSUSERNAME', 19);
define('CMDHTACCESSPASSWORD', 20);
define('CMDRETURNURL', 21);
define('CMDRETURNHEADER', 22); /* last request header, could be an img  :/ */
define('CMDRETURNHTMLSOUP', 23);
define('CMDRETURNIMAGE', 24);
define('CMDRUNJQUERY', 25);
define('CMDINPUTFILL', 26);
define('CMDINPUTCHECK', 27);
define('CMDINPUTUNCHECK', 28);
define('CMDINPUTCHOOSE', 29);
define('CMDINPUTSELECT', 30);
define('CMDFORMSUBMIT', 31);
define('CMDSTAT', 99);
define('CMDHELP', 0);

$globalerrcode = null;
$globalerrstr = null;

run_test();

function webkitd_connect($webkitdip = '127.0.0.1', $webkitdport = 3817){
	$fd = fsockopen($webkitdip, $webkitdport, $errno, $errstr, 10);
	return $fd;
}

//0
function webkitd_help($fd){
	fwrite($fd, CMDHELP."\n");
	$str = '';
	$len = fgets($fd, 1024);
	$tlen = 0;
	while($tlen < $len) {
		$fragment = fread($fd, $len);
		$str .= $fragment;
		$tlen += strlen($fragment);
	}
	return $str;
}

//1
function webkitd_url($fd, $url){
	fwrite($fd, CMDURL." ".$url."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//2
function webkitd_get($fd){
	fwrite($fd, CMDGET."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//3
function webkitd_post($fd){
	fwrite($fd, CMDPOST."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//4
function webkitd_setpostval($fd,$key,$val){
	fwrite($fd, CMDSETPOSTVAL." ".$key." ".$val."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

// 8
function webkitd_setproxy($fd,$proxy){
	fwrite($fd, CMDSETPROXY." ".$proxy."\n");
	$str = fgets($fd, 1024);
	return $str;
}

//9
function webkitd_returnproxy($fd){
	fwrite($fd, CMDRETURNPROXY."\n");
	$str = fgets($fd, 1024);
	return $str;
}

//10
function webkitd_setreferrer($fd, $ref){
	fwrite($fd, CMDSETREFERRER." ".$ref."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//11
function webkitd_setuseragent($fd, $ua){
	fwrite($fd, CMDSETUSERAGENT." ".$ua."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//13
function webkitd_execute($fd){
	global $globalerrcode;
	global $globalerrstr;
	fwrite($fd, CMDEXECUTE."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	$p = explode(" ",$str,3);
	$globalerrcode = $p[1];
	$globalerrstr = $p[2];
	return false;
}

//14
function webkitd_close($fd){
	fwrite($fd, CMDQUIT."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		fclose($fd);
		return true;
	}
	fclose($fd);
	return false;
}

//15
function webkitd_gethtml($fd){
	fwrite($fd, CMDRETURNHTMLSOUP."\n");
	$html = '';
	$len = fgets($fd, 1024);
	$tlen = 0;
	while($tlen < $len) {
		$fragment = fread($fd, $len);
		$html .= $fragment;
		$tlen += strlen($fragment);
	}
	return $html;
}

//16
function webkitd_runjs($fd, $js){
	fwrite($fd, CMDRUNJS." ".$js."\n");
	$str = fgets($fd, 1024);
	return $str;
}

//21
function webkitd_returnurl($fd){
	fwrite($fd, CMDRETURNURL."\n");
	$str = fgets($fd, 1024);
	return $str;
}

//22
function webkitd_returnheader($fd){
	fwrite($fd, CMDRETURNHEADER."\n");
	$header = '';
	$len = fgets($fd, 1024);
	$tlen = 0;
	while($tlen < $len) {
		$fragment = fread($fd, $len);
		$header .= $fragment;
		$tlen += strlen($fragment);
	}
	return $header;
}

//24
function webkitd_returnimage($fd,$regex){
	fwrite($fd, CMDRETURNIMAGE." ".$regex."\n");
	$img = '';
	$len = fgets($fd, 1024);
	//echo "imagelen ".$len;
	$tlen = 0;
	while($tlen < $len) {
		//echo "tlen: ".$tlen;
		//echo "len: ".$len;
		$fragment = fread($fd, $len);
		//echo $fragment;
		$img .= $fragment;
		$tlen += strlen($fragment);
	}
	return $img;
}

//25
function webkitd_runjquery($fd, $js){
	fwrite($fd, CMDRUNJQUERY." ".$js."\n");
	$str = fgets($fd, 1024);
	return $str;
}


function run_test(){
	//connect to webkitd
	$fd = webkitd_connect('127.0.0.1', 3817);
	if($fd == false){
		die('Error: webkitd couldn\'t connect'."\n");
	}

	//$res = webkitd_help($fd);
	//echo $res;


	//set url
	$res = webkitd_url($fd, 'http://www.thepicklingjar.com/code/webkitd/test302.php');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set url'."\n");
	}

	//set useragent
	$res = webkitd_setuseragent($fd, 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set user agent'."\n");
	}

	//set proxy
	$res = webkitd_setproxy($fd, 'http://127.0.0.1:8080');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set proxy'."\n");
	}
	//get current proxy
	$proxy = webkitd_returnproxy($fd);

	//reset proxy
	$res = webkitd_setproxy($fd, '');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set proxy'."\n");
	}

	//set referrer
	$res = webkitd_setreferrer($fd,'http://www.testreferrer.com');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set referrer'."\n");
	}

	//set get request
	$res = webkitd_get($fd);
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set http get'."\n");
	}

	//run request
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

	//run jquery, NOTE use _jQuery not _$
	$res = webkitd_runjs($fd, "_jQuery('#test').html('test text js1');");

	//returns tag soup
	$html = webkitd_gethtml($fd);
	if($html == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t get html'."\n");
	}
	echo "HTML: ".$html."\n";

	//return image based on regex
	$regex = '';
	$imgdata = webkitd_returnimage($fd, $regex);
	if($imgdata == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t return image'."\n");
	}
	else {
		$ifd = fopen('./img.jpg','w');
		if($ifd){ 
			fwrite($ifd,$imgdata);
			fclose($ifd);
			echo ('wrote image'."\n");
		}
		else {
			echo ('didn\'t write image'."\n");
		}
	}

	$res = webkitd_runjquery($fd, "_jQuery('#test').html('test text js 2');");
	$html = webkitd_gethtml($fd);
	if($html == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t get html'."\n");
	}
	echo "HTML: ".$html."\n";

	/*
	$res = webkitd_inputfill("input[name=enit]", "hola");
	$res = webkitd_inputcheck("input[name=enit]", "hola");
	$res = webkitd_inputuncheck("input[name=enit]", "hola");
	$res = webkitd_inputchoose("input[name=enit]", "hola");
	$res = webkitd_inputselect("input[name=enit]", "hola");
	$res = webkitd_formsubmit("input[name=enit]", "hola");
	*/

	//close the connection to be nice to the server
	$res = webkitd_close($fd);
	if($res == false){
		die('Error: webkitd couldn\'t close'."\n");
	}
}
?>
