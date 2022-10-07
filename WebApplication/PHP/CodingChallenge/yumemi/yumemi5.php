
<?php
$in = [
    ['2nd' => 'two', 'four' => '4th'],
    'three' => '3rd',
    ['one' => '1st'],
    '10th' => 'ten',
    ['6th' => 'six'],
    '5th' => 'five',
    'seven' => '7th',
    ['fourteen' => '14th', '11th' => 'eleven'],
    ['8th' => 'eight'],
    'thirteen' => '13th',
    '12th' => 'twelve',
    'nine' => '9th',
    ['15th' => 'fifteen'],
];

$out = [];
foreach($in as $key1 => $item1) {
    if (is_array($item1)){
        foreach($item1 as $key2 => $item2) {
            setKeyAndValue($key2,$item2,$out);
        }
    } else {
        setKeyAndValue($key1,$item1,$out);
    }
}
ksort($out,SORT_NUMERIC);
print_r($out);

function setKeyAndValue($key, $item, &$out) {
    $firstChar = str_split($key)[0];
    if (is_numeric($firstChar)) {
        $out[$key] = $item;
    } else {
        $out[$item] = $key;
    }
}

?>