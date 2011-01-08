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

run_test();

function run_test(){
	//connect to webkitd
	$fd = webkitd_connect('127.0.0.1', 3817);
	if($fd == false){
		die('Error: webkitd couldn\'t connect'."\n");
	}

	//$res = webkitd_help($fd);
	//echo $res;


	//set url
	$res = webkitd_url($fd, 'http://www.thepicklingjar.com/');
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

	$res = webkitd_runjquery($fd, "_jQuery('#content').html('test text js 2');");
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
