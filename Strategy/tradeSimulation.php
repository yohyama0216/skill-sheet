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
    private $totalBenefit = 0;
    private $maxDrawdown = 0;
    private $tradeCount= 0;
    private $width = 100;

    public function __construct($initial,$tradeLot)
    {
        $this->initial = $initial;
        $this->tradeLot = $tradeLot;
        $this->Positions = new Positions();
    }

    public function trade($priceData)
    {
        foreach($priceData as $price) {
            $this->settle($price);
            $this->entry($price);
        }
        $this->showPositions();
        $this->showTotalBenefit();
        $this->showPositionsTotalBenefit($price);
        $this->showMaxDrawdown();
        $this->showTradeCount($priceData); // 微妙
    }

    /** 決済する */
    public function settle($currentPrice)
    {
        if ($this->Positions == null) {
            return ;
        }
        
        $benefit = $this->Positions->getAllCurrentBenefit($currentPrice);
        $pairPositions = $this->findPairSettlePosition($currentPrice);
        if ($pairPositions) {
            $ids = [$pairPositions['minus']->getId(),$pairPositions['plus']->getId()];
            $this->Positions->removePosition($ids);
            $benefits = $pairPositions['minus']->getCurrentBenefit($currentPrice) + $pairPositions['plus']->getCurrentBenefit($currentPrice);
            $this->setTotalBenefit($benefits);
            $this->addTradeCount();
        } else {
            $this->setMaxDrawdown($benefit);
        }
    }

    public function findPairSettlePosition($currentPrice)
    {
        $minusPositions = $this->Positions->findAllMinusPosition($currentPrice);
        $plusPositions = $this->Positions->findAllMoreThanZeroPosition($currentPrice);
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

    /** 決済の判定 */
    public function canSettle($currentPrice)
    {
        
    }

    /** ポジションを持つ */
    public function entry($currentPrice)
    {
        $entryType = $this->setEntryType($currentPrice);
        if ($this->canEntry($currentPrice)) {
            $this->Positions->addPosition(new Position($this->tradeLot, $entryType, $currentPrice));
            $this->addTradeCount();
        }
    }

    /** ポジションを追加する条件 */
    public function canEntry($currentPrice)
    {
        $currentPositionsBenefit = $this->Positions->getAllCurrentBenefit($currentPrice);
        $isLargerThanZeroTotal = (($this->initial + $this->totalBenefit + $currentPositionsBenefit) > 0);
        return $isLargerThanZeroTotal && ($this->Positions->countPositions() < self::POSITION_COUNT_MAX);
    }

    public function setEntryType($currentPrice)
    {
        $entryTypes = $this->Positions->getAllEntryType();
        if ($entryTypes['SELL'] >= 3) {
            return 'BUY';
        } else if ($entryTypes['BUY'] >= 3) {
            return 'SELL';
        } else {
            return (rand(1,10) % 2 == 0) ? 'SELL' : 'BUY'; 
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

    public function setMaxDrawdown($drawdown)
    {
        if ($this->maxDrawdown > $drawdown) {
            $this->maxDrawdown = $drawdown;
        }
    }

    public function showPositions()
    {
        var_dump($this->Positions);
        echo '--------------'.PHP_EOL;
    }

    public function showPositionsTotalBenefit($currentPrice)
    {
        echo '含み損'.$this->Positions->getAllCurrentBenefit($currentPrice).PHP_EOL;
    }

    public function showTotalBenefit()
    {
        echo '通算'.$this->totalBenefit.PHP_EOL;
    }

    public function showMaxDrawdown()
    {
        echo '最大ドローダウン'.$this->maxDrawdown.PHP_EOL;
    }

    public function showTradeCount($priceData)
    {
        echo '全データ数'.count($priceData).PHP_EOL;
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
        for($i=0;$i<365;$i++) {
            $data[] = 1000 + rand(1,300) - rand(1,300);
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