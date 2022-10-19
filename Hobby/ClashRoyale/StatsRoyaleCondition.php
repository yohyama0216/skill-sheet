<?php

class StatsRoyaleCondition
{
    private $baseUrl = "http://statsroyale.com/ja/decks/challenge-winners?type=top200&page=%s";
    private $range = [];
    private $targetUrlList = [];
    private $targetPattenList = [
        'movieUrl' => '#https://youtube.com/embed/.*?autoplay=1&showinfo=0#',
        'deckCopyUrl' => '#https://link.clashroyale.com/deck/.*deck=\d{8};\d{8};\d{8};\d{8};\d{8};\d{8};\d{8};\d{8}#',
    ];

    public function __construct($start,$end)
    {
        $this->range = range($start,$end);
        $this->targetUrlList = $this->createTargetUrlList();
    }

    public function getRangeStart()
    {
        return $this->range[0];
    }

    public function getTargetUrlList()
    {
        return $this->targetUrlList;
    }

    public function getTargetPatternList()
    {
        return $this->targetPattenList;
    }

    private function createTargetUrlList()
    {
        $result = [];
        foreach($this->range as $num) {
            $pageUrl = sprintf($this->baseUrl,$num);
            $targetUrlList = $this->getTargetUrl($pageUrl);
            foreach($targetUrlList as $targetUrl) {
                $result[] = $targetUrl;
            }
        }
        return $result;
    }    

    private function getTargetUrl($pageUrl)
    {
        $html = file_get_contents($pageUrl);
        $pattern = '#<a href="(.*)?" class="recentWinners__footerAction ui__layoutOneLine ui__link">#';
        preg_match_all($pattern, $html, $matches);
        return $matches[1];
    }
}
