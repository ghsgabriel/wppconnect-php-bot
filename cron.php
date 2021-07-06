<?php

ini_set('max_execution_time', '70');
$time_init = time();

require __DIR__ . '/vendor/autoload.php';

use Bot\Request;
use Bot\Session\Session;
use Bot\Storage;

$storage = Storage::load();

while(true){
    $results = $storage->getCronNumbers();
    foreach ($results as $r){
        $request = Request::load();
        $request->setParam('to', $r['bot']);
        $request->setParam('from', $r['user']);
        $request->setParam('content', false);
        $session = Session::load();
        $session->processaCron();
        $storage->__destruct();
        unset($request);
        unset($session);
        sleep(1);
    }
    sleep(2);
    if (time() - $time_init > 50) die();
}