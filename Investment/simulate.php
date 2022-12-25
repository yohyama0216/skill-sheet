<?php

class simulate
{
    // 確率
    private $probability = 0.5;

    // 所持金
    private $initial = 10000;
    private $wallet = 0;

    // 掛け金の所持金に対する割合
    private $betRatio = 0;

    // オッズ
    private $return = 0;

    // 繰り返し数
    private $loop = 100;

    // 勝利数
    private $winCount = 0;

    // 負け数
    private $loseCount = 0;

    // 途中結果の表示
    private $isDisplayProcess = false;

    // セット数
    private $set = 100;

    // セットの結果
    private $totalResult = [];

    public function __construct($betRatio,$probability,$return)
    {
        $this->betRatio = $betRatio;
        $this->probability = $probability;
        $this->return = $return;
    }

    public function doSet()
    {
        for($i=0;$i<$this->set;$i++) {
            $this->doLoop();
        }
        $this->showTotalSetResult();
    }

    private function doLoop()
    {
        $this->wallet = $this->initial;
        $this->winCount = 0;
        $this->loseCount = 0;
        for($j=0;$j<$this->loop;$j++) {
            $bet = $this->betRatio * $this->initial;
            $this->wallet -= $bet;
            $result = "";
            if ($this->roulette()) {
                $result = 'WIN   ';
                $this->winCount++;
                $this->wallet += $bet * $this->return;   
            } else {
                $this->loseCount++;
                $result = 'LOSE  ';
            }
            $this->displayProcess($result);

            if ($this->wallet <= $bet) {
                break;
            }
        }
        $this->setLoopResult();
    }

    private function roulette()
    {
        $max = 100;
        return rand(1,$max) <= $max * $this->probability;
    }

    private function displayProcess($result)
    {
        if ($this->isDisplayProcess) {
            echo $result."  wallet : $this->wallet;".PHP_EOL;
        }
    }

    private function setLoopResult()
    {
        $this->totalResult[] = [
            'WIN' => [
                'label' => '勝ち数',
                'value' => $this->winCount
            ],
            'LOSE' => [
                'label' => '負け数',
                'value' => $this->loseCount
            ],
            'RESULT' => [
                'label' => '結果',
                'value' => ($this->wallet - $this->initial),
            ]
        ];
    }

    private function showTotalSetResult()
    {
        $format = "{label}:{value}  ";
        $countBankrupt = 0;
        $max = 0;
        $min = 0;
        foreach($this->totalResult as $key => $result) {
            $min = $result['RESULT']['value'];
            $num = str_pad($key+1,3,"0",STR_PAD_LEFT);
            
            if (false) {
                echo $num."セット目 - ";
                foreach($result as $item) {
                    echo str_replace(["{label}","{value}"],[$item['label'],$item['value']],$format)."   ";
                }

                echo PHP_EOL;
            }
            if ($result['RESULT']['value'] < 0) {
                $countBankrupt++;
            }

            if ($max < $result['RESULT']['value']) {
                $max = $result['RESULT']['value'];
            }
            if ($min > $result['RESULT']['value']) {
                $min = $result['RESULT']['value'];
            }

        }
        if ($countBankrupt <= 2) {
            echo "ケース結果 初期: $this->initial 確率: $this->probability ベット比率: $this->betRatio リターン: $this->return 破産回数: $countBankrupt 最大: $max 最小: $min";
            echo PHP_EOL;
        }
    }
}

$probabilityList = range(0.1, 0.5, 0.01);
$betRatioList = range(0.01, 0.5, 0.1);
$returnList = range(1.0,5,0.5);


foreach ($probabilityList as $probability) {
    foreach ($betRatioList as $betRatio) {
        foreach ($returnList as $return) {
            $simulate = new simulate($betRatio, $probability, $return);
            $simulate->doSet();
        }
    }
}


