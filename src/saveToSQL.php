
<?php
    function saveToSQL($connection,$max_id,$cursor) {
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

    // GET from timeline and set $next_max_id
      // If this is the first GET, don't put $max_id as parameter
        if($max_id == null){
          $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
        }else{
          $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true, "max_id" => $max_id));
        }
    // GET friends/list
        if($cursor == null){
          $json_friends = $connection->get("friends/list", array("user_id" => $userid));
        }else{
          $json_friends = $connection->get("friends/list", array("user_id" => $userid, "cursor" => $cursor));
        }

    // prepare and bind
        $stmt_data = $conn->prepare("INSERT INTO data (tweet_id, user_id, tweet_text, tweet_popularity, poster_frequency, verified, sentiment, user_url, user_profile_img_url, user_screen_name, tweet_create_date, tweet_urls, tweet_images, tweet_hashtags, user_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE tweet_id=tweet_id");

        if ( false===$stmt_data ) {
            die('prepare() failed: ' . htmlspecialchars($mysqli->error));
        }
    // Define parameters
        $stmt_data->bind_param("iisiiiissssssss", $tweet_id, $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName);

    // Check if you can't bind parameters
        $rc = $stmt_data->bind_param("iisiiiissssssss", $tweet_id, $userid, $text, $popularity, $posterFrequency, $verified, $happyValue, $userUrl, $userImg, $userSN, $tweetTime, $tweetUrl, $tweetImg, $tweetHash, $userName);
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
                #$userid = 1;
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
                $stmt_data->execute();



            }

            // return $next_max_id;
            return array("cursor" => $cursor, "next_max_id" => $tweet_id);

            $stmt_data->close();




        }


        $conn->close();



    }
?>
