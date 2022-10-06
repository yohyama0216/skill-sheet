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

def build(numbersData,range,start,end):
    df = pd.DataFrame(numbersData)
    result = addPrevColumns(range, df)
    result.columns = createColumns('col',start,end)
    return result