<?php
// GET from timeline and set $next_max_id

    if($max_id == null){ // If this is the first GET, don't put $max_id as parameter
      $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
    }else{
      $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true, "max_id" => $max_id));
    }

// Prepare and bind
    $stmt_data = $conn->prepare("INSERT INTO data (tweet_id, user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, retweet, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags, user_name, retweet_count, favorite_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE tweet_id=tweet_id");

    if ( false===$stmt_data ) {
        die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }
// Define parameters
    $stmt_data->bind_param("iisiiiiissssssssii", $tweet_id, $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $retweet, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName, $retweetCount, $favoriteCount);

// Check if you can't bind parameters
    $rc = $stmt_data->bind_param("iisiiiiissssssssii", $tweet_id, $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $retweet, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName, $retweetCount, $favoriteCount);
    if ( false===$rc ) {
        // again execute() is useless if you can't bind the parameters. Bail out somehow.
        die('bind_param() failed: ' . htmlspecialchars($stmt_data->error));
    }

    if ( $json ) {

        $now = new DateTime();
        $now->format('D M d H:i:s O Y');
        $now->getTimestamp();

        $jsonTweets = json_encode($json);

        $response = json_decode($jsonTweets,true);
    // Evaluate each response
        foreach($response as $key => $tweet){
        // Set retweet and favorite counts
            $retweetCount = $tweet["retweet_count"];
            $favoriteCount = $tweet["favorite_count"];
        //Set retweeted boolean
            if(is_null($tweet["retweeted_status"])){
                $retweet = 0;
            } else {
                $retweet = 1;
            }

        //   var_dump($tweet);
        //   echo "<br> <br>";

        // Set $tweet_id
            $tweet_id = $tweet['id'];
        // Initalize user parameters
            $userUrl = $tweet['user']['url'];
            $userImg = $tweet['user']['profile_image_url'];
            $userSN = $tweet['user']['screen_name'];
            $userName = $tweet['user']['name'];
            $tweetTime = $tweet['created_at'];
            $urlArray = [];
            //                                var_dump($tweet["entities"]["media"][0]["media_url"]);
            //                                echo "<br>";


            //                                if (isset($tweet->entities->media)) {
            //                                    $media_url = $tweet["entities"]["media"][0]["media_url"];
            //                                    $urlArray[] = $media_url;
            //                                    var_dump($media_url);
            //                                    echo "<br>";
            ////                                    foreach ($tweet->entities->media as $media) {
            ////                                        $media_url = $media["media_url"]; // Or $media->media_url_https for the SSL version.
            ////                                        $urlArray[] = $media_url;
            ////                                        var_dump($media_url);
            ////                                        echo "<br>";
            ////                                    }
            //                                }
            //                                    foreach($tweet["entities"]["media"] as $urlinfo){
            //                                        $url = $urlinfo["media_url"];
            //                                        var_dump($url);
            //                                        $urlArray[] = $url;
            //                                    }

            $urlArray = $tweet["entities"]["media"][0]["media_url"];
            $tweetUrl = json_encode($tweet["entities"]["urls"]);
            $tweetImg = serialize($urlArray);
            //                                $tweetImg = json_encode($tweet["entities"]["media"]);
            $tweetHash = json_encode($tweet["entities"]["hashtags"]);


            $status_count = $tweet['user']['statuses_count'];
            $user_time = $tweet['user']['created_at'];
            $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $user_time);
            //$create_date = $new_date->format('Y-m-d H:i:s');
            $amt_time = $now->diff($create_date);


            $posterFrequency = round($status_count/$amt_time);
            $text = $tweet['text'];
            $popularity = $tweet['retweet_count'];
            if ($tweet['user']['verified']) {
                $verified = 1;
            } else {
                $verified = 0;
            }


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

            // $rc = $stmt_data->execute();
            // if ( false===$rc ) {
            //   die('execute() failed: ' . htmlspecialchars($stmt_data->error));
            // }

            // if($conn){
            //     var_dump($conn);
            // }
            // else{
            //     echo "not connected";
            // }


        // Bind each $tweet with the paramters
        if ($stmt_data->execute() === false) {
          die('execute() failed: ' . htmlspecialchars($stmt_data->error));
        }

        }

        $stmt_data->close();


    }
?>
