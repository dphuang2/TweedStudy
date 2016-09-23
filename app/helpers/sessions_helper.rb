module SessionsHelper
  def current_user
    if (twitter_id = session[:twitter_id])
      @current_user ||= User.find_by(twitter_id: twitter_id)
    end
  end

  def logged_in?
    !current_user.nil?
  end

  def log_out
    session.delete(:twitter_id)
    @user = nil
  end

  def save_tweets(user, client)
    tweets = get_all_tweets(user.screen_name, client)
    tweets.each do |tweet| # tweet refers to Tweet from Twitter
      user.tweet.find_or_create_by(tweet_id: tweet.id) do |t|
        t.text = tweet.full_text
        t.retweet_user_screen_name = tweet.in_reply_to_screen_name
        t.retweet_user_profile_img = tweet.retweeted_status.user.profile_image_url.to_s
        t.retweet_user_url = "https://twitter.com/#{tweet.retweeted_status.user.screen_name}"
        t.user_profile_img = tweet.user.profile_image_url.to_s
        t.user_screen_name = tweet.user.screen_name
        t.media_url = JSON.parse(tweet.retweeted_status.to_json)["extended_entities"]["media"][0]["media_url"] if JSON.parse(tweet.to_json).has_key? "retweeted_status" && tweet.media?
        t.media_url = JSON.parse(tweet.to_json)["extended_entities"]["media"][0]["media_url"] if JSON.parse(tweet.to_json).has_key? "extended_entities" && tweet.media?
        #t.hashtags
        t.retweet_count = tweet.retweet_count
        t.favorite_count = tweet.favorite_count
        t.tweet_id = tweet.id
        t.tweet_created_at = tweet.created_at
        #t.popularity
        #t.poster_frequency
        #t.fake_popularity
        #t.fake_poster_frequency
      end
    end
  end

  # Functions to cursor through hometimeline
  def collect_with_max_id(collection=[], max_id=nil, &block)
    response = yield(max_id)
    collection += response
    response.empty? ? collection.flatten : collect_with_max_id(collection, response.last.id - 1, &block)
  end

  def get_all_tweets(user, client)
    collect_with_max_id do |max_id|
      options = {count: 200, include_rts: true}
      options[:max_id] = max_id unless max_id.nil?
      client.home_timeline(options)
    end
  end

  def compute_popularity
  end

end
