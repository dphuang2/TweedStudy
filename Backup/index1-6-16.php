<?php
session_start();
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Tweed Twitter Feed Research </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/stylesheet.css">
        <script>
            $("#btn").click(function())
            {
                if($_SESSION['sentiment_positive'].value==false){
                    $_SESSION['sentiment_positive'].value=true;}
                
                else {
                    $_SESSION['sentiment_positive'].value=false;}
            }
        </script>
	</head>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-8">
				<?php
                    
                    $_SESSION['tweet_popular'] = false;
                    $_SESSION['tweet_unpopular'] = false;
                    $_SESSION['poster_frequent'] = false;
                    $_SESSION['poster_infrequent'] = false;
                    $_SESSION['verified'] = false;
                    $_SESSION['unverified'] = true;
                    $_SESSION['sentiment_positive'] = false;
                    $_SESSION['sentiment_negative'] = false;
                    

                    function saveToSQL($connection,$user) {
                        $servername = "engr-cpanel-mysql.engr.illinois.edu";
                        $username = "twitterf_user";
                        $password = "IIA@kT$7maLt";
                        $dbname = "twitterf_tweet_store";
                        
                                
                        /** Array of Happy and Sad words using external .txt file. **/
                        $happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
                        $happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
                        $sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
                        $sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
                        $happyWords = array_filter($happyWords); //Remove all empty elements
                        $happyWords = array_values($happyWords); //Re-key array numerically
                        $sadWords = array_filter($sadWords); //Remove all empty elements
                		$sadWords = array_values($sadWords); //Re-key array numerically                        
                       
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        
                        // Pagination here using cursor string

                        $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
                        
                        // prepare and bind
                        $stmt = $conn->prepare("INSERT INTO data (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

                        if ( false===$stmt ) {
                          die('prepare() failed: ' . htmlspecialchars($mysqli->error));
                        }

                        $stmt->bind_param("isiiiissssssss", $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName);
                        
                        $rc = $stmt->bind_param("isiiiissssssss", $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName);

                        if ( false===$rc ) {
                          // again execute() is useless if you can't bind the parameters. Bail out somehow.
                          die('bind_param() failed: ' . htmlspecialchars($stmt->error));
                        }

                        $user = json_decode(json_encode($user),true);
                        $userid = $user["id"];

                        if ( $json ) {


                            $now = new DateTime();
                            $now->format('D M d H:i:s O Y');
                            $now->getTimestamp();
                            
                            $jsonTweets = json_encode($json);
                            
                            $response = json_decode($jsonTweets,true);
                            
                            foreach($response as $key => $tweet){
                                
                                $userUrl = $tweet['user']['url'];
                                $userImg = $tweet['user']['profile_image_url'];
                                $userSN = $tweet['user']['screen_name'];
                                $userName = $tweet['user']['name'];
                                $tweetTime = $tweet['created_at'];
                                $urlArray = [];
//                                var_dump($tweet["entities"]["media"][0]["media_url"]);
//                                echo "<br>";
                                
                                
//                                if (isset($tweet->entities->media)) {
//                                    $media_url = $tweet["entities"]["media"][0]["media_url"];
//                                    $urlArray[] = $media_url;
//                                    var_dump($media_url);
//                                    echo "<br>";
////                                    foreach ($tweet->entities->media as $media) {
////                                        $media_url = $media["media_url"]; // Or $media->media_url_https for the SSL version.
////                                        $urlArray[] = $media_url;
////                                        var_dump($media_url);
////                                        echo "<br>";
////                                    }
//                                }
//                                    foreach($tweet["entities"]["media"] as $urlinfo){
//                                        $url = $urlinfo["media_url"];
//                                        var_dump($url);
//                                        $urlArray[] = $url;
//                                    }
                                
                                $urlArray = $tweet["entities"]["media"][0]["media_url"];
                                $tweetUrl = json_encode($tweet["entities"]["urls"]);
                                $tweetImg = serialize($urlArray);
//                                $tweetImg = json_encode($tweet["entities"]["media"]);
                                $tweetHash = json_encode($tweet["entities"]["hashtags"]);
                                
                                
                                $status_count = $tweet['user']['statuses_count'];
                                $user_time = $tweet['user']['created_at'];
                                $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $user_time);
                                //$create_date = $new_date->format('Y-m-d H:i:s');
                                $amt_time = $now->diff($create_date);
                                
                                
                                $posterFrequency = round($status_count/$amt_time);
                                #$userid = 1;
                                $text = $tweet['text'];
                                $popularity = $tweet['retweet_count'];
                                if ($tweet['user']['verified']) {
                                    $verified = 1;
                                } else {
                                    $verified = 0;
                                }
                                

                                $happyValue = 0;
                                $tweetArray = explode(" ", $tweet['text']); //explode tweet into Array
                                $tweetArray = preg_replace("/[^a-zA-Z 0-9]+/", "", $tweetArray); // Remove punctuations
                                $tweetArray = array_filter($tweetArray); //Remove all empty elements
                                $tweetArray = array_values($tweetArray); //Re-key array numerically
                                
                                
                                foreach($tweetArray as $tweetWord){ // For each word in the tweet
                                    foreach($happyWords as $happyWord){ // Check with happyWords to
                                        $pos = stripos($tweetWord, $happyWord);
                                        if($pos === 0){
                                            $happyValue++;
                                            break;
                                        }
                                    }
                                }
                                foreach($tweetArray as $tweetIndex => $tweetWord){
                                    foreach($sadWords as $sadIndex => $sadWord){
                                        $pos = stripos($tweetWord, $sadWord);
                                        if($pos === 0){
                                            $happyValue--;
                                            break;
                                        }
                                    }
                                    // echo $tweetWord.$happyValue;
                                }
                                
                                // $rc = $stmt->execute();
                                // if ( false===$rc ) {
                                //   die('execute() failed: ' . htmlspecialchars($stmt->error));
                                // }

                                // if($conn){
                                //     var_dump($conn);
                                // }
                                // else{
                                //     echo "not connected";
                                // }    



                                $stmt->execute();
                                
                                
                                
                            }
                            
                            $stmt->close();

                            


                        }
                        
                        
                        $conn->close();
                        
                    
                    
                    }
                    

                    function getData($connection)
                    {
                        //error_reporting( 0 ); // don't let any php errors ruin the feed
                        //$number_tweets = 200;

                        // check the cache file
                        $cache_file = dirname(__FILE__).'/cache/'.'twitter-cache';
                        if ( file_exists($cache_file)) {
                            
                            $modified = filemtime( $cache_file );
                            $now = time();
                            $interval = 600; // ten minutes
                            
                            if ( ( $now - $modified ) > $interval  ) {
                                $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
                                
                                if ( $json ) {
                                    $cache_static = fopen( $cache_file, 'w' );
                                    fwrite( $cache_static, json_encode($json) );
                                    fclose( $cache_static );
                                }
                            }
                        } else {
                            
                            $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
                            
                            if ( $json ) {
                                $cache_static = fopen( $cache_file, 'w' );
                                fwrite( $cache_static, json_encode($json) );
                                fclose( $cache_static );
                            }
                        
                        }
                        
                        $json = file_get_contents( $cache_file );
                        
                        return $json;
                    }

                    
					// Authorization
					ini_set('display_errors', 1);
					require_once('TwitterAPIExchange.php');
					require 'TwitterOAuth/autoload.php';
					use Abraham\TwitterOAuth\TwitterOAuth;

					define('CONSUMER_KEY', 'HDhjz43hHgbl6B7fEVy3wHApk');
                    define('CONSUMER_SECRET', '9xaTyEdOWSs8O9JCdHUjnYpZCoTj1pn75y7FmAS4o8EzH83LPu');
					define('OAUTH_CALLBACK', 'http://twitterfeed.web.engr.illinois.edu/TweedStudy/index.php');


					$request_token = [];
					$request_token['oauth_token'] = $_SESSION['oauth_token'];
					$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

					if (isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) {
    				// Abort! Something is wrong.
					}

					if ((!isset($_SESSION['oauth_access_token'])) || ($_SESSION['oauth_access_token'])=='') {
					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);
					$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
					$_SESSION['oauth_access_token'] = $access_token['oauth_token'];
					$_SESSION['oauth_access_token_secret'] = $access_token['oauth_token_secret'];
					}
					
					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);

					$user = $connection->get("account/verify_credentials");
                    
                    echo $_SESSION['oauth_access_token'];
                    echo "<br>";
                    echo CONSUMER_KEY;
                    echo "<br>";
                    var_dump($connection);
                    echo "<br>";
                    var_dump($user);

