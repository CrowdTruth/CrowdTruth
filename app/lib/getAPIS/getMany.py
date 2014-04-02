from __future__ import print_function
import json
import requests
import random
import urllib2, os
import urllib, cStringIO
import predict_adopted
import numpy as np
import sys
import time
import cPickle as pickle
import urlparse
import string
import Image, ImageFilter
import cloudinary
import cloudinary.uploader
import cloudinary.api

DELAY = 2    
if len(sys.argv) < 4:
    print('wrong parameters', file=sys.stderr)
    exit()
################### 

try:
    Cloud_key = "265111278284499"
    Cloud_secret = "I62pcPP1G6UCRmm9al1A3KorgdU"   

    cloudinary.config( 
    cloud_name = "dnx94fr1w", 
    api_key = Cloud_key, 
    api_secret = Cloud_secret 
    )
except:
    print('error CLOUDINARY connecting', file=sys.stderr)
    

for iter in range(3, len(sys.argv)):
    time.sleep(DELAY)
    ImURL = sys.argv[iter]
    
    
    Features = {}
    Features['FacesNumber'] = {'rekognition':'_', 'skybiometry':'_', 'cloudinary':'_', }
    
    data = {}
    data['content'] = {}
    
    data['title'] = ImURL.split('/')[-1]
    data['domain'] = sys.argv[1]
    parsed_uri = urlparse.urlparse(ImURL )
    domain = '{uri.scheme}://{uri.netloc}/'.format(uri=parsed_uri)
    
    data['source'] = domain
    data['format'] = "Image"
    data['documentType'] = sys.argv[2]
    data['content']['URL'] = ImURL
    data['softwareAgent_id'] = 'featureRecognizer'
    data['softwareAgent_label'] = 'Recognizes different features [objects, scene, faces, ...] in the image'
    data['softwareAgent_configuration'] = 1


    #####################   REKOGNITION   ####################################
    Reck_key = "kVnLUSqqaPlnpzdq"
    Reck_secret = "smLk6SzFKAENwmc8"
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_2&urls="+ImURL + "&num_return=7"
        response = urllib2.urlopen(Comm)    
        data1 = json.load(response)    
        # print data1["scene_understanding"]
        Features['Scene'] = data1["scene_understanding"]
    except urllib2.URLError, e:
        print('error REKOGNITION a' + str(e), file=sys.stderr)
        
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_3&urls="+ImURL + "&num_return=7"
        response = urllib2.urlopen(Comm)
        data2 = json.load(response)    
        # print data2["scene_understanding"] 
        Features['Object'] = data2["scene_understanding"]
    except urllib2.URLError, e:
         print('error REKOGNITION b' + str(e), file=sys.stderr)
        
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=face_gender_aggressive&urls="+ImURL
        response = urllib2.urlopen(Comm)
        data3 = json.load(response)    
        # print (data3 )
        Features['FacesNumber']['rekognition'] = len(data3["face_detection"])
        Features['Faces'] = data3["face_detection"]
        if Features['FacesNumber']['rekognition'] > 0:
            Features['AverageSexRekognition'] = np.mean([float(a["sex"]) for a in data3["face_detection"]])
    except urllib2.URLError, e:
         print('error REKOGNITION c' + str(e), file=sys.stderr) 
    #########################   CLOUDINARY   ############################################    

    try:
        data4 = cloudinary.uploader.upload(ImURL, faces = True, colors=True)
        # print (json.dumps(data4, indent=1))
        if "faces" in data4:
            Features['FacesRekognition'] = data4["faces"]
            Features['FacesNumber']['cloudinary']  = len(data4["faces"])
        else:
            Features['FacesRekognition'] = 0
            Features['FacesNumber']['cloudinary'] = 0
        Features['ColorsHistogram'] = data4["colors"]
        Features['ColorsMain'] = data4["predominant"]["google"]
        
    except urllib2.URLError, e:
        print('error CLOUDINARY' + str(e), file=sys.stderr)  

    ###############################   SKYBIOMETRY   ############################################  
    Sky_key = "7e544588316542b382d286988b83d679"
    Sky_secret = "3bb713ca57b94c709d55c2add9d1c882"
    try:
        Comm = "http://api.skybiometry.com/fc/faces/detect.json?api_key="+Sky_key + "&api_secret="+Sky_secret+"&urls=" +ImURL + "&attributes=all"
        response = urllib2.urlopen(Comm)
        data5 = json.load(response)    
        l = []
        
        for a in data5["photos"][0]["tags"]:
           
            if "attributes" in a:
                if "face" in a["attributes"]:
                    if "gender" in a["attributes"]:
                        val = float(a["attributes"]["gender"]["confidence"])/100
                        if a["attributes"]["gender"]["value"] == "male":
                            val = val / 2 + 0.5
                        else:
                            val = 0.5 - val / 2
                        l.append(val)
        Features['FacesNumber']['skybiometry'] = len(l)
        if Features['FacesNumber']['skybiometry'] > 0:
            Features['AverageSexSkybiometry'] = np.mean(l)
        data['content']['height'] = data4['height']
        data['content']['width'] = data4['width']
        # print len(l), l

    except urllib2.URLError, e:
        print('error SKYBIOMETRY' + str(e), file=sys.stderr)

          
    #############################   LUKASZ.FLOWERS, BIRDS        ################################# 
    Features["Classifier"] = {}
    try:
        file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
        image = Image.open(file)
        Features["Classifier"]['Flowers'] = predict_adopted.predict("FLOWERS", image)
       
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr)
    try:
        file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
        image = Image.open(file)
        Features["Classifier"]['Birds'] = -1
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr)
    #############################   SAVE [ TO FILE + STDOUT ]       #################################    

      
    try:
      
        data['content']['features'] = Features     

        output = open('data.json', 'wb')
        output.write(json.dumps(data, indent = 2))
        output.close()
    except Exception, e:
        print('error FINALIZE' + str(e), file=sys.stderr)

    # output = open('data.json', 'rb')
    # data = json.load(output)
    # output.close()      

    url = 'http://jolicrowd.net/api/media/post'

    # http://localhost:8888/media/api/post
    # http://localhost:8888/api/v1/

    headers = {'content-type': 'application/json'}
    r = requests.post(url, data=json.dumps(data), headers=headers)
    print (r)
 
 
     
     
     
     
 
 
 
 
 
 
 
 
 
 
 
 
 
 

      
      
      
      
        