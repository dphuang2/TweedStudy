<!DOCTYPE html>

<html>
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

		function in_array_case_insensitive($needle, $haystack) //in_array case-insensitive function
		{
		 return in_array( strtolower($needle), array_map('strtolower', $haystack) );
		}

		/** Array of Happy and Sad words using external .txt file. **/
		$happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
		$happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords);
		$sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
		$sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
		/** Sets $filter to " " and then sets it to the checked radio button ($_POST['filter']).
		 Then checks if $filter says it should be filtered by a specific word (ie. == value of filter is "FilterBySpecificWord". 
		 If yes, then $filter is set to $_POST['word'] **/
		$filter = $_POST['filter'];
		$happySlider = $_POST['slider'];
		if($filter == "FilterBySpecificWord"){
			$filter = $_POST['word'];
		}
		echo "Happy Slider Value = ". $happySlider . "<br>";

		if($_SERVER["REQUEST_METHOD"] == "POST"){ //If a server request has been made, update filter word.
			//Switch to change from filtering by sentiment or specific word.
			switch($filter) {
				case "HappySentimentPortion": //In the case of "HappySentinmentFilter"
					foreach($response as $tweet){ //Foreach response, known as tweet, create happyValue = 0, and tweetArray from tweet['response']
						$happyValue = 0;
						$tweetArray = explode(" ", $tweet['text']); //explode tweet into Array and then get rid of all punctuation.
						$tweetArray = preg_replace("/[^a-zA-Z 0-9]+/", "", $tweetArray); // Remove punctuations
						$tweetArray = array_filter($tweetArray); //Remove all empty elements
						$tweetArray = array_values($tweetArray); //Re-key array numerically
						
						for($i = 0;$i < count($tweetArray);$i++){ //for each item in tweetArray, check if it is in happyWords, if yes, happyValue++
							if(in_array_case_insensitive($tweetArray[$i], $happyWords)){
								$happyValue++;
							}
							if(in_array_case_insensitive($tweetArray[$i], $sadWords)){ //for each item in tweetArray, check if it is in happyWords, if yes, happyValue--
								$happyValue--;
							}
							// echo "[".$i."]";
							// echo "<b>".$happyValue."</b>";

						}
						// echo "<br>";
						// for($i = 0;$i < count($tweetArray);$i++){
						// 	echo $i.$tweetArray[$i]." ";
						// }
						// 	echo "<br>";
						
						if($happyValue > 0){ //If happy value is > 0, then echo tweet if a random value is less than $happySlider
							$randomNumber = rand(0,100);
							// echo $randomNumber. " ";
							// echo $happySlider;
							if ($randomNumber < $happySlider){
								echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']} | | Happy = <b><i>" .$happyValue. "</b></i><br>";
							}
						}
						elseif($happyValue < 0){
							$randomNumber = rand(0,100);
							if ($randomNumber > $happySlider){
								echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']} | | Happy = <b><i>" .$happyValue. "</b></i><br>";
							}
						}
						elseif($happyValue == 0){
							$randomNumber = rand(0,100);
							if ($randomNumber < 50){
								echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']} | | Happy = <b><i>" .$happyValue. "</b></i><br>";
							}
						}

						}
				break;
				case "SadSentiment":
					foreach($response as $tweet)
						if($happyValue > 0){
							{
									echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
							}
						}
				break;
				case "HappySentiment":
					foreach($response as $tweet)
						if($happyValue < 0){
							{
									echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
							}
						}
				break;
				default: // filter by string
					foreach($response as $tweet)
					{
						if(is_string(stristr($tweet['text'], "$filter", false))){
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
						}
					}
				break;
			}
		}
		//If a server request has not been made, for each twitter, echo the following format.
		else{
			foreach($response as $tweet)
			{
					echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
			}
		}
		// Implement a input where the user provides the ratio of positive tweets and negative tweets between 0 and 1. Using a random number generator
		// Produce a random number between 0 and 1 and if the number is less than the input number, then print the tweet, if not, do not print.
		// For negative tweets, do 1 - the input value and do the same process.
		// For 0 happyValues, use .5 as an input
		// next step is to implement weight of happyValue
		// OCR posemo and negemo list for new dictionary from LIWC 2015
	?>
	
	<h3> Control Panel </h3>
	
	<form method="POST" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<input type="radio" name="filter" value="man"> Find tweets with the word "man". <br>
		<input type="radio" name="filter" value="woman"> Find tweets with the word "woman". <br>
		<input type="radio" name="filter" value="the"> Find tweets with the word "the". <br>
		<input type="radio" name="filter" value="FilterBySpecificWord"> 
		<input type="text" name="word">Filter any word!<br>
		
		<input type="radio" name="filter" value="HappySentimentPortion"> Portion Happy and Sad tweets with slider!<br>
		SAD<input type="range" name="slider" min="0" max="100" step="5">HAPPY (default 50)<br>
		
		<input type="radio" name="filter" value="HappySentiment"> Find Happy tweets! <br>
		<input type="radio" name="filter" value="SadSentiment"> Find Sad tweets! <br>
		
		<input type="radio" name="filter" value=" " checked="checked"> Do not filter.<br>
		<input type="submit">
	</form>
</html>

