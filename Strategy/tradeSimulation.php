<?php

class TradeSimulation
{
    private $PriceData = null;
    private $typeData = []; // @todo 取引はStrategyクラスにまとめる
    private $Positions = null; // @todo 取引はStrategyクラスにまとめる
    private $totalBenefit = 0; // @todo 取引はStrategyクラスにまとめる
    private $maxDrawdown = 0; // @todo 取引はStrategyクラスにまとめる
    private $tradeCount= 0; // @todo 取引はStrategyクラスにまとめる

    public function __construct()
    {
        $this->PriceData = new PriceData();
        $this->typeData = $this->generateTypeData();
        $this->Positions = new Positions();
    }

    private function generateTypeData()
    {
        $data = [];
        foreach($this->PriceData->get() as $price) {
            $data[] = ($price % 3 == 0) ? 'SELL' : 'BUY'; 
        }
        return $data;
    }
    
    public function setMaxDrawdown($drawdown)
    {
        if ($this->maxDrawdown > $drawdown) {
            $this->maxDrawdown = $drawdown;
        }
    }

    public function trade()
    {
        $priceData = $this->PriceData->get();
        foreach($priceData as $key => $price) {
            $this->settle($price);
            $this->entry($key,$price);
            $this->showPositions();
        }
    }

    public function getType($key)
    {
        return $this->typeData[$key];
    }

    /** 決済する */
    public function settle($currentPrice)
    {
        if ($this->Positions == null) {
            return ;
        }
        
        $benefit = $this->Positions->getAllCurrentBenefit($currentPrice);
        if ($benefit > 0) {
            $this->Positions->clearPositions();
            $this->setTotalBenefit($benefit);
        } else {
            $this->setMaxDrawdown($benefit);
        }
    }

    /** ポジションを持つ */
    public function entry($key,$currentPrice)
    {
        $type = $this->getType($key);
        if (empty($this->Positions) || $this->Positions->countPositions() < 5) {
            $this->Positions->addPosition(new Position(1, $type, $currentPrice));
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



    public function showPositions()
    {
        var_dump($this->Positions);
        echo '--------------'.PHP_EOL;
    }

    public function showPositionsTotalBenefit()
    {
        
        echo '含み損'.$this->Positions->getAllCurrentBenefit($this->PriceData->getLastPrice()).PHP_EOL;
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
        echo '全データ数'.count($this->PriceData->get()).PHP_EOL;
        echo '取引回数'.$this->tradeCount.PHP_EOL;
    }
}

// 価格データ
class PriceData
{
    private $priceData = []; // Priceクラスも作る？


    public function __construct()
    {
        $this->priceData = $this->generateDummyPriceData();
    }

    private function generateDummyPriceData()
    {
        $data = [];
        for($i=0;$i<100;$i++) {
            $data[] = rand(1,100) - rand(1,100);
        }
        return $data;
    }

    // public function setCurrentPrice($price)
    // {
    //     $this->currentPrice = $price;
    // }

    public function get()
    {
        return $this->priceData;
    }

    public function getLastPrice()
    {
        $priceData = $this->get(); // なんか微妙
        return end($priceData);
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
$tradeSimulation = new TradeSimulation();
$tradeSimulation->trade();
$tradeSimulation->showTotalBenefit();
$tradeSimulation->showPositionsTotalBenefit();
$tradeSimulation->showMaxDrawdown();
$tradeSimulation->showTradeCount();


