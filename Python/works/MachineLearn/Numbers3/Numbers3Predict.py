import loadData as lD
import buildData as bD
import predictRandomForest as RF

numbersData = []
# index = 0
# for p in range(0,4):
#     numbersData.append([p,p**3,1])

numbersData = lD.loadNumbers3Data()
print(numbersData)

df = bD.build(numbersData,range(1,400),1,1201) # endで一ずれる？
df.dropna(inplace=True)

X_columns = bD.createColumns('col',4,1200)
y_columns = bD.createColumns('col',1,3)
X = df[X_columns].values  # 説明変数
y = df[y_columns].values     # 目的変数

# print(df)
# RF.predictRandomForestRegression(X,y)

#結果：過去400回のデータを見たがR2がまったく上がらないので辞める。 20220528


