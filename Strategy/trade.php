<?php

class Trading
{
    private $priceData = []; // @todo PriceDataクラスを作成する
    private $typeData = []; // @todo 取引はStrategyクラスにまとめる
    private $Positions = null; // @todo 取引はStrategyクラスにまとめる
    private $currentPrice = 0; // @todo PriceDataクラスを作成する
    private $totalBenefit = 0; // @todo 取引はStrategyクラスにまとめる
    private $maxDrawdown = 0; // @todo 取引はStrategyクラスにまとめる
    private $tradeCount= 0; // @todo 取引はStrategyクラスにまとめる

    public function __construct()
    {
        $this->priceData = $this->generatePriceData();
        $this->typeData = $this->generateTypeData();
        $this->Positions = new Positions();
    }

    private function generatePriceData()
    {
        $data = [];
        for($i=0;$i<100;$i++) {
            $data[] = rand(1,100) - rand(1,100);
        }
        return $data;
    }

    private function generateTypeData()
    {
        $data = [];
        foreach($this->priceData as $price) {
            $data[] = ($price % 3 == 0) ? 'SELL' : 'BUY'; 
        }
        return $data;
    }

    public function setCurrentPrice($price)
    {
        $this->currentPrice = $price;
    }
    
    public function setMaxDrawdown($drawdown)
    {
        if ($this->maxDrawdown > $drawdown) {
            $this->maxDrawdown = $drawdown;
        }
    }

    public function trade()
    {
        foreach($this->priceData as $key => $price) {
            $this->setCurrentPrice($price);
            $this->settle();
            $this->entry($key);
            $this->showPositions();
        }
    }

    public function getType($key)
    {
        return $this->typeData[$key];
    }

    /** 決済する */
    public function settle()
    {
        if ($this->Positions == null) {
            return ;
        }
        
        $benefit = $this->Positions->getAllCurrentBenefit($this->currentPrice);
        if ($benefit > 0) {
            $this->Positions->clearPositions();
            $this->setTotalBenefit($benefit);
        } else {
            $this->setMaxDrawdown($benefit);
        }
    }

    /** ポジションを持つ */
    public function entry($key)
    {
        $type = $this->getType($key);
        if (empty($this->Positions) || $this->Positions->countPositions() < 5) {
            $this->Positions->addPosition(new Position(1, $type, $this->currentPrice));
            $this->addTradeCount();
        }
    }

    public function addTradeCount()
    {
        $this->tradeCount++; 
    }

    public function setTotalBenefit($benefit)
    {
        $this->totalBenefit += $benefit;
    }

    public function getTotalBenefit()
    {
        return $this->totalBenefit;
    }

    public function getPriceData()
    {
        return $this->priceData;
    }

    public function showPositions()
    {
        var_dump($this->Positions);
        echo '--------------'.PHP_EOL;
    }

    public function showPositionsTotalBenefit()
    {
        echo '含み損'.$this->Positions->getAllCurrentBenefit(end($this->priceData)).PHP_EOL;
    }

    public function showTotalBenefit()
    {
        echo '通算'.$this->totalBenefit.PHP_EOL;
    }

    public function showMaxDrawdown()
    {
        echo '最大ドローダウン'.$this->maxDrawdown.PHP_EOL;
    }

    public function showTradeCount()
    {
        echo '全データ数'.count($this->priceData).PHP_EOL;
        echo '取引回数'.$this->tradeCount.PHP_EOL;
    }
}

/** ポジションのコレクションクラス */
class Positions
{
    /** ポジションの配列 */
    private $positionList = [];

    public function __construct()
    {

    }
    
    /** リストに追加する */
    public function addPosition($Position)
    {
        $this->positionList[] = $Position;
    }

    /** リストを初期化する */
    public function clearPositions()
    {
        $this->positionList = [];
    }

    /** 現在のポジションの損益の合計を取得する */
    public function getAllCurrentBenefit($currentPrice)
    {
        $total = 0;
        foreach($this->positionList as $position) {
            $total += $position->getCurrentBenefit($currentPrice);
        }
        return $total;
    }

    /** 建玉の数を取得する */
    public function countPositions()
    {
        return count($this->positionList);
    }

    /** ポジションの一覧を表示する */
    public function show()
    {
        if (empty($this->positionList)) {
            echo 'ポジションなし'.PHP_EOL;
        } else {
            var_dump($this->positionList);
        }
       
    }
}

/** 各建玉 */
class Position
{    
    /** ロット */
    private $lot = 0;

    /** 売りor買い */
    private $type = '';

    /** 取得時の値段 */
    private $gotPrice = 0;

    public function __construct($lot, $type, $gotPrice)
    {
        $this->lot = $lot;
        $this->type = $type;
        $this->gotPrice = $gotPrice;
    }

    /** 現在価格での損益を取得する */
    public function getCurrentBenefit($nowPrice)
    {
        if ($this->type == 'BUY') {
            return $nowPrice - $this->gotPrice;
        } else if ($this->type == 'SELL')  {
            return  $this->gotPrice - $nowPrice;
        }
    }
}

// mainメソッドにまとめる？
// 実際はシミュレーターなのでSimulateでは？
$trading = new Trading();
$trading->trade();
$trading->showTotalBenefit();
$trading->showPositionsTotalBenefit();
$trading->showMaxDrawdown();
$trading->showTradeCount();


