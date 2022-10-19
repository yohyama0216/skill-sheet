
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
array_walk_recursive($in, function($value, $key) use (&$out) {
    $firstChar = str_split($key)[0];
    if (is_numeric($firstChar)) {
        $out[$key] = $value;
    } else {
        $out[$value] = $key;
    }
});
?>