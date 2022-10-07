<?php

class Compare
{
    private $files = [];
    private $data = [];

    public function __construct()
    {
        $this->files = glob("*.html");
        $this->data = $this->setData();        
    }

    private function setData()
    {
        $plRatePattern = '#class="chartArea_value.*>(.*)?</span>#';
        $detailsPattern = '#class="chartArea_detailsValue.*>(.*)?</div#';
        $data = [];
        foreach($this->files as $file) {
            if ($file == 'result.html') {
                continue ;
            }
            $content = file_get_contents($file); 
            $pl = $this->getValues($plRatePattern,$content); 
            $details = $this->getValues($detailsPattern,$content); 
            $plRate = $this->trimPercent($pl[0]);
            $pl = $this->trimYen($pl[1]);
            $recommend = $this->trimYen($details[0]);
            $minimum = $this->trimYen($details[1]);
            $bolatility = round($recommend / $minimum,2);
            $sharpe = round($plRate / $bolatility);
            $convenient = round($sharpe / $recommend,6) * 10000;

            $data[] = [
                'ファイル名' => $file,
                '期間収益' => $pl,
                '推奨証拠金' => $recommend,
                '発注証拠金' => $minimum,
                '期間収益/推奨' => round($pl / $recommend,2),
                '期間収益率' => $plRate,
                '期間収益/発注' => round($pl / $minimum,2),
                '推奨/発注<br/>（変動性）' => $bolatility,
                '仮シャープレシオ<br/>（期間収益率/変動性）' => $sharpe,
                //'シャープ/推奨<br/>（手軽さ）' => $convenient,
            ];   
        }
        $key = '仮シャープレシオ<br/>（期間収益率/変動性）';
        $this->sortByKey($data,$key);
        return $data;
    }

    private function sortByKey(&$data,$key)
    {
        uasort($data,function($a, $b) use ($key) {
            if ($a[$key] == $b[$key]) {
                return 0;
            }
            return ($a[$key] < $b[$key]) ? -1 : 1;
        });
    }

    public function displayCompareResult()
    {
        echo '<html><body><table style="border: 1px solid black;"><tbody style="border: 1px solid black;">';
        echo '<tr><th>'.$this->getHeader().'</th><tr>'.PHP_EOL;
        foreach($this->data as $item) {
            echo '<tr>';
            foreach($item as $key => $value){
                    echo '<td>'.$value.'</td>';
            }
            echo '<tr>'.PHP_EOL;
            // echo '<tr style="border: 1px solid black;"><td>'
            // .$item['ファイル名'].'</td><td>'
            // .$item['期間収益率']."</td><td>"
            // .$item['推奨 / 発注（変動性）']."</td><td>"
            // .$item['仮シャープレシオ（期間収益率 / 変動性）']."</td><td>"
            // .$item['シャープ / 推奨（手軽さ）']."</td></tr>"
            // .PHP_EOL;
        }
        echo '</tbody></table></body></html>';
    }

    private function getHeader()
    {
        $header = array_keys($this->data[0]);
        return implode('</th><th>',$header);
    }

    private function getValues($pattern,$text)
    {
        preg_match_all($pattern,$text,$matches);
        return $matches[1];
    }

    private function trimPercent($text)
    {
        return str_replace(['+','%'],'',$text);
    }

    private function trimYen($text)
    {
        return str_replace(['+',',','円'],'',$text);
    }
}

$CompareAlog = new Compare();
$CompareAlog->displayCompareResult();