<?php
    function getData($connection)
    {
        //error_reporting( 0 ); // don't let any php errors ruin the feed
        //$number_tweets = 200;
        
        // check the cache file
        $cache_file = dirname(__FILE__).'/cache/'.'twitter-cache';
        if ( file_exists($cache_file)) {
            
            $modified = filemtime( $cache_file );
            $now = time();
            $interval = 600; // ten minutes
            
            if ( ( $now - $modified ) > $interval  ) {
                $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
                
                if ( $json ) {
                    $cache_static = fopen( $cache_file, 'w' );
                    fwrite( $cache_static, json_encode($json) );
                    fclose( $cache_static );
                }
            }
        } else {
            
            $json = $connection->get("statuses/home_timeline", array("count" => 200, "include_entities" => true));
            
            if ( $json ) {
                $cache_static = fopen( $cache_file, 'w' );
                fwrite( $cache_static, json_encode($json) );
                fclose( $cache_static );
            }
            
        }
        
        $json = file_get_contents( $cache_file );
        
        return $json;
    }
?>