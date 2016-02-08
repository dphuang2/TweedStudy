<?php

  $dmjson = $connection->get('direct_messages');
  // prepare and bind
  $stmt2 = $conn->prepare("INSERT INTO directMessages (user_id, dm_text, sender_id, sender_name) VALUES (?, ?, ?, ?)");
  if ( false===$stmt2 ) {
      die('prepare() failed: ' . htmlspecialchars($mysqli->error));
  }
  $stmt2->bind_param("isis", $userid, $dmtext, $senderId, $senderName);
  $rc2 = $stmt2->bind_param("isis", $userid, $dmtext, $senderId, $senderName);
  if ( false===$rc2 ) {
      // again execute() is useless if you can't bind the parameters. Bail out somehow.
      die('bind_param() failed: ' . htmlspecialchars($stmt->error));
  }
  if ( $dmjson ) {
      $jsonDM = json_encode($dmjson);
      $response = json_decode($jsonDM,true);
      foreach($response as $key => $tweet){

          $dmtext = $tweet["text"];
          $senderId = $tweet["sender_id"];
          $senderName = $tweet["sender"]["name"];

          $stmt2->execute();
      }
      $stmt2->close();
  }

?>