//					/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
//					$settings = array(
//									  'oauth_access_token' => $_SESSION['oauth_access_token'],
//									  'oauth_access_token_secret' => $_SESSION['oauth_access_token_secret'],
//									  'consumer_key' => CONSUMER_KEY,
//									  'consumer_secret' => CONSUMER_SECRET
//									  );
//
//					/** Perform a GET request and echo the response **/
//					$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
//					$requestMethod = 'GET';
//					$twitter = new TwitterAPIExchange($settings);
//					$jsonTweets = $twitter->buildOauth($url, $requestMethod)
//								->performRequest();
                    
                    //$jsonTweets = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));

                    saveToSQL($connection, $user);
                    
//                    $jsonTweets = getData($connection);
                    
					/** Process the response (JSON format) using json_decode: http://docs.php.net/json_decode **/
                    $response = json_decode($jsonTweets,true);

					/** Go through every tweet and print out line by line -- will ideally need some pleasant wrapping with bootstrap -- maybe add IDs to process instead
					 Example of the kind of information that can be returned here: https://dev.twitter.com/rest/reference/get/statuses/home_timeline **/
                    
                   

					 echo "<br>";

					//________________________________________________________EVERYTHING BELOW THIS LINE IS THE ALGORITHM_______________________________________________________________
                    
                    
                    $user_ip = getenv('REMOTE_ADDR');
                    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
                    $country = $geo["geoplugin_countryName"];
                    $city = $geo["geoplugin_city"];
                    
                    $place = $connection->get("geo/search", array("query",$city));
                    
                    if ($city == "") {
                        $place = 23424977;
                    }
                    
                    $trends = $connection->get("trends/place", array("id" => $place));
                    
                    $trends = json_decode(json_encode($trends),true);
                    
