<?php

class simulate
{
    // 確率
    private $probability = 0.5;

    // 確率用　最大の数
    private $max = 100;

    // 掛け金
    private $bet = 1000;

    // 所持金
    private $initial = 10000;
    private $wallet = 0;

    // オッズ
    private $return = 2.5;

    // 繰り返し数
    private $loop = 100;

    // 勝利数
    private $winCount = 0;

    // 負け数
    private $loseCount = 0;

    public function calculate()
    {
        $this->wallet = $this->initial;
        for($i=0;$i<$this->loop;$i++) {
            $this->wallet -= $this->bet;
            if ($this->roulette()) {
                echo 'WIN   ';
                $this->winCount++;
                $this->wallet += $this->bet * $this->return;   
            } else {
                $this->loseCount++;
                echo 'LOSE  ';
            }
            echo " wallet : $this->wallet;".PHP_EOL;

            if ($this->wallet <= $this->bet) {
                echo "BANKRUPT!";
                break;
            }
        }
        $this->showResult();
    }

    private function roulette()
    {
        return rand(1,$this->max) <= $this->max * $this->probability;
    }

    private function showResult()
    {
        $result = $this->wallet - $this->initial;
        echo "勝ち数: $this->winCount; 負け数: $this->loseCount; 結果： $result";
    }
}

$simulate = new simulate();
$simulate->calculate();

