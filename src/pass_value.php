<?php
	session_start();


// Import all functions
// include 'echoStatement.php';
	include 'printEachTweet.php';
	include 'printTweets_SQL.php';


//SaveToSQL if data_in_db is false
	// if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db'])== false) {
	//     $_SESSION['data_in_db'] = true;
	//     saveToSQL($connection, $user);
	// }


    $pairs = array(
				'only_retweets' =>'no_retweets',
				'no_retweets' =>'only_retweets',
				'tweet_popular' =>'tweet_unpopular',
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
	$value = $_POST['value'];

	if($dataString == 'slider'){
		echo $dataString."<br>";
		echo $value."<br>";
		$_SESSION['button'][$dataString."Value"] = $value;
		if($value == 0){
			$_SESSION['button'][$dataString] = false;
			$_SESSION['button']['close_friends'] = false;
			$_SESSION['button']['distant_friends'] = false;
		} else {
			$_SESSION['button'][$dataString] = true;
			if($value > 0){
				$_SESSION['button']['close_friends'] = true;
				$_SESSION['button']['distant_friends'] = false;
			}elseif($value < 0){
				$_SESSION['button']['close_friends'] = false;
				$_SESSION['button']['distant_friends'] = true;
			}
		}

	}
    elseif ($dataString == 'alloff'){
        foreach($_SESSION['button'] as $key => $value){
            $_SESSION['button'][$key] = false;
        }
        // $response = 'false';
    }
    elseif($dataString == 'refresh'){
				echo dirname("pass_value.php")."/pass_value.php";
        include 'authorization.php';
        include 'saveToSQL.php';
//        include 'tweetsToSQL.php';

        $next_max_id = null;
        $cursor = null;

        echo "The if statement is true, now paging through tweets to refresh. <br>";
        // While there are still tweets, run saveToSQL
        while(true){
            echo "The tweet while statement is true <br>";
            // Preserve previously recieved cursor
            $next_max_id_temp = $next_max_id;
            // Run saveToSQL and store return array into $return_array
            $next_max_id = saveToSQL($connection, $next_max_id_temp);

            $next_max_id_str = (string) $next_max_id;
            echo "The next_max_id is " . $next_max_id_str . "<br>";

            if($next_max_id == $next_max_id_temp || $next_max_id == null){
                break;
            }
        }
    }
    elseif($_SESSION['button'][$dataString]){
		$_SESSION['button'][$dataString] = false;
		// $response = 'false';
	}
	else{
		// $response = 'true';
        if ( array_key_exists($dataString,$pairs)) {
            $pair_name = $pairs[$dataString];
            if ($_SESSION['button'][$pair_name]) {
                $_SESSION['button'][$pair_name] = false;
            }
        } else {
					// run through all other things in sessiopn[]button
					foreach($_SESSION['button'] as $filterkey => $filter)	{
						if(!array_key_exists($filterkey,$pairs)){
					$_SESSION['button'][$filterkey] = false;
				}
				}
			}

			$_SESSION['button'][$dataString] = true;

	}


	// echo '$_SESSION[' . $dataString . '] is ' . $response;

	// echo "\n \n";
	// echoStatement();
	// printTweets_SQL($_SESSION["user"]);

	printTweets_SQL();
?>
