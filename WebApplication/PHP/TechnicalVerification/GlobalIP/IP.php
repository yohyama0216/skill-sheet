<?php

namespace Test\IP;

/**
 * 現在地をIPを表示するだけ
 */
class IP
{
    private $url = "http://api.ipify.org/?format=json";
    private $response = [];

    public function __construct()
    {
        $this->responseJson = json_decode(file_get_contents($this->url),true);
    }

    public function getResponse(){
        return $this->response;
    }
}