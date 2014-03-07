from bs4 import BeautifulSoup
import requests
import re
import random
import urllib2
import os
import sys
import string


def get_soup(url):
    return BeautifulSoup(requests.get(url).text)

print """ Parameters: OPTIONS FOLDER_NAME QUERY 
    OPTIONS:
        R -> Random
        
        +
        
        P -> Paintings
        D -> Drawings
        S -> Sketches
        N -> Pure query
        
        
    FOLDER_NAME:
        E.G: images
        
    QUERY
        cloud flower   -> cloud flower
        @ cloud flower -> "cloud flower"
        
    -------------------   
    EXAMPLES:
        RN blue blue eye -> Random images of << blue eye >> to ./blue folder
        RP blue @ blue eye -> Random paintings of << "blue eye" >> to ./blue folder
        
     
"""
    
if len(sys.argv) < 3:
    print "wrong parameters" 
    exit()
    
params = sys.argv[1]

folder = sys.argv[2]    
if not os.path.exists(folder):
    os.makedirs(folder)
	
#random parameters
N = 3
ITERS = 10

# keywords
Q = {'P': "painting art", 'D': "drawing art", 'S': "sketch art", "N":""}

query = ""
i = 3;
full = 0;
while (i < len(sys.argv)):
    if sys.argv[i] == "@":
		full = 1
		i = 4
		continue
    query = query + sys.argv[i] + " "
    i = i + 1
	

	
image_type = query
if full == 1:
	query = '"' + query + '"'
    

for what in "PDSN":
    if not 'R' in params:
        if what in params:
            print "  DOWNLOADING >>> " + query + " " + Q[what]
                
            url = "http://www.bing.com/images/search?q=" + query + " " + Q[what]  + "&qft=+filterui:imagesize-large&FORM=R5IR3"

            soup = get_soup(url)
            images = [a['src2'] for a in soup.find_all("img", {"src2": re.compile("mm.bing.net")})]

            for img in images:
                print ".",
                raw_img = urllib2.urlopen(img).read()
                cntr = len([i for i in os.listdir(folder) if image_type in i]) + 1
                f = open(folder + "/" + image_type + "_"+ str(cntr) +".jpg", 'wb')
                f.write(raw_img)
                f.close()
    if 'R' in params:
         if what in params:
            for i in range (ITERS):
                image_type = "random"
                print "  DOWNLOADING >>> " + query
                    
                url = "http://www.bing.com/images/search?q=" + query + " " + Q[what] +  ' '.join(random.choice(string.ascii_lowercase) for x in range(N)) + "&qft=+filterui:imagesize-large&FORM=R5IR3"

                soup = get_soup(url)
                images = [a['src2'] for a in soup.find_all("img", {"src2": re.compile("mm.bing.net")})]

                number = 0
                for img in images:
                    print ".",
                    number += 1
                    if number == 5:
                        break
                    raw_img = urllib2.urlopen(img).read()
                    cntr = len([i for i in os.listdir(folder) if image_type in i]) + 1
                    f = open(folder + "/" + image_type + "_"+ str(cntr) +".jpg", 'wb')
                    f.write(raw_img)
                    f.close()
                print '.',
                
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        