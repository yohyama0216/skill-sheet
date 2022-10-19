<?php

class Scraper
{
    private $number = 0;
    private $fileName = './result/result%d.json';
    private $StatsRoyaleCondition = null;
    private $resultList = [];

    public function __construct($StatsRoyaleCondition)
    {
        $this->StatsRoyaleCondition = $StatsRoyaleCondition;
        $this->number = $StatsRoyaleCondition->getRangeStart();
    }

    public function getResultList()
    {
        return $this->resultList;
    }

    public function execute()
    {
        $urlList = $this->StatsRoyaleCondition->getTargetUrlList();
        $targetPatternList = $this->StatsRoyaleCondition->getTargetPatternList();
        $fileName = 1;
        foreach($urlList as $url) {
            $data = [];
            foreach($targetPatternList as $target => $pattern) {

                $data[$target] = $this->scrape($url, $pattern);
            }
            if (empty($data['movieUrl']) || empty($data)) {
                continue ;
            }
            $this->resultList[] = $data;
            $this->createResultJson();
        }
    }
    private function getNow() // デバッグ
    {
        $datetime = new DateTime();
        echo $datetime->format('Y-m-d H:i:s').PHP_EOL;
    }

    private function scrape($url, $pattern)
    {
        $ch = curl_init(); // はじめ

        //Guzzleでやってみる

        //オプション
        curl_setopt($ch, CURLOPT_URL, $url); 
        //curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $html = curl_exec($ch);
        $this->getNow();
        if (strpos($html,'26000004;') == false
        || strpos($html,'26000036;') == false) {
            curl_close($ch); //終了
            return [];
        }
        $this->getNow();
        curl_close($ch); //終了
        preg_match_all($pattern, $html, $matches);
        return $matches[0];
    }


    private function createResultJson()
    {
        echo count($this->resultList).PHP_EOL;
        if (!empty($this->result) && count($this->resultList) % 3 == 0) {
            $fileName = sprintf($this->fileName,$this->number); 
            file_put_contents($fileName,json_encode($this->resultList));
            echo "$fileName created".PHP_EOL;
            $this->resultList = [];
            $this->number += 1;
        }
    }
}
