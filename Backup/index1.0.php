<!DOCTYPE html>

<html>
	<?php
		//sets $filter to nothing and then assigns it to radio value if a radio value exists.
		$filter = '';
		if(!empty($_POST['filter'])){
			$filter = $_REQUEST['filter'];
		}
		
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
		
		//If a server request has been made, use switches to determine how the text is going to be filtered.
		if($_SERVER["REQUEST_METHOD"] == "POST"){
			switch($filter){
				case "man":
					foreach($response as $tweet)
					{
						if(is_string(strstr($tweet['text'], "man", false))){
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
						}
					}
					break;
				case "woman":
					foreach($response as $tweet)
					{
						if(is_string(strstr($tweet['text'], "woman", false))){
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
						}
					}
					break;
				case "the":
					foreach($response as $tweet)
					{
						if(is_string(strstr($tweet['text'], "the", false))){
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
						}
					}
					break;
				default:
					foreach($response as $tweet)
					{
							echo "<a href={$tweet['user']['url']}>{$tweet['user']['screen_name']}</a> {$tweet['text']}<br />";
					}
			}
		}
	?>

	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
		<input type="radio" name="filter" value="man"> Find tweets with the word "man". <br>
		<input type="radio" name="filter" value="woman"> Find tweets with the word "woman". <br>
		<input type="radio" name="filter" value="the"> Find tweets with the word "the". <br>
		<input type="radio" name="filter" value=""> Do not filter.<br>
		<input type="submit">
	</form>
</html>