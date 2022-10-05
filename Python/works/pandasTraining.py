import pandas as pd

def createColumns(prefix, start, end):
    result = []
    for n in range(start,end):
        name = prefix + str(n)
        result.append(name)

    return result

def addPrevColumns(prevs, df):
    result = df
    for prev in prevs:
        tmp = df.shift(-prev)
        result = pd.concat([result,tmp],axis=1)
    
    return result

def buildData(df,range):
    df = pd.DataFrame(numbersData)
    result = addPrevColumns(range, df)
    result.columns = createColumns('col',1,9)


numbersData = []
index = 0
for p in range(0,4):
    for m in range(1, 4): 
        numbersData.append([p,m,1])

# print(numbersData)


print(result)


