<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function dbRequest($type, $data){
	$client = new rabbitMQClient("rabbitMQ.ini", "database");
	$response = $client->send_request(array("type" => $type, "data" => $data));
	return $response;
}
?>