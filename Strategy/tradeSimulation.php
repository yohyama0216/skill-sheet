<?php

class TradeSimulation
{
    private $PriceData = null;
    private $Strategy = null;

    public function __construct()
    {
        $this->PriceData = new PriceData();
        $this->Strategy = new Strategy(100000,1);
    }

    public function simulate()
    {
        $priceData = $this->PriceData->get(); // 微妙
        $this->Strategy->trade($priceData); // 微妙
    }
}

// トレード戦略
class Strategy
{
    const POSITION_COUNT_MAX = 6;
    
    private $initial = 0;
    private $tradeLot = 0; // ロット？金額？　微妙
    private $Positions = null; 
    private $currentPrice = 0;
    private $Settlement = null;
    private $Entry = null;

    public function setCurrentPrice($currentPrice)
    {
        $this->currentPrice = $currentPrice; 
    }

    public function __construct($initial,$tradeLot)
    {
        $this->initial = $initial;
        $this->tradeLot = $tradeLot;
        $this->Positions = new Positions();
        $this->Settlement = new Settlement(200);
        $this->Entry = new Entry();
    }

    public function trade($priceData)
    {
        foreach($priceData as $price) {
            $this->setCurrentPrice($price);
            $this->settle();
            $this->entry();
        }
        $this->showPositions();
        $this->showTotalBenefit();
        $this->showPositionsTotalBenefit($price);
        $this->showMaxDrawdown();
        $this->showTradeCount($priceData); // 微妙
    }

    /** 決済する */
    public function settle()
    {
        $this->Settlement->settlePairPosition($this->Positions, $this->currentPrice, 500);
    }



    /** 決済の判定 */
    public function canSettle($currentPrice)
    {
        
    }

    /** ポジションを持つ */
    public function entry()
    {
        $totalBenefit = $this->Settlement->getTotalBenefit();
        $this->Entry->entryNormal($this->Positions,$this->currentPrice,$this->initial,$totalBenefit);
        // 直観的にはinitial不要では？　
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
        echo '含み損'.$this->Positions->getAllCurrentBenefit($this->currentPrice).PHP_EOL;
    }

    public function showTotalBenefit()
    {
        echo '通算'.$this->Settlement->getTotalBenefit().PHP_EOL;
    }

    public function showMaxDrawdown()
    {
        echo '最大ドローダウン'.$this->Settlement->getMaxDrawdown().PHP_EOL;
    }

    public function showTradeCount($priceData)
    {
        echo '全データ数'.count($priceData).PHP_EOL;
        echo '取引回数'.($this->Settlement->getTradeCount() + $this->Entry->getTradeCount()).PHP_EOL;
    }
}

// エントリー
class Entry
{
    private $maxPositionCount = 6;
    private $tradeCount= 0;
    
    public function addTradeCount()
    {
        $this->tradeCount++; 
    }
    
    public function getTradeCount()
    {
        return $this->tradeCount;
    }

    public function entryNormal(&$Positions,$currentPrice,$initial,$totalBenefit)
    {
        $entryType = $this->setEntryType($Positions);
        if ($this->canEntry($Positions,$currentPrice,$initial,$totalBenefit)) {
            $Positions->addPosition(new Position(1, $entryType, $currentPrice));
            $this->addTradeCount();
        }
    }

    public function setEntryType(&$Positions)
    {
        $entryTypes = $Positions->getAllEntryType();
        if ($entryTypes['SELL'] >= 3) {
            return 'BUY';
        } else if ($entryTypes['BUY'] >= 3) {
            return 'SELL';
        } else {
            return (rand(1,10) % 2 == 0) ? 'SELL' : 'BUY'; 
        }
    }

    /** ポジションを追加する条件 */
    public function canEntry(&$Positions,$currentPrice,$initial,$totalBenefit)
    {
        $currentPositionsBenefit = $Positions->getAllCurrentBenefit($currentPrice);
        // なんかinitialが微妙
        $isLargerThanZeroTotal = (($initial + $totalBenefit + $currentPositionsBenefit) > 0);
        return $isLargerThanZeroTotal && ($Positions->countPositions() < $this->maxPositionCount);
    }
} 

// 決済ロジック
class Settlement
{
    private $totalBenefit = 0;
    private $maxDrawdown = 0;
    private $tradeCount= 0;
    private $width = 200;

    public function __construct($width)
    {
        $this->width = $width;
    }

    public function addTradeCount()
    {
        $this->tradeCount++; 
    }

    private function setTotalBenefit($benefit)
    {
        $this->totalBenefit += $benefit;
    }

    public function setMaxDrawdown($drawdown)
    {
        if ($this->maxDrawdown > $drawdown) {
            $this->maxDrawdown = $drawdown;
        }
    }

