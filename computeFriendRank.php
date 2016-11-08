<?php
    
    // TO DO : SAVE MAX AND MIN COMPUTED AND FAKE RANKS AS SESSION VARS
    function computeFriendRank() {
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


        // Need to get a select/insert statement prepared
        // "INSERT INTO friends(computed_rank) ** theoretically could do insert directly from select e.g. http://stackoverflow.com/questions/5903912/sql-insert-based-on-select-results
        // But it seems like that won't be worth it, based on the processing we want to do
        // Eg. force the original rank value to [-1, 1] based on whether it's in the top or bottom half
        // Could (?) do with round()/+/-/etc but maybe just not worth it
        
        // Instead: first select rank, followers_count, friends_count, verified, and for that user
        //          left join from direct messages sum(word count), sum(intimacy), sum(sentiment), min(create_date)
        // For the demo version, just select rank
        // And then compute thisrank/maxrank --> if >.5, +1 else -1
        
        
        $init_sql = "SELECT max(rank) FROM `friends` WHERE user_id = {$userid}";
        
        if(!$result = $db->query($init_sql)){
            die('There was an error running the query [' . $db->error . ']');
        }
        
        $init_row = $result->fetch_assoc();
        
        $max_rank = $init_row['max(rank)'];
        
        var_dump($init_row);
        var_dump($init_row['max(rank)']);
        
//        $sql = "SELECT rank, friends.user_id,friend_id,followers_count,friends_count,verified, max_date,avg_sent,avg_int,sum_word FROM `friends` LEFT JOIN (SELECT user_id,sender_id,min(create_date) as min_date,avg(intimacy) as avg_int, avg(sentiment) as avg_sent, sum(word_count) as sum_word FROM `directMessages`) as tab2 ON `friends`.`friend_id`=tab2.`sender_id` AND `friends`.`user_id`=tab2.`user_id` WHERE `friends`.user_id = {$userid}";
        
         $sql = "SELECT rank, friends.user_id,friends.friend_id,followers_count,friends_count,verified, max_date,max_date2,avg_sent,avg_sent2,avg_int,sum_word,sum_word2 FROM `friends` LEFT JOIN (SELECT user_id,sender_id,max(create_date) as max_date,avg(intimacy) as avg_int, avg(sentiment) as avg_sent, sum(word_count) as sum_word FROM `directMessages`) as tab2 ON `friends`.`friend_id`=tab2.`sender_id` AND `friends`.`user_id`=tab2.`user_id` LEFT JOIN (SELECT user_id,friend_id, avg(sentiment) as avg_sent2, sum(word_count) as sum_word2, max(create_date) as max_date2 FROM `mentions`) as tab3 ON `friends`.`friend_id`=tab3.`friend_id` AND `friends`.`user_id`=tab3.`user_id` WHERE `friends`.user_id = {$userid}";
        
        
        
//        260761339
//        SELECT * FROM `friends` LEFT JOIN `directMessages`
//        ON `friends`.`friend_id`=`directMessages`.`sender_id` WHERE `friends`.user_id = 260761339
        
//        echo $sql;
        
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }
        
