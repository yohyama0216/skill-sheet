<?php

use Test\Numbers;
use Test\Loto\Loto;

require('vendor/autoload.php');

//$Converter = new Test\Converter('numbers3');
//$html = $Converter->generateResultSQL();

// $NumbersPastData = new NumbersPastData(3);
// //$data = $NumbersPastData->getData();
// $Search = new Test\Search\Search($NumbersPastData);
// $Search->searchNumbersDigitPattern(10,2,'1digit');
// $Search->displayResult();

$Loto = new Loto(6,100,'2020/09/01','01 02 03 04 05 06 07','11 22');


?>
<html>
<head></head>
<body>
    <main>
        test <?php  ?>
    </main>
</body>
</html>