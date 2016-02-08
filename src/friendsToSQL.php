<?php
  // Friends List
      if($cursor == null){
        $json_friends = $connection->get("friends/list", array("user_id" => $userid));
      }else{
        $json_friends = $connection->get("friends/list", array("user_id" => $userid, "cursor" => $cursor));
      }
?>
