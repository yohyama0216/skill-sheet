<?php

class ClashRoyale
{
    public static function getDeckNameFromCopyUrl($deckCopyUrl)
    {
        $cardIdList = self::getIdListFromCopyUrl($deckCopyUrl);
        return self::getDeckName($cardIdList);
    }

    public static function getIdListFromCopyUrl($url)
    {
        $query_str = parse_url($url)['query'];
        parse_str($query_str, $queries);
        return explode(';',$queries['deck']);
    }

    public static function getDeckName($cardIdList)
    {
        $deckName = "";
        $mainCardList = [
            '26000023' => '遅延',
            '27000002' => '迫撃',
            '26000009' => 'ゴレ',
            '26000060' => 'ゴブジャイ',
            '26000003' => 'ジャイ',
            '26000033' => 'スパーキー',
            '26000029' => 'ラヴァ',
            '28000013' => 'クローン',
            '26000004' => 'ペッカ',
            '26000035' => 'ランバー',
            '26000006' => 'バルーン',
            '26000020' => '巨スケ',
            '28000014' => 'クエイク',
            '26000021' => 'ホグ',
            '26000024' => 'ロイジャイ',
            '26000072' => 'アチャクイ',
            '27000013' => 'ゴブドリ',
            '26000074' => 'ゴルナイ',
            '26000069' => 'スケキン',
            '26000059' => 'ロイホグ',
            '26000028' => '三銃士',
            '26000047' => '親衛隊',
            '26000055' => 'メガナイト',
            '26000032' => 'ディガー',
            '26000067' => 'エリゴレ',
            '26000085' => 'エレジャイ',
            '27000008' => 'クロス',
            '26000034' => 'ボウラー',
            '26000036' => '攻城',
            '26000043' => 'エリババ',
            '26000045' => 'ファルチェ',
            //'26000048' => 'ダクネ',
            '26000051' => 'ラム',
            '26000054' => 'ムート',
            '28000010' => 'スケラ',
            '26000058' => 'WB',
            //'26000018' => 'ミニぺ',
            '28000004' => '枯渇',
            '28000006' => 'ミラー'

        ];

        foreach($mainCardList as $id => $name) {
            if (in_array($id,$cardIdList)) {
                $deckName .= $name;
            }
        }
        return $deckName;
    }
}

// $testUrl = "https://link.clashroyale.com/deck/en?deck=26000026;26000043;26000046;26000047;26000050;26000058;26000064;27000013";
// ClashRoyale::getDeckNameFromCopyUrl($testUrl);
