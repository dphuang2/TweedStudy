<?php
	function printTweets_SQL($user){

	//SQL Authorization
	    $servername = "engr-cpanel-mysql.engr.illinois.edu";
	    $username = "twitterf_user";
	    $password = "IIA@kT$7maLt";
	    $dbname = "twitterf_tweet_store";

	    $db = new mysqli($servername, $username, $password, $dbname);

	    if($db->connect_errno > 0){
	        die('Unable to connect to database [' . $db->connect_error . ']');
	    }

	    $user = json_decode(json_encode($user),true);
	    $userid = $user["id"];
	    $username = $user["name"];
	//Retrieve session booleans
       $popular_bool = $_SESSION['tweet_popular'];
       $unpopular_bool = $_SESSION['tweet_unpopular'];
       $frequent_bool = $_SESSION['poster_frequent'];
       $infrequent_bool = $_SESSION['poster_infrequent'];
       $verified_bool = $_SESSION['verified'];
       $unverified_bool = $_SESSION['unverified'];
       $sentimentPos_bool = $_SESSION['sentiment_positive'];
       $sentimentNeg_bool = $_SESSION['sentiment_negative'];


	    // (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)

	//Create array of booleans and their corresponding statement
       $sql_filter_statements = array(
               "popular_bool" => array($popular_bool, "AND tweet_popularity > 10 "),
               "unpopular_bool" => array($unpopular_bool, "AND tweet_popularity < 10 "),
               "frequent_bool" => array($frequent_bool, "AND poster_frequency > 1000 "),
               "infrequent_bool" => array($infrequent_bool, "AND poster_frequency < 1000 "),
               "verified_bool" => array($verified_bool, "AND verified = 1 "),
               "unverified_bool" => array($unverified_bool, "AND verified = 0 "),
               "sentimentPos_bool" => array($sentimentPos_bool, "AND sentiment > 0 "),
               "sentimentNeg_bool" => array($sentimentNeg_bool, "AND sentiment < 0 "),
           );
	// Initalize filter statement
       $sql_filter = "";


    // Check each boolean then add statement if true
       foreach($sql_filter_statements as $statement){
           if ($statement[0]){
               $sql_filter .= $statement[1];
           }
       }

	    echo 'USERID IS ' . $userid . "<br>";
	//Compose statement
	    $sql_syntax = "SELECT * FROM `data` WHERE user_id = {$userid} ";

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
?>
