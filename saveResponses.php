<?php
session_start();
// print_r($_SESSION);
    $userid = 123;
    $source_website = $_SESSION['last_referrer_url'];
    $q1 = 2;
    $q2 = 1;

    $servername = "engr-cpanel-mysql.engr.illinois.edu";
    $username = "twitterf_user";
    $password = "IIA@kT$7maLt";
    $dbname = "twitterf_tweet_store";


    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("INSERT INTO survey_responses (user_id, source_website, q1, q2) VALUES (?, ?, ?, ?)");

    if ( false===$stmt ) {
        die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }

    $stmt->bind_param("isss", $userid, $source_website, $q1, $q2);


    # set the four variables (current version, somehow using get? or by passing data in the AJAX?)


    $stmt->execute();


    $stmt->close();


    $conn->close();
    // "success"

    // print_r($_SESSION["index"]);

    $feedsLeft = -1; $indexesLeft = array();
    foreach($_SESSION["index"] as $number => $visited){
      if(!$visited){
        array_push($indexesLeft, $number);
        $feedsLeft++;
      }
    }
    // print_r($feedsLeft);
    // print_r($indexesLeft);
    $randomNumber = rand(0, $feedsLeft);
    $indexNumber = $indexesLeft[$randomNumber];
    echo "http://twitterfeed.web.engr.illinois.edu/TweedStudy/index-".$indexNumber.".php";
?>
