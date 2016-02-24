<?php
    function saveToSQL($connection,$max_id) {
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";

    // Initalize userid variable with session "user_id"
        $userid = $_SESSION["user_id"];

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

    // // Friends
    //   include 'toSQL/friendsToSQL.php';
    // // Direct Messages
    //   include 'toSQL/directMessagesToSQL.php';
    // Tweets
      include 'toSQL/tweetsToSQL.php';


        $conn->close();

        // return $next_max_id and $cusor in array.
        // $next_max_id is for paging through tweets
        // $cursor is for paging through friends
        // return array("cursor" => $cursor, "next_max_id" => $tweet_id);
        return $tweet_id;

    }
?>
