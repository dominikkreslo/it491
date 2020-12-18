<?php
include('helperFunctions.php');

$username = $_REQUEST['username'];
$password = $_REQUEST['password'];


if(empty($username) || empty($password)){
	$return_data = array("error" => true, "message" => "username and or password is invalid");
}else{
	$res = dbRequest("login", array("username" => $username, "password" => $password));
	if(!$res['error']){
		session_start();
		$_SESSION['user'] = $username;
		$return_data = true;
	}else{
		error_log($res['message']);
		$return_data = false;
	}
}

echo json_encode($return_data);
?>
