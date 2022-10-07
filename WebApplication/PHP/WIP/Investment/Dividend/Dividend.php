<?php

class Dividend {
    private $basePath = "https://finance.yahoo.com/quote/{etfName}/risk/";
    private $etfList = [
        'DGRW','DIA',
        // 'VTI', 'VIG', 'VYM','VHT','VOO','VGT',
        // 'AGG', 'TIP', 'TLT',
        // 'SOXL','SPXL','CURE','TECL',
        // 'QYLD','QYLG','XYLD','XYLG',
    ];
    private $urlList = [];

    public function __construct() {
        foreach ($this->etfList as $etf) {
            $this->urlList[$etf]['url'] = str_replace('{etfName}',$etf,$this->basePath);
        }
    }

    public function getEtfInfo() {
        //$result = [];
        //$dom = new DOMDocument();
        foreach ($this->urlList as $etf => $url) {
            $html = file_get_contents($url['url']);
            $sharpePattern = '#<div class="Bdbw.*"><div class="W.*"><span>Sharpe Ratio</span></div><div class="W.*"><span class="W.*">1.48</span><span class="W.*">1</span><span class="Cl.*"></span></div><div class="W.*"><span class="W.*">1.41</span><span class="W.*">1.38</span><span class="Cl.*"></span></div><div class="W.*"><span class="W.*">2.35</span><span class="W.*">(.*)</span><span class="Cl.*"></span></div></div>#';
            // $dividendPattern = '#直近配当額 (\d{2}/\d{2}/\d{4}.*)?</div>#';
            $this->urlList[$etf]['sharpe'] = $this->getString($sharpePattern, $html);
            //this->urlList[$etf]['dividend'] = $this->getString($dividendPattern, $html);
        }
        var_dump($this->urlList);
        //$this->urlList
    }

    private function getString($pattern, $subject) {
        preg_match_all($pattern, $subject, $matches);
        return $matches[1];
    }
}
$Dividend = new Dividend();
$Dividend->getEtfInfo();
