<?php

class totoCondition
{
    private $targetUrlList = [
        'https://www.rakuten-bank.co.jp/toto/big/megabig.html',
        'https://www.rakuten-bank.co.jp/toto/big/',
        'https://www.rakuten-bank.co.jp/toto/big/100big.html',
        'https://www.rakuten-bank.co.jp/toto/big/big_1000.html',
        'https://www.rakuten-bank.co.jp/toto/big/mini_big.html',
    ];
    private $targetPattenList = [
        'probability' => '#<li>(.等　.*[^％])?</li>#',
    ];

    public function __construct()
    {

    }

    public function getTargetUrlList()
    {
        return $this->targetUrlList;
    }

    public function getTargetPatternList()
    {
        return $this->targetPattenList;
    }
}
