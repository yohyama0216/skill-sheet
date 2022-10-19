<?php

namespace Test\Numbers;

class Numbers {
    private $numbersType = '';
    private $round = "";
    private $date = "";
    private $numbers = [];
    private $numbersString = '';
    private $uraNumbers = [];
    private $mini = [];

    public function __construct($numbersType, $round, $date, $numbersString)
    {
        if ($numbersType != strlen($numbersString)) {
            return null;
        }
        $this->numbersType = $numbersType;
        $this->round = $round;
        $this->date = $date;
        $this->numbersString = $numbersString;
        $this->numbers = $this->createNumbersArray($numbersString);
        $this->uraNumbers = $this->setUraNumbers($numbersString);
        
        if ($numbersType == 3) {
            $this->mini = [
                '10digit' => $this->numbers['10digit'], 
                '1digit' => $this->numbers['1digit']
            ];
        }
    }

    /*
     *  数字を分割して、キーに桁をつける
     */
    private function createNumbersArray($numbersString)
    {
        $result = [];
        $arr = str_split($numbersString);
        foreach($arr as $key => $char) {
            $key = "1".str_repeat(0,(count($arr)-$key-1))."digit";
            $result[$key] = $char;
        }
        return $result;
    }

    public function getNumbersType()
    {
        return $this->numbersType;
    }

    public function getRound()
    {
        return $this->round;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getNumbers()
    {
        return $this->numbers;
    }

    public function getUraNumbers()
    {
        return $this->numbers;
    }

    public function getMini()
    {
        return $this->mini;
    }

    public function getNumbersString()
    {
        return $this->numbersString;
    }

    public function isSameDigit()
    {
        return count(array_unique($this->numbers)) == 1;
    }

    public function isStep()
    {
        $numbers = $this->numbers;
        if ($this->numbersType == 3) {
            return (
                ($numbers['1digit'] == $numbers['10digit'] + 1)
            &&  ($numbers['10digit'] == $numbers['100digit'] + 1)           
            );
        } else if ($this->numbersType == 4) {
            return (
                ($numbers['1digit'] == $numbers['10digit'] + 1)
            &&  ($numbers['10digit'] == $numbers['100digit'] + 1)
            &&  ($numbers['100digit'] == $numbers['1000digit'] + 1)          
            );
        }
    }

    public function isMirror()
    {
        $numbers = $this->numbers;
        if ($this->numbersType == 3) {
            return ($numbers['1digit'] == $numbers['100digit']);
        } else if ($this->numbersType == 4) {
            return (
                ($numbers['1digit'] == $numbers['1000digit'])
            &&  ($numbers['10digit'] == $numbers['100digit'])       
            );
        }
    }

    private function setUraNumbers()
    {
        foreach($this->numbers as $value){
            $this->uraNumbers[] = Windmill::getUraNumber($value);
        }
    }
}
