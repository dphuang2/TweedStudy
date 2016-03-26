
<?php
    function saveTrendsToSQL($connection) {
        $servername = "engr-cpanel-mysql.engr.illinois.edu";
        $username = "twitterf_user";
        $password = "IIA@kT$7maLt";
        $dbname = "twitterf_tweet_store";

    // Initalize userid variable with session "user_id"
        $userid = $_SESSION["user_id"];
        $user_ip = getenv('REMOTE_ADDR');
        $geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
        $country = $geo["geoplugin_countryName"];
        $city = $geo["geoplugin_city"];
        
        $place = $connection->get("geo/search", array("query",$city));
        
        //                    if ($city == "") {
        if (!is_numeric($place)) {
            $place = strval(2379574); // currently chicago, can't do champaign, 23424977 = US
        }
        
        $trends = $connection->get("trends/place", array("id" => $place));
        $trends = json_decode(json_encode($trends),true);
        
    // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

    // prepare and bind
        $stmt_data = $conn->prepare("INSERT INTO trends (user_id, hashtag) VALUES (?, ?)");

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

        if ( $trends ) {

            foreach ($trends[0]["trends"] as $trend) {
                $thisTrend=$trend["name"];
                
                // Bind each $tweet with the paramters
                $stmt_data->execute();
                
            }
            
            $stmt_data->close();
        }


        $conn->close();



    }
?>
