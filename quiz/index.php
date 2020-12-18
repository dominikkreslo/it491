<?php
include('helperFunctions.php');

$identifier = $_REQUEST['identifier'];


$res = dbRequest("login", array("username" => $username, "password" => $password));

echo json_encode($res);
?>
