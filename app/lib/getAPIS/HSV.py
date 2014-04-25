from __future__ import division
import numpy as np
import Image, ImageFilter
import ImageEnhance
import ImageOps
import cv2

 
def img_to_matrix(img, tr, STANDARD_SIZE, verbose=False):
    #img = Image.open(filename) 
   #img = ImageOps.autocontrast(img, cutoff = 2)
    #img = ImageEnhance.Brightness(img)
   # img = img.enhance(0.8)
    
    if tr==2:
        img = img.resize((100,100), Image.ANTIALIAS)
        img = img.crop((5,5,95,95))
        img = img.resize(STANDARD_SIZE, Image.ANTIALIAS)
    if tr==1:
        img = img.resize((100,100), Image.ANTIALIAS)
        img = img.crop((15,15,85,85))
        img = img.resize(STANDARD_SIZE, Image.ANTIALIAS)
    if tr==0:
        img = img.resize(STANDARD_SIZE, Image.ANTIALIAS)
    img2 = img.convert('RGB')   
    H,S,V = HSV(img2)    
    r, g, b = img2.split()

    r = cv2.equalizeHist(np.array(r))
    g = cv2.equalizeHist(np.array(g))
    b = cv2.equalizeHist(np.array(b))
    
    img = np.hstack ((H,S,V,np.array(r), np.array(g), np.array(b)))

    return img

def flatten_image(img):
    s = img.shape[0] * img.shape[1]
    img_wide = img.reshape(1, s)
    return img_wide[0]
  
  
  
def HSV(img2):
    a = np.asarray(img2, float)

    R, G, B = a.T

    m = np.min(a,2).T
    M = np.max(a,2).T

    C = M-m #chroma
    Cmsk = C!=0

    # Hue
    H = np.zeros(R.shape, float)
    mask = (M==R)&Cmsk
    H[mask] = np.mod(60.0*(G-B)/C, 360)[mask]
    mask = (M==G)&Cmsk
    H[mask] = (60*(B-R)/C + 120)[mask]
    mask = (M==B)&Cmsk
    H[mask] = (60*(R-G)/C + 240)[mask]
    H *= 255.0
    H /= 360 # if you prefer, leave as 0-360, but don't convert to uint8

    # Value
    V = M

    # Saturation
    S = np.zeros(R.shape, float)
    S[Cmsk] = ((255.0*C)/V)[Cmsk]
    return H,S,V
    
def double(l):
    ll = []
    for i in l:
        ll = ll + [i,i,i] 
    return ll
    
def double0(l):
    ll = []
    for i in l:
        ll = ll + [i,i] 
    return ll