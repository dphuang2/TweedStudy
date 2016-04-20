<?php
session_start();

// print_r($_SESSION);

?>
<!DOCTYPE html>
<html>
	<head>
		<title> Tweed Twitter Feed Research </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
		<script language="JavaScript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	</head>
  <body>
  	<div class="container-fluid">
  		<div class="row-fluid">
        <!-- Twitter Feed -->
        <div class="col-xs-8" id="feed">
  				<?php

// Importing all functions

include 'src/saveToSQL.php';

 // Save current user's tweets to SQL database

include 'src/saveTrendsToSQL.php';

 // Save trends for current user to DB

include 'src/getData.php';

 // Fetch data and put it into cache

include 'src/printEachTweet.php';

 // Formatting for each tweet

include 'src/printTweets_SQL.php';

 // Printing all tweets

include 'src/saveFriendsToSQL.php';

 // Save friends

include 'src/computeFriendRank.php';

 // As a second step, compute friend rank (need max friend num to do so)

include 'src/savedirectMessagesToSQL.php';

 // Save direct messages
// Resetting all session booleans

$_SESSION['button']['only_links'] = false;
$_SESSION['button']['no_links'] = false;
$_SESSION['button']['only_retweets'] = false;
$_SESSION['button']['no_retweets'] = false;
$_SESSION['button']['tweet_popular'] = false;
$_SESSION['button']['tweet_unpopular'] = false;
$_SESSION['button']['poster_frequent'] = false;
$_SESSION['button']['poster_infrequent'] = false;
$_SESSION['button']['verified'] = false;
$_SESSION['button']['unverified'] = false;
$_SESSION['button']['sentiment_positive'] = false;
$_SESSION['button']['sentiment_negative'] = false;
$_SESSION['button']['close_friends'] = false;
$_SESSION['button']['distant_friends'] = false;
$_SESSION['button']['only_videos'] = false;
$_SESSION['button']['no_videos'] = false;
$_SESSION['button']['only_text'] = false;
$_SESSION['button']['no_text'] = false;
$_SESSION['button']['only_pics'] = false;
$_SESSION['button']['no_pics'] = false;

if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db']) == '') {
	$_SESSION['data_in_db'] = false;
}

function controlPanel()
{
}

// Authorization

include 'src/authorization.php';

echo "<br />";

// SaveToSQL if data_in_db is false

if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db']) == false) {
	$_SESSION['data_in_db'] = true;

	// Initialize $next_max_id and $cursor variable

	$next_max_id = null;
	$cursor = null;
	echo "The if statement is true, now paging through tweets. <br />";

	// While there are still tweets, run saveToSQL

	while (true) {
		echo "The tweet while statement is true <br />";

		// Preserve previously recieved cursor

		$next_max_id_temp = $next_max_id;

		// Run saveToSQL and store return array into $return_array

		$next_max_id = saveToSQL($connection, $next_max_id_temp);
		$next_max_id_str = (string)$next_max_id;
		echo "The next_max_id is " . $next_max_id_str . "<br />";
		if ($next_max_id == $next_max_id_temp || $next_max_id == null) {
			break;
		}
	}

	echo "Saving trends now <br />";
	saveTrendsToSQL($connection);
	echo "Saving DMs now <br />";
	savedirectMessagesToSQL($connection);
	echo "Direct messages saved, now paging through friends. <br />";
	$_SESSION["rank_counter"] = 0;
	while (true) {
		echo "The friends while statement is true <br />";
		$cursor_temp = $cursor;
		$cursor = saveFriendsToSQL($connection, $cursor_temp);
		$cursor_str = (string)$cursor;
		echo "The cursor is " . $cursor_str . "<br />";
		if ($cursor == $cursor_temp || $cursor == null) {
			$_SESSION["rank_counter"]--;
			break;
		}
	}

	unset($_SESSION["rank_counter"]);
	echo "Computing and saving computed friend rank";
	computeFriendRank();

	// saveToSQL($connection, $user, $last_max_id);

}

// $filter = $_GET['filter'];
//                    if($_SERVER["REQUEST_METHOD"] == "GET"){ //If a server request has been made, update filter word.
//                        //Switch to change from filtering by sentiment or specific word.
//
//                    }
//
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
// Print tweets

printTweets_SQL();

// Set index-1 to visited

$_SESSION[index][1] = true;

$servername = "engr-cpanel-mysql.engr.illinois.edu";
$username = "twitterf_user";
$password = "IIA@kT$7maLt";
$dbname = "twitterf_tweet_store";
$userid = $_SESSION["user_id"];

// Create connection

$db = new mysqli($servername, $username, $password, $dbname);

// Check connection

if ($db->connect_error) {
die("Connection failed: " . $conn->connect_error);
}
?>
  			</div>
        <!-- Control Panel -->
  			<div class="col-xs-4 totop">
          <button class="btn" id="toggle">Hide/Show</button>
          <div id="newpost">
		  <div id="loginWrap">
                <?php
