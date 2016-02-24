<?php
  // Friends List
    if($cursor == null){
      $json_friends = $connection->get("friends/list", array("user_id" => $userid, "count" => 10));
    }else{
      $json_friends = $connection->get("friends/list", array("user_id" => $userid, "count" => 10, "cursor" => $cursor));
    }

  // Prepare and bind_param
    $stmt_friends = $conn->prepare("INSERT INTO friends (rank, user_id, friend_id, screen_name, followers_count, verified, friends_count) VALUES (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE user_id = user_id");

  // Check for false statement
    if ( false===$stmt_friends ) {
        die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }

  // Define Parameters
    $stmt_friends->bind_param("iiisiii", $rank, $user_id, $friend_id, $screen_name, $followers_count, $verified, $friends_count);

  // Check if you can't bind Parameters
    $rc = $stmt_friends->bind_param("iiisiii", $rank, $user_id, $friend_id, $screen_name, $followers_count, $verified, $friends_count);
    if ( false===$rc ) {
        // again execute() is useless if you can't bind the parameters. Bail out somehow.
        die('bind_param() failed: ' . htmlspecialchars($stmt_friends->error));
    }

  // Run attachments
    if($json_friends){
      $jsonfriends = json_encode($json_friends);
      $response = json_decode($jsonfriends,true);

      $cursor = $response['next_cursor'];

      foreach($response['users'] as $key => $friend){
        $rank = $key;
        $user_id = $userid;
        $friend_id = $friend['id'];
        $screen_name = $friend['screen_name'];
        $followers_count = $friend['followers_count'];
        if ($friend['verified']) {
            $verified = 1;
        } else {
            $verified = 0;
        }
        $friends_count = $friend['friends_count'];

        if ($stmt_friends->execute() === false) {
            die('execute() failed: ' . htmlspecialchars($stmt_friends->error));
        }
      }
      $stmt_friends->close();
    }
?>
