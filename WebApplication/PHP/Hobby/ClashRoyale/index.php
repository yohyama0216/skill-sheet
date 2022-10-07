<?php

require('./StatsRoyaleCondition.php');
require('./Scraper.php');
require('../Debug/Timer.php');

$start = $argv[1];
$end = $argv[2];
$StatsRoyaleCondition = new StatsRoyaleCondition($start,$end);
//Timer::debug();
$StatsRoyaleCondition->getTargetUrlList();
//Timer::debug();
$Scraper = new Scraper($StatsRoyaleCondition);
$Scraper->execute();
var_dump($Scraper->getResultList());
$Scraper->createResultJson();