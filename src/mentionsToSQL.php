<?php
    function mentionsToSQL($connection) {
        
        /** Array of Happy and Sad words using external .txt file. **/
        $happyWords = explode(PHP_EOL, file_get_contents("happyWords.txt"));
        $happyWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $happyWords); // remove punctuations
        $sadWords = explode(PHP_EOL, file_get_contents("sadWords.txt"));
        $sadWords = preg_replace("/[^a-zA-Z 0-9]+/", "", $sadWords);
        $happyWords = array_filter($happyWords); //Remove all empty elements
        $happyWords = array_values($happyWords); //Re-key array numerically
        $sadWords = array_filter($sadWords); //Remove all empty elements
        $sadWords = array_values($sadWords); //Re-key array numerically
        
        
        
        // get actual data
  $dmjson = $connection->get('statuses/mentions_timeline',array("count" => 200, "include_entities" => true));
 
        
        // get connection to db
        // prepare and bind
        
        /** Set up SQL connection **/
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";
        
        $userid = $_SESSION["user_id"];
        
        $conn = new mysqli($servername, $username, $password, $dbname);
        
  $stmt2 = $conn->prepare("INSERT INTO mentions (user_id, tweet_text, friend_id, friend_name, sentiment,word_count,create_date) VALUES (?, ?, ?, ?, ?, ?,?)");
  if ( false===$stmt2 ) {
      die('prepare() failed: ' . htmlspecialchars($mysqli->error));
  }
  $stmt2->bind_param("isisiis", $userid, $text, $senderId, $senderName,$sentiment,$wordcount,$create_date);
  $rc2 = $stmt2->bind_param("isisiis", $userid, $dmtext, $senderId, $senderName,$sentiment,$wordcount,$create_date);
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
          $senderId = $tweet["user"]["id_str"];
          $senderName = $tweet["user"]["name"];
          
          $happyValue = 0;
          $tweetArray = explode(" ", $tweet['text']); //explode tweet into Array
          $tweetArray = preg_replace("/[^a-zA-Z 0-9]+/", "", $tweetArray); // Remove punctuations
          $tweetArray = array_filter($tweetArray); //Remove all empty elements
          $wordcount = count($tweetArray);
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
          
          $sentiment = $happyValue;

          $stmt2->execute();
      }
      $stmt2->close();
  }
    }
  
?>
