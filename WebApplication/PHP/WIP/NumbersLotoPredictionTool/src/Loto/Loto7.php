<?php

class Loto7 {
    private $sourceFile = "../pastResult/loto7-past-result.json";
    private $StaticsService;
    private $PredictService;

    public function __construct()
    {
        $data = json_decode(file_get_contents($this->sourceFile),true);
        $this->StaticsService = new StaticsService($data);
        $this->PredictService = new PredictService($data);
    }

    public function displayStatics()
    {
        //$this->StaticsService->displayStaticRoundsWithinRange(10,37); // 0.12 
        // $this->StaticsService->displayStaticsConsecutiveDifferentCharOneNumbers(2); // 0.55
        //$this->StaticsService->displayStaticsRoundsHitWithInPreviousNumbers(5); // 54.62
        // $this->StaticsService->displayStaticsRoundsHitWithSameNumber(); // 0.81%
        // $this->StaticsService->displayStaticsRoundsHitWithPlusoneNumber(); // 0.81%
        $this->StaticsService->getPreviousAllNumbers(2,5);
    }

    public function predict()
    {
        $this->PredictService->predict();
    }
}

$loto7 = new Loto7();
$loto7->displayStatics();
//$loto7->predict();
// 3桁の数字が連続 123, 456など
// 両端の数字が同じ 121など
// 前回数字と数字が一つだけ同じ 123→145とか
// 前回数字とひっくり返した数字　123 → 321など

class StaticsService {
    private $data;
    private $totalCount;
    private $showRounds = false;

    public function __construct($data)
    {
        $this->data = $data;
        $this->totalCount = count($data);
    }

    public function getNumbersByRound($round)
    {
        return $this->data[$round];
    }

    public function getRoundListByNumbers($numbers)
    {
        $result = [];
        foreach($this->data as $key => $item) {
            if ($item['numbers'] == $numbers) {
                $result[] = $key;
            }
        }
        return $result;
    }

    private function getProbabilityPerTotal($roundCount)
    {
        return round($roundCount / $this->totalCount * 100, 2) ."%";
    }

    public function getPreviousAllNumbers($start, $previous)
    {
        $range = range($start,$start+$previous-1);
        $data = $this->data;
        rsort($data);
        foreach($range as $num) {
            $numbers = explode(" ",$data[$num]['numbers']);
            $bonus = explode(" ",$data[$num]['bonus']);
            $array[] = array_merge($numbers, $bonus); 
        }

        $result = [];
        foreach($array as $round) {
            foreach($round as $number) {
                if (array_key_exists($number, $result)){
                    $result[$number] += 1;
                } else {
                    $result[$number] = 1;
                }

            }
        }
        var_dump($array);
        ksort($result);
        var_dump($result);
    }


    // /*
    //  * 連続かつ±1,±10,±100の数字が出た回。の統計を表示する。
    //  * 
    //  * @params $times n回の連続　(3回以上は無い模様) 
    //  */
    // public function displayStaticsConsecutiveDifferentCharOneNumbers($times)
    // {
    //     echo $times."回連続かつ±1,±10,±100の数字が出た回。".PHP_EOL; //Loto7に依存
    //     $rounds = $this->getConsecutiveDifferentCharOneNumbers(2);
    //     $this->displayResultMessages($rounds);
    // }
    // /*
    //  * 連続かつ±1,±10,±100の数字が出た回を表示する。
    //  * 
    //  * @params $times n回の連続　(3回以上は無い模様) 
    //  */
    // public function getConsecutiveDifferentCharOneNumbers($times)
    // {
    //     $rounds = [];
    //     foreach($this->data as $round => $result) {
    //         if ((int)$round+ $times >= count($this->data)) {
    //             break;
    //         }

    //         $array = [];
    //         for($i=0;$i<$times;$i++){
    //             $array[] = $this->data[(int)$round+$i]['numbers']; // 2回しかないなら、もっと単純に。
    //         }

    //         if ($this->getCondition($array, 'differentOneNumber')) {
    //             $rounds[] = $round;
    //         } else {
    //             continue ;
    //         }
    //     }
    //     return $rounds;        
    // }

    private function displayResultMessages($rounds)
    {
        if ($this->showRounds){
            foreach($rounds as $round) {
                echo $round."回".PHP_EOL;
            }
        }
        echo "全".$this->totalCount."中、".count($rounds)."回 : ".$this->getProbabilityPerTotal(count($rounds));
        echo "-----------".PHP_EOL;
    }

    /*
     * 当選数字が$min~$max までだった回を取得する。
     */
    public function displayStaticRoundsWithinRange($min,$max)
    {
        echo $min."から".$max."までの数字だけが出た回。".PHP_EOL;
        $rounds = $this->getRoundsWithinRange($min,$max);

        $this->displayResultMessages($rounds);
    }

