<?php

namespace Test;

class Converter {

    private $sourceHtmlFilePath = "data/%s-past-result.html";
    private $convertedFilePath = "data/%s-past-result.json";
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
        $this->sourceHtmlFilePath = sprintf($this->sourceHtmlFilePath,$this->type);
        $this->convertedFilePath = sprintf($this->convertedFilePath,$this->type);
        $this->resultDataList = $this->createResultDataList();

    }

    private function createResultDataList()
    {
        $html = file_get_contents($this->sourceHtmlFilePath);

        if (!$html) {
            echo "ファイルは空です";
            return ;
        }
        $html = $this->replaceHtml($html);

        return $this->extractResultData($html);
    }

    public function generateResultJson()
    {
        file_put_contents($this->convertedFilePath, json_encode($this->resultNumbersList));
        echo json_last_error_msg();
    }

    public function generateResultSQL()
    {
        return 'test';
    }


    private function replaceHtml($html)
    {
        $html = str_replace(' ',' ',$html); //特殊スペースを半角スペースに
        return str_replace(['  ', '  ', "\r\n", "\r", "\n", "\t"],'',$html);
    }

    private function extractResultData($html)
    {
        $roundsPattern = '#<tr class=.*?</tr>#';
        preg_match_all($roundsPattern,$html,$matches);

        if ($this->type == 'loto7'){
            $numberPattern = '#<td class="text-center text-bold">(.*).{17}?</td>#'; //もっと綺麗に？
            $bonusPattern = '#<td class="text-center text-bold">.{17}(.*)?</td>#';
            $datePattern = '#<td nowrap="nowrap" class="text-center">(\d{4}/\d{2}/\d{2})?</td>#';
            $roundPattern = '#<td nowrap="nowrap" class="text-center">第(\d{4})回?</td>#';
        } else if ($this->type == 'numbers3') {
            $numberPattern = '#<td class="text-center text-bold">(\d{3,4})</td>#';
            $datePattern = '#<td nowrap="nowrap" class="text-center">(\d{4}/\d{2}/\d{2})</td>#';
            $roundPattern = '#<td nowrap="nowrap" class="text-center">(\d{4,5})</td>#';
        }


        $roundData = [];
        foreach($matches[0] as $match){
            $round = $this->getStrings($roundPattern,$match);
            $roundData[(int)$round] = [
                'date' => $this->getStrings($datePattern,$match),
                'numbers' => $this->getStrings($numberPattern,$match),
                
            ];
            if ($this->type == 'loto7') {
                $roundData[(int)$round]['bonus'] =
                 $this->getBonus($this->getStrings($bonusPattern,$match));
            }
        }
        //var_dump($roundData);
        ksort($roundData);
        return $roundData;
    }

    private function getStrings($pattern,$subject)
    {
        if ($this->type == 'loto7'){
            $subject = str_replace('<br>','/',$subject);
        }
        
        preg_match($pattern,$subject,$number);
        $result = $number[1];
        if ($result) {
            return mb_convert_encoding($result, 'UTF-8', 'UTF-8'); //マルチバイトエラー対応
        } else {
            echo $pattern.PHP_EOL;
           // echo "空です".PHP_EOL;
            return "";
        }
    }

    private function getBonus($numbers)
    {
        preg_match("#\(.*\)#", $numbers, $matches);
        return str_replace(['(',')'],'',$matches[0]);
    }
}