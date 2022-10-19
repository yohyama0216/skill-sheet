<?php

class Scraper
{
    private $fileName = 'result.json';
    private $StatsRoyaleCondition = null;
    private $resultList = [];

    public function __construct($StatsRoyaleCondition)
    {
        $this->StatsRoyaleCondition = $StatsRoyaleCondition;
    }

    public function getResultList()
    {
        return $this->resultList;
    }

    public function execute()
    {
        $urlList = $this->StatsRoyaleCondition->getTargetUrlList();
        $targetPatternList = $this->StatsRoyaleCondition->getTargetPatternList();
        foreach($urlList as $url) {
            $temp = [];
            foreach($targetPatternList as $target => $pattern) {
                $temp[$target] = $this->scrape($url, $pattern);
            }
            $this->resultList[] = $temp;
        }
    }

    private function scrape($url, $pattern)
    {
        $html = file_get_contents($url);
        preg_match_all($pattern, $html, $matches);
        return $matches[1]; // 切り替える？
    }

    public function createResultJson()
    {
        return file_put_contents($this->fileName,json_encode($this->resultList));
    }
}
