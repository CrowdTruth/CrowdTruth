from __future__ import print_function
import json
import requests
import random
import urllib2, os
import urllib, cStringIO
# import numpy as np
import sys
import cPickle as pickle
import urlparse
import string
# import Image, ImageFilter


_domain = sys.argv[1]
_type = sys.argv[2]
N = int(sys.argv[3])
_keywords = sys.argv[4]

# URLSET = [
# "http://eofdreams.com/data_images/dreams/eye/eye-03.jpg",
# "http://upload.wikimedia.org/wikipedia/commons/f/fe/Beatty_Drive,_Congleton,_oil_painting_by_Natalia_Chernogolova-_DSC03517.JPG",
# "http://wallpoper.com/images/00/21/78/90/artistic-painting_00217890.jpg",
# "http://www.webmastergrade.com/wp-content/uploads/2011/05/Church-Painting.jpg",
# "http://ipicturee.com/wp-content/uploads/2013/10/Sunsets_wallpapers_316.jpg",
# "http://www.artisticrealism.com/paintings/peeled-grapefruit-painting-realistic.jpg",
# "http://puntito131.puntopressllc.netdna-cdn.com/wp-content/uploads/2013/02/animal-rights-animal-rights-32573282-1615-1312.jpg",
# "http://images6.fanpop.com/image/photos/33400000/animals-animal-rights-33409648-1024-768.jpg",
# "http://s.ngm.com/2008/03/animal-minds/img/animal-minds-hdr.jpg",
# "http://littlepnuts.com/home/wp-content/uploads/2013/06/Elephant-image.jpg",
# "http://3rdbillion.net/wp-content/uploads/2013/12/f6aa5d04531f8ff9416ace6e273405304.jpg"
# ]


key = "qKLXngOH"
        #https://www.rijksmuseum.nl/api/en/collection?key=qKLXngOH &format=json&ps=100&q=wolk&f=1&p=1&ps=12&type=painting&ii=0

#print (data)
URLLIST = []
page = 0

try:   
    while len(URLLIST) < N:      
        Comm = "https://www.rijksmuseum.nl/api/en/collection?key=" + key +"&format=json&ps="+str(100) +"&p=" + str(page) + "&q=" + _keywords +"&type=" + _type 
        response = urllib2.urlopen(Comm)    
        data0 = json.load(response)  
        for a in data0["artObjects"]:
            if len(URLLIST)>= N:
                break
            URLLIST.append(a["webImage"]["url"])
        page+=1
except Exception, e:
    print('We have__: '+ str(len(URLLIST)) + 'Exception getting' + str(e), file=sys.stderr)
    
print (json.dumps(URLLIST))
# print  (URLLIST)
# print (" :) ")
url = 'http://jolicrowd.net/api/media/post'

datalist = []   

for u in URLLIST:
    data = {}
    data['content'] = {} 
    data['domain'] = sys.argv[1]
    data['format'] = "Image"
    data['documentType'] = sys.argv[2]
    data['content']['URL'] = u
    data['softwareAgent_id'] = 'featurerecognizer'   
    data['processed'] = 0
    datalist.append(data)
#print (json.dumps(datalist, indent = 2))
#headers = {'content-type': 'application/json'}
#r = requests.post(url, data=json.dumps(datalist), headers=headers)
#print (r)
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

      
      
      
      
        