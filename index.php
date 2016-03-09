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
                      include 'src/saveToSQL.php'; // Save current user's tweets to SQL database
                      include 'src/saveTrendsToSQL.php'; // Save trends for current user to DB
                      include 'src/getData.php'; // Fetch data and put it into cache
                      include 'src/printEachTweet.php'; // Formatting for each tweet
                      include 'src/printTweets_SQL_min.php'; // Printing all tweets
                      include 'src/saveFriendsToSQL.php'; // Save friends
                      include 'src/computeFriendRank.php'; // As a second step, compute friend rank (need max friend num to do so)
                      include 'src/savedirectMessagesToSQL.php'; //Save direct messages
                    // Resetting all session booleans
                  // Resetting all session booleans
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

                      if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db'])=='') {
                          $_SESSION['data_in_db'] = false;
                      }

                      function controlPanel() {
                      }

                  // Authorization
                      include 'src/authorization.php';

                      echo "<br>";

                  //SaveToSQL if data_in_db is false
                      if ((!isset($_SESSION['data_in_db'])) || ($_SESSION['data_in_db'])== false) {
                          $_SESSION['data_in_db'] = true;

												// Initialize $next_max_id and $cursor variable
													$next_max_id = null;
													$cursor = null;

													echo "The if statement is true, now paging through tweets. <br>";
												// While there are still tweets, run saveToSQL
													saveToSQL($connection, $next_max_id_temp);
													// while(true){
													// 	echo "The tweet while statement is true <br>";
													// // Preserve previously recieved cursor
													// 	$next_max_id_temp = $next_max_id;
													// // Run saveToSQL and store return array into $return_array
													// 	$next_max_id = saveToSQL($connection, $next_max_id_temp);
													//
													// 	$next_max_id_str = (string) $next_max_id;
													// 	echo "The next_max_id is " . $next_max_id_str . "<br>";
													//
													// 	if($next_max_id == $next_max_id_temp || $next_max_id == null){
													// 		break;
													// 	}
													// }

                          echo "Saving trends now <br>";
                          saveTrendsToSQL($connection);
                          echo "Saving DMs now <br>";
                          savedirectMessagesToSQL($connection);

                          echo "Direct messages saved, now paging through friends. <br>";
                          $_SESSION["rank_counter"] = 0;
                          while(true){
                              echo "The friends while statement is true <br>";
                              $cursor_temp = $cursor;
                              $cursor = saveFriendsToSQL($connection, $cursor_temp);
                              $cursor_str = (string) $cursor;
                              echo "The cursor is " . $cursor_str . "<br>";
                              if($cursor == $cursor_temp || $cursor == null){
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
                      printTweets_SQL_min();
									// Set Session booleans for index-visited
									$_SESSION["index"][1] = false;
									$_SESSION["index"][2] = false;
									$_SESSION["index"][3] = false;

  				?>

  			</div>
        <!-- Control Panel -->
  			<div class="col-xs-4 totop">
          <!-- <button>Hide/Show</button> -->
          <div id="newpost">

						<?php
						echo "Logged in as <b>".$user->screen_name;
						echo "</b> <img src='".$user->profile_image_url."' alt='error'>";
						?>
						<a href="logout.php"><button id="logout">Logout</button></a>
								<hr> <br>
								<button id="nextstep" class="btn"> Go to next feed </button>

<!--
                <h3> Control Panel </h3>

                <p>Change the Content You See</p>
                <div id="changeButton">
                <button class="astext" id="sentiment_positive">
                See more positive tweets </button> </div>
                <button class="astext" id="sentiment_negative">
                See more negative tweets </button>
                <hr/>

                <button class="astext" id="tweet_popular">
                See more popular tweets </button> <br>
                <button class="astext" id="tweet_unpopular">
                See more tweets that haven't gotten attention </button>
                <hr/>
                <p>Some trending topics:</p>
                <?php
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

                    // prepare and bind
                    $sql = "SELECT * FROM trends WHERE user_id={$userid}";
                    if(!$result = $db->query($sql)){
                        die('There was an error running the query [' . $db->error . ']');
                    }

                    $trendsArray = array();
                    while($row = $result->fetch_assoc()){
                        $trendsArray[]=$row['hashtag'];
                    }

                    $subArray = array_rand($trendsArray, min(7, count($trendsArray)));
                    foreach ($subArray as $ind) {
                        $trend = $trendsArray[$ind];
                        echo "&nbsp&nbsp&nbsp&nbsp<button class='astext' id='{$trend}'>{$trend}</button> <br>";
                    }
                    ?>
                <br>
                <p>Change the People You See</p>
                <button class="astext" id="poster_frequent">
                See more frequent posters </button> <br>
                <button class="astext" id="poster_infrequent">
                See more infrequent posters </button>
                <hr/>
                <button class="astext" id="close_friends">
								See more of your close friends </button> <br>
                <button class="astext" id="distant_friends">
                See more distant friends</button>
                <hr/>
                <button class="astext" id="verified">
                See more celebrities </button> <br>
                <button class="astext" id="unverified">
                See more real people </button> <br>
          </div>

-->
  			</div>
  		</div>
  	</div>
    <script>
        // Keeping Control Panel on screen
          jQuery(window).scroll(function() {
              jQuery('.totop').stop().animate({ right: '0px' });
          });

    window.onload = function () {
			$("#nextstep").click(function(){
					var randomNumber = Math.floor((Math.random() * 3) + 1);
					var indexLocation = "/TweedStudy/index-" + randomNumber + ".php";
					var hostname = window.location.hostname;
					var url = "http://"+hostname + indexLocation
					window.location.href=url;
			});

      $("button").on('click', function( event ) {

                     //This is for Internet Explorer
                     var target = (event.target) ? event.target : event.srcElement;
                     var elem = $( this );
                     var dataString = elem.attr("id");
										 if(dataString == "nextstep" || dataString == "toggle" || dataString == "survey" || dataString == "logout"){
											 return;
										 }
                     count = +target.dataset.count;

                     var pairs = {};
                     pairs['tweet_popular']= "tweet_unpopular";
                     pairs['tweet_unpopular']= "tweet_popular";
                     pairs['poster_infrequent']= 'poster_frequent';
                     pairs['poster_frequent']= 'poster_infrequent';
                     pairs['verified']= 'unverified';
                     pairs['unverified']= 'verified';
                     pairs['sentiment_positive']= 'sentiment_negative';
                     pairs['sentiment_negative']= 'sentiment_positive';
                     pairs['close_friends']= 'distant_friends';
                     pairs['distant_friends']= 'close_friends';


//                      target.style.color = count === 1 ? '#323232' : '#000000';
//                      target.dataset.count = count === 1 ? 0 : 1;
//
// //                     document.write(dataString, pairs[dataString]);
//
//                      var target2 = document.getElementById(pairs[dataString]);
// //                     document.write(target2);
//                      target2.style.color = count === 1 ? '#D3D3D3' : '#000000';
//                      target2.dataset.count = count === 1 ? 0 : 1;


                     $.ajax({

                            type: "POST",
                            url: "src/pass_value.php",
                            data: { dataString: dataString },
                            dataType: 'text',
                            cache: false,
                            success: function(response) {

                            //alert(response);
                            $("#feed").html(response);
                            //document.getElementById("feed").innerHTML=xmlhttlp.response;

                            }
                            });

                     return false;
                      });

    };


        //need to create pass_value.php
        //should update session variables
        // dataStrng here should be in json format
        // just update the one element ID
        // maybe within or maybe in update_tweets.php (below)
        // should create long string of SQL query and
        // update the tweet list div
        // to update the div you want this line::
        // document.getElementById("txtHint").innerHTML=xmlhttp.responseText;  Pretty sure this is the AJAX
        // this will update whatever is inside of the the div with ID equal to "txtHint"
        // and will replace it with (hopefully correctly formatted html) from responseText
        // so ideally responseText will already be formatted right (ie.
        // the output of printTweet)

  //                  # PUT THE AJAX SESSION VARIABLE UPDATE HERE!
  //                               if ( elem.attr( "id" ).match("sentiment_positive")) {
  //                               #testString = "$test = ($_SESSION['" + elem.attr("id") + "'].value) ? 'true' : 'false'; echo $test;";
  //                               #alert(testString);
  //                               } else {
  //                                       alert("something not working");
  //                               }

                     // alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");



  //                $("div#changeButton").on('click', 'button.astext', function( eventObject ) {
  ////                                             <?php
  ////                                             if($_SESSION['sentiment_positive'].value==false){
  ////                                                 $_SESSION['sentiment_positive'].value=true;}
  ////
  ////                                             else {
  ////                                             $_SESSION['sentiment_positive'].value=false;} ?>
  //                                         var elem = $( this );
  //                                         if ( elem.attr( "id" ).match("pos_sen")) {
  //                                             alert("pos_sen");
  //                                         }
  ////                                         alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
  //                                         });
                 // $("button").click(function() {
                 //     $("#newpost").toggle();
                 // });
    </script>
  </body>
</html>
