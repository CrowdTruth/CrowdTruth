from __future__ import print_function
import json
import requests
import random
import urllib
import urllib2, cStringIO
import os
import numpy as np
import Image, ImageFilter
import sys
import cPickle as pickle
import urlparse
import string
import cloudinary
import cloudinary.uploader
import cloudinary.api
import predict_adopted

if  True:
    if len(sys.argv) < 4:
        print('wrong parameters', file=sys.stderr)
        exit()
    ################### 
    ImURL = sys.argv[1]


    Features = {}
    data = {}
    data['content'] = {}

    data['title'] = ImURL.split('/')[-1]
    data['domain'] = sys.argv[2]
    parsed_uri = urlparse.urlparse(ImURL )
    domain = '{uri.scheme}://{uri.netloc}/'.format(uri=parsed_uri)
  
    data['source'] = domain
    data['format'] = "Image"
    data['documentType'] = sys.argv[3]
    data['content']['URL'] = ImURL
    data['softwareAgent_id'] = 'featureRecognizer'
    data['softwareAgent_label'] = 'Recognizes different features [objects, scene, faces, ...] in the image'
    data['softwareAgent_configuration'] = 1




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
    except Exception , e:
        print('error REKOGNITION a'+ str(e), file=sys.stderr)
        
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_3&urls="+ImURL + "&num_return=7"
        response = urllib2.urlopen(Comm)
        data2 = json.load(response)    
        # print data2["scene_understanding"] 
        Features['Object'] = data2["scene_understanding"]
    except Exception , e:
         print('error REKOGNITION b'+ str(e), file=sys.stderr)
        
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=face_gender_aggressive&urls="+ImURL
        response = urllib2.urlopen(Comm)
        data3 = json.load(response)    
        # print (data3 )
        Features['FacesNumber'] = len(data3["face_detection"])
        Features['Faces'] = data3["face_detection"]
        if Features['FacesNumber'] > 0:
            Features['AverageSex'] = np.mean([float(a["sex"]) for a in data3["face_detection"]])
    except Exception , e:
         print('error REKOGNITION c'+ str(e), file=sys.stderr) 
    #########################   CLOUDINARY   ############################################    

    try:
        data4 = cloudinary.uploader.upload(ImURL, faces = True, colors=True)
        #print (data4)
        #print (json.dumps(data4, indent=1))
        if "faces" in data4:
            Features['Faces2'] = data4["faces"]
            Features['FacesNumber2'] = len(data4["faces"])
        else:
            Features['Faces2'] = 0
            Features['FacesNumber2'] = 0
        Features['ColorsHistogram'] = data4["colors"]
        Features['ColorsMain'] = data4["predominant"]["google"]
        
    except Exception , e:
        print('error CLOUDINARY ' + str(e), file=sys.stderr)  

    ###############################   SKYBIOMETRY   ############################################  
    Sky_key = "7e544588316542b382d286988b83d679"
    Sky_secret = "3bb713ca57b94c709d55c2add9d1c882"
    try:
        Comm = "http://api.skybiometry.com/fc/faces/detect.json?api_key="+Sky_key + "&api_secret="+Sky_secret+"&urls=" + ImURL + "&attributes=all"
        response = urllib2.urlopen(Comm)
        data5 = json.load(response)    
        #print (json.dumps(data5, indent=1))
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
        Features['FacesNumber3'] = len(l)
        if Features['FacesNumber3'] > 0:
            Features['AverageSex2'] = np.mean(l)
        # print len(l), l

    except Exception , e:
        print('error SKYBIOMETRY '+ str(e), file=sys.stderr)

          
    #############################   LUKASZ.FLOWERS, BIRDS        ################################# 
    try:
        file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
        image = Image.open(file)
    
        Features['Classifier_Flowers_Score'] = predict_adopted.predict("FLOWERS", image)
        Features['Classifier_Birds_Score'] = -1
    except Exception , e:
        print('error CLASSIFIER ' + str(e), file=sys.stderr)
    #############################   SAVE [ TO FILE + STDOUT ]       #################################    

      

    data['content']['height'] = data4['height']
    data['content']['width'] = data4['width']
    data['content']['features'] = Features     

    output = open('data.json', 'wb')
    output.write(json.dumps(data, indent = 2))
    output.close()

#output = open('data.json', 'rb')
#data = json.load(output)
#output.close()      

url = 'http://jolicrowd.net/api/media/post'

# http://localhost:8888/media/api/post
# http://localhost:8888/api/v1/

headers = {'content-type': 'application/json'}
r = requests.post(url, data=json.dumps(data), headers=headers)
print (r)
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 

      
      
      
      
        
