module SaveData
    include Compute
    include GetAll

    def save_messages(user, client)
        messages = get_all_messages(client)
        messages.each do |message|
            user.message.find_or_create_by(twitter_id: message.id) do |m|
                m.text = message.text
                m.sender_id = message.sender.id
                m.sender_name = message.sender.screen_name
                m.sent_date = message.created_at # %a %b %e %m %M %S %z %Y 
                m.sentiment = compute_sentiment(message.text)
                m.word_count = message.text.split.size
                m.twitter_id = message.id
            end
        end
    end

    def save_friends(user, client)
        friends = twitter_followed_users(client)
        index = 1
        friends.each do |friend|
            user.friend.find_or_create_by(twitter_id: friend.id) do |f|
                f.nickname = friend.screen_name
                f.name = friend.name
                f.image_url = friend.profile_image_url
                f.twitter_url = friend.url
                f.statuses_count = friend.statuses_count
                f.followers_count = friend.followers_count
                f.location = friend.location
                f.screen_name = friend.screen_name
                f.lang = friend.lang
                f.friends_count = friend.friends_count
                f.description = friend.description
                f.twitter_creation_date = friend.created_at
                f.time_zone = friend.time_zone
                f.twitter_id = friend.id
                f.celebrity = compute_celebrity(friend)
                f.verified = friend.verified
                f.post_frequency = compute_frequency(friend.created_at, friend.statuses_count)
                f.closeness = compute_closeness(friend, index, friends.count)
            end
            index += 1
        end

        # Updating fake values
        shuffle("frequency")
        shuffle("closeness")
        shuffle("celebrity")
        Friend.all.each do |t| 
          t.update(fake_celebrity: grab_value("celebrity")) if t.fake_verified == nil
          t.update(fake_post_frequency: grab_value("frequency")) if t.fake_post_frequency == nil
          t.update(fake_closeness: grab_value("closeness")) if t.fake_closeness == nil
        end

    end

    # Save tweets to database including the calculations of sentiments, and popularity
    def save_tweets(user, client)
        tweets = get_all_tweets(user.screen_name, client)
        #tweets = get_few_tweets(client)
        tweets.each do |tweet| # tweet refers to Tweet from Twitter
            unless tweet.user.screen_name == user.screen_name then
                user.tweet.find_or_create_by(tweet_id: tweet.id) do |t|
                    #debugger if tweet.media?
                    #debugger if tweet.retweeted_status.media?
                    t.complete_json = tweet.to_json
                    t.text = tweet.full_text
                    if JSON.parse(tweet.to_json).has_key? "retweeted_status" 
                        t.retweet_user_screen_name = tweet.retweeted_status.user.screen_name
                        t.retweet_user_name = tweet.retweeted_status.user.name
                        t.retweet_user_profile_img = tweet.retweeted_status.user.profile_image_url.to_s
                        t.retweet_user_url = "https://twitter.com/#{tweet.retweeted_status.user.screen_name}"
                    end
                    t.user_profile_img = tweet.user.profile_image_url.to_s
                    t.user_screen_name = tweet.user.screen_name
                    t.user_name = tweet.user.name
                    t.user_url = "https://twitter.com/#{tweet.user.screen_name}"
                    t.verified = tweet.user.verified
                    t.fake_verified = Friend.find_by(screen_name: t.user_screen_name).fake_verified
                    # If the tweet is a retweet, the media location is in a different location
                    if tweet.retweeted_status.media?
                        media = JSON.parse(tweet.retweeted_status.media.to_json)
                    elsif tweet.media?
                        media = JSON.parse(tweet.media.to_json)
                    end

                    if !media.nil?
                        # If the media is a video, then the location of the media is idfferent
                        if media[0].key? "video_info"
                            # If the media is a gif or mp4, the url is different
                            media[0]["video_info"]["variants"].each do |video|
                                if video["content_type"] == "video/mp4"
                                    media = video["url"]
                                end
                            end
                        else
                            media = media[0]["media_url"]
                        end
                        t.media_url = media
                    end

                    #t.hashtags
                    t.retweet_count = tweet.retweet_count
                    t.favorite_count = tweet.favorite_count
                    t.tweet_id = tweet.id
                    t.tweet_created_at = tweet.created_at
                    t.sentiment = compute_sentiment(tweet.full_text)
                    t.popularity = compute_popularity(tweet.retweet_count)
                    t.poster_frequency = Friend.find_by(screen_name: tweet.user.screen_name).post_frequency
                    t.fake_poster_frequency = Friend.find_by(screen_name: tweet.user.screen_name).fake_post_frequency
                    t.closeness = Friend.find_by(screen_name: tweet.user.screen_name).closeness
                    t.fake_closeness = Friend.find_by(screen_name: tweet.user.screen_name).fake_closeness
                    t.verified = Friend.find_by(screen_name: tweet.user.screen_name).verified
                    t.fake_verified = Friend.find_by(screen_name: tweet.user.screen_name).fake_verified
                    t.celebrity = Friend.find_by(screen_name: tweet.user.screen_name).celebrity
                    t.fake_celebrity = Friend.find_by(screen_name: tweet.user.screen_name).fake_celebrity
                end
            end
        end

        shuffle("popularity")
        shuffle("sentiment")
        Tweet.all.each do |t| 
          t.update(fake_popularity: grab_value("popularity")) if t.fake_popularity == nil
          t.update(fake_sentiment: grab_value("sentiment")) if t.fake_sentiment == nil
        end

    end
end
