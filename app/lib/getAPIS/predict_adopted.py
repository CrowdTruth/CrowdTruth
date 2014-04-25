import os
import sys
import pandas as pd
import numpy as np
from time import sleep
import pylab as pl
import random
import Image, ImageFilter
import sklearn
import colorsys
import pickle
import mlpy
import ImageEnhance
import sklearn.utils.sparsetools._graph_validation
import ImageOps
from HSV import double, flatten_image, img_to_matrix
#from sklearn import svm
from sklearn.decomposition import RandomizedPCA, PCA

def predict(classifier, image):
    W = 50
    K = 4
    STANDARD_SIZE = (W, W)
    try:
        svm = mlpy.LibSvm.load_model("classifiers/" + classifier + ".cffpart1")
        with open("classifiers/" + classifier + ".cffpart2", 'rb') as handle:
                pca = pickle.load(handle)     
        test_data = []    
        img = img_to_matrix(image, 2, STANDARD_SIZE)
        img = flatten_image(img)
        test_data.append(img)
        test_data = pca.transform(test_data)
        pp = svm.pred_probability(test_data)  
        return int(round(pp[0][1]*100))
    except Exception , e:
        print str(e)

        return -1