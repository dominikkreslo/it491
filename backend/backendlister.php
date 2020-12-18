<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function logError($data){
    $client = new rabbitMQClient("rabbitMQ.ini", "logger");
    $response = $client->send_request($data);
    return $response;
}

function sanatize($db, $input){
    return mysqli_real_escape_string($db, $input);
}

function login($db, $username, $password){
    $password = sha1($password);
    $sql = "SELECT * FROM users WHERE username = '{$username}' AND password = '{$password}';";
    $result = mysqli_query($db, $sql);
    if(!$result){
        logError(array("server" => "backend", "log" => "'$sql' failed to execute"));
        return array("error" => true, "code" => 1, "message" => "failed to execute sql query");
    }
    if(mysqli_num_rows($result) == 0){
        logError(array("server" => "backend", "log" => "user failed to log in"));
        return array("error" => true, "code" => 2, "message" => "login failed");
    }else{
        return array("error" => false, "code" => 0, "message" => "login succesful");
    }
}

function register($db, $input){
    $username = sanatize($db, $input['username']);
    $password = sha1(sanatize($db, $input['password']));

    $sql = "SELECT * FROM users WHERE username = '{$username}';";
    $result = mysqli_query($db, $sql);
    if(!$result){
        logError(array("server" => "backend", "log" => "'$sql' failed to execute"));
        return array("error" => true, "code" => 1, "message" => "failed to execute sql query");
    }
    if(mysqli_num_rows($result) == 0){
        $sql = "INSERT INTO users (username, password) values ('{$username}', '{$password}');";
        $result = mysqli_query($db, $sql);
        if(!$result){
            return array("error" => true, "code" => 1, "message" => "failed to create user");
        }else{
            return array("error" => false, "code" => 0, "message" => "user created properly");
        }
    }else{
        return array("error" => true, "code" => 2, "message" => "username already in use");
    }
}

function process($input){
    include('credentials.php');
    echo "new request received";
    var_dump($input);
    $db = mysqli_connect($hostname,$username,$password,$project);
	if(mysqli_connect_error()){
		Print "Failed to connect to MYSQL:" . mysqli_conect_error();
        $result = mysqli_connect_error();
        logError(array("server" => "backend", "log" => "Failed to connect to MYSQL:" .mysqli_conect_error()));
		exit();
	}
    mysqli_select_db($db, $project);
    
    switch($input['type']){
        case "login":
            return login($db, sanatize($db, $input['data']['username']), sanatize($db, $input['data']['password']));
        case "register":
            return register($db, $input['data']);
    }
}

$server = new rabbitMQServer("rabbitMQ.ini", "database");
echo "server started up";
$server->process_requests('process');
?>