//        mysqli_report(MYSQLI_REPORT_ALL);
        
        // Set up insert
        $stmt_friends = $db->prepare("UPDATE friends SET computed_rank = ? WHERE user_id = {$userid} AND friend_id = ?");
        
        // Check for false statement
        if ( false===$stmt_friends ) {
            die('prepare() failed: ' . htmlspecialchars($mysqli->error));
        }
        
        // Define Parameters
        $stmt_friends->bind_param("ii", $computedRank,$friendid);
        
        // Check if you can't bind Parameters
        $rc = $stmt_friends->bind_param("ii", $computedRank,$friendid);
        
        //test : 2475800184, user_id = 260761339

        if ( false===$rc ) {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            die('bind_param() failed: ' . htmlspecialchars($stmt_friends->error));
        }
        
        while($row = $result->fetch_assoc()){
//            echo "getting here again? <br>";
//            var_dump($row);
//            echo "<br>";
//            processAndInsert($row,$max_rank,$db, $userid, $stmt_friends);
//            printEachTweet($row);
            
            $friendInfo = $row;
            

// If at front end of the list, you are less close, back end then older friend and more close
// Extra: If not in list (Very close)
            if ((float)$friendInfo['rank']/(float)$max_rank > .5) {
                $rankVal = 1;
            } else {
                $rankVal = -1;
            }
            
            $computedRank = $rankVal;
            
            // How many friends and followers do they have? If above 1000 total, not close
            if ((float)$friendInfo['followers_count'] + (float)$friendInfo['friends_count'] > 100000){
                $computedRank = $computedRank-3;
            } elseif ((float)$friendInfo['followers_count'] + (float)$friendInfo['friends_count'] > 10000){
                $computedRank = $computedRank-2;
            } elseif ((float)$friendInfo['followers_count'] + (float)$friendInfo['friends_count'] > 1000){
                $computedRank = $computedRank-1;
            } else {
                $computedRank = $computedRank+1;
            }
            
            // Are they verified? If so, not close
            if ((float)$friendInfo['verified'] > 0){
                $computedRank = $computedRank-1;
            } else {
                $computedRank = $computedRank+1;
            }

// Average sentiment (direct messages and tweets)
            if ((float)$friendInfo['avg_sent'] > 0){
                $computedRank = $computedRank+1;
            } elseif ((float)$friendInfo['avg_sent'] < 0) {
                $computedRank = $computedRank-1;
            }
            
            if ((float)$friendInfo['avg_sent2'] > 0){
                $computedRank = $computedRank+1;
            } elseif ((float)$friendInfo['avg_sent'] < 0) {
                $computedRank = $computedRank-1;
            }
// Intimacy on tweets
            if ((float)$friendInfo['avg_int'] > 0){
                $computedRank = $computedRank+1;
            } elseif ((float)$friendInfo['avg_int'] < 0) {
                $computedRank = $computedRank-1;
            }

// Total number of words in direct messages
            if ((float)$friendInfo['sum_word'] > 1000) {
                $computedRank = $computedRank+10;
            } elseif ((float)$friendInfo['sum_word'] > 100) {
                $computedRank = $computedRank+5;
            } elseif ((float)$friendInfo['sum_word'] > 10) {
                $computedRank = $computedRank+2;
            } elseif ((float)$friendInfo['sum_word'] > 0) {
                $computedRank = $computedRank+1;
            }
            
            if ((float)$friendInfo['sum_word2'] > 1000) {
                $computedRank = $computedRank+10;
            } elseif ((float)$friendInfo['sum_word2'] > 100) {
                $computedRank = $computedRank+5;
            } elseif ((float)$friendInfo['sum_word2'] > 10) {
                $computedRank = $computedRank+2;
            } elseif ((float)$friendInfo['sum_word2'] > 0) {
                $computedRank = $computedRank+1;
            }
// Most recent direct message (More recent then closer)       
            $mostrecent = max($friendInfo['max_date'],$friendInfo['max_date2']);
     
            if (strtotime($mostrecent) - strtotime('-6 hour')  > 0 ) {
                $computedRank = $computedRank+10;
            } elseif (strtotime($mostrecent) - strtotime('-1 day')  > 0 ) {
                $computedRank = $computedRank+5;
            } elseif (strtotime($mostrecent) - strtotime('-7 day')  > 0 ) {
                $computedRank = $computedRank+2;
            } elseif (strtotime($mostrecent) - strtotime('-31 day')  > 0 ) {
                $computedRank = $computedRank+1;
            }
            
            // Should do something with min_date --> haven't done it yet
            
//            if min_date,avg_sent,avg_int,sum_word
            
//            echo $computedRank;
//            echo "<br>";
            
//            var_dump($stmt_friends);
//            echo "<br>";
            
            $friendid = (float)$friendInfo['friend_id'];
            
//            var_dump($friendid);
//            echo "<br>";
            
            if ($stmt_friends->execute() === false) {
                die('execute() failed: ' . htmlspecialchars($stmt_friends->error));
            }
            
            
        }
        
        $stmt_friends->close();
        

        $init_sql2 = "SELECT computed_rank FROM `friends` WHERE user_id = {$userid}";
        
        if(!$result = $db->query($init_sql2)){
                        die('There was an error running the query [' . $db->error . ']');
                    }
        
        while($row = $result->fetch_assoc()){
    
            $fakeRanks[] = $row['computed_rank'];
            
        }
        
        asort($fakeRanks);
        
        $_SESSION["real_ranks"]=$fakeRanks;
        
