<?php
    
    
		session_start(); ?>

<!DOCTYPE html>
<html>
<head>
<title> Tweed Twitter Feed Research </title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css">

</head>

<?php

		require_once 'TwitterOAuth/autoload.php';
		use Abraham\TwitterOAuth\TwitterOAuth;

		define('CONSUMER_KEY', 'HDhjz43hHgbl6B7fEVy3wHApk');
        define('CONSUMER_SECRET', '9xaTyEdOWSs8O9JCdHUjnYpZCoTj1pn75y7FmAS4o8EzH83LPu');
		define('OAUTH_CALLBACK', 'http://twitterfeed.web.engr.illinois.edu/TweedStudy/');

		session_start();
    
        unset($_SESSION['oauth_access_token']);
        unset($_SESSION['oauth_access_token_secret']);
        unset($_SESSION['data_in_db']);

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);

		$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

		$_SESSION['oauth_request_token'] = $request_token['oauth_token'];
		$_SESSION['oauth_request_token_secret'] = $request_token['oauth_token_secret'];

		$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

    echo '<div class="row" > <div class="classWithPad" style="margin:20px; padding:20px;"> <div class="col-xs-12" >';

    echo '<img src="img/imark_bold.gif">';
    
    echo "<p>Welcome to the Tweed Twitter News Feed Evaluation Study!</p>";
    
    echo "<p><b>DESCRIPTION</b>: We are researchers at the University of Illinois doing a research study about what people want to see in their Twitter news feed and whether it is possible to build systems that help people see more of what they want.  All data collected in this study are for research purposes only. We will show you three different systems we have built and ask you to answer questions about your preferences and/or experience of your own news feed. Participation should take approximately 45 minutes.";
    
    echo "<p><b>RISKS and BENFITS</b>: The risks to your participation in this online study are those associated with basic computer tasks, including boredom, fatigue, or mild stress. The only benefit to you is the learning experience from participating in a research study.  The benefit to society is the contribution to scientific knowledge.";
    
    echo "<p><b>COMPENSATION</b>: We will pay participants $10/hour for the duration of their interview. The interview process should take approximately 45 minutes.</p>";

    echo "<p><b>CONFIDENTIALITY</b>: Your interview will be recorded, but the original data will not be shared with anyone outside of our group at the University of Illinois. We will also maintain a single key linking your interview number with your email address (which will be stored in a locked safe in the PI’s office). We may contact you after the initial interview with follow up questions, but you are not obligated to participate and will be similarly compensated for any follow up contributions.</p>";
    
    echo "<p>Any reports and presentations about the findings from this study will not include your name or any other information that could identify you.</p>";
    
    echo "<p><b>SUBJECT'S RIGHTS</b>: Your participation is voluntary.  You may stop participating at any time by withdrawing from the study. Partial data will not be analyzed but you will still be compensated for the time that you spent in the interview.</p>";
    
    echo "<p>For additional questions about this research, you may contact:</p>";
    
    echo "<p>Kristen Vaccaro: kvaccaro@illinois.edu</p>";
    echo "<p>Prof. Karrie Karahalios: kkarahal@cs.uiuc.edu</p>";
    
    echo "<p>For questions about your rights as a research participant, you may contact the University of Illinois Urbana-Champaign Institutional Review Board at 217-333-2670.</p>";
    
        echo "<p><b>TWITTER ACCESS</b>: On the next page we will begin the study, by having you log onto your Twitter account. This will allow you to look at your own Twitter feed when considering the tools. We will not retain any account information after the study is complete and will destroy our access credentials after your interview has finished.</p>";
    
    echo "<p>Please indicate below that you are at least 18 years old, have read and understand this consent form, and you agree to participate in this online research study.</p>";

	    echo "<a href='$url'>I consent </a>";
    
    echo '</div></div></div>';

?>

</html>