//                    print_r($trends["trends"]);
                    
                    $trendsArray = array();
                    
                    foreach ($trends["trends"] as $trend) {
                        //$trendsArray[$trend["tweet_volume"]]=$trend["name"]; //or "query"
                        $trendsArray[]=$trend["name"];
                    }
                    
                    #echo $trendsArray;

//					function in_array_case_insensitive($needle, $haystack) //in_array case-insensitive function
//					{
//					 return in_array( strtolower($needle), array_map('strtolower', $haystack) );
//					}

//					/** Array of Happy and Sad words using external .txt file. **/
//					$happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
//					$happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
//					$sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
//					$sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
//					$happyWords = array_filter($happyWords); //Remove all empty elements
//					$happyWords = array_values($happyWords); //Re-key array numerically
//					$sadWords = array_filter($sadWords); //Remove all empty elements
//					$sadWords = array_values($sadWords); //Re-key array numerically
//
//
//					/** Sets $filter to " " and then sets it to the checked radio button ($_POST['filter']).
//					 Then checks if $filter says it should be filtered by a specific word (ie. == value of filter is "FilterBySpecificWord".
//					 If yes, then $filter is set to $_POST['word'] **/
//					// $happySlider = $_POST['slider'];
//					// if($filter == "FilterBySpecificWord"){
//					// 	$filter = $_POST['word'];
//					// }
//					//echo "Happy Slider Value = ". $happySlider . "<br>";
//
//					//Create an Array of happyValues for the tweets that are returned.
//                    //Create an array of poster frequencies for the tweets
//					$happyValueArray = array();
//                    $posterFrequency = array();
//
//                    //Get current time (to get poster frequency as a function of tweets posted / time since account created)
//                    $now = new DateTime();
//                    $now->format('D M d H:i:s O Y');
//                    $now->getTimestamp();

