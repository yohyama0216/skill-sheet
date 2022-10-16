<?php

class HedgeFollow
{
    private $baseUrl = "https://hedgefollow.com/fund/{fundName}";
    private $urls = [
        "Berkshire+Hathaway",
        "BlackRock",
        "Bridgewater+Associates",
        "Citadel+Advisors",
        "Fisher+Asset+Management",
        "Millennium+Management",
        "Renaissance+Technologies",
        "Scion+Asset+Management"
    ];
    private $fundStockList;
    private $result;

    public function __construct()
    {
        foreach($this->urls as $url) {
            $this->fundStockList[$url] = $this->getStockListFromUrl($url);
        }
    }

    public function getResult()
    {
        return $this->result;
    }

    private function getStockListFromUrl($url) {
        $fileName = $url.".html";
        $html = file_get_contents('./data/'.$fileName);
        preg_match_all("#/stocks/(.*)\">?#",$html,$matches);
        $result = array_unique($matches[1]);
        return $result;
    }

    public function printCommonStockList()
    {
        $this->groupByStock(4);
        var_dump($this->getResult());
    }

    private function groupByStock($border = 0) {
        foreach($this->fundStockList as $fundName => $stocks) {
            foreach ($stocks as $stock) {
                $this->result[$stock][] = $fundName;
            }
        }

        if ($border > 0) {
            foreach($this->result as $stock => $fund) {
                if (count($fund) <= $border) {
                    unset($this->result[$stock]);
                }
            }
        }
        ksort($this->result);
    }
}

$hedgeFollow = new HedgeFollow();
$hedgeFollow->printCommonStockList();

