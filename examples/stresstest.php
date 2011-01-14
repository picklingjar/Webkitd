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



for($i = 0; $i < 1000; $i++){
	//connect to webkitd
	$fd = webkitd_connect('127.0.0.1', 3817);
	if($fd == false){
		die('Error: webkitd couldn\'t connect'."\n");
	}

	//set url
	$res = webkitd_url($fd, 'http://127.0.0.1/wkd/index.php');
	if($res == false){
		webkitd_close($fd);
		die('Error: webkitd couldn\'t set url'."\n");
	}

	echo "I = $i\n";
	$res = webkitd_execute($fd);
	if($res == false){
		echo ('Error: webkitd couldn\'t execute - errcode: '.$globalerrcode.' errstr: '.$globalerrstr."\n");
		webkitd_close($fd);
		die(1);
	}

	//$html = webkitd_gethtml($fd);

	//close the connection to be nice to the server
	$res = webkitd_close($fd);
	if($res == false){
		die('Error: webkitd couldn\'t close'."\n");
	}
}
?>
