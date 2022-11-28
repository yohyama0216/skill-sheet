<?php

class Trading
{
    private $priceData = [];
    private $typeData = [];
    private $Positions = null;
    private $currentPrice = 0;
    private $totalBenefit = 0;


    public function __construct()
    {
        $this->priceData = $this->generatePriceData();
        $this->typeData = $this->generateTypeData();
    }

    private function generatePriceData()
    {
        return range(1000, 2000, 100);
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
    
    public function trade()
    {
        foreach($this->priceData as $key => $price) {
            $this->setCurrentPrice($price);
            $this->settle();
            $this->have($key);
            var_dump([$this->totalBenefit,$this->positions]);
            echo '--------------';
        }
    }

    public function getType($key)
    {
        return $this->typeData[$key];
    }

    public function settle()
    {
        $benefit = $this->Positions->getAllCurrentBenefit();
        if ($benefit > 0) {
            $this->Positions->clearPositions();
        }
        $this->setTotalBenefit($benefit);
    }

    public function have($key)
    {
        $type = $this->getType($key);
        if (count($this->positionList) <= 5) {
            $this->Positions->addPosition(new Position(1, $type, $this->currentPrice));
        }
    }

    public function setTotalBenefit($benefit)
    {
        $this->totalBenefit += $benefit;
    }

    public function getTotalBenefit()
    {
        return $this->totalBenefit;
    }
}

class Positions
{
    private $positionList = [];
    
    public function addPosition($Position)
    {
        $this->positionList[] = $Position;
    }

    public function clearPositions()
    {
        $this->positionList = [];
    }

    public function getAllCurrentBenefit($currentPrice)
    {
        $total = 0;
        foreach($this->positionList as $position) {
            $total += $position->getCurrentBenefit($currentPrice);
        }
        return $total;
    }
}

class Position
{
    private $lot = 0;
    private $type = '';
    private $gotPrice = 0;

    public function __construct($lot, $type, $gotPrice)
    {
        $this->lot = $lot;
        $this->type = $type;
        $this->gotPrice = $gotPrice;
    }

    public function getCurrentBenefit($nowPrice)
    {
        if ($this->type == 'BUY') {
            return $nowPrice - $this->gotPrice;
        } else if ($this->type == 'SELL')  {
            return  $this->gotPrice - $nowPrice;
        }
    }
}

$trading = new Trading();
$trading->trade();
echo $trading->getTotalBenefit();