//        $init_sql2 = "SELECT max(computed_rank) FROM `friends` WHERE user_id = {$userid}";
//        
//        if(!$result = $db->query($init_sql2)){
//            die('There was an error running the query [' . $db->error . ']');
//        }
//        
//        $init_row = $result->fetch_assoc();
//        
//        $max_comp_rank = $init_row['max(computed_rank)'];
//        
//        $_SESSION["max_real_rank"]=$max_comp_rank;
//        
//        $init_sql2 = "SELECT min(computed_rank) FROM `friends` WHERE user_id = {$userid}";
//        
//        if(!$result = $db->query($init_sql2)){
//            die('There was an error running the query [' . $db->error . ']');
//        }
//        
//        $init_row = $result->fetch_assoc();
//        
//        $min_comp_rank = $init_row['min(computed_rank)'];
//        
//        $_SESSION["min_real_rank"]=$min_comp_rank;
        
        $sql = "SELECT friends.user_id,friends.friend_id FROM `friends` WHERE `friends`.user_id = {$userid}";

        
        //        260761339
        //        SELECT * FROM `friends` LEFT JOIN `directMessages`
        //        ON `friends`.`friend_id`=`directMessages`.`sender_id` WHERE `friends`.user_id = 260761339
        
        //        echo $sql;
        
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }
        
        //        mysqli_report(MYSQLI_REPORT_ALL);
        
        // Set up insert
        $stmt_friends = $db->prepare("UPDATE friends SET fake_comp_rank = ?, fake_verified = ? WHERE user_id = {$userid} AND friend_id = ?");
        
        // Check for false statement
        if ( false===$stmt_friends ) {
            die('prepare() failed: ' . htmlspecialchars($mysqli->error));
        }
        
        // Define Parameters
        $stmt_friends->bind_param("iii", $fakecomputedRank,$fakeverified,$friendid);
        
        // Check if you can't bind Parameters
        $rc = $stmt_friends->bind_param("iii", $fakecomputedRank,$fakeverified,$friendid);
        
        //test : 2475800184, user_id = 260761339
        
        if ( false===$rc ) {
            // again execute() is useless if you can't bind the parameters. Bail out somehow.
            die('bind_param() failed: ' . htmlspecialchars($stmt_friends->error));
        }
        
        $_SESSION["max_fake_rank"]=-10000;
        $_SESSION["min_fake_rank"]=10000;
        
        $tempFakeRanks = $fakeRanks;
        
        while($row = $result->fetch_assoc()){
            //            echo "getting here again? <br>";
            //            var_dump($row);
            //            echo "<br>";
            //            processAndInsert($row,$max_rank,$db, $userid, $stmt_friends);
            //            printEachTweet($row);
            
            $friendInfo = $row;
            
            $friendid = (float)$friendInfo['friend_id'];
            
            $k = array_rand($tempFakeRanks);
            
            $fakecomputedRank = $tempFakeRanks[$k];
            
            unset($tempFakeRanks[$k]);
            
//            $fakecomputedRank = rand ( $min_comp_rank,  $max_comp_rank );
            
            $fakeverified = rand(0,1);
            
//            if ($fakecomputedRank > $_SESSION["max_fake_rank"]){
//                $_SESSION["max_fake_rank"] = $fakecomputedRank;
//            }
//            
//            if ($fakecomputedRank < $_SESSION["min_fake_rank"]){
//                $_SESSION["min_fake_rank"] = $fakecomputedRank;
//            }
            
            
            //            var_dump($friendid);
            //            echo "<br>";
            
            if ($stmt_friends->execute() === false) {
                die('execute() failed: ' . htmlspecialchars($stmt_friends->error));
            }
            
            echo "finished one more friend";
            
            
        }
        
        $stmt_friends->close();
        
        $db->close();
        
        
        
        
    }
    
    
    
    //    function processAndInsert($friendInfo,$max_rank,$db,$userid,$stmt_friends) {
