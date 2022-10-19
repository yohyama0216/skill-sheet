<?php

require('./YouTubeApi.php');
require('./DoNotUpload.php');

$api = new YouTubeApi();
$api->sendRepeat();