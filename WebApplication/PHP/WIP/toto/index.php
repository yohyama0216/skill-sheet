<?php

require('./totoCondition.php');
require('./Scraper.php');

$StatsRoyaleCondition = new totoCondition();
$StatsRoyaleCondition->getTargetUrlList();
$Scraper = new Scraper($StatsRoyaleCondition);
$Scraper->execute();
// $Scraper->createResultJson();