//
////        var_dump($friendInfo);
////        echo "<br>";
//    
//        // Are they closer than half way? If so not close (larger rank = further back = older friend)
//        if ((float)$friendInfo['rank']/(float)$max_rank > .5) {
//            $rankVal = 1;
//        } else {
//            $rankVal = -1;
//        }
//        
//        $computedRank = $rankVal;
//        
//        // How many friends and followers do they have? If above 1000 total, not close
//        if ((float)$friendInfo['followers_count'] + (float)$friendInfo['friends_count'] > 1000){
//            $computedRank = $computedRank-1;
//        } else {
//            $computedRank = $computedRank+1;
//        }
//        
//        // Are they verified? If so, not close
//        if ((float)$friendInfo['verified'] > 0){
//            $computedRank = $computedRank-1;
//        } else {
//            $computedRank = $computedRank+1;
//        }
//        
//        echo $computedRank;
//        echo "<br>";
//        
//        var_dump($stmt_friends);
//        echo "<br>";
//        
//        $friendid = (float)$friendInfo['friend_id'];
//        
//        var_dump($friendid);
//        echo "<br>";
//        
//        if ($stmt_friends->execute() === false) {
//            die('execute() failed: ' . htmlspecialchars($stmt_friends->error));
//        }
//    
//    }
//    
    
//        // Prepare and bind_param
//        //      $stmt_friends = $conn->prepare("INSERT INTO friends (rank, user_id, friend_id, screen_name, followers_count, verified, friends_count) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE user_id = user_id");
//
//        // Check for false statement
//          if ( false===$stmt_friends ) {
//              die('prepare() failed: ' . htmlspecialchars($mysqli->error));
//          }
//
//        // Define Parameters
//          $stmt_friends->bind_param("iiisiii", $rank, $user_id, $friend_id, $screen_name, $followers_count, $verified, $friends_count);
//
//        // Check if you can't bind Parameters
//          $rc = $stmt_friends->bind_param("iiisiii", $rank, $user_id, $friend_id, $screen_name, $followers_count, $verified, $friends_count);
//          if ( false===$rc ) {
//              // again execute() is useless if you can't bind the parameters. Bail out somehow.
//              die('bind_param() failed: ' . htmlspecialchars($stmt_friends->error));
//          }
//
//        // Run attachments
//          if($json_friends){
//            $jsonfriends = json_encode($json_friends);
//            $response = json_decode($jsonfriends,true);
//            $cursor = $response['next_cursor'];
//            foreach($response['users'] as $friend){
//              $user_id = $userid;
//              $rank = $_SESSION["rank_counter"];
//              $_SESSION["rank_counter"]++;
//              $friend_id = $friend['id'];
//              $screen_name = $friend['screen_name'];
//              $followers_count = $friend['followers_count'];
//              if ($friend['verified']) {
//                  $verified = 1;
//              } else {
//                  $verified = 0;
//              }
//              $friends_count = $friend['friends_count'];
//
//              if ($stmt_friends->execute() === false) {
//                  die('execute() failed: ' . htmlspecialchars($stmt_friends->error));
//              }
//            }
//            $stmt_friends->close();
//          }
//          return $cursor;
//          $conn->close();
     

?>
