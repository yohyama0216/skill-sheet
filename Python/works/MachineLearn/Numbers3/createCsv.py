import loadData as lD

data = lD.loadNumbers3Data()

import csv
# header = ['num100', 'num10','num1']
# vbody = [['cherry',50], ['strawberry',90], ['peach',350]]

with open('numbers3.csv', 'w', newline="") as f:
  writer = csv.writer(f)
  # writer.writerows(header)
  writer.writerows(data)

f.close()