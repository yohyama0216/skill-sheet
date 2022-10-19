<?php

class RankingSummary
{
    private $files = [
        '1month.html',
        '2month.html',
        '3month.html',
        '6month.html'
    ];

    public function __construct()
    {
        $rowPattern = "#<tr title=[\S\s]*?</tr>#";
        $result = [];
        foreach($this->files as $file){
            $content = file_get_contents($file);
            preg_match_all($rowPattern,$content, $matches);
            $rows = $matches[0]; 
            foreach($rows as $row) {
                
                $result[$file][] = [
                    'ポジ' => $this->getValue('maxPosi',$row),
                    '最低' => $this->getValue('taishouShisan',$row),
                    '設定' => $this->getValue('tsuuka',$row),
                    '損益' => $this->getValue('proLoss',$row),
                ];
            }

        }
        $list = [];
        foreach($result as $fileName => $fileValues) {
            foreach($fileValues as $row) {
                $list[]
            }
        }
        var_dump($list);
    }

    private function getValue($column,$subject)
    {
        $format = '#<span id="%s".*>(.*)</span>#';
        $pattern = sprintf($format,$column);
        preg_match($pattern,$subject,$mat);
        return $mat[1];
    }

    public function createSummary()
    {

    }
}

$rankingSummary = new RankingSummary();
