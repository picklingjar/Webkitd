#Webkitd - A headless webkit daemon written in python
"""
Copyright (C) 2010 The Pickling Jar Limited

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
"""

import spynner
import pyquery
import SocketServer
import sys
import tempfile
import platform
import os
from PyQt4.QtNetwork import QNetworkRequest
from PyQt4.QtCore import QUrl

class Webkitd(SocketServer.BaseRequestHandler):
	quit = 0
	url = None
	httpmethod = 'GET'

	def setup(self):
		Webkitd.url = None
		Webkitd.quit = 0
		Webkitd.browser = spynner.Browser(None, 3)
		Webkitd.browser.set_html_parser(pyquery.PyQuery)
		Webkitd.browser.user_agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6; en-US; rv:1.9.2.7) Gecko/20100701 Firefox/3.6.7'
		Webkitd.browser.referrer = None

	def find_key(self, dic, val):
		return [k for k, v in dic.iteritems() if v == val][0]

	def cmdurl(self, cmd):
		Webkitd.url = cmd
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdget(self,cmd):
		Webkitd.httpmethod = Webkitd.find_key(self,Webkitd.browser._operation_names, 'get')
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]

	def cmdpost(self,cmd):
		Webkitd.httpmethod = Webkitd.find_key(self,Webkitd.browser._operation_names, 'post')
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdsetpostval(self,cmd):
		self.request.send('TODO\n')
		print "%s << TODO" % self.client_address[0]
	
	def cmdsetcookies(self,cmd):
		Webkitd.browser.set_cookies(cmd)
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmddelcookie(self,cmd):
		self.request.send('TODO\n')
		print "%s << TODO" % self.client_address[0]

	def cmdgetcookies(self,cmd):
		cookies = Webkitd.browser.get_cookies(self)
		self.request.send(Webkitd.browser.get_cookies(cookies) + '\n' + '# End Netscape HTTP Cookie File\n')
		print "%s << %s " % (self.client_address[0], cookies + '\n' + '# End Netscape HTTP Cookie File\n')
	
	def cmdsetproxy(self,cmd):
		proxy = Webkitd.browser.set_proxy(cmd)
		Webkitd.cmdreturnproxy(self,cmd)
	
	def cmdreturnproxy(self,cmd):
		proxystruct = Webkitd.browser.manager.proxy()
		proxytype = proxystruct.type()
		proxy = '';

		if proxytype == 2 :
			proxy = '';
		elif proxytype == 1 :
			if proxystruct.user() == None or proxystruct.user() == '':
				proxy = "socks://%s:%d" % (proxystruct.hostName(), proxystruct.port())
			else :
				proxy = "http://%s:%s@%s:%d" % (proxystruct.user(), proxystruct.password(), proxystruct.hostName(), proxystruct.port())
				
		elif proxytype == 3 :
			if proxystruct.user() == None or proxystruct.user() == '':
				proxy = "http://%s:%d" % (proxystruct.hostName(), proxystruct.port())
			else :
				proxy = "http://%s:%s@%s:%d" % (proxystruct.user(), proxystruct.password(), proxystruct.hostName(), proxystruct.port())

		self.request.send("%s\n" % proxy)
		print "%s << %s" % (self.client_address[0], proxy)
	
	def cmdsetreferrer(self,cmd):
		Webkitd.browser.referrer = cmd
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdsetuseragent(self, cmd):
		Webkitd.browser.user_agent = cmd
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdnewcookiejar(self,cmd):
		self.request.send('TODO\n')
		print "%s << TODO" % self.client_address[0]
	
	def cmdexecute(self,cmd):
		req = QNetworkRequest()
		#req = Webkitd.browser.manager.QWebNetworkRequest()
		req.setUrl(QUrl(Webkitd.url))
		req.setRawHeader("Accept-Language","en-us,en;q=0.5");
		req.setRawHeader("Accept-Charset","ISO-8859-1,utf-8;q=0.7,*;q=0.7");
		req.setRawHeader("Keep-Alive","115");
		req.setRawHeader("Connection","keep-alive");
		if(Webkitd.browser.referrer != None and Webkitd.browser.referrer != ''):
			req.setRawHeader("Referer",Webkitd.browser.referrer);
		Webkitd.browser.load_request(req)
		if Webkitd.browser._reply_status == False:
			self.request.send('Error %d %s\n' % (Webkitd.browser._errorCode, Webkitd.browser._errorMessage))
			print "%s << Error %d %s" % (self.client_address[0],Webkitd.browser._errorCode, Webkitd.browser._errorMessage)
		else :
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]
	
	def cmdquit(self,cmd):
		Webkitd.quit = 1;
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdreturnhtml(self,cmd):
		print Webkitd.browser.html
		self.request.send('%s' % Webkitd.browser.html)
	
	def cmdreturnhtmlsoup(self,cmd):
		#soup len = 1 (object), so print to var and get len of that
		#Lame
		#print Webkitd.browser.html
		#Webkitd.browser.set_html_parser(pyquery.PyQuery)
		Webkitd.browser.soup = Webkitd.browser._get_soup()
		Webkitd.browser.soup = Webkitd.browser.soup.make_links_absolute(base_url=Webkitd.browser.url)
		#Webkitd.browser.soup = Webkitd.browser.soup.make_img_urls_absolute(base_url=Webkitd.browser.url)
		soup = "%s" % Webkitd.browser.soup
		length = len(soup);
		self.request.send('%d\n' % length)
		self.request.sendall('%s' % soup)
		print "%s << %s" % (self.client_address[0],soup)
	
	def cmdrunjs(self,cmd):
		#_runjs_on_jquery
		Webkitd.browser.runjs(cmd);
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]

	def cmdrunjquery(self,cmd):
		#_runjs_on_jquery
		Webkitd.browser._runjs_on_jquery('USERINPUT', cmd);
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdsslerrorsoff(self,cmd):
		Webkitd.browser.ignore_ssl_errors = True
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]
	
	def cmdsslerrorson(self,cmd):
		Webkitd.browser.ignore_ssl_errors = False
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]

	def cmdhtaccessusername(self,cmd):
		self.request.send('TODO\n')
		print "%s << TODO" % self.client_address[0]

	def cmdhtaccesspassword(self,cmd):
		self.request.send('TODO\n')
		print "%s << TODO" % self.client_address[0]

	def cmdreturnurl(self,cmd):
		self.request.send('%s\n' % Webkitd.browser.url)
		print "%s << %s" % (self.client_address[0],Webkitd.browser.url)

	def cmdreturnheader(self,cmd):
		#Lame
		header = "Status: %d\n%s" % (Webkitd.browser._httpstatuscode, Webkitd.browser._header)
		length = len(header);
		self.request.send('%d\n' % length)
		self.request.sendall('%s' % header)
		print "%s << %s" % (self.client_address[0],header)

	def cmdreturnimage(self,regex):
                imgnumberfunc = ("\
			function imgnumberfromregex() { \
				var i = 0; \
				for(i = 0; i < document.images.length; i++){ \
					var re = "+regex+"; \
					match = re.exec(document.images[i].src); \
					if(match){ \
						return i; \
		                        } \
        	        	} \
				return -1; \
			} \
			imgnumberfromregex(); \
		");
		imgnumber = Webkitd.browser.runjs(imgnumberfunc).toString()
		if imgnumber == -1:
			self.request.send('0\n')
			print "%s << 0 img byte count - regex failed to find match" % (self.client_address[0])
			return
		
		#Public Domain findpos funcs thanks to Peter-Paul Koch (quirksmode.org) & Alex Tingle (blog.firetree.net) 
		posfuncs = ("function findPosX(obj) { \
		    var curleft = 0; \
		    if(obj.offsetParent) \
			while(1) \
			{ \
			  curleft += obj.offsetLeft; \
			  if(!obj.offsetParent) \
			    break; \
			  obj = obj.offsetParent; \
			} \
		    else if(obj.x) \
			curleft += obj.x; \
		    return curleft; \
		} \
		function findPosY(obj){ \
		    var curtop = 0; \
		    if(obj.offsetParent) \
			while(1) \
			{ \
			  curtop += obj.offsetTop; \
			  if(!obj.offsetParent) \
			    break; \
			  obj = obj.offsetParent; \
			} \
		    else if(obj.y) \
			curtop += obj.y; \
		    return curtop; \
		} \
		")
		
		posfuncsx1 = ("findPosX(document.images["+imgnumber+"])");
		posfuncsy1 = ("findPosY(document.images["+imgnumber+"])");
		posfuncsx2 = ("findPosX(document.images["+imgnumber+"])+document.images["+imgnumber+"].width");
		posfuncsy2 = ("findPosY(document.images["+imgnumber+"])+document.images["+imgnumber+"].height");
		x1 = int(Webkitd.browser.runjs(posfuncs + posfuncsx1).toString())
		y1 = int(Webkitd.browser.runjs(posfuncs + posfuncsy1).toString())
		x2 = int(Webkitd.browser.runjs(posfuncs + posfuncsx2).toString())
		y2 = int(Webkitd.browser.runjs(posfuncs + posfuncsy2).toString())
		box = [x1, y1, x2, y2]

		tffileno, tfname= tempfile.mkstemp('.png')
		Webkitd.browser.snapshot(box).save(tfname)
		fd = os.fdopen(tffileno, 'r')
		self.request.send('%d\n' % os.path.getsize(tfname))
		print "%s << %d img byte count" % (self.client_address[0],os.path.getsize(tfname))
		fd.seek(0,0)
		self.request.sendall('%s' % (fd.read(os.path.getsize(tfname))))
		print "%s << IMG BINARY DATA" % self.client_address[0]
		fd.close()
		os.remove(tfname)
		
        def cmdinputfill(self,cmd):
		c = cmd.partition(" ")
		selector = c[0]
		value = c[2]
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+selector+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],selector)
		else:
			Webkitd.browser.fill(selector,value);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]
		
        def cmdinputcheck(self,cmd):
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+cmd+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],cmd)
		else:
			Webkitd.browser.check(cmd);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]

        def cmdinputuncheck(self,cmd):
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+cmd+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],cmd)
		else:
			Webkitd.browser.uncheck(cmd);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]

        def cmdinputchoose(self,cmd):
		c = cmd.partition(" ")
		selector = c[0]
		value = c[2]
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+selector+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],selector)
		else:
			Webkitd.browser.choose(selector, value);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]

        def cmdinputselect(self,cmd):
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+cmd+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],cmd)
		else:
			Webkitd.browser.select(cmd);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]

        def cmdformsubmit(self,cmd):
		self.request.send('todo\n')
		print "%s << todo" % self.client_address[0]

        def cmdscreenshot(self,cmd):
                tffileno, tfname= tempfile.mkstemp('.png')
                Webkitd.browser.snapshot().save(tfname)
                fd = os.fdopen(tffileno, 'r')
                self.request.send('%d\n' % os.path.getsize(tfname))
                print "%s << %d img byte count" % (self.client_address[0],os.path.getsize(tfname))
                fd.seek(0,0)
                self.request.sendall('%s' % (fd.read(os.path.getsize(tfname))))
                print "%s << IMG BINARY DATA" % self.client_address[0]
                fd.close()
                os.remove(tfname)

        def cmdclicklink(self,cmd):
		c = cmd.partition(" ")
		selector = c[0]
		timeout = c[2]
		valid = int(Webkitd.browser.runjs("(function () { if(_jQuery('"+selector+"').length){ return(1) } else { return(0) } })();").toString())
		if valid != 1:
			self.request.send('fail\n')
			print "%s << failed to find selector - %s" % (self.client_address[0],selector)
		else:
			Webkitd.browser.click_link(selector, timeout);
			self.request.send('ok\n')
			print "%s << ok" % self.client_address[0]

	def cmdstat(self,cmd):
		self.request.send('ok\n')
		print "%s << ok" % self.client_address[0]

	def cmdhelp(self,cmd):
		cmds = "%s\n" % Webkitd.cmds
		length = len(cmds);
		self.request.send('%d\n' % length)
		self.request.sendall('%s' % cmds)
		print "%s << %s" % (self.client_address[0],cmds)

	cmds = {
		1 : cmdurl,
		2 : cmdget,
		3 : cmdpost,
		4 : cmdsetpostval,
		5 : cmdsetcookies,
		6 : cmddelcookie,
		7 : cmdgetcookies,
		8 : cmdsetproxy,
		9 : cmdreturnproxy,
		10 : cmdsetreferrer,
		11 : cmdsetuseragent,
		12 : cmdnewcookiejar,
		13 : cmdexecute,
		14 : cmdquit,
		15 : cmdreturnhtml,
		16 : cmdrunjs,
		17 : cmdsslerrorsoff,
		18 : cmdsslerrorson,
		19 : cmdhtaccessusername,
		20 : cmdhtaccesspassword,
		21 : cmdreturnurl,
		22 : cmdreturnheader,
		23 : cmdreturnhtmlsoup,
		24 : cmdreturnimage,
		25 : cmdrunjquery,
		26 : cmdinputfill,
		27 : cmdinputcheck,
		28 : cmdinputuncheck,
		29 : cmdinputchoose,
		30 : cmdinputselect,
		31 : cmdformsubmit,
		32 : cmdscreenshot,
		33 : cmdclicklink,
		99 : cmdstat,
		0 : cmdhelp
	}

	def handle(self):
		print >> sys.stderr, "%s Connected" % self.client_address[0]

		Webkitd.httpmethod = Webkitd.find_key(self,Webkitd.browser._operation_names, 'get')

		# self.request is the TCP socket connected to the client
		while Webkitd.quit == 0:
			self.data = self.request.recv(4096).strip()
			command = self.data.partition(" ")
			cmd = int(command[0])
			print "%s >> Command %d (%s): %s" % (self.client_address[0], cmd,Webkitd.cmds[cmd], command[2])
		
			Webkitd.cmds[cmd](self,command[2])

		print >> sys.stderr, "%s Disconnected" % self.client_address[0]
		Webkitd.browser.close()
		
class ForkingServer(SocketServer.ForkingMixIn,SocketServer.TCPServer):
	pass

if __name__ == "__main__":
	HOST, PORT = "localhost", 3817
	SocketServer.TCPServer.allow_reuse_address = 1 
	if platform.system() == 'Darwin':
		#no forking on os x due to CoreFoundations - TODO
		server = SocketServer.TCPServer((HOST, PORT), Webkitd)
	else:
		server = ForkingServer((HOST, PORT), Webkitd)
	print >> sys.stderr, 'WebkitD server started: waiting for connections...'
	server.serve_forever()
