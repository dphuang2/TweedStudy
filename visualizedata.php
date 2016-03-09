<!DOCTYPE html>
<html>
	<head>
		<title> Tweed Twitter Feed Research </title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="css/stylesheet.css">
		<script language="JavaScript" type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	</head>
</html>
<?php
    session_start();
//SQL Authorization
    $servername = "engr-cpanel-mysql.engr.illinois.edu";
    $username = "twitterf_user";
    $password = "IIA@kT$7maLt";
    $dbname = "twitterf_tweet_store";

    $db = new mysqli($servername, $username, $password, $dbname);

    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $user_id = $_SESSION["user_id"];

    $sql = "SELECT * FROM `friends` WHERE user_id = {$user_id} ORDER BY computed_rank DESC LIMIT 600";
    //Print each friend in computed_rank descending order
        if(!$result = $db->query($sql)){
            die('There was an error running the query [' . $db->error . ']');
        }

        while($row = $result->fetch_assoc()){
            $computed_rank = $row['computed_rank'] + 5;
            $bar_length = 11;
            echo '<div class="container-fluid">';
    	    echo '<div class="row-fluid">';
            echo '<div class="col-xs-2">';
            echo "<b> @{$row['screen_name']} </b> ";
            echo '</div>';
            echo '<div class="col-xs-4">';
            while($bar_length > 0){
            if($computed_rank == 0){
                echo "<b>[x]</b>";
            }elseif($bar_length == 6){
                echo "[0]";
            }
            else {
                echo "[ ]";
            }
            $computed_rank--;
            $bar_length--;
            }
            echo '</div>';
            echo '<div class="col-xs-2">';
            echo "{$row['computed_rank']}";
            echo '</div>';
            echo '</div> </div>';
            echo '<br>';
        }

        $db->close();

?>
