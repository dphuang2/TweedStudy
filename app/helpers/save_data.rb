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
    friends = client.friends
    friends.each do |friend|
      user.friend.find_or_create_by(twitter_id: friend.id) do |f|
        f.nickname = friend.screen_name
        f.name = friend.name
        f.image_url = friend.profile_image_url
        f.twitter_url = friend.url
        f.statuses_count = friend.statuses_count
        f.verified = friend.verified
        f.followers_count = friend.followers_count
        f.location = friend.location
        f.screen_name = friend.screen_name
        f.lang = friend.lang
        f.friends_count = friend.friends_count
        f.description = friend.description
        f.twitter_creation_date = friend.created_at
        f.time_zone = friend.time_zone
        f.twitter_id = friend.id
        f.post_frequency = compute_frequency(friend.created_at, friend.statuses_count)
        #f.closeness = 
      end
    end

    if Friend.first.fake_post_frequency.nil? # Checking for one checks for all
      Friend.all.each do |t| 
        t.update(fake_post_frequency: grab_fake_frequency)
      end
    end

  end

  # Save tweets to database including the calculations of sentiments, and popularity
  def save_tweets(user, client)
    #tweets = get_all_tweets(user.screen_name, client)
    tweets = get_few_tweets(client)
    tweets.each do |tweet| # tweet refers to Tweet from Twitter
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
        if tweet.retweeted_status.media?
          media = JSON.parse(tweet.retweeted_status.media.to_json)
          t.media_url = !media[0]["video_info"].nil? ? media[0]["video_info"]["variants"][0]["url"] : media[0]["media_url"]
        elsif tweet.media?
          media = JSON.parse(tweet.media.to_json)
          t.media_url = !media[0]["video_info"].nil? ? media[0]["video_info"]["variants"][0]["url"] : media[0]["media_url"]
        end
        #t.hashtags
        t.retweet_count = tweet.retweet_count
        t.favorite_count = tweet.favorite_count
        t.tweet_id = tweet.id
        t.tweet_created_at = tweet.created_at
        t.sentiment = compute_sentiment(tweet.full_text)
        t.popularity = compute_popularity(tweet.retweet_count)
        if tweet.user.screen_name != user.screen_name
          t.poster_frequency = Friend.find_by(screen_name: tweet.user.screen_name).post_frequency
          t.fake_poster_frequency = Friend.find_by(screen_name: tweet.user.screen_name).fake_post_frequency
        else 
          t.poster_frequency = 0
          t.fake_poster_frequency = 0
        end
      end
    end

    if Tweet.first.fake_popularity.nil? # Checking for one checks for all
      Tweet.all.each do |t| 
        t.update(fake_popularity: grab_fake_popularity)
        t.update(fake_sentiment: grab_fake_sentiment)
      end
    end

  end
end
