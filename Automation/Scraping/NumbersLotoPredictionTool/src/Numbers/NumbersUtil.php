<?php

namespace Test\Numbers;

// ?? 意味ある？
class NumbersUtil {

    /*
     *  全パターンを出力
     */
    public static function getAllNumbersPattern($numbersType) {
        $result = [];
        $max = str_repeat(9,$numbersType);
        for ($i = 0; $i <= $max; $i++) {
            $result[] = str_pad($i, $numbersType, '0', STR_PAD_LEFT);
        }
        return $result;
    }

    /**
     * 移植　BOXの全パターンを出す。
     */
    private function getAllBoxNumbersPattern() {
        $result = [];
        foreach($this->allNumbersStringList as $numbers) {
            $number_array = str_split($numbers);
            sort($number_array);
            $result[] = implode($number_array);
        }
        return array_unique($result);
    }
}
