<?php
session_start();
//print_r($_SESSION);
?>
<!DOCTYPE html>
<html>
	<head>
		<title> Twitter Control Panel Research </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	</head>

	<div class="container-fluid">
		<div class="row-fluid">
			<div class="col-xs-8">
				<?php
                    
                    //Pagination



                    function saveToSQL($connection,$user) {
                        $servername = "engr-cpanel-mysql.engr.illinois.edu";
                        $username = "twitterf_user";
                        $password = "IIA@kT$7maLt";
                        $dbname = "twitterf_tweet_store";
                        
                        // Create connection
                        $conn = new mysqli($servername, $username, $password, $dbname);
                        
                        // Check connection
                        if ($conn->connect_error) {
                            die("Connection failed: " . $conn->connect_error);
                        }
                        
                        $user = json_decode(json_encode($user),true);
                        $userid = $user["id"];
                        $username = $user["name"];
                        
                        $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
                        
                        
                        // prepare and bind
                        $stmt = $conn->prepare("INSERT INTO data (user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->bind_param("isiiii", $userid, $text, $popularity, $posterFrequency, $verified, $happyValue);
                        
                        if ( $json ) {
                            
                            $now = new DateTime();
                            $now->format('D M d H:i:s O Y');
                            $now->getTimestamp();
                            
                            $jsonTweets = json_encode($json);
                            
                            $response = json_decode($jsonTweets,true);
                            
                            foreach($response as $key => $tweet){
                                
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
                                
                                
                                /** Array of Happy and Sad words using external .txt file. **/
                                $happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
                                $happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
                                $sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
                                $sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
                                $happyWords = array_filter($happyWords); //Remove all empty elements
                                $happyWords = array_values($happyWords); //Re-key array numerically
                                $sadWords = array_filter($sadWords); //Remove all empty elements
                                $sadWords = array_values($sadWords); //Re-key array numerically

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

					define('CONSUMER_KEY', 'XDMrnx4b7Gdu6fMepQxGC4tfS');
					define('CONSUMER_SECRET', 'ZDXy8Bs63UJqqn6E30gRmeZZrNGoPXSNXN9U8xdKUn5lpHHkFy');
					define('OAUTH_CALLBACK', 'http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php');


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
                    
                    $jsonTweets = getData($connection);
                    
					/** Process the response (JSON format) using json_decode: http://docs.php.net/json_decode **/
                    $response = json_decode($jsonTweets,true);

					/** Go through every tweet and print out line by line -- will ideally need some pleasant wrapping with bootstrap -- maybe add IDs to process instead
					 Example of the kind of information that can be returned here: https://dev.twitter.com/rest/reference/get/statuses/home_timeline **/
                    
                   

					 echo "<br>";
					 echo "<br>";

					//________________________________________________________EVERYTHING BELOW THIS LINE IS THE ALGORITHM_______________________________________________________________

                    $user_ip = getenv('REMOTE_ADDR');
                    $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
                    $country = $geo["geoplugin_countryName"];
                    $city = $geo["geoplugin_city"];
                    
                    $place = $connection->get("geo/search", array("query",$city));
                    
                    if ($city == "") {
                        $place = 1;
                    }
                    
                    $trends = $connection->get("trends/place", array("id" => $place));
                    
                    $trends = json_decode(json_encode($trends[0]),true);
                    
//                    print_r($trends["trends"]);
                    
                    $trendsArray = array();
                    
                    foreach ($trends["trends"] as $trend) {
                        //$trendsArray[$trend["tweet_volume"]]=$trend["name"]; //or "query"
                        $trendsArray[]=$trend["name"];
                    }
                    
                    echo $trendsArray;

					function in_array_case_insensitive($needle, $haystack) //in_array case-insensitive function
					{
					 return in_array( strtolower($needle), array_map('strtolower', $haystack) );
					}

					/** Array of Happy and Sad words using external .txt file. **/
					$happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
					$happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
					$sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
					$sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
					$happyWords = array_filter($happyWords); //Remove all empty elements
					$happyWords = array_values($happyWords); //Re-key array numerically
					$sadWords = array_filter($sadWords); //Remove all empty elements
					$sadWords = array_values($sadWords); //Re-key array numerically


					/** Sets $filter to " " and then sets it to the checked radio button ($_POST['filter']).
					 Then checks if $filter says it should be filtered by a specific word (ie. == value of filter is "FilterBySpecificWord".
					 If yes, then $filter is set to $_POST['word'] **/
					$filter = $_GET['filter'];
					// $happySlider = $_POST['slider'];
					// if($filter == "FilterBySpecificWord"){
					// 	$filter = $_POST['word'];
					// }
					//echo "Happy Slider Value = ". $happySlider . "<br>";

					//Create an Array of happyValues for the tweets that are returned.
                    //Create an array of poster frequencies for the tweets
					$happyValueArray = array();
                    $posterFrequency = array();

                    //Get current time (to get poster frequency as a function of tweets posted / time since account created)
                    $now = new DateTime();
                    $now->format('D M d H:i:s O Y');
                    $now->getTimestamp();

					foreach ($response as $key => $tweet){
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

                                // Get the total number of statuses and the amount of time the account has existed:
                                $status_count = $tweet['user']['statuses_count'];
                                $user_time = $tweet['user']['created_at'];
                                $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $user_time);
                                //$create_date = $new_date->format('Y-m-d H:i:s');
                                $amt_time = $now->diff($create_date);
                                $poster_frequency = $status_count/$amt_time;

                                array_push($posterFrequency, $poster_frequency);
								array_push($happyValueArray, $happyValue); //add tweet's happyValue to $happyValueArray
							}

                    function printTweet($tweet, $boolean, $key, $thisValueArray){
                        $datetime = $tweet['created_at'];
                        $new_date = DateTime::createFromFormat('D M d H:i:s O Y', $datetime);
                        $new_date = $new_date->format('M d');
                        $tweet_urls = $tweet["entities"]["urls"];
                        $images = $tweet["entities"]["media"];
                        $hashtags = $tweet["entities"]["hashtags"];
                        if($boolean){
                            echo '<div class="container-fluid">';
                            echo '<div class="row-fluid">';
                            echo '<div class="col-xs-1">';
                            echo "<a href={$tweet['user']['url']}><img src={$tweet['user']['profile_image_url']} height='42' width='42'></a>";
                            echo '</div>';
                            echo '<div class="col-xs-10">';
                            echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']}";
                            if (!empty($tweet_urls)){
                                foreach ($tweet_urls as $url) {
                                    echo "<a href={$url['url']}>{$url['display_url']}</a>";
                                }}
                            if (!empty($hashtags)){
                                foreach ($hashtags as $hash) {
                                    echo "<a href='https://twitter.com/hashtag/{$hash['text']}\?src=hash'>#{$hash['text']}</a>";
                                }}
                            echo "|| Value = <b><i>".$thisValueArray[$key]."</b></i><br>";
                            if (!empty($images)){
                                foreach ($images as $img) {
                                    echo "<img src={$img['media_url']} style='max-width:100%;' >";
                                    //                                    '<br> <img src={$img["media_url"]} >';
                                }}
                            echo'</div> </div> </div> <br>';
                        }
                        else{
                            echo '<div class="container-fluid">';
                            echo '<div class="row-fluid">';
                            echo '<div class="col-xs-1">';
                            echo "<a href={$tweet['user']['url']}><img src={$tweet['user']['profile_image_url']} height='42' width='42'></a>";
                            echo '</div>';
                            echo '<div class="col-xs-10">';
                            echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']}";
                            if (!empty($tweet_urls)){
                                foreach ($tweet_urls as $url) {
                                    echo "<a href={$url['url']}>{$url['display_url']}</a>";
                                }}
                            if (!empty($hashtags)){
                                foreach ($hashtags as $hash) {
                                    echo "<a href='https://twitter.com/hashtag/{$hash['text']}\?src=hash'>#{$hash['text']}</a>";
                                }}
                            echo "<br>";
                            if (!empty($images)){
                                foreach ($images as $img) {
                                    echo "<img src={$img['media_url']} style='max-width:100%;' >";
//                                    '<br> <img src={$img["media_url"]} >';
                                }}
                            echo'</div> </div> </div> <br>';
                        }
                    }


					if($_SERVER["REQUEST_METHOD"] == "GET"){ //If a server request has been made, update filter word.
						//Switch to change from filtering by sentiment or specific word.
						switch($filter) {
							case "HappySentimentPortion": //In the case of "HappySentinmentPortion"
								foreach($response as $key => $tweet){ //Foreach response, known as tweet, create happyValue = 0, and tweetArray from tweet['response']
									if($happyValueArray[$key] > 0){ //If happy value is > 0, then echo tweet if a random value is less than $happySlider
										$randomNumber = rand(0,100);
										if ($randomNumber < $happySlider){
											printTweet($tweet, true, $key);
										}
									}
									elseif($happyValueArray[$key] < 0){
										$randomNumber = rand(0,100);
										if ($randomNumber > $happySlider){
											printTweet($tweet, true, $key);
										}
									}
									elseif($happyValueArray[$key] == 0){
										$randomNumber = rand(0,100);
										if ($randomNumber < 50){
											printTweet($tweet, true, $key);
										}
									}
									}
								break;
							case "HappySentiment":
								foreach($response as $key => $tweet)
								{
									if($happyValueArray[$key] > 0){
										printTweet($tweet, true, $key,$happyValueArray);
										}
								}
								break;
							case "SadSentiment":
								foreach($response as $key => $tweet)
								{
									if($happyValueArray[$key] < 0){
												printTweet($tweet, true, $key,$happyValueArray);
										}
								}
								break;
                            case "Public":
                                foreach($response as $key => $tweet)
                            {
                                if($tweet['user']['verified']){
                                    printTweet($tweet, false, $key,$happyValueArray);
                                }
                            }
                                break;
                            case "Real":
                                foreach($response as $key => $tweet)
                            {
                                if(!$tweet['user']['verified']){
                                    printTweet($tweet, false, $key,$happyValueArray);
                                }
                            }
                                break;
                            case "Popular":
                                foreach($response as $key => $tweet)
                            {
                                if($tweet['retweet_count']>=10){
                                    printTweet($tweet, false, $key,$happyValueArray);
                                }
                            }
                                break;
                            case "Unpopular":
                                foreach($response as $key => $tweet)
                            {
                                if($tweet['retweet_count']<10){
                                    printTweet($tweet, false, $key,$happyValueArray);
                                }
                            }
                                break;
                            case "Frequent":
                                foreach($response as $key => $tweet)
                            {
                                if($posterFrequency[$key] > 10000){
                                    printTweet($tweet, true, $key,$posterFrequency);
                                }
                            }
                                break;
                            case "Infrequent":
                                foreach($response as $key => $tweet)
                            {
                                if($posterFrequency[$key] < 10000){
                                    printTweet($tweet, true, $key,$posterFrequency);
                                }
                            }
                                break;
							default: // filter by string
								foreach($response as $tweet)
								{
									printTweet($tweet, false, $key,$happyValueArray);
								}
								break;
						}
					}
					//If a server request has not been made, for each twitter, echo the following format.
					else{
						foreach($response as $tweet)
						{
								printTweet($tweet, false, $key);
						}
					}
					//___________________________________________________EVERYTHING ABOVE THIS LINE IS THE ALGORITHM_______________________________________________________________
				?>

			</div>
			<div class="col-xs-4">

				<?php
				echo "Logged in as ".$user->screen_name;
				echo "<img src='".$user->profile_image_url."' alt='error'>";
				?>

				<h3> Control Panel </h3>
				<form id="panel" method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">


                    <p>Change the Content You See</p>
                        <!-- binarily filter happy content -->
                        <a href= "index.php?filter=HappySentiment">
                            See more positive tweets </a> <br>
                        <a href="index.php?filter=SadSentiment">
                            See more negative tweets </a> <br>
                        <!-- do not filter
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                            Remove filter </a> <br> -->
                        <hr/>
                        <a href="index.php?filter=Popular">
                            See more popular tweets </a> <br>
                        <a href="index.php?filter=Unpopular">
                            See more tweets that haven't gotten attention</a> <br>
                        <hr/>
                        <p>See more on these topics:</p>
                            <?php
                                $subArray = array_rand($trendsArray, 7);
                                foreach ($subArray as $ind) {
                                    $trend = $trendsArray[$ind];
                                    echo "&nbsp&nbsp&nbsp&nbsp<a href='index.php?filter={$trend}'>{$trend}</a> <br>";
                                }
                            ?>
                    <br>
                    <p>Change the People You See</p>

                        <!-- binarily filter happy content -->
                        <a href="index.php?filter=Frequent">
                        See more frequent posters </a> <br>
                        <a href="index.php?filter=Infrequent">
                        See more infrequent posters </a> <br>
                        <hr/>
                        <a href="index.php?filter=Close">
                        See more of your close friends </a> <br>
                        <a href="index.php?filter=Distant">
                        See more distant friends</a> <br>
                        <hr/>
                        <a href="index.php?filter=Public">
                        See more celebrities </a> <br>
                        <a href="index.php?filter=Real">
                        See more real people</a> <br>

				</form>
			</div>
		</div>
	</div>

</html>
