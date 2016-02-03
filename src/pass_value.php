<?php
	session_start();

	include 'authorization.php';
	include 'echoStatement.php';
	include 'printEachTweet.php';
	include 'printTweets_SQL.php';
	include 'saveToSQL.php';

//SaveToSQL if data_in_db is false
	if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db'])== false) {
	    $_SESSION['data_in_db'] = true;
	    saveToSQL($connection, $user);
	}
	$dataString = $_POST['dataString'];

	if($_SESSION[$dataString]){
		$_SESSION[$dataString] = false;
		$response = 'false';
	}
	else{
		$_SESSION[$dataString] = true;
		$response = 'true';
	}

	// echo '$_SESSION[' . $dataString . '] is ' . $response;

	// echo "\n \n";
	// echoStatement();
	// printTweets_SQL($_SESSION["user"]);
	printTweets_SQL($user);
?>
