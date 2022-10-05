# 
# https://di-acc2.com/programming/python/13877/
# データ前処理
import numpy as np
import pandas as pd

# データ可視化
import matplotlib.pyplot as plt
import seaborn as sns
plt.style.use("ggplot")

# グラフの日本語表記対応
from matplotlib import rcParams
rcParams["font.family"]     = "sans-serif"
rcParams["font.sans-serif"] = "Hiragino Maru Gothic Pro"

# データセット読込
from sklearn.datasets import load_boston
boston = load_boston()

# DataFrame作成
df = pd.DataFrame(boston.data)
df.columns = boston.feature_names
df["MEDV"] = boston.target




""" グラフ可視化 """
# # flatten：1次元の配列を返す、argsort：ソート後のインデックスを返す
# sort_idx = X_train.flatten().argsort()

# # 可視化用に加工
# X_train_plot  = X_train[sort_idx]
# Y_train_plot  = y_train[sort_idx]
# train_predict = forest.predict(X_train_plot)

# # 可視化
# plt.scatter(X_train_plot, Y_train_plot, color='lightgray', s=70, label='Traning Data')
# plt.plot(X_train_plot, train_predict, color='blue', lw=2, label="Random Forest Regression")    

# # グラフの書式設定
# plt.xlabel('LSTAT（低所得者の割合）')
# plt.ylabel('MEDV（住宅価格の中央値）')
# plt.legend(loc='upper right')
# plt.show()

from sklearn.metrics import r2_score            # 決定係数
from sklearn.metrics import mean_squared_error  # RMSE

# 予測値(Train）
y_train_pred = forest.predict(X_train)

# 予測値（Test)
y_test_pred = forest.predict(X_test)

# 平均平方二乗誤差(RMSE)
print('RMSE 学習: %.2f, テスト: %.2f' % (
        mean_squared_error(y_train, y_train_pred, squared=False), # 学習
        mean_squared_error(y_test, y_test_pred, squared=False)    # テスト
      ))

# 決定係数(R^2)
print('R^2 学習: %.2f, テスト: %.2f' % (
        r2_score(y_train, y_train_pred), # 学習
        r2_score(y_test, y_test_pred)    # テスト
      ))

# 出力結果
# RMSE 学習: 1.28, テスト: 3.33
# R^2 学習: 0.98, テスト: 0.88


# ToDo グリッドサーチ、外れ値削除、null除去？
#　

