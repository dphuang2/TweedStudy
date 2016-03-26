
<?php
    function saveTrendsToSQL($connection) {
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";

    // Initalize userid variable with session "user_id"
        $userid = $_SESSION["user_id"];
        
        
        // Create connection
        $db = new mysqli($servername, $username, $password, $dbname);
        
        // Check connection
        if ($db->connect_error) {
            die("Connection failed: " . $db->connect_error);
        }
    
        
        $sql = "SELECT tweet_text FROM `data` WHERE user_id = {$userid} AND tweet_text LIKE '%#%'";
        
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }
        
        //        mysqli_report(MYSQLI_REPORT_ALL);
        
        // Set up insert

    // prepare and bind
        $stmt_data = $db->prepare("INSERT INTO trends (user_id, hashtag) VALUES (?, ?)");

        if ( false===$stmt_data ) {
            die('prepare() failed: ' . htmlspecialchars($mysqli->error));
        }

    // Define parameters
        $stmt_data->bind_param("is", $userid, $thisTrend);

    // Check if you can't bind parameters
        $rc = $stmt_data->bind_param("is", $userid, $thisTrend);
        if ( false===$rc ) {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            die('bind_param() failed: ' . htmlspecialchars($stmt_data->error));
        }

        while($row = $result->fetch_assoc()){
            //            echo "getting here again? <br>";
            //            var_dump($row);
            //            echo "<br>";
            //            processAndInsert($row,$max_rank,$db, $userid, $stmt_friends);
            //            printEachTweet($row);
            
            $trendInfo = $row;
            
            $text = $trendInfo['tweet_text'];
            
            $tweetArray = explode(" ", $text); //explode tweet into Array
            $tweetArray = preg_replace("/[^a-zA-Z 0-9 #]+/", "", $tweetArray); // Remove punctuations
            $tweetArray = array_filter($tweetArray); //Remove all empty elements
            $wordcount = count($tweetArray);
            $tweetArray = array_values($tweetArray); //Re-key array numerically
            
            foreach($tweetArray as $tweetWord){
                $pos = stripos($tweetWord, '#');
                if($pos === 0){
                    $thisTrend = $tweetWord;
                    $stmt_data->execute();
                    
                }
                
            }
            
            
            
        }
        
        $stmt_data->close();
        
        $db->close();



    }
?>
