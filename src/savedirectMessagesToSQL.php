
<?php
    function savedirectMessagesToSQL($connection) {
        
        /** Array of Happy and Sad words using external .txt file. **/
        $happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
        $happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
        $sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
        $sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
        $happyWords = array_filter($happyWords); //Remove all empty elements
        $happyWords = array_values($happyWords); //Re-key array numerically
        $sadWords = array_filter($sadWords); //Remove all empty elements
        $sadWords = array_values($sadWords); //Re-key array numerically
        
        /** Set up SQL connection **/
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";
        
        $userid = $_SESSION["user_id"];

        $conn = new mysqli($servername, $username, $password, $dbname);
//        mysqli_report(MYSQLI_REPORT_ALL);
        
        $dmjson = $connection->get('direct_messages');
        // prepare and bind
        $stmt2 = $conn->prepare("INSERT INTO directMessages (user_id, dm_text, sender_id, sender_name,create_date,sentiment,word_count,intimacy) VALUES (?, ?, ?, ?,?,?,?,?)");
        if ( false===$stmt2 ) {
            die('prepare() failed: ' . htmlspecialchars($mysqli->error));
        }
        
        $stmt2->bind_param("isissiii", $userid, $dmtext, $senderId, $senderName, $create_date, $sentimentValue, $wordCount,$intimacyValue);
        
        $rc2 = $stmt2->bind_param("isissiii", $userid, $dmtext, $senderId, $senderName, $create_date, $sentimentValue, $wordCount,$intimacyValue);
//
        if ( false===$rc2 ) {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            die('bind_param() failed: ' . htmlspecialchars($stmt->error));
        }
        if ( $dmjson ) {
            $jsonDM = json_encode($dmjson);
            $response = json_decode($jsonDM,true);
            foreach($response as $key => $tweet){

                $create_date = $tweet["created_at"];
                $dmtext = $tweet["text"];
                $senderId = $tweet["sender_id"];
                $senderName = $tweet["sender"]["name"];
                
                
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
                var_dump($happyValue);
                
                
                $sentimentValue = $happyValue;
//                $intimacyValue = 
                $wordCount = count($tweetArray);
                
                $intimacyValue = 0;
                var_dump($wordCount);

                $stmt2->execute();
            }
            $stmt2->close();
        }
    }
?>
