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
import smtplib
from keys import getkey
from email.MIMEMultipart import MIMEMultipart
from email.MIMEBase import MIMEBase
from email.MIMEText import MIMEText
from email import Encoders
import warnings
warnings.filterwarnings('ignore')

DELAY = 2    
WRITE_FILE = 0   
if len(sys.argv) < 5:
    print('wrong parameters', file=sys.stderr)
    exit()
################### 

try:
    Cloud_key = "265111278284499"
    Cloud_secret = getkey('cloudinary')   

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

#### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
#url = 'http://localhost/api/media/test'
#url = 'http://crowdtruth.org/api/media/test'
url = 'http://dev.crowdtruth.org/api/media/test'
#### !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

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
LOG = ""

def log (t):
    global LOG
    LOG += str(t) + "\n"

em = sys.argv[3]

for iter in range(4, len(sys.argv), 2):
    time.sleep(DELAY)
    ImURL = sys.argv[iter]
    parentID = sys.argv[iter+1]
  
    
    data = {}
    data['content'] = {}
    data['parents'] = [parentID]
    data['domain'] = sys.argv[1]
    data['tags'] = ['apiFeatures']
    #data['documentType'] = sys.argv[2]

    data['content']['URL'] = ImURL
    log(ImURL)
    log(parentID)


    #####################   REKOGNITION   ####################################
    Reck_key = "kVnLUSqqaPlnpzdq"
    Reck_secret = getkey('rekognition')
    data['softwareAgent_id'] = 'fr_rekognition'
    data['documentType'] = data['softwareAgent_id']
    data['softwareAgent_label'] = 'rekognition: [object, scene, faces]'
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_2&urls="+ImURL + "&num_return=5"
        response = urllib2.urlopen(Comm)    
        data1 = json.load(response)    
        
        data['softwareAgent_configuration'] = "scene"
        data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
        Features = {}
        Features['scene'] = []
        for that in data1["scene_understanding"]:
            Features['scene'].append({"label": that['label'], "score" : that['score']})
        data['content']['features'] = Features   
        th = 0.4
        data['threshold'] = th
        data['relevantFeatures'] = []
        for what in Features['scene']:
            if what['score'] > th:
                data['relevantFeatures'].append(what['label'])
        r = requests.post(url, data=json.dumps(data), headers=headers)
        if WRITE_FILE==1:
            output.write(json.dumps(data, indent = 2))  
        log(r)
        print (r)    
        closse(response)        
    except Exception, e:
        print('error REKOGNITION a' + str(e), file=sys.stderr)  
        log('error REKOGNITION a' + str(e))
    
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=scene_understanding_3&urls="+ImURL + "&num_return=5"
        response = urllib2.urlopen(Comm)
        data2 = json.load(response)    
        # print data2["scene_understanding"] 
        Features = {}
        data['softwareAgent_configuration'] = "object"
        data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
        Features['object'] = []
        for that in data2["scene_understanding"]["matches"]:
            Features['object'].append({"label": that['tag'], "score" : that['score']})

        data['content']['features'] = Features    
        th = 0.5
        data['threshold'] = th
        data['relevantFeatures'] = []
        for what in Features['object']:
            if what['score'] > th:
                data['relevantFeatures'].append(what['label'])



        r = requests.post(url, data=json.dumps(data), headers=headers)
        log(r)
        print (r)  
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))   
        closse(response)            
    except Exception, e:
         print('error REKOGNITION b' + str(e), file=sys.stderr)
         log('error REKOGNITION b' + str(e))
    
    try:
        Comm = "https://rekognition.com/func/api/?api_key="+Reck_key+"&api_secret="+Reck_secret+"&" + \
        "jobs=face_gender_aggressive&urls=" + ImURL
        response = urllib2.urlopen(Comm)
        data3 = json.load(response)    
        # print (data3 )
        data['softwareAgent_configuration'] = "faces"
        data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
        Features = []
        Features.append({   'label' : 'facesNumber', 'score' :  len(data3["face_detection"])    })
        Features.append({    'label' : 'facesDetails' , 'score' : data3["face_detection"]      })
        if len(data3["face_detection"]) > 0:
            Features.append({     'label' : 'averageSex', 'score' : np.mean([float(a["sex"]) for a in data3["face_detection"]])   })
        data['content']['features'] = {}
        data['content']['features']['faces'] = Features  
        data['relevantFeatures'] = data3["face_detection"]
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)  
        log(r)
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        closse(response)    
    except Exception, e:
         print('error REKOGNITION c' + str(e), file=sys.stderr) 
         log('error REKOGNITION c' + str(e))
    #########################   CLOUDINARY   ############################################    
    
    try:
        data4 = cloudinary.uploader.upload(ImURL, faces = True, colors=True)
        Features = {}
        # print (json.dumps(data4, indent=1))
        data['softwareAgent_id'] = 'fr_cloudinary'
        data['documentType'] = data['softwareAgent_id']
        data['softwareAgent_label'] = 'cloudinary: faces, colors'
        # data['softwareAgent_configuration'] = "faces"
        # if "faces" in data4:
        #     Features['Faces'] = data4["faces"]
        #     Features['FacesNumber']  = len(data4["faces"])
        # else:
        #     Features['Faces'] = 0
        #     Features['FacesNumber'] = 0
        # r = requests.post(url, data=json.dumps(data), headers=headers)
        # if WRITE_FILE==1:
        #     output.write(json.dumps(data, indent = 2))  
        Features = {}
        data['softwareAgent_configuration'] = "colors"
        data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
        Features['ColorsHistogram'] = []
        Features['ColorsMain'] = []
        for fi in data4["predominant"]["google"]:
            Features['ColorsMain'].append({'label': fi[0], 'score' : fi[1]})
        for fi in data4["colors"]:
            Features['ColorsHistogram'].append({'label': fi[0], 'score' : fi[1]})
        data['content']['features'] = Features  
        th = 0.40  
        data['threshold'] = th
        data['relevantFeatures'] = []
        for fi in data4["predominant"]["google"]:
            if fi[1] > 100*th:
                data['relevantFeatures'].append(fi[0])


        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)   
        log(r)
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        closse(response)    
    except Exception, e:
        print('error CLOUDINARY' + str(e), file=sys.stderr)  
        log('error CLOUDINARY' + str(e))
    
    ###############################   SKYBIOMETRY   ############################################  
    Sky_key = "7e544588316542b382d286988b83d679"
    Sky_secret = getkey('skybiometry')
    data['softwareAgent_id'] = 'fr_skybiometry'
    data['documentType'] = data['softwareAgent_id']
    data['softwareAgent_label'] = 'skybiometry: faces'
    data['softwareAgent_configuration'] = "faces"
    data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
    Features = {}
    Features['faces'] = []
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
        Features['faces'].append ({  'label'  :  'facesNumber', 'score'  :  len(l) })
        if len(l) > 0:
            Features['faces'].append({ 'label' : 'averageSex', 'score' : np.mean(l) })
        data['relevantFeatures'] = l
        #data['content']['height'] = data4['height']
        #data['content']['width'] = data4['width']
        data['content']['features'] = Features    
        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)   
        log(r)  
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  

    except Exception, e:
        print('error SKYBIOMETRY' + str(e), file=sys.stderr)
        log('error SKYBIOMETRY' + str(e))   
    #############################   LUKASZ.FLOWERS, BIRDS        ################################# 
    data['softwareAgent_id'] = 'fr_classifier'
    data['documentType'] = data['softwareAgent_id']
    data['softwareAgent_label'] = 'classifier: set of classes'
    data['softwareAgent_configuration'] = "flowers"
    data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
    data['content'] = {}
    data['parents'] = [parentID]
    data['content']['URL'] = ImURL
    Features = {}
    Features["classifier"] = []
    try:    
        file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
        image = Image.open(file)
        val =  predict_adopted.predict("FLOWERS", image) / 100.0 
        Features["classifier"].append({'label' : 'flowers', 'score' : val })
        data['content']['features'] = Features  
        th = 0.55  
        data['threshold'] = th
        data['relevantFeatures'] = []
        if val > th:
            data['relevantFeatures'].append("flowers")

        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r)     
        log(r) 
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))             
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr)
        log('error CLASSIFIER FLOWERS' + str(e))
        
    Features = {}
    Features["classifier"] = []
    try:
    #test
        data['softwareAgent_configuration'] = "birds"
        #file = cStringIO.StringIO(urllib.urlopen(ImURL).read())
       # image = Image.open(file)
        val = predict_adopted.predict("BIRDS", image) / 100.0 
        Features["classifier"].append({ 'label' : 'birds', 'score' : val})
        data['documentType'] = data['softwareAgent_id'] + "-" + data['softwareAgent_configuration']
        data['content']['features'] = Features   
        th = 0.55  
        data['threshold'] = th
        data['relevantFeatures'] = []
        if val > th:
            data['relevantFeatures'].append("birds")


        r = requests.post(url, data=json.dumps(data), headers=headers)
        print (r) 
        log(r) 
        if WRITE_FILE==1:        
            output.write(json.dumps(data, indent = 2))  
        
    except Exception, e:
        print('error CLASSIFIER' + str(e), file=sys.stderr) 
        log('error CLASSIFIER BIRDS' + str(e))
    
    #############################   SAVE [ TO FILE + STDOUT ]       #################################    

    if WRITE_FILE==1:
        output.close()
    # except Exception, e:
        # print('error FINALIZE' + str(e), file=sys.stderr)
    # data['processed'] = 1
    # output = open('data.json', 'rb')
    # data = json.load(output)
    # output.close()      

   
 



gmail_user = "crowdwatson@gmail.com"
with open('/var/yo.txt') as f:
         for line in f:
             gmail_pwd = line.strip()
             break



def mail(to, subject, text, attach):
   msg = MIMEMultipart()

   msg['From'] = gmail_user
   msg['To'] = to
   msg['Subject'] = subject

   msg.attach(MIMEText(text))
   mailServer = smtplib.SMTP("smtp.gmail.com", 587)
   mailServer.ehlo()
   mailServer.starttls()
   mailServer.ehlo()
   mailServer.login(gmail_user, gmail_pwd)
   mailServer.sendmail(gmail_user, to, msg.as_string())
   mailServer.close()

if len(em) > 4:
    mail(em,
       "Images preprocessing",
       "Your preprocessing is finished! \n Log: \n" + LOG,
       "___")
 
print ("Finished! - email sent to", em)
log("Finished")
 
 
 
 
 
 
 

      
      
      
      
        