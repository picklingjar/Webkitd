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
define('CMDSCREENSHOT', 32);
define('CMDCLICKLINK', 33);
define('CMDSTAT', 99);
define('CMDHELP', 0);

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
	if($len == 0) return false; //regex failed to find match

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

//26
function webkitd_inputfill($fd, $selector, $value){
	fwrite($fd, CMDINPUTFILL." ".$selector." ".$value."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//27
function webkitd_inputcheck($fd, $selector){
	fwrite($fd, CMDINPUTCHECK." ".$selector."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//28
function webkitd_inputuncheck($fd, $selector){
	fwrite($fd, CMDINPUTUNCHECK." ".$selector."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//29
function webkitd_inputchoose($fd, $selector){
	fwrite($fd, CMDINPUTCHOOSE." ".$selector."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//30
function webkitd_inputselect($fd, $selector){
	fwrite($fd, CMDINPUTSELECT." ".$selector."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}

//32
function webkitd_screenshot($fd){
	fwrite($fd, CMDSCREENSHOT."\n");
	$img = '';
	$len = fgets($fd, 1024);
	if($len == 0) return false;

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

//33
function webkitd_clicklink($fd,$selector){
	fwrite($fd, CMDCLICKLINK." ".$selector."\n");
	$str = fgets($fd, 1024);
	if(trim($str) == 'ok'){
		return true;
	}
	return false;
}
?>
