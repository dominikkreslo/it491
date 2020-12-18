<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function process($identifier){
	$ch = curl_init();
	$url = "https://pokeapi.co/api/v2/pokemon/" . $identifier;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$repsonse = curl_exec($ch);
	return json_decode($response, TRUE);
}

$server = new rabbitMQServer("rabbitMQ.ini", "dmz");
echo "server started up";
$server->process_requests('process');
?>