    public function getTradeCount()
    {
        return $this->tradeCount;
    }

    public function getTotalBenefit()
    {
        return $this->totalBenefit;
    }

    public function getMaxDrawdown()
    {
        return $this->maxDrawdown;
    }
    
    public function settlePairPosition(&$Positions, $currentPrice)
    {
        if ($Positions == null) {
            return ;
        }
        
        $benefit = $Positions->getAllCurrentBenefit($currentPrice);
        $pairPositions = self::findPairSettlePositionId($Positions, $currentPrice);
        if ($pairPositions) {
            $ids = [$pairPositions['minus']->getId(),$pairPositions['plus']->getId()];
            $Positions->removePosition($ids);
            $benefits = $pairPositions['minus']->getCurrentBenefit($currentPrice) + $pairPositions['plus']->getCurrentBenefit($currentPrice);
            $this->setTotalBenefit($benefits);
            $this->addTradeCount();
        } else {
            $this->setMaxDrawdown($benefit);
        }
    }

    public function findPairSettlePositionId($Positions,$currentPrice,)
    {
        $minusPositions = $Positions->findAllMinusPosition($currentPrice);
        $plusPositions = $Positions->findAllMoreThanZeroPosition($currentPrice);
        if (!$minusPositions || !$plusPositions) {
            return [];
        }
        foreach($minusPositions as $minusPosition) {
            foreach($plusPositions as $plusPosition) {
                if ($minusPosition->getCurrentBenefit($currentPrice) 
                + $plusPosition->getCurrentBenefit($currentPrice) >= $this->width) {
                    return [
                        'minus' => $minusPosition,
                        'plus' => $plusPosition
                    ];
                }
            }
        }
    }
}

// 価格データ
class PriceData
{
    private $priceData = []; // Priceクラスも作る？
    private $prevPrice = 33000;

    public function __construct()
    {
        $this->priceData = $this->generateDummyPriceData();
    }

    private function generateDummyPriceData()
    {
        $data = [];
        for($i=0;$i<365;$i++) {
            $priceDiff = rand(1,1500) - rand(1,1500);
            $currentPrice = $this->prevPrice + $priceDiff;
            $data[] = $currentPrice; 
            $this->prevPrice = $currentPrice;
        }
        return $data;
    }

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

    /** */
    public function getPositions()
    {
       return $this->positionList;        
    }
    
    /** リストに追加する */
    public function addPosition($Position)
    {
        $positionId = $Position->getId();
        $this->positionList[$positionId] = $Position;
    }

    /** リストから削除する */
    public function removePosition($ids)
    {
        foreach($ids as $id) {
            unset($this->positionList[$id]);
        }
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

    /** 現在のポジションの損益の合計を取得する */
    public function getAllEntryType()
    {
        $entryTypes = [
            'BUY' => 0,
            'SELL' => 0, 
        ];
        foreach($this->positionList as $position) {
            $entryType = $position->getEntryType();
            $entryTypes[$entryType] += 1;
        }
        return $entryTypes;
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

    /** 現在の価格でマイナスになっているポジションの全IDを取得する */
    public function findAllMinusPosition($currentPrice)
    {
        $positions = [];
        foreach($this->positionList as $position) {
            if ($position->getCurrentBenefit($currentPrice) < 0) {
                $positions[] = $position;
            }
        }
        return $positions;
    }

    /** 現在の価格でゼロ以上になっているポジションの全IDを取得する */
    public function findAllMoreThanZeroPosition($currentPrice)
    {
        $positions = [];
        foreach($this->positionList as $position) {
            if ($position->getCurrentBenefit($currentPrice) >= 0) {
                $positions[] = $position;
            }
        }
        return $positions;
    }
}

/** 各建玉 */
class Position
{    
    /** ロット */
    private $lot = 0;

    /** 売りor買い */
    private $entryType = '';

    /** 取得時の値段 */
    private $gotPrice = 0;

    /** ポジションのID */
    private $id = '';

    public function getEntryType()
    {
        return $this->entryType;
    }

    public function getId()
    {
        return $this->id;
    }

    public function __construct($lot, $entryType, $gotPrice)
    {
        $this->lot = $lot;
        $this->entryType = $entryType;
        $this->gotPrice = $gotPrice;
        $this->id = $this->generateId($entryType);
    }

    /** 現在価格での損益を取得する */
    public function getCurrentBenefit($currentPrice)
    {
        if ($this->entryType == 'BUY') {
            return $currentPrice - $this->gotPrice;
        } else if ($this->entryType == 'SELL')  {
            return  $this->gotPrice - $currentPrice;
        }
    }

    public function generateId()
    {
        return uniqid();
    }
}

$tradeSimulation = new TradeSimulation();
$tradeSimulation->simulate();