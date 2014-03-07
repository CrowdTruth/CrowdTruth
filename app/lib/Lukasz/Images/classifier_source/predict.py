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
from sklearn import svm
from sklearn.decomposition import RandomizedPCA, PCA

W = 50
K = 4
STANDARD_SIZE = (W, W)
if len(sys.argv) <3:
    print "Give 2 arguments [classifier, image_path]"
    exit()

svm = mlpy.LibSvm.load_model(sys.argv[1]+".cffpart1")
with open(sys.argv[1]+".cffpart2", 'rb') as handle:
    pca = pickle.load(handle)

f = sys.argv[2]
lf = len(f)
images = [f] # one image


if f[lf-4:] == ".txt":    # .txt file containing pathes
    images = []
    with open(f) as ff:
        for line in ff:
            images.append(line.strip())
    
test_data = []
for image in images:
    img = img_to_matrix(image, 2, STANDARD_SIZE)
    img = flatten_image(img)
    test_data.append(img)

test_data = pca.transform(test_data)
pp = svm.pred_probability(test_data)
for aa in pp:
    print int(round(aa[1]*100))