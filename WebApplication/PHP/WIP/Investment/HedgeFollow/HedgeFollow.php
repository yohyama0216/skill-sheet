<?php

class HedgeFollow
{
    //private $baseUrl = "https://hedgefollow.com/fund/";
    private $urls = [
        "https://hedgefollow.com/funds/Berkshire+Hathaway",
        //"https://hedgefollow.com/funds/Fisher+Asset+Management",
        //"https://hedgefollow.com/funds/Citadel+Advisors",
        //"https://hedgefollow.com/funds/Renaissance+Technologies",
        //"https://hedgefollow.com/funds/Millennium+Management",
        "https://hedgefollow.com/funds/Bridgewater+Associates",
    ];
    private $stockList;

    public function __construct()
    {
        $this->setStockList();
    }

    private function setStockList(){
        foreach($this->urls as $url) {
            $this->stockList[$url] = $this->getStockListFromUrl($url);
        }
        $this->getCommonStockList();
    }

    private function getStockListFromUrl($url) {
        $fileName = explode("/",$url)[4].".html";
        $html = file_get_contents($fileName);
        preg_match_all("#/stocks/(.*)\">?#",$html,$matches);
        $result = array_unique($matches[1]);
        return $result;
    }

    public function printCommonStockList()
    {
        $list = $this->getCommonStockList();
        var_dump($list);
        // foreach($list as $key => $count) {
        //     if ($count >= 4) {
        //         echo $key." => ".$count.PHP_EOL;
        //     }

        // }
    }

    private function getCommonStockList() {
        $result = [];
        foreach($this->stockList as $fundName => $stocks) {
            foreach ($stocks as $stock) {
                //if (array_key_exists($item,$result)) {
                //     $result[$item] += 1;
                // } else {
                //     $result[$item] = 1;
                // }
                $result[$stock][]= $fundName;
            }
        }
        asort($result);
        return $result;
    }
}

$hedgeFollow = new HedgeFollow();
$hedgeFollow->printCommonStockList();

