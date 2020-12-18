<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function process($input){
    echo "new Request received";
    $server = $input['server'];
    $log = $input['log'];
    $date = date_create();
    $timestamp = date_timestamp_get($date);

    $file = fopen('errorLog.txt','a+');
    fwrite($file, $timestamp . ":" . $server . ":" . $log . "\n");
    fclose($file);
}

$server = new rabbitMQServer("rabbitMQ.ini", "logger");
echo "server started up";
$server->process_requests('process');
?>
