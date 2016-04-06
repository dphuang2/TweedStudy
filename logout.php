<?php
session_unset();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>TweedStudy Logout</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
    </head>
        <body style="text-align: center;">
        <h1>You have been logged out</h1>
        <p>
            For you to fully log out from your twitter account, you need to be redirected to our
            authorization page and logout of your account at the top right of the page. Thank you!
        </p>

        <?php
        require_once 'src/TwitterOAuth/autoload.php';
        use Abraham\TwitterOAuth\TwitterOAuth;
        define('CONSUMER_KEY', 'HDhjz43hHgbl6B7fEVy3wHApk');
        define('CONSUMER_SECRET', '9xaTyEdOWSs8O9JCdHUjnYpZCoTj1pn75y7FmAS4o8EzH83LPu');
        define('OAUTH_CALLBACK', 'http://twitterfeed.web.engr.illinois.edu/TweedStudy/');
        unset($_SESSION['oauth_access_token']);
        unset($_SESSION['oauth_access_token_secret']);
        unset($_SESSION['data_in_db']);
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
        $_SESSION['oauth_request_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_request_token_secret'] = $request_token['oauth_token_secret'];
        $url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
        echo "<a style='color: black;' href='$url'> <button> De-Authorize </button> </a>";
        ?>


        <!-- <a style="color: black;" href="twitter.php"> <button> De-Authorize </button> </a> -->


        <br> <br> <img src="img/imark_bold.gif">
    </body>
</html>
