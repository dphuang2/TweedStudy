<html>
	<?php


		require_once('TwitterOAuth/autoload.php');
		use Abraham\TwitterOAuth\TwitterOAuth;

		define('CONSUMER_KEY', 'XDMrnx4b7Gdu6fMepQxGC4tfS');
		define('CONSUMER_SECRET', 'ZDXy8Bs63UJqqn6E30gRmeZZrNGoPXSNXN9U8xdKUn5lpHHkFy');
		define('OAUTH_CALLBACK', 'http://web.engr.illinois.edu/~dphuang2/ControlPanelStudy/index.php');

		session_start();

		if(isset($_GET['lougout'])){
			session_unset();
			$redirect= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			head('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
		}

		if(!isset($_SESSION['data']) && !isset($_GET[oauth_token])) {
			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
			$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

			if($request_token){
				$token = $request_token['oauth_token'];
				$_SESSION['request_token'] = $token ;
				$_SESSION['request_token_secret'] = $request_token['oauth_token_secret'];
				$login_url = $connection->getAuthorizeURL($token);
			}
		}

		if(isset($_GET['oauth_token'])){

			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['request_token_secret']);
			$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			if($access_token){
				$connection = new TwitterOauth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
				$params = array('include_entities' => 'false');
				$data = $connection->get('account/verify_credentials', $params);
				if($data){
					$_SESSION['data']=$data;
					$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
					head('LocationL ' . filter_var($redirect, FILTER_SANITIZE_URL));
				}
			}
		}

		if(isset($login_url) && !isset($_SESSION['data'])){
			echo "<a href='$login_url'><button> Login to twitter! </button> </a>";
		}
		else{
			$data=$_SESSION
		}



	?>
</html>