    /*
     * 当選数字が$min~$max までだった回を取得する。
     */
    public function getRoundsWithinRange($min,$max)
    {
        $rounds = [];
        foreach($this->data as $round => $item) {
            $numbers = explode(' ',$item['numbers']);
            
            $bonus = explode(' ',$item['bonus']);
            

            if (($min <= min($bonus) && max($bonus) <= $max)
                && $min <= min($numbers) && max($numbers) <= $max) {
                    $rounds[(int)$round] = [
                        'numbers' => $numbers,
                        'bonus' => $bonus
                    ];
            }
        }
        //var_dump($numbers);
        return $rounds;
    }

    public function displayStaticsRoundsHitWithInPreviousNumbers($previous)
    {
        echo $previous."回さかのぼって同じ数字が出た回。".PHP_EOL;
        $rounds = $this->getRoundsHitWithInPreviousNumbers($previous);

        $this->displayResultMessages($rounds);
    }

    /*
     * 過去、$previous回さかのぼって同じ数字が出た回を取得する。
     * 
     * @params $previous さかのぼるn回
     */
    public function getRoundsHitWithInPreviousNumbers($previous)
    {
        $rounds = [];

        foreach($this->data as $round => $item) {
            //echo count($this->data).PHP_EOL;
            if ((int)$round+ $previous >= count($this->data)) {
                break;
            }

            $previousNumbersList = $this->getNumbersRange($round, $previous, 1);
            $currentNumbers = explode(' ', $this->data[$round]['numbers']);

            foreach($currentNumbers as $currentNumber) {
                //echo $currentNumber.PHP_EOL;
                foreach($previousNumbersList as $previousNumbers) {
                    if (in_array($currentNumber,$previousNumbers)) {
                        $rounds[] = $round;
                        continue 3;
                        // 100超えてる　
                    }
                }
            }
        }
        var_dump($rounds);
        return $rounds;
    }

    private function getNumbersRange($start, $end, $step){
        $array = [];
        for($i=1;$i<=$end;$i++){
            $numbers = (int)$this->data[(int)$start+$i*$step]['numbers'];
            $array[] = explode(" ", $numbers);
        }
        return $array;
    }

 


    // public function displayStaticsRoundsHitWithSameNumber()
    // {
    //     echo "全桁とも同じ数字が出た回。".PHP_EOL;
    //     $rounds = $this->getRoundsHitWithSameNumber();

    //     $this->displayResultMessages($rounds);
    // }

    // /*
    //  * 全桁とも同じ数字が出た回を取得する。
    //  * 
    //  */
    // public function getRoundsHitWithSameNumber()
    // {
    //     $rounds = [];
    //     foreach($this->data as $round => $item) {
    //         $numbersArray = str_split($item['numbers']);
    //         if (count(array_unique($numbersArray)) == 1) {
    //             $rounds[] = $round;
    //         } else {
    //             continue ;
    //         }
    //     }
    //     return $rounds;
    // }

    // public function displayStaticsRoundsHitWithPlusoneNumber()
    // {
    //     echo "連続した数字が出た回。".PHP_EOL;
    //     $rounds = $this->getRoundsHitWithPlusoneNumber();

    //     $this->displayResultMessages($rounds);
    // }
    // /*
    //  * 123のような連続した数字が出た回を取得する。
    //  * 
    //  */
    // public function getRoundsHitWithPlusoneNumber()
    // {
    //     $rounds = [];
    //     foreach($this->data as $round => $item) {
    //         $numbersArray = str_split($item['numbers']);
    //         if ($numbersArray[0] + 1 == $numbersArray[1]
    //         && $numbersArray[1] + 1 == $numbersArray[2]) { // Loto7に依存
    //             $rounds[] = $round;
    //         } else {
    //             continue ;
    //         }
    //     }
    //     return $rounds;
    // }
    // private function getCondition($array, $type)
    // {
    //     if ($type == 'allSame') {
    //         return (count(array_unique($array)) == 1);
    //     } else if ($type == 'twoSame') {
    //         return (count(array_unique($array)) == 2);
    //     } else if ($type == 'leftCharSame') {

    //     } else if ($type == 'rightCharSame') {

    //     } else if ($type == 'middleCharSame') {

    //     } else if ($type == 'differentOneNumber') {
    //         return (
    //             in_array(abs($array[1] - $array[0]),[1,10,100]) 
    //         );
    //     }
    // }
}
class PredictService {
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function predict()
    {
        $data = $this->getPreviousNumberRange($data, $start, $end, $step);
        $data = $this->filterSameNumber($data);
        $data = $this->filterPlusOneNumber($data);
    }
}