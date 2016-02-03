<?php
	function printEachTweet($tweet){
	    
	    $now = new DateTime();
	    $now->format('D M d H:i:s O Y');
	    $now->getTimestamp();
	    
	    $create_date = DateTime::createFromFormat('D M d H:i:s O Y', $tweet['tweet_create_date']);
	    //$create_date = $new_date->format('Y-m-d H:i:s');
	    $amt_time = $now->diff($create_date);
	    
	    if ($amt_time < 3600) {
	        $print_time = $create_date->format('i') . "m";
	    } elseif ($amt_time < 86400) {
	        $print_time = $create_date->format('H') . "h";
	    } else {
	        $print_time = $create_date->format('M d');
	    }

	    echo '<div class="tweet">';
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
	    
	    echo "<hr>";
	    echo "</div>";
	    
	}
?>