//					foreach ($response as $key => $tweet){
//								$happyValue = 0;
//								$tweetArray = explode(" ", $tweet['text']); //explode tweet into Array
//								$tweetArray = preg_replace("/[^a-zA-Z 0-9]+/", "", $tweetArray); // Remove punctuations
//								$tweetArray = array_filter($tweetArray); //Remove all empty elements
//								$tweetArray = array_values($tweetArray); //Re-key array numerically
//
//
//								foreach($tweetArray as $tweetWord){ // For each word in the tweet
//									foreach($happyWords as $happyWord){ // Check with happyWords to
//										$pos = stripos($tweetWord, $happyWord);
//										if($pos === 0){
//											$happyValue++;
//											break;
//										}
//									}
//								}
//								foreach($tweetArray as $tweetIndex => $tweetWord){
//									foreach($sadWords as $sadIndex => $sadWord){
//										$pos = stripos($tweetWord, $sadWord);
//										if($pos === 0){
//											$happyValue--;
//											break;
//										}
//									}
//									// echo $tweetWord.$happyValue;
//								}
//
//                                // Get the total number of statuses and the amount of time the account has existed:
//                                $status_count = $tweet['user']['statuses_count'];
//                                $user_time = $tweet['user']['created_at'];
//                                $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $user_time);
//                                //$create_date = $new_date->format('Y-m-d H:i:s');
//                                $amt_time = $now->diff($create_date);
//                                $poster_frequency = $status_count/$amt_time;
//
//                                array_push($posterFrequency, $poster_frequency);
//								array_push($happyValueArray, $happyValue); //add tweet's happyValue to $happyValueArray
//							}

//                    function printTweet($tweet, $boolean, $key, $thisValueArray){
//                        $datetime = $tweet['created_at'];
//                        $new_date = DateTime::createFromFormat('D M d H:i:s O Y', $datetime);
//                        $new_date = $new_date->format('M d');
//                        $tweet_urls = $tweet["entities"]["urls"];
//                        $images = $tweet["entities"]["media"];
//                        $hashtags = $tweet["entities"]["hashtags"];
//                        if($boolean){
//                        	echo '<div class="tweet">';
//                            echo '<div class="container-fluid">';
//                            echo '<div class="row-fluid">';
//                            echo '<div class="col-xs-1">';
//                            echo "<a href={$tweet['user']['url']}><img src={$tweet['user']['profile_image_url']} height='42' width='42'></a>";
//                            echo '</div>';
//                            echo '<div class="col-xs-10">';
//                            echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']}";
//                            if (!empty($tweet_urls)){
//                                foreach ($tweet_urls as $url) {
//                                    echo "<a href={$url['url']}>{$url['display_url']}</a>";
//                                }}
//                            if (!empty($hashtags)){
//                                foreach ($hashtags as $hash) {
//                                    echo "<a href='https://twitter.com/hashtag/{$hash['text']}\?src=hash'>#{$hash['text']}</a>";
//                                }}
//                            echo "|| Value = <b><i>".$thisValueArray[$key]."</b></i><br>";
//                            if (!empty($images)){
//                                foreach ($images as $img) {
//                                    echo "<img src={$img['media_url']} style='max-width:100%;' >";
//                                    //                                    '<br> <img src={$img["media_url"]} >';
//                                }}
//                            echo'</div> </div> </div> <br>';
//
//                            echo "<hr>";
//                            echo "</div>";
//                        }
//                        else{   }
//                    }
                    
//                   (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)
//                    

                    function printEachTweet($tweet){
                        
                        $now = new DateTime();
                        $now->format('D M d H:i:s O Y');
                        $now->getTimestamp();
                        
                        $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $tweet['tweet_create_date']);
                        //$create_date = $new_date->format('Y-m-d H:i:s');
                        $amt_time = $now->diff($create_date);
                        
                        if ($amt_time < 3600) {
                            $print_time = $create_date->format('i') . "m";
                        } elseif ($amt_time < 86400) {
                            $print_time = $create_date->format('H') . "h";
                        } else {
                            $print_time = $create_date->format('M d');
                        }
                        
                        echo '<div class="tweet">';
                        echo '<div class="container-fluid">';
                        echo '<div class="row-fluid">';
                        echo '<div class="col-xs-1">';
                        echo "<a href={$tweet['user_url']}><img src={$tweet['user_profile_img_url']} height='42' width='42'></a>";
                        echo '</div>';
                        echo '<div class="col-xs-10">';
                        echo "<a href={$tweet['user_url']}><b>{$tweet['user_name']}</b></a> @{$tweet['user_screen_name']} • {$print_time}<br>{$tweet['tweet_text']}<br>";
                        
//                        echo $tweet['tweet_images'];
                        
                       
                        
//                        var_dump($tweet['tweet_images']);
                        
//                        var_dump($tweet['tweet_images']);
                        
                        $images = unserialize($tweet['tweet_images']);
                        
//                        $images = json_decode($tweet['tweet_images'], true);
                        
