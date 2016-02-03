<?php
session_start();
print_r($_SESSION);
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
			<div class="col-md-8">
				<?php

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

					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $request_token['oauth_token'], $request_token['oauth_token_secret']);

					$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));

					$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);

					$user = $connection->get("account/verify_credentials");

					/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
					$settings = array(
									  'oauth_access_token' => $access_token["oauth_token"],
									  'oauth_access_token_secret' => $access_token["oauth_token_secret"],
									  'consumer_key' => CONSUMER_KEY,
									  'consumer_secret' => CONSUMER_SECRET
									  );

					/** Perform a GET request and echo the response **/
					$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
					$requestMethod = 'GET';
					$twitter = new TwitterAPIExchange($settings);
					$jsonTweets = $twitter->buildOauth($url, $requestMethod)
								->performRequest();

					/** Process the response (JSON format) using json_decode: http://docs.php.net/json_decode **/
					$response = json_decode($jsonTweets,true);

					/** Go through every tweet and print out line by line -- will ideally need some pleasant wrapping with bootstrap -- maybe add IDs to process instead
					 Example of the kind of information that can be returned here: https://dev.twitter.com/rest/reference/get/statuses/home_timeline **/

					 echo "<br>";
					 echo "<br>";

					//________________________________________________________EVERYTHING BELOW THIS LINE IS THE ALGORITHM_______________________________________________________________

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
						if($boolean){
							echo '<div class="container-fluid">';
							echo '<div class="row-fluid">';
							echo '<div class="col-md-1">';
							echo "<a href={$tweet['user']['url']}><img src={$tweet['user']['profile_image_url']} height='42' width='42'></a>";
							echo '</div>';
							echo '<div class="cold-md-6">';
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']} || Value = <b><i>".$thisValueArray[$key]."</b></i>";
							echo'</div> </div> </div> <br>';
						}
						else{
							echo '<div class="container-fluid">';
							echo '<div class="row-fluid">';
							echo '<div class="col-md-1">';
							echo "<a href={$tweet['user']['url']}><img src={$tweet['user']['profile_image_url']} height='42' width='42'></a>";
							echo '</div>';
							echo '<div class="cold-md-6">';
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']}";
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
			<div class="col-md-4">

				<?php
				echo "Logged in as ".$user->screen_name;
				echo "<img src='".$user->profile_image_url."' alt='error'>";
				?>

				<h3> Control Panel </h3>
				<form id="panel" method="GET" action="<?php echo $_SERVER['PHP_SELF'];?>">


					<!-- Filter by string -->
					<!-- <input type="radio" name="filter" value="man"> Find tweets with the word "man". <br>
					<input type="radio" name="filter" value="woman"> Find tweets with the word "woman". <br>
					<input type="radio" name="filter" value="the"> Find tweets with the word "the". <br>
					<input type="radio" name="filter" value="FilterBySpecificWord">
					<input type="text" name="word">Filter any word!<br>
					<!-- filter happy sentiment with slider -->
					<!-- <input type="radio" name="filter" value="HappySentimentPortion"> Portion Happy and Sad tweets with slider!<br>
					SAD<input type="range" name="slider" min="0" max="100" step="5">HAPPY (default 50)<br> -->

                    <p>Change the Content You See</p>
                        <!-- binarily filter happy content -->
                        <a href= "index.php?filter=HappySentiment">
                            See more positive tweets </a> <br>
                        <a href="index.php?filter=SadSentiment">
                            See more negative tweets </a> <br>
                        <!-- do not filter -->
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                            Remove filter </a> <br>
                        <hr/>
                        <a href="index.php?filter=Popular">
                            See more popular tweets </a> <br>
                        <a href="index.php?filter=Unpopular">
                            See more tweets that haven't gotten attention</a> <br>
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                            Remove filter </a> <br>
                        <hr/>
                        <p>See more on these topics:</p>
                    <p>Change the People You See</p>

                        <!-- binarily filter happy content -->
                        <a href="index.php?filter=Frequent">
                        See more frequent posters </a> <br>
                        <a href="index.php?filter=Infrequent">
                        See more infrequent posters </a> <br>
                        <!-- do not filter -->
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                        Remove filter </a> <br>
                        <hr/>
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=Close">
                        See more of your close friends </a> <br>
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=Distant">
                        See more distant friends</a> <br>
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                        Remove filter </a> <br>
                        <hr/>
                        <a href="index.php?filter=Public">
                        See more celebrities </a> <br>
                        <a href="index.php?filter=Real">
                        See more real people</a> <br>
                        <a href="http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php?filter=+">
                        Remove filter </a> <br>

				</form>
			</div>
		</div>
	</div>

</html>
