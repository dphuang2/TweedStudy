<?php
	function printTweets_SQL(){

	//SQL Authorization
	    $servername = "engr-cpanel-mysql.engr.illinois.edu";
	    $username = "twitterf_user";
	    $password = "IIA@kT$7maLt";
	    $dbname = "twitterf_tweet_store";

	    $db = new mysqli($servername, $username, $password, $dbname);

	    if($db->connect_errno > 0){
	        die('Unable to connect to database [' . $db->connect_error . ']');
	    }

			$user_id = $_SESSION["user_id"];

	//Retrieve session booleans
			$only_retweets_bool = $_SESSION['button']['only_retweets'];
			$no_retweets_bool = $_SESSION['button']['no_retweets'];
			$popular_bool = $_SESSION['button']['tweet_popular'];
			$unpopular_bool = $_SESSION['button']['tweet_unpopular'];
			$frequent_bool = $_SESSION['button']['poster_frequent'];
			$infrequent_bool = $_SESSION['button']['poster_infrequent'];
			$verified_bool = $_SESSION['button']['verified'];
			$unverified_bool = $_SESSION['button']['unverified'];
			$sentimentPos_bool = $_SESSION['button']['sentiment_positive'];
			$sentimentNeg_bool = $_SESSION['button']['sentiment_negative'];
			$closeFriends_bool = $_SESSION['button']['close_friends'];
			$distantFriends_bool = $_SESSION['button']['distant_friends'];
			$sessionArray = ['only_retweets', 'no_retweets', 'tweet_popular','tweet_unpopular','poster_frequent','poster_infrequent','verified','unverified','sentiment_positive','sentiment_negative','close_friends','distant_friends'];
        foreach ($_SESSION['button'] as $key=>$val) {
            if (! in_array( $key, $sessionArray )) {
                $trend_bool = $val;
                $trend_name = $key;
            }

        }


	    // (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)


			// Execute two different base SQL syntaxes depending on if where care about closeness.

	//Create array of booleans and their corresponding statement
       $sql_filter_statements = array(
//									"closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` > (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
//									"distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` < (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
									"only_retweets" => array($only_retweets_bool, "AND retweet = 1 "),
									"no_retweets" => array($no_retweets_bool, "AND retweet = 0 "),
									"closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` > 0 "),
                                    "distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`computed_rank` < 0 "),
									"popular_bool" => array($popular_bool, "AND tweet_popularity > 10 "),
									"unpopular_bool" => array($unpopular_bool, "AND tweet_popularity < 10 "),
									"frequent_bool" => array($frequent_bool, "AND poster_frequency > 1000 "),
									"infrequent_bool" => array($infrequent_bool, "AND poster_frequency < 1000 "),
									"verified_bool" => array($verified_bool, "AND verified = 1 "),
									"unverified_bool" => array($unverified_bool, "AND verified = 0 "),
									"sentimentPos_bool" => array($sentimentPos_bool, "AND sentiment > 0 "),
									"sentimentNeg_bool" => array($sentimentNeg_bool, "AND sentiment < 0 "),
                  					"trend_bool" => array($trend_bool, "AND tweet_text LIKE  '%{$trend_name}%' "),
           );
	// Initalize filter statement
       $sql_filter = "";

    // Check each boolean then add statement if true
       foreach($sql_filter_statements as $statement){
           if ($statement[0]){
               $sql_filter .= $statement[1];
           }
       }

	    echo 'USERID IS ' . $user_id . "<br>";
	//Compose statement
		if($closeFriends_bool || $distantFriends_bool){
			$sql_syntax = "SELECT * FROM `data` ";
		}
		else{
			$sql_syntax = "SELECT * FROM `data` WHERE user_id = {$user_id} ";
		}

	    $sql = $sql_syntax . $sql_filter . "ORDER BY tweet_create_date DESC LIMIT 600";

		echo $sql;
	//Print each tweet
	    if(!$result = $db->query($sql)){
	        die('There was an error running the query [' . $db->error . ']');
	    }

	    while($row = $result->fetch_assoc()){
	        printEachTweet($row);
	    }

	    $db->close();
	}


    function printTweets_SQL_rand(){

        //SQL Authorization
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";

        $db = new mysqli($servername, $username, $password, $dbname);

        if($db->connect_errno > 0){
            die('Unable to connect to database [' . $db->connect_error . ']');
        }

        $user_id = $_SESSION["user_id"];

        //Retrieve session booleans
        $popular_bool = $_SESSION['button']['tweet_popular'];
        $unpopular_bool = $_SESSION['button']['tweet_unpopular'];
        $frequent_bool = $_SESSION['button']['poster_frequent'];
        $infrequent_bool = $_SESSION['button']['poster_infrequent'];
        $verified_bool = $_SESSION['button']['verified'];
        $unverified_bool = $_SESSION['button']['unverified'];
        $sentimentPos_bool = $_SESSION['button']['sentiment_positive'];
        $sentimentNeg_bool = $_SESSION['button']['sentiment_negative'];
        $closeFriends_bool = $_SESSION['button']['close_friends'];
        $distantFriends_bool = $_SESSION['button']['distant_friends'];
        $sessionArray = ['tweet_popular','tweet_unpopular','poster_frequent','poster_infrequent','verified','unverified','sentiment_positive','sentiment_negative','close_friends','distant_friends'];
        foreach ($_SESSION['button'] as $key=>$val) {
            if (! in_array( $key, $sessionArray )) {
                $trend_bool = $val;
                $trend_name = $key;
            }

        }


        // (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)


        // Execute two different base SQL syntaxes depending on if where care about closeness.

        //Create array of booleans and their corresponding statement
        $sql_filter_statements = array(
                                       "closeFriends_bool" => array($closeFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` > (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
                                       "distantFriends_bool" => array($distantFriends_bool, "LEFT JOIN `friends` ON `data`.`user_screen_name` = `friends`.`screen_name` WHERE `friends`.`user_id` = {$user_id} AND `friends`.`rank` < (SELECT max(`rank`) FROM `friends` WHERE `user_id` = {$user_id})/2 "),
                                       "popular_bool" => array($popular_bool, "AND tweet_popularity > 10 "),
                                       "unpopular_bool" => array($unpopular_bool, "AND tweet_popularity < 10 "),
                                       "frequent_bool" => array($frequent_bool, "AND poster_frequency > 1000 "),
                                       "infrequent_bool" => array($infrequent_bool, "AND poster_frequency < 1000 "),
                                       "verified_bool" => array($verified_bool, "AND verified = 1 "),
                                       "unverified_bool" => array($unverified_bool, "AND verified = 0 "),
                                       "sentimentPos_bool" => array($sentimentPos_bool, "AND sentiment > 0 "),
                                       "sentimentNeg_bool" => array($sentimentNeg_bool, "AND sentiment < 0 "),
                                       "trend_bool" => array($trend_bool, "AND tweet_text LIKE  '%{$trend_name}%' "),
                                       );
        // Initalize filter statement
        $sql_filter = "";

        // Check each boolean then add statement if true
        foreach($sql_filter_statements as $statement){
            if ($statement[0]){
                $sql_filter .= $statement[1];
            }
        }

        echo 'USERID IS ' . $user_id . "<br>";
        //Compose statement
        if($closeFriends_bool || $distantFriends_bool){
            $sql_syntax = "SELECT COUNT(*) FROM `data` ";
        }
        else{
            $sql_syntax = "SELECT COUNT(*) FROM `data` WHERE user_id = {$user_id} ";
        }

        $sql = $sql_syntax . $sql_filter . "ORDER BY tweet_create_date DESC LIMIT 600";

        echo $sql;
        //Print each tweet
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }

        $numTweets = $result->fetch_assoc();
        echo "Number of tweets <br>";
        var_dump($numTweets["COUNT(*)"]);
        echo "<br>";

        //        $numTweets = (int)$numTweets;

        $sql_new = "SELECT * FROM (SELECT * FROM `data` WHERE user_id = {$user_id} ORDER BY RAND() LIMIT {$numTweets['COUNT(*)']}) a ORDER BY tweet_create_date DESC";

        echo $sql_new;

        if(!$result = $db->query($sql_new)){
            die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
            printEachTweet($row);
        }

        $db->close();
    }

?>
