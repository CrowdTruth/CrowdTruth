#!/usr/bin/python

from __future__ import print_function
import json
import requests
import random
import urllib2, os
import urllib, cStringIO
#import predict_adopted
import numpy as np
import sys
import cPickle as pickle
import urlparse
import string
import Image, ImageFilter
import cloudinary
import cloudinary.uploader
import cloudinary.api


_domain = sys.argv[1]
_type = sys.argv[2]
N = int(sys.argv[3])
_keywords = sys.argv[4]

URLSET = [
"http://eofdreams.com/data_images/dreams/eye/eye-03.jpg",
"http://upload.wikimedia.org/wikipedia/commons/f/fe/Beatty_Drive,_Congleton,_oil_painting_by_Natalia_Chernogolova-_DSC03517.JPG",
"http://wallpoper.com/images/00/21/78/90/artistic-painting_00217890.jpg",
"http://www.webmastergrade.com/wp-content/uploads/2011/05/Church-Painting.jpg",
"http://ipicturee.com/wp-content/uploads/2013/10/Sunsets_wallpapers_316.jpg",
"http://www.artisticrealism.com/paintings/peeled-grapefruit-painting-realistic.jpg",
"http://puntito131.puntopressllc.netdna-cdn.com/wp-content/uploads/2013/02/animal-rights-animal-rights-32573282-1615-1312.jpg",
"http://images6.fanpop.com/image/photos/33400000/animals-animal-rights-33409648-1024-768.jpg",
"http://s.ngm.com/2008/03/animal-minds/img/animal-minds-hdr.jpg",
"http://littlepnuts.com/home/wp-content/uploads/2013/06/Elephant-image.jpg",
"http://3rdbillion.net/wp-content/uploads/2013/12/f6aa5d04531f8ff9416ace6e273405304.jpg"
]

data = {}
data['urls'] = [URLSET[i] for i in np.random.choice(len(URLSET), 2)]

print (json.dumps(data))
# http://localhost:8888/media/api/post
# http://localhost:8888/api/v1/


url = 'http://jolicrowd.net/api/media/post'
headers = {'content-type': 'application/json'}
#r = requests.post(url, data=json.dumps(data), headers=headers)
#print (r)

 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

      
      
      
      
        
