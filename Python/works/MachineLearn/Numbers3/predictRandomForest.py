import numpy as np
import pandas as pd

from sklearn.ensemble import RandomForestRegressor
from sklearn.model_selection import train_test_split

def predictRandomForestRegression(X, y):
    # 変数定義
    #X = df[['LSTAT','CRIM']].values  # 説明変数
    #y = df['MEDV'].values     # 目的変数（住宅価格の中央値）
    
    # データ分割
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.4, random_state=1)

    # ランダムフォレスト回帰
    forest = RandomForestRegressor(n_estimators=100,
                               criterion='mse', 
                               max_depth=None, 
                               min_samples_split=2, 
                               min_samples_leaf=1, 
                               min_weight_fraction_leaf=0.0, 
                               max_features='auto', 
                               max_leaf_nodes=None, 
                               min_impurity_decrease=0.0, 
                               bootstrap=True, 
                               oob_score=False, 
                               n_jobs=None, 
                               random_state=None, 
                               verbose=0, 
                               warm_start=False, 
                               ccp_alpha=0.0, 
                               max_samples=None
                              )
    # モデル学習
    forest.fit(X_train, y_train)

    # 推論
    y_train_pred = forest.predict(X_train)
    y_test_pred  = forest.predict(X_test)

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