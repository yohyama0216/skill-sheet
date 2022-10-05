import numpy as np
import pandas as pd

df = pd.read_csv('AirPassengers.csv', index_col='Month', dtype={1: 'float'})
ts = df['#Passengers']

x = [] # train
y = [] # test (answer)
for i in range(0, 72):
    tmpX = []
    for j in range(0, 24):
        tmpX.append(ts[i+j])
    x.append(tmpX)
    
    tmpY = []
    for j in range(0, 12):
        tmpY.append(ts[24+i+j])
    y.append(tmpY)

x = np.array(x)
print(x)
y = np.array(y)
print(y)
x = x.reshape((x.shape[0], x.shape[1], 1))
y = y.reshape((y.shape[0], y.shape[1], 1))