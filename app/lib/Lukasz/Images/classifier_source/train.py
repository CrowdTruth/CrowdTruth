import os
import sklearn.utils.sparsetools._graph_validation
import sys
from pandas import crosstab
import numpy as np
from time import sleep
import random
import Image, ImageFilter
import colorsys
import pickle
import mlpy
import ImageEnhance
import ImageOps


    
from HSV import double, flatten_image, img_to_matrix
#from sklearn import svm
from sklearn.decomposition import RandomizedPCA, PCA

W = 50
K = 4
STANDARD_SIZE = (W, W)
#--------------------------------------------------------------------
if len(sys.argv) <4:
    print "Give 3 arguments [positive, negative, output]"
    exit()
    
img_dir_pos = sys.argv[1]
img_dir_neg = sys.argv[2]
print "Positive:", img_dir_pos, " |  Negative:", img_dir_neg
imagesp = [img_dir_pos + "\\" + f for f in os.listdir(img_dir_pos)] 
imagesn = [img_dir_neg + "\\" + f for f in os.listdir(img_dir_neg)]    
images =  imagesn + imagesp
labels = np.array(["false" ]* len(imagesn) + ["true" ]* len(imagesp) )
print ">> converting"
data = []


for image in images:
    img = img_to_matrix(image, 1, STANDARD_SIZE)
    img = flatten_image(img)
    i = img
    data.append(img)
    print '.'.format(),
#--------------------------------------------------------------------
   
data = np.array(data)
avgerr = 0.0;
isf = True
print "total: ", len(data), "images"

cnt = 0
indic = []
for j in range(len(data)):
    indic += [j%K]
random.shuffle(indic, lambda:0.24)
is_train = np.array(indic)
#--------------------------------------------------------------------

C_min = [0.0]*K
g_min = [0.0]*K
NC_min = [0]*K
for i in range(K):
    print "FOLD: ", i
    y = np.where(np.array(labels)=="true", 1, 0)
    train_x, train_y = data[is_train!=i], y[is_train!=i]
    test_x, test_y = data[is_train==i], y[is_train==i]
    print "test:", len(test_x), "images"
    
    #--------------------------------------------------------------------
    C_range = 5.0 ** np.arange(-9, -5) 
    gamma_range = 5.0 ** np.arange(-5, 9)
    # C_range = [0.0000005]
    #gamma_range = [20000]
    for NC in [50]:
        print ">> FEAT.",
        pca = PCA(n_components=NC, whiten=True)
        train_x = pca.fit_transform(train_x)
        test_x = pca.transform(test_x)  
        #--------------------------------------------------------------------
        print ">> CLASS", NC
        errmin = 100000.0;
        for C in C_range:
            for g in gamma_range:
                print C,g
                svm = mlpy.LibSvm(svm_type='c_svc', kernel_type='poly', degree=1, eps=0.05, gamma=g, C = C, probability = True)
                svm.learn(train_x, train_y)
                pred = svm.pred(test_x)
                
       #--------------------------------------------------------------------  
                a = crosstab(test_y, pred, rownames = ["Actual"], colnames=["predicted"])
                pp = svm.pred_probability(test_x)
                pred2 = []
                for aa in pp:
                    cc = 2
                    if aa[1] > 0.80:
                        cc = 1    
                    if aa[1] < 0.50:
                        cc = 0        
                    pred2 = pred2 + [cc]
                a2 = crosstab(test_y, np.array(pred2), rownames = ["Actual"], colnames=["predicted"])
                if isf:
                    fscore = a
                    isf = False
                else:
                    fscore = fscore + a
                if len(C_range)==1:
                    print a     
                err = 1-sklearn.metrics.f1_score(test_y, pred)
                if (len(a.columns)==1):
                    err = 1
               
                err3=1
                if (len(a2.columns)>1 and a2.columns.values.tolist()[1]==1 and len(a.columns)==2):
                    err3 = - (1.0 * a2.loc[1,1])/(a.loc[1,0] + a.loc[1,1]) \
                           + (3.0 * a2.loc[0,1])/(a.loc[0,1] + a.loc[0,0]) \
                           + (1.0 * a2.loc[0,2])/(a.loc[0,1] + a.loc[0,0])  
                    err3 
                ee = err + err3
                if ee<1:
                    print a
                    print a2
                    print "                                Error = ", int(round(err*100)), "%"
                    print "                                Error = ", int(round(err3*100)), "%"
                    
                    print "                                Error = ", int(round(ee*100)), "%"
                if errmin > ee:
                    errmin = ee
                    C_min[i] = C
                    g_min[i] = g
                    NC_min[i] = NC
    avgerr += errmin;
    #--------------------------------------------------------------------
print "----------------------------\n"
print C_min, g_min, NC_min
C = np.median(C_min)
g = np.median(g_min) 
NC = int(np.median(NC_min))    
print "final ", C, g  , NC  
print "Average error = ", int(round(avgerr*100) / K), "%",
print " {} OK, {} WRONG \n\n".format(fscore.loc[0,0]+fscore.loc[1,1], fscore.loc[0,1]+fscore.loc[1,0])
print fscore
#--------------------------------------------------------------------
print "\n\n>> test -> learning full \n\n"
y = np.where(np.array(labels)=="true", 1, 0)
pca = PCA(n_components=NC, whiten=True)
train_x = pca.fit_transform(data)
svm = mlpy.LibSvm(svm_type='c_svc', kernel_type='poly', degree=1, eps=0.0000001, gamma=g, C =C, probability=True)
svm.learn(train_x, y)
mlpy.LibSvm.save_model(svm, sys.argv[3]+".cffpart1")
with open(sys.argv[3]+".cffpart2", 'wb') as handle:
    pickle.dump(pca, handle)
print "done, saved :", sys.argv[3]













