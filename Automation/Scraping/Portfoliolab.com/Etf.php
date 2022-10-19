<?php

class Etf {
    private $basePath = "https://portfolioslab.com/symbol/{etfName}";
    private $etfList = [
        'DGRW','DIA','DHS','INKM','GAL','QYLD','QYLG','XYLD','XYLG',
        'VTI', 'VIG', 'VYM','VHT','VOO','VGT','VDE','VFH',
        'AGG', 'TIP', 'TLT','LQD','HYG',
        'SOXL','SPXL','CURE','TECL','TQQQ','WEBL'
    ];
    private $urlList = [];

    public function __construct() {
        foreach ($this->etfList as $etf) {
            $this->urlList[$etf]['url'] = str_replace('{etfName}',$etf,$this->basePath);
        }
    }

    public function getEtfInfo() {
        foreach ($this->urlList as $etf => $url) {
            $html = file_get_contents($url['url']);
            $sharpePattern = '# Sharpe ratio is <b>(.*?)</b>. A#';
            $worstDrawdownPattern = '#The maximum drawdown since .* is <b>(.*?)%</b>, #';
            $volatilityPattern = '#volatility is <b>(.*?)%</b>. #';
            $this->urlList[$etf]['sharpe'] = $this->getString($sharpePattern, $html);
            $this->urlList[$etf]['worstDrawdown'] = $this->getString($worstDrawdownPattern, $html);
            $this->urlList[$etf]['volatility'] = $this->getString($volatilityPattern, $html);
        }
    }

    public function display()
    {
        echo implode("\t",['ticker','sharpe','drawdown','volatility','score']).PHP_EOL;
        foreach($this->urlList as $etf => $item) {
            $score = $item['sharpe'] / $item['worstDrawdown'] / $item['volatility'] * 1000;
            $row = [$etf,$item['sharpe'],$item['worstDrawdown'],$item['volatility'],$score];
            echo implode("\t",$row).PHP_EOL;
        }
    }

    private function getString($pattern, $subject) {
        preg_match($pattern, $subject, $matches);
        return $matches[1];
    }
}
$Etf = new Etf();
$Etf->getEtfInfo();
$Etf->display();
