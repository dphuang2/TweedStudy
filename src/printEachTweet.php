<?php
	function printEachTweet($tweet){

	    $now = new DateTime();
	    $now->format('D M d H:i:s O Y');
	    $now->getTimestamp();

	    $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $tweet['tweet_create_date']);
        
        
        $amt_time = $now->diff($create_date);

        if($amt_time->d>0){
            $print_time = $create_date->format('M d');
        }
        elseif($amt_time->h>0){
            $print_time = $amt_time->h . "h";
        }
        elseif($amt_time->i>0){
            $print_time = $amt_time->i . "m";
        }
        else{
            $print_time = $amt_time->s . 's';
        }
        
        
        

//			$now2 = $now->format('Y-m-d H:i:s');
//			$create_date2 = $create_date->format('Y-m-d H:i:s');
//			$difference = strtotime($create_date2)-strtotime($now2);
//			echo "difference";
//			var_dump($difference);
//			$amt_time_sec = $difference;
	    // $create_date = $new_date->format('Y-m-d H:i:s');
// 	    $amt_time = $now->diff($create_date);
// 			// var_dump($amt_time);
// 			// $amt_time_sec = $amt_time->format('%U');
//			var_dump($amt_time_sec);
//			if($amt_time_sec < 60){
//				$print_time = $amt_time->format('s') . 's';
//			}
//	    elseif ($amt_time_sec < 3600) {
//	        $print_time = $amt_time->format('i') . "m";
//	    } elseif ($amt_time_sec < 86400) {
//	        $print_time = $amt_time->format('H') . "h";
//	    } else {
//	        $print_time = $amt_time->format('M d');
//	    }

	    echo '<div class="tweet">';
			echo "<br>";
	    echo '<div class="container-fluid">';
	    echo '<div class="row-fluid">';
	    echo '<div class="col-xs-1">';
	    echo "<a href={$tweet['user_url']}><img src={$tweet['user_profile_img_url']} height='42' width='42'></a>";
	    echo '</div>';
	    echo '<div class="col-xs-10">';
	    echo "<a href={$tweet['user_url']}><b>{$tweet['user_name']}</b></a> @{$tweet['user_screen_name']} • {$print_time}<br>{$tweet['tweet_text']}<br>";

	    $images = unserialize($tweet['tweet_images']);

	    if (!is_null($images)){
	        //                            foreach ($images as $img) {
	        //                                echo $img;
	        echo "<img src={$images} style='max-width:100%;' >";
	    } //}

	    //                        echo $tweet_urls;

	    //                        if (!empty($tweet_urls)){
	    //                            foreach ($tweet_urls as $url) {
	    //                                echo "<a href={$url['url']}>{$url['display_url']}</a>";
	    //                            }}
	    //                        if (!empty($hashtags)){
	    //                            foreach ($hashtags as $hash) {
	    //                                echo "<a href='https://twitter.com/hashtag/{$hash['text']}\?src=hash'>#{$hash['text']}</a>";
	    //                            }}
	    //                        echo "|| Value = <b><i>".$thisValueArray[$key]."</b></i><br>";

	    echo '</div> </div> </div>';
			echo "<br>";
	    echo "<hr>";
	    echo "</div>";

	}
?>
