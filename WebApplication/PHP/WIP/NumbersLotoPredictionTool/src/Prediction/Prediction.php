<?php

namespace Test\Prediction;

use Test\Numbers\NumbersPastData;

class Prediction {
    private $NumbersPastData;
    private $StaticsService;

    public function __construct($data) {
        $this->NumbersPastData = new NumbersPastData(3);
    }

    public function predict() {
        $this->predict();
    }
}