//                        var_dump($images);
                        
                        if (!is_null($images)){
//                            foreach ($images as $img) {
//                                echo $img;
                                echo "<img src={$images} style='max-width:100%;' >";
                            } //}

//                        echo $tweet_urls;
                        
//                        if (!empty($tweet_urls)){
//                            foreach ($tweet_urls as $url) {
//                                echo "<a href={$url['url']}>{$url['display_url']}</a>";
//                            }}
//                        if (!empty($hashtags)){
//                            foreach ($hashtags as $hash) {
//                                echo "<a href='https://twitter.com/hashtag/{$hash['text']}\?src=hash'>#{$hash['text']}</a>";
//                            }}
//                        echo "|| Value = <b><i>".$thisValueArray[$key]."</b></i><br>";

                        echo'</div> </div> </div> <br>';
                        
                        echo "<hr>";
                        echo "</div>";

                    }
                    
//                    $_SESSION['tweet_popular'] = false;
//                    $_SESSION['tweet_unpopular'] = false;
//                    $_SESSION['poster_frequent'] = false;
//                    $_SESSION['poster_infrequent'] = false;
//                    $_SESSION['verified'] = false;
//                    $_SESSION['unverified'] = true;
//                    $_SESSION['sentiment_positive'] = false;
//                    $_SESSION['sentiment_negative'] = false;
//                    $_SESSION['sql_filter'] = "AND tweet_popularity > 10 ";
                    
                    function printTweets_SQL($user){
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

                        $popular_bool = $_SESSION['tweet_popular'];
                        $unpopular_bool = $_SESSION['tweet_unpopular'];
                        $frequent_bool = $_SESSION['poster_frequent'];
                        $infrequent_bool = $_SESSION['poster_infrequent'];
                        $verified_bool = $_SESSION['verified'];
                        $unverified_bool = $_SESSION['unverified'];
                        $sentimentPos_bool = $_SESSION['sentiment_positive'];
                        $sentimentNeg_bool = $_SESSION['sentiment_negative'];

                        // $filterBool_array = array(
                        //     $popular_bool,
                        //     $unpopular_bool,
                        //     $frequent_bool,
                        //     $infrequent_bool,
                        //     $verified_bool,
                        //     $unverified_bool,
                        //     $sentimentPos_bool,
                        //     $sentimentNeg_bool,
                        //     );

// (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags)
                        
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

                        $sql_filter = $_SESSION['sql_filter'];

//                        foreach($sql_filter_statements as $statement){
//                            if ($statement[0]){
//                                $sql_filter .= $statement[1];
//                                echo "boolean is true <br>";
//                            }
//                            else
//                            {
//                                $sql_filter = str_replace($statement[1], '', $sql_filter);
//                                echo "boolean is false <br>";
//                            }
//                        }
                        
                        echo $userid;

                        $sql_syntax = "SELECT * FROM `data` WHERE user_id = {$userid} ";
                        
                        $sql = $sql_syntax . $sql_filter . "ORDER BY tweet_create_date LIMIT 600";

                        echo $sql;

                        if(!$result = $db->query($sql)){
                            die('There was an error running the query [' . $db->error . ']');
                        }

                        while($row = $result->fetch_assoc()){
                            printEachTweet($row);
                        }

                        $_SESSION['tweet_popular'] = $popular_bool;
                        $_SESSION['tweet_unpopular'] = $unpopular_bool;
                        $_SESSION['poster_frequent'] = $frequent_bool;
                        $_SESSION['poster_infrequent'] = $infrequent_bool;
                        $_SESSION['verified'] = $verified_bool;
                        $_SESSION['unverified'] = $unverified_bool;
                        $_SESSION['sentiment_positive'] = $sentimentPos_bool;
                        $_SESSION['sentiment_negative'] = $sentimentNeg_bool;
                        $_SESSION['sql_filter'] = $sql_filter;

                        $db->close();
                    }

                    // $filter = $_GET['filter'];
                    
                    
                    if($_SERVER["REQUEST_METHOD"] == "GET"){ //If a server request has been made, update filter word.
                        //Switch to change from filtering by sentiment or specific word.
                            printTweets_SQL($user);
                    }

                    


