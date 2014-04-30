from __future__ import print_function
import json
import requests
import random
import urllib2, os
import urllib, cStringIO
#import predict_adopted
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
WRITE_FILE = 0
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
   
def closse(response):
    try: # where handle is the object you get from urllib2's urlopen
                response.fp._sock.recv = None
    except: # in case it's not applicable, ignore this.
        pass
#url = 'http://jolicrowd.net/api/media/post'
url = 'http://127.0.0.1:8888/api/media/test'
url = 'http://127.0.0.1/api/media/test'
url = 'http://jolicrowd.net/api/media/test'

headers = {'content-type': 'application/json'}


# data1 = {
# "_id": "entity/image/art/painting/1",
# "format": "image",
# "domain": "art",
# "documentType": "painting",
# "source": "lh4.ggpht.com",
# "parents": "entity/image/art/painting/0",
# "content": {
# "url": "http://lh4.ggpht.com/5EAw9FBBwVmOwHhFvXCUupfoMZjd3-NHj8HdDvVecJEgFHfKeofAfpEEEvj4MTn3JBW-hhLABubkbchqMVYjdL0nxIo=s0",
# "features" : 'yes55'
# },
# "user_id": "lukasz"
# }



if WRITE_FILE==1:
    output = open('data.json', 'wb')
# output.write(json.dumps(data1, indent = 2)) 
# r = requests.post(url, data=json.dumps(data1), headers=headers)
# print (r)   


 

for iter in range(3, len(sys.argv), 2):
    time.sleep(DELAY)
    ImURL = sys.argv[iter]
    parentID = sys.argv[iter+1]
  
    
    data = {}
    data['content'] = {}
    data['parents'] = [parentID]
    data['domain'] = sys.argv[1]
    data['documentType'] = sys.argv[2]
    data['content']['URL'] = ImURL
    


    #####################   REKOGNITION   ####################################
    Reck_key = "kVnLUSqqaPlnpzdq"
    Reck_secret = "smLk6SzFKAENwmc8"
    data['softwareAgent_id'] = 'fr_rekognition'
    data['softwareAgent_label'] = 'rekognition: [object, scene, faces]'
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_2&urls="+ImURL + "&num_return=7"
        response = urllib2.urlopen(Comm)    
        data1 = json.load(response)    
        # print data1["scene_understanding"]
        
        data['softwareAgent_configuration'] = "scene"
        Features = {}
        Features['scene'] = data1["scene_understanding"]
        data['content']['features'] = Features   
        #print (data)
        r = requests.post(url, data=json.dumps(data), headers=headers)
        if WRITE_FILE==1:
            output.write(json.dumps(data, indent = 2))  
        
        print (r)    
        closse(response)        
    except Exception, e:
        print('error REKOGNITION a' + str(e), file=sys.stderr)  
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_3&urls="+ImURL + "&num_return=7"
        response = urllib2.urlopen(Comm)
        data2 = json.load(response)    
        # print data2["scene_understanding"] 
        Features = {}
        data['softwareAgent_configuration'] = "object"
        Features['object'] = data2["scene_understanding"]
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)  
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))   
        closse(response)            
    except Exception, e:
         print('error REKOGNITION b' + str(e), file=sys.stderr)
        
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=face_gender_aggressive&urls="+ImURL
        response = urllib2.urlopen(Comm)
        data3 = json.load(response)    
        # print (data3 )
        data['softwareAgent_configuration'] = "faces"
        Features = {}
        Features['FacesNumber'] = len(data3["face_detection"])
        Features['Faces'] = data3["face_detection"]
        if Features['FacesNumber']> 0:
            Features['AverageSex'] = np.mean([float(a["sex"]) for a in data3["face_detection"]])
        data['content']['features'] = Features  
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)  
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        closse(response)    
    except Exception, e:
         print('error REKOGNITION c' + str(e), file=sys.stderr) 
    #########################   CLOUDINARY   ############################################    

    try:
        data4 = cloudinary.uploader.upload(ImURL, faces = True, colors=True)
        Features = {}
        # print (json.dumps(data4, indent=1))
        data['softwareAgent_id'] = 'fr_cloudinary'
        data['softwareAgent_label'] = 'cloudinary: faces, colors'
        data['softwareAgent_configuration'] = "faces"
        if "faces" in data4:
            Features['Faces'] = data4["faces"]
            Features['FacesNumber']  = len(data4["faces"])
        else:
            Features['Faces'] = 0
            Features['FacesNumber'] = 0
        r = requests.post(url, data=json.dumps(data), headers=headers)
        if WRITE_FILE==1:
            output.write(json.dumps(data, indent = 2))  
        Features = {}
        data['softwareAgent_configuration'] = "colors"
        Features['ColorsHistogram'] = data4["colors"]
        Features['ColorsMain'] = data4["predominant"]["google"]
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)   
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        closse(response)    
    except Exception, e:
        print('error CLOUDINARY' + str(e), file=sys.stderr)  

    ###############################   SKYBIOMETRY   ############################################  
    Sky_key = "7e544588316542b382d286988b83d679"
    Sky_secret = "3bb713ca57b94c709d55c2add9d1c882"
    data['softwareAgent_id'] = 'fr_skybiometry'
    data['softwareAgent_label'] = 'skybiometry: faces'
    data['softwareAgent_configuration'] = "faces"
    Features = {}
    try:
        Comm = "http://api.skybiometry.com/fc/faces/detect.json?api_key="+Sky_key + "&api_secret="+Sky_secret+"&urls=" +ImURL + "&attributes=all"
        response = urllib2.urlopen(Comm)
        
        data5 = json.load(response)    
        closse(response)
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
        Features['FacesNumber'] = len(l)
        if Features['FacesNumber'] > 0:
            Features['AverageSex'] = np.mean(l)
        data['content']['height'] = data4['height']
        data['content']['width'] = data4['width']
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)     
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  

    except Exception, e:
        print('error SKYBIOMETRY' + str(e), file=sys.stderr)

          
    #############################   LUKASZ.FLOWERS, BIRDS        ################################# 
    data['softwareAgent_id'] = 'fr_classifier'
    data['softwareAgent_label'] = 'classifier: set of classes'
    data['softwareAgent_configuration'] = "flowers"
    data['content'] = {}
    data['parents'] = [parentID]
    data['content']['URL'] = ImURL
    Features = {}
    Features["Classifier"] = {}
    try:
        file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
        image = Image.open(file)
        Features["Classifier"]['Flowers'] = predict_adopted.predict("FLOWERS", image)
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)      
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))             
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr)
        
    Features = {}
    Features["Classifier"] = {}
    try:
        data['softwareAgent_configuration'] = "birds"
        #file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
       # image = Image.open(file)
        Features["Classifier"]['Birds'] = predict_adopted.predict("BIRDS", image)
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)  
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr)   
    
    #############################   SAVE [ TO FILE + STDOUT ]       #################################    

      
    # try:
      
        # data['content']['features'] = Features     

    if WRITE_FILE==1:
        output.close()
    # except Exception, e:
        # print('error FINALIZE' + str(e), file=sys.stderr)
    # data['processed'] = 1
    # output = open('data.json', 'rb')
    # data = json.load(output)
    # output.close()      

   
 
 
     
     
     
     
 
 
 
 
 
 
 
 
 
 
 
 
 
 

      
      
      
      
        