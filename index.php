<?php

require __DIR__ . '/vendor/autoload.php';

use Bot\Bot;
use Bot\Request;

$request = Request::load();
if(@$request->getEvent() !== 'onmessage' || !@$request->getMessage() || $request->getIsMedia()){
    die();
}
//$request->logRequest();

Bot::load()->run();