class SessionsController < ApplicationController

  # Holds all active filters -> [low, high]
  # Low numbers should always be 0 for index 0
  # High number should be the size of tweets for that user (ALL the same at the very beginning)
  # Have an array for each filter and then AND union all the sets 
  #
  # Sentiment = [-4, -4, -3, -3, -3, -3, -3 , -2, 0, 0, 0, 1, 1, 1....]
  # Closeness = [-4, -4, -3, -3, -3, -3, -3 , -2, 0, 0, 0, 1, 1, 1....]
  # Poster Frequency = [-4, -4, -3, -3, -3, -3, -3 , -2, 0, 0, 0, 1, 1, 1....]
  # Celebrity = [-4, -4, -3, -3, -3, -3, -3 , -2, 0, 0, 0, 1, 1, 1....]

  $filters = {
    "sentiment"=> [0,999999999],
    "closeness"=> [0,999999999],
    "poster_frequency"=> [0,999999999],
    "celebrity"=> [0,999999999],
    "popularity"=> [0,999999999]
  }

  def create
    $user = User.find_or_create_from_auth_hash(auth_hash)
    client = Twitter::REST::Client.new do |config|
      config.consumer_key = "VplV7u5kB7QEJXMPQwJbudQAH"
      config.consumer_secret = "ZmCI2RCZa9xYqaXuCRmxmsAvSQQ1wlG6Y1NR9dmPxDwXXqjoYe"
      config.access_token = auth_hash[:credentials][:token]
      config.access_token_secret = auth_hash[:credentials][:secret]
    end
    session[:twitter_id] = auth_hash[:uid]
    save_messages $user, client
    save_friends $user, client
    save_tweets $user, client
    redirect_to '/pick'
  end

  def destroy
    log_out if logged_in?
    redirect_to root_path
  end

  def pick
    if logged_in?
      $user = current_user
      $tweets = $user.tweet.order("RANDOM()").order(tweet_id: :desc).limit(10)
    else
      redirect_to root_path
    end
  end

  def feed1

    params.each do |tweet|
      next if tweet == "commit" || tweet == "controller" || tweet == "action"

      dTweet = Tweet.find(tweet)
      dTweet.update(pick: params[tweet])
    end

    feed_handler
  end

  def feed2
    feed_handler
  end

  def reset

    $filters = {
      "sentiment"=> [0,999999999],
      "closeness"=> [0,999999999],
      "poster_frequency"=> [0,999999999],
      "celebrity"=> [0,999999999],
      "popularity"=> [0,999999999]
    }

    tweets = $all_tweets.order(tweet_id: :desc).limit(100)
    @real_tweets = tweets
    @fake_tweets = tweets
  end

  def filter
    # Capture data as integer
    low = params[:low].to_i
    high = params[:high].to_i

    # Capture filter
    filter = params[:filter]

    # Set new high and low of that specific filter
    $filters[filter][0] = low
    $filters[filter][1] = high

    $out_tweets_sent = $tweets_sent[$filters["sentiment"][0]..$filters["sentiment"][1]]
    $out_tweets_close = $tweets_close[$filters["closeness"][0]..$filters["closeness"][1]]
    $out_tweets_post = $tweets_post[$filters["poster_frequency"][0]..$filters["poster_frequency"][1]]
    $out_tweets_pop = $tweets_pop[$filters["popularity"][0]..$filters["popularity"][1]]
    $out_tweets_celeb = $tweets_celeb[$filters["celebrity"][0]..$filters["celebrity"][1]]

    @real_tweets = $out_tweets_sent & $out_tweets_close & $out_tweets_post & $out_tweets_pop & $out_tweets_celeb

    $fake_out_tweets_sent = $fake_tweets_sent[$filters["sentiment"][0]..$filters["sentiment"][1]]
    $fake_out_tweets_close = $fake_tweets_close[$filters["closeness"][0]..$filters["closeness"][1]]
    $fake_out_tweets_post = $fake_tweets_post[$filters["poster_frequency"][0]..$filters["poster_frequency"][1]]
    $fake_out_tweets_pop = $fake_tweets_pop[$filters["popularity"][0]..$filters["popularity"][1]]
    $fake_out_tweets_celeb = $fake_tweets_celeb[$filters["celebrity"][0]..$filters["celebrity"][1]]

    @fake_tweets = $fake_out_tweets_sent & $fake_out_tweets_close & $fake_out_tweets_post & $fake_out_tweets_pop & $fake_out_tweets_celeb

    @real_tweets = @real_tweets.sort_by{|tweet| tweet.id}
    @real_tweets = @real_tweets[0..99]
    @fake_tweets = @fake_tweets.sort_by{|tweet| tweet.id}
    @fake_tweets = @fake_tweets[0..99]

    # For tracking every filter change
    Track.create do |t|
      t.twitter_id = session[:twitter_id]
      t.screen_name = $user.screen_name
      t.celebrity_low = $filters["celebrity"][0]
      t.celebrity_high = $filters["celebrity"][1]
      t.poster_frequency_low = $filters["poster_frequency"][0]
      t.poster_frequency_high = $filters["poster_frequency"][1]
      t.closeness_low = $filters["closeness"][0]
      t.closeness_high = $filters["closeness"][1]
      t.sentiment_low = $filters["sentiment"][0]
      t.sentiment_high = $filters["sentiment"][1]
      t.popularity_low = $filters["popularity"][0]
      t.popularity_high = $filters["popularity"][1]
    end

  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end

  def feed_handler
    if logged_in?
      # Get all tweets and friends of the current user
      $user = current_user
      $all_tweets = $user.tweet.all
      $num_of_tweets = $all_tweets.count
      $all_friends = $user.friend.all

      # Get the first 100 tweets
      $tweets = $all_tweets.order(tweet_id: :desc).limit(100)

      $tweets_sent = $all_tweets.order(sentiment: :asc)
      $tweets_close = $all_tweets.order(closeness: :asc)
      $tweets_post = $all_tweets.order(poster_frequency: :asc)
      $tweets_pop = $all_tweets.order(popularity: :asc)
      $tweets_celeb = $all_tweets.order(celebrity: :asc)
      $fake_tweets_sent = $all_tweets.order(fake_sentiment: :asc)
      $fake_tweets_close = $all_tweets.order(fake_closeness: :asc)
      $fake_tweets_post = $all_tweets.order(fake_poster_frequency: :asc)
      $fake_tweets_pop = $all_tweets.order(fake_popularity: :asc)
      $fake_tweets_celeb = $all_tweets.order(fake_celebrity: :asc)
    else
      redirect_to root_path
    end
  end
end
