module SessionsHelper
  require 'rails_autolink'

  @@happyWords = File.readlines("app/assets/words/happyWords.txt")
  @@happyWords = @@happyWords.map{ |word| word.delete "\n"}
  @@sadWords = File.readlines("app/assets/words/sadWords.txt")
  @@sadWords = @@sadWords.map{ |word| word.delete "\n"}
  @@words = Hash.new
  @@sadWords.each do |sadWord|
      @@words[sadWord] = -1
  end
  @@happyWords.each do |happyWord|
      @@words[happyWord] = 1
  end

  # For fake calculating
  @@sentimentArray = []

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

  # Save tweets to database including the calculations of sentiments, and popularity
  def save_tweets(user, client)
    tweets = get_all_tweets(user.screen_name, client)
    tweets.each do |tweet| # tweet refers to Tweet from Twitter
      user.tweet.find_or_create_by(tweet_id: tweet.id) do |t|
        #debugger if tweet.media?
        #debugger if tweet.retweeted_status.media?
        t.complete_json = tweet.to_json
        t.text = tweet.full_text
        t.sentiment = compute_sentiment(tweet.full_text)
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
        #t.popularity
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
      #client.home_timeline(options)
      client.user_timeline(user, options)
    end
  end

  def time_ago_from_string(string)
    # 2016-09-23 06:18:44 +0000
    datetime = DateTime.strptime(string, '%Y-%m-%d %H:%M:%S')
    return time_ago_in_words(datetime)
  end

  def retweet?(tweet)
    !tweet.retweet_user_screen_name.nil?
  end

  def format_tweet_text(text)
    return auto_link text, :html => { :target => '_blank'  }
  end

  def compute_popularity
  end

  def compute_sentiment(text)
      # Format text into array
      text = text.gsub(/[^a-z0-9\s]/i, '').downcase
      tweet_array = text.split(" ")

      # Initialize sentiment to 0
      sentiment = 0

      tweet_array.each do |word|
          sentiment += @@words[word] if @@words.has_key?(word) 
      end

      @@sentimentArray.push(sentiment)

      #debugger

      return sentiment
  end

end
