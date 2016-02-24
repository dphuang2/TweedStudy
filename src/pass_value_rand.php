<?php
	session_start();

// include 'authorization.php';

// Import all functions
// include 'echoStatement.php';
	include 'printEachTweet.php';
	include 'printTweets_SQL.php';
	include 'saveToSQL.php';

//SaveToSQL if data_in_db is false
	// if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db'])== false) {
	//     $_SESSION['data_in_db'] = true;
	//     saveToSQL($connection, $user);
	// }


    $pairs = array('tweet_popular' =>'tweet_unpopular',
                  'tweet_unpopular' => 'tweet_popular',
                  'poster_frequent' => 'poster_infrequent',
                  'poster_infrequent' => 'poster_frequent',
                  'verified' => 'unverified',
                  'unverified' => 'verified',
                  'sentiment_positive' => 'sentiment_negative',
                  'sentiment_negative' => 'sentiment_positive',
                  'close_friends' => 'distant_friends',
                  'distant_friends' => 'close_friends'
    );

	$dataString = $_POST['dataString'];

    if ($dataString == 'alloff'){
        foreach($_SESSION['button'] as $key => $value){
            $_SESSION['button'][$key] = false;
        }
        // $response = 'false';
    }
    elseif($_SESSION['button'][$dataString]){
		$_SESSION['button'][$dataString] = false;
		// $response = 'false';
	}
	else{
		$_SESSION['button'][$dataString] = true;
		// $response = 'true';
        if ( array_key_exists($dataString,$pairs)) {
            $pair_name = $pairs[$dataString];
            if ($_SESSION['button'][$pair_name]) {
                $_SESSION['button'][$pair_name] = false;
            }
        }
	}


	// echo '$_SESSION[' . $dataString . '] is ' . $response;

	// echo "\n \n";
	// echoStatement();
	// printTweets_SQL($_SESSION["user"]);

	printTweets_SQL_rand();
?>
