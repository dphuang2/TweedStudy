<?php
    // Authorization
    ini_set('display_errors', 1);
    //					require_once('TwitterAPIExchange.php');
    require 'TwitterOAuth/autoload.php';
    use Abraham\TwitterOAuth\TwitterOAuth;

    define('CONSUMER_KEY', 'HDhjz43hHgbl6B7fEVy3wHApk');
    define('CONSUMER_SECRET', '9xaTyEdOWSs8O9JCdHUjnYpZCoTj1pn75y7FmAS4o8EzH83LPu');
    define('OAUTH_CALLBACK', 'http://twitterfeed.web.engr.illinois.edu/TweedStudy/index.php');

    if ((!isset($_SESSION['oauth_access_token'])) || ($_SESSION['oauth_access_token'])=='') {
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_request_token'], $_SESSION['oauth_request_token_secret']);
        //                        echo $_REQUEST['oauth_verifier'];
        //                        echo "<br>";
        $access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
        $_SESSION['oauth_access_token'] = $access_token['oauth_token'];
        $_SESSION['oauth_access_token_secret'] = $access_token['oauth_token_secret'];
    } else {
        //                        echo $_SESSION['oauth_access_token'];
        //                        echo "<br>";
    }


    //                    $request_token = [];
    //                    $request_token['oauth_token'] = $_SESSION['oauth_token'];
    //                    $request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];


    $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_access_token'], $_SESSION['oauth_access_token_secret']);

    $user = $connection->get("account/verify_credentials");

// Set user as session variable
    $_SESSION["user"] = $user;

    //                    echo $_SESSION['oauth_access_token'];
    //                    echo "<br>";
    //                    echo $_SESSION['oauth_token'];
    //                    echo "<br>";
    //                    echo CONSUMER_KEY;
    //                    echo "<br>";
    //                    var_dump($connection);
    //                    echo "<br>";
    //                    var_dump($user);

    //					/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
    //					$settings = array(
    //									  'oauth_access_token' => $_SESSION['oauth_access_token'],
    //									  'oauth_access_token_secret' => $_SESSION['oauth_access_token_secret'],
    //									  'consumer_key' => CONSUMER_KEY,
    //									  'consumer_secret' => CONSUMER_SECRET
    //									  );
    //
    //					/** Perform a GET request and echo the response **/
    //					$url = 'https://api.twitter.com/1.1/statuses/home_timeline.json';
    //					$requestMethod = 'GET';
    //					$twitter = new TwitterAPIExchange($settings);
    //					$jsonTweets = $twitter->buildOauth($url, $requestMethod)
    //								->performRequest();

    //$jsonTweets = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));


    //                    $jsonTweets = getData($connection);

    /** Process the response (JSON format) using json_decode: http://docs.php.net/json_decode **/
    $response = json_decode($jsonTweets,true);

    /** Go through every tweet and print out line by line -- will ideally need some pleasant wrapping with bootstrap -- maybe add IDs to process instead
     Example of the kind of information that can be returned here: https://dev.twitter.com/rest/reference/get/statuses/home_timeline **/
?>
