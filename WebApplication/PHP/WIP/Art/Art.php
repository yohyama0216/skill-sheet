<?php

class Art
{
    private $html = "";
    private $priceList = [];
    
    public function __construct()
    {
        $this->html = file_get_contents('straym.html');
        $this->priceList = $this->createPriceList();
    }

    public function createPriceList()
    {
        $artInfoPattern = '#(<h3[\s\S]*?)<h3 .*#'; 
        preg_match_all($artInfoPattern,$this->html,$matches);
        $pricePattern = '#class="art-amount__value">([\s\S]*?)</div>#';
        $labelPattern = '#class="art-amount__label">([\s\S]*?)</div>#';
        $namePattern = '#<h3 class="art__title text-center">(.*?)</h3>#';
        $ratioPattern = '#<span class="p-top01__ratio--up t_token_ratio">(.*?)</span>#';
        //var_dump($matches);
        // $result = [];
        foreach($matches[0] as $match) {
            $price = $this->getString($pricePattern,$match);
            //$label = $this->getString($labelPattern,$match);
            $name = $this->getString($namePattern,$match);
            $ratio = $this->getString($ratioPattern,$match);
            $result[] = [
                'price' => $price,
                //'label' => $label,
                'name' => $name,
                'ratio' => $ratio,
            ]; 
        }
        return $result;
    }

    private function getString($pattern,$text)
    {
        preg_match($pattern,$text,$match);
        return trim($match[1]);
    }

    public function groupByName()
    {
        $result = [];
        foreach($this->priceList as $price) {
            $key = $price['name']; 
            $result[$key][] = [
                'price' => $price['price'],
                'ratio' => $price['ratio']
            ];
        }
        return $result;
    }

    public function groupByPrice()
    {
        $result = [];
        foreach($this->priceList as $price) {
            $key = str_replace(',','',ltrim($price['price'],'¥')) / 1000; 
            $result[$key][] = [
                'name' => $price['name'],
                'ratio' => $price['ratio']
            ];
        }
        ksort($result);
        return $result;
    }
}

$Art = new Art();
$list = $Art->createPriceList();
var_dump($Art->groupByPrice());
