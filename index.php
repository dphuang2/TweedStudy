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
                      include 'src/getData.php'; // Fetch data and put it into cache
                      include 'src/printEachTweet.php'; // Formatting for each tweet
                      include 'src/printTweets_SQL.php'; // Printing all tweets
                  // Resetting all session booleans
                      $_SESSION['tweet_popular'] = false;
                      $_SESSION['tweet_unpopular'] = false;
                      $_SESSION['poster_frequent'] = false;
                      $_SESSION['poster_infrequent'] = false;
                      $_SESSION['verified'] = false;
                      $_SESSION['unverified'] = false;
                      $_SESSION['sentiment_positive'] = false;
                      $_SESSION['sentiment_negative'] = false;

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

												// Initialize $max_id variable
													$next_max_id = null;

													echo "The if statement is true <br>";
												// While there are still tweets, run saveToSQL
													while(true){
														echo "The while statement is true <br>";

														$next_max_id_temp = $next_max_id;
														$next_max_id = saveToSQL($connection, $user, $next_max_id_temp);

														$next_max_id_str = (string) $next_max_id;
														echo "The next_max_id is " . $next_max_id_str . "<br>";

														if($next_max_id == $next_max_id_temp || $next_max_id == null){
															break;
														}

													}

												// saveToSQL($connection, $user, $last_max_id);

                      }

									// Location for trends
                      $user_ip = getenv('REMOTE_ADDR');
                      $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
                      $country = $geo["geoplugin_countryName"];
                      $city = $geo["geoplugin_city"];

                      $place = $connection->get("geo/search", array("query",$city));

                      //                    if ($city == "") {
                      if (!is_numeric($place)) {
                          $place = strval(23424977);
                      }

                      $trends = $connection->get("trends/place", array("id" => $place));

                      $trends = json_decode(json_encode($trends),true);

                      $trendsArray = array();

                      foreach ($trends[0]["trends"] as $trend) {
                          $trendsArray[]=$trend["name"];
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
                      printTweets_SQL($user);


  				?>

  			</div>
        <!-- Control Panel -->
  			<div class="col-xs-4 totop">
          <button>Hide/Show</button>
          <div id="newpost">

                <?php
      					echo "Logged in as ".$user->screen_name;
      					echo "<img src='".$user->profile_image_url."' alt='error'>";
      					?>

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
                    $subArray = array_rand($trendsArray, min(7, count($trendsArray)));
                    foreach ($subArray as $ind) {
                        $trend = $trendsArray[$ind];
                        echo "&nbsp&nbsp&nbsp&nbsp<a href='index.php?filter={$trend}'>{$trend}</a> <br>";
                    }
                    ?>
                <br>
                <p>Change the People You See</p>
                <button class="astext" id="poster_frequent">
                See more frequent posters </button> <br>
                <button class="astext" id="poster_infrequent">
                See more infrequent posters </button>
                <hr/>
                <a href="index.php?filter=Close">
                See more of your close friends </a> <br>
                <a href="index.php?filter=Distant">
                See more distant friends</a>
                <hr/>
                <button class="astext" id="verified">
                See more celebrities </button> <br>
                <button class="astext" id="unverified">
                See more real people </button> <br>
          </div>
  			</div>
  		</div>
  	</div>
    <script>
        // Keeping Control Panel on screen
          jQuery(window).scroll(function() {
              jQuery('.totop').stop().animate({ right: '0px' });
          });


//            $("#btn").click(function())
//            {
//                if($_SESSION['sentiment_positive'].value==false){
//                    $_SESSION['sentiment_positive'].value=true;}
//
//                else {
//                    $_SESSION['sentiment_positive'].value=false;}
//            };

  //                function buttonChange() {
  ////                    var buttonVal
  //
  //                }

      $("button").on('click', function( event ) {
  //                        alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
               //                                             <?php
               //                                             if($_SESSION['sentiment_positive'].value==false){
               //                                                 $_SESSION['sentiment_positive'].value=true;}
               //
               //                                             else {
               //                                             $_SESSION['sentiment_positive'].value=false;} ?>
  //                               console.log( event.which );
  //                               console.log( event.target);
                     //This is for Internet Explorer
                     var target = (event.target) ? event.target : event.srcElement;
                     var elem = $( this );
                     var dataString = elem.attr("id");

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


  //                  # PUT THE AJAX SESSION VARIABLE UPDATE HERE!
  //                               if ( elem.attr( "id" ).match("sentiment_positive")) {
  //                               #testString = "$test = ($_SESSION['" + elem.attr("id") + "'].value) ? 'true' : 'false'; echo $test;";
  //                               #alert(testString);
  //                               } else {
  //                                       alert("something not working");
  //                               }

                     // alert("<?php $test = ($_SESSION['sentiment_positive'].value) ? 'true' : 'false'; echo $test ?>");
               });


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