//									if($happyValueArray[$key] > 0){
//									if($happyValueArray[$key] < 0){
//                                if($tweet['user']['verified']){
//                                    printTweet($tweet, false, $key,$happyValueArray);
//                                if(!$tweet['user']['verified']){
//                                    printTweet($tweet, false, $key,$happyValueArray);
//                            case "Popular":
//                                if($tweet['retweet_count']>=10){
//                                    printTweet($tweet, false, $key,$happyValueArray);
//                            case "Unpopular":
//                                if($tweet['retweet_count']<10){
//                            case "Frequent":
//                                if($posterFrequency[$key] > 10000){
//                            case "Infrequent":
//                                if($posterFrequency[$key] < 10000){

				?>

			</div>

				<div class="col-xs-4 totop">
                    <button>Hide/Show</button>
                    <div id="newpost">
    					<?php
    					echo "Logged in as ".$user->screen_name;
    					echo "<img src='".$user->profile_image_url."' alt='error'>";
    					?>

    					<h3> Control Panel </h3>
    					<form id="panel" method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">

                            <p>Change the Content You See</p>
                            <button class="astext" id="btn">
                            See more positive tweets </button>
                            <button class="astext" id="btn">
                            See more negative tweets </button>
                            <hr/>

                            <a href="index.php?filter=Popular">
                            See more popular tweets </a> <br>
                            <a href="index.php?filter=Unpopular">
                            See more tweets that haven't gotten attention</a>
                            <hr/>
                            <p>Some trending topics:</p>
                            <?php
                                $subArray = array_rand($trendsArray, min(7, count($trendsArray)));
                                foreach ($subArray as $ind) {
                                    $trend = $trendsArray[$ind];
                                    echo "&nbsp&nbsp&nbsp&nbsp<a href='index.php?filter={$trend}'>{$trend}</a> <br>";
                                }
                                ?>
                            <br>
                            <p>Change the People You See</p>
                            <a href="index.php?filter=Frequent">
                            See more frequent posters </a> <br>
                            <a href="index.php?filter=Infrequent">
                            See more infrequent posters </a>
                            <hr/>
                            <a href="index.php?filter=Close">
                            See more of your close friends </a> <br>
                            <a href="index.php?filter=Distant">
                            See more distant friends</a>
                            <hr/>
                            <a href="index.php?filter=Public">
                            See more celebrities </a> <br>
                            <a href="index.php?filter=Real">
                            See more real people</a> <br>


//    	                    <p>Change the Content You See</p>
//    	                        <a href= "index.php?filter=HappySentiment">
//    	                            See more positive tweets </a> <br>
//    	                        <a href="index.php?filter=SadSentiment">
//    	                            See more negative tweets </a>
//    	                        <!-- do not filter
//    	                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
//    	                            Remove filter </a> <br> -->
//    	                        <hr/>
//    	                        <a href="index.php?filter=Popular">
//    	                            See more popular tweets </a> <br>
//    	                        <a href="index.php?filter=Unpopular">
//    	                            See more tweets that haven't gotten attention</a>
//    	                        <hr/>
//    	                        <p>Some trending topics:</p>
//    	                            <?php
//    	                                $subArray = array_rand($trendsArray, min(7, count($trendsArray)));
//    	                                foreach ($subArray as $ind) {
//    	                                    $trend = $trendsArray[$ind];
//    	                                    echo "&nbsp&nbsp&nbsp&nbsp<a href='index.php?filter={$trend}'>{$trend}</a> <br>";
//    	                                }
//    	                            ?>
//    	                    <br>
//    	                    <p>Change the People You See</p>
//    	                        <a href="index.php?filter=Frequent">
//    	                        See more frequent posters </a> <br>
//    	                        <a href="index.php?filter=Infrequent">
//    	                        See more infrequent posters </a> 
//    	                        <hr/>
//    	                        <a href="index.php?filter=Close">
//    	                        See more of your close friends </a> <br>
//    	                        <a href="index.php?filter=Distant">
//    	                        See more distant friends</a> 
//    	                        <hr/>
//    	                        <a href="index.php?filter=Public">
//    	                        See more celebrities </a> <br>
//    	                        <a href="index.php?filter=Real">
//    	                        See more real people</a> <br>
    					</form>
                    </div>
				</div>
    		</div>
    	</div>
			<script>
				jQuery(window).scroll(function() {
		   			jQuery('.totop').stop().animate({ right: '0px' });
				});

                $("button").click(function() {
                    $("#newpost").toggle();
                });
			</script>
</html>