echo "<b>" . $_SESSION["user"]["screen_name"];
echo "</b> <img src=" . $_SESSION['user']['profile_image_url'] . " alt='error'>";
?>
								<a href="logout.php"><button class="btn" id="logout">Logout</button></a>
		</div>
								<!-- <h3> Control Panel </h3> -->
				<!-- <h4> See... </h4> -->
			<div id="people">
                <button onclick='boldButton(this.id)' class="astext" id="poster_frequent" data-count="0">
                Frequent posters </button> <br />
                <button onclick='boldButton(this.id)' class="astext" id="poster_infrequent" data-count="0">
                Infrequent posters </button> <br>

				<?php
				$sql = "SELECT MAX(poster_frequency) AS MAX FROM data WHERE user_id={$userid}";

				if (!$result = $db->query($sql)) {
					die('There was an error running the query [' . $db->error . ']');
				}
				$max = $result->fetch_assoc();
				$max = $max["MAX"];
				?>

				<input id="frequencySlider" class="slider" type="range" min="0" max="<?php echo $max; ?>" value="<?php echo $max/2; ?>" step="5">

                <hr/>

                <button onclick='boldButton(this.id)' class="astext" id="close_friends" data-count="0">
                Close friends </button> <br />
                <button onclick='boldButton(this.id)' class="astext" id="distant_friends" data-count="0">
                Distant friends</button> <br>

				<?php
				$sql = "SELECT MAX(computed_rank) AS MAX FROM friends WHERE user_id={$userid}";
				if (!$result = $db->query($sql)) {
					die('There was an error running the query [' . $db->error . ']');
				}
				$max = $result->fetch_assoc();
				$max = $max["MAX"] - 1;

				$sql = "SELECT MIN(computed_rank) AS MIN FROM friends WHERE user_id={$userid}";
				if (!$result = $db->query($sql)) {
					die('There was an error running the query [' . $db->error . ']');
				}
				$min = $result->fetch_assoc();
				$min = $min["MIN"] + 1;
				?>

				<input id="distanceSlider" class="slider" type="range" min="<?php echo $min; ?>" max="<?php echo $max; ?>" value="0" step="1">

								<hr/>
                <button onclick='boldButton(this.id)' class="astext" id="verified" data-count="0">
                Celebrities </button> <br />
                <button onclick='boldButton(this.id)' class="astext" id="unverified" data-count="0">
                Real people </button>
				<hr/>
			</div>
			<div id="content">
	<div class="container-fluid type">
		<div class="row-fluid">
			<div class="col-xs-3 typeColumn">
				<button onclick='boldButton(this.id)' class="astext" id="only_videos" data-count="0">
				Only videos </button> <br>
				<!-- <button onclick='boldButton(this.id)' class="astext" id="no_videos" data-count="0">
				No videos </button> -->
			</div>
			<div class="col-xs-3 typeColumn">
				<button onclick='boldButton(this.id)' class="astext" id="only_text" data-count="0">
                Only text </button> <br>
				<!-- <button onclick='boldButton(this.id)' class="astext" id="no_text" data-count="0">
				No text </button> -->
			</div>
			<div class="col-xs-3 typeColumn">
				<button onclick='boldButton(this.id)' class="astext" id="only_pics" data-count="0">
                Only pictures </button> <br>
				<!-- <button onclick='boldButton(this.id)' class="astext" id="no_pics" data-count="0">
                No pictures </button> -->
			</div>
			<div class="col-xs-3 typeColumn">
				<button onclick='boldButton(this.id)' class="astext" id="only_links" data-count="0">
								Only links </button> <br>
				<!-- <button onclick='boldButton(this.id)' class="astext" id="no_links" data-count="0">
								No links </button> -->
			</div>
		</div>
	</div>
                <hr/>
				<button onclick='boldButton(this.id)' class="astext" id="only_retweets" data-count="0">
				Only retweets </button> <br>
				<button onclick='boldButton(this.id)' class="astext" id="no_retweets" data-count="0">
				No retweets </button> <br>
				<hr/>
                <button onclick='boldButton(this.id)' class="astext" id="sentiment_positive" data-count="0">
                Positive tweets </button> <br>
                <button onclick='boldButton(this.id)' class="astext" id="sentiment_negative" data-count="0">
                Negative tweets </button>
                <hr/>

                <button onclick='boldButton(this.id)' class="astext" id="tweet_popular" data-count="0">
                Popular tweets </button> <br />
                <button onclick='boldButton(this.id)' class="astext" id="tweet_unpopular" data-count="0">
                Tweets that haven't gotten attention </button> <br>

				<?php
				$sql = "SELECT MAX(tweet_popularity) AS MAX FROM data WHERE user_id={$userid}";

				if (!$result = $db->query($sql)) {
					die('There was an error running the query [' . $db->error . ']');
				}
				$max = $result->fetch_assoc();
				$max = $max["MAX"];
				?>

				<input id="popularitySlider" class="slider" type="range" min="0" max="<?php echo $max; ?>" value="<?php echo $max/2; ?>" step="10">

								<hr/>
                Trending topics:<br>
<?php

// prepare and bind

$sql = "SELECT * FROM trends WHERE user_id={$userid}";

if (!$result = $db->query($sql)) {
	die('There was an error running the query [' . $db->error . ']');
}

$trendsArray = array();

while ($row = $result->fetch_assoc()) {
	$trendsArray[] = $row['hashtag'];
}

$subArray = array_rand($trendsArray, min(7, count($trendsArray)));

foreach($subArray as $ind) {
	$trend = $trendsArray[$ind];
	echo "&nbsp&nbsp&nbsp&nbsp<button onclick='boldButton(this.id)' class='astext trend' id='{$trend}' >{$trend}</button> <br />";
}

?>
			</div>
                <button onclick='boldButton(this.id)' class="astext" id="alloff">
                Turn off all filters </button> <br />
								<hr>

                <button onclick='boldButton(this.id)' class="astext" id="refresh">
                Refresh for new tweets since you logged in </button> <br />
                <br />
                <a href="survey.php"><button id="survey" class="btn"> Go to survey </button></a>

          </div>
  			</div>
  		</div>
  	</div>

	<script type="text/javascript" src="js/highlighting.js"></script>
    <script type="text/javascript" src="js/script.js"></script>

  </body>
</html>
