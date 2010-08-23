#!/usr/bin/python
import spynner
import os
import pyquery
from StringIO import StringIO

def images_filter(operation, url):
    return os.path.splitext(url)[1] not in (".jpg", ".png", ".gif")
  
browser = spynner.Browser(debug_level=spynner.INFO)
browser.set_html_parser(pyquery.PyQuery)
browser.set_url_filter(images_filter)
#browser.create_webview()
#browser.show()
browser.load("http://www.wordreference.com")
browser.select("#esen")
browser.fill("input[name=tranword]", "hola")
browser.click("input[name=B10]", True)
print "url:", browser.url
browser.soup.make_links_absolute(base_url=browser.url)
print "html:", browser.soup("#Otbl").html()
image = StringIO()
browser.download(browser.soup("img:first").attr('src'), outfd=image)
print "image length:", len(image.getvalue())
browser.close()
