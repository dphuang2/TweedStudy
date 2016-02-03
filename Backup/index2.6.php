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
					ini_set('display_errors', 1);
					require_once('TwitterAPIExchange.php');
					
					/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
					$settings = array(
									  'oauth_access_token' => "260761339-a3pWqxRpZV0y9A0QyTVnRLVWOzlmX0x8bfN58g4N",
									  'oauth_access_token_secret' => "rKKWoOtvG1rPSxVvfsbnL9CK8eCQpLrGkeJuUi1Pbv0oq",
									  'consumer_key' => "AA3VCruTsNQKUArY0V2vSznVC",
									  'consumer_secret' => "xuZeCJagWK70LGOQKfdMpjYVmNx0wArhKnirz5MyGyWIbWPQ6B"
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
					$filter = $_POST['filter'];
					$happySlider = $_POST['slider'];
					if($filter == "FilterBySpecificWord"){
						$filter = $_POST['word'];
					}
					//echo "Happy Slider Value = ". $happySlider . "<br>";

					//Create an Array of happyValues for the tweets that are returned.
					$happyValueArray = array();

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
								
								array_push($happyValueArray, $happyValue); //add tweet's happyValue to $happyValueArray
							}
									
					function printTweet($tweet, $boolean, $key){
						global $happyValueArray;
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
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> • {$new_date}<br>{$tweet['text']} || Happy = <b><i>".$happyValueArray[$key]."</b></i>";
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


					if($_SERVER["REQUEST_METHOD"] == "POST"){ //If a server request has been made, update filter word.
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
										printTweet($tweet, true, $key);
										}
								}
								break;
							case "SadSentiment":
								foreach($response as $key => $tweet)
								{
									if($happyValueArray[$key] < 0){
												printTweet($tweet, true, $key);
										}
								}
								break;
							default: // filter by string
								foreach($response as $tweet)
								{
									if(is_string(stristr($tweet['text'], "$filter", false))){
										printTweet($tweet, false, $key);
										}
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
				<h3> Control Panel </h3>
				<form id="panel" method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">


					<!-- Filter by string -->
					<!-- <input type="radio" name="filter" value="man"> Find tweets with the word "man". <br>
					<input type="radio" name="filter" value="woman"> Find tweets with the word "woman". <br>
					<input type="radio" name="filter" value="the"> Find tweets with the word "the". <br>
					<input type="radio" name="filter" value="FilterBySpecificWord"> 
					<input type="text" name="word">Filter any word!<br>
					<!-- filter happy sentiment with slider -->
					<!-- <input type="radio" name="filter" value="HappySentimentPortion"> Portion Happy and Sad tweets with slider!<br>
					SAD<input type="range" name="slider" min="0" max="100" step="5">HAPPY (default 50)<br> -->
					

					<!-- binarily filter happy content -->
					<input type="radio" name="filter" value="HappySentiment"> See more positive tweets <br>
					<input type="radio" name="filter" value="SadSentiment"> See more negative tweets <br>
					<!-- do not filter -->
					<input type="radio" name="filter" value=" " checked="checked"> Do not filter.<br>
					<input type="submit">
				</form>
			</div>
		</div>
	</div>

</html>