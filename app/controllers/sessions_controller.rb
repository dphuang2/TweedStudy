class SessionsController < ApplicationController

  # Holds all active filters -> [low, high]
  $filters = {
    "sentiment"=> [-100,100],
    "closeness"=> [-100,100],
    "poster_frequency"=> [0,500000],
    "verified"=> [0,1],
    "popularity"=> [0,99999999]
  }

  def create
    @user = User.find_or_create_from_auth_hash(auth_hash)
    client = Twitter::REST::Client.new do |config|
      config.consumer_key = "VplV7u5kB7QEJXMPQwJbudQAH"
      config.consumer_secret = "ZmCI2RCZa9xYqaXuCRmxmsAvSQQ1wlG6Y1NR9dmPxDwXXqjoYe"
      config.access_token = auth_hash[:credentials][:token]
      config.access_token_secret = auth_hash[:credentials][:secret]
    end
    session[:twitter_id] = auth_hash[:uid]
    save_messages @user, client
    save_friends @user, client
    save_tweets @user, client
    redirect_to '/pick'
  end

  def destroy
    log_out if logged_in?
    redirect_to root_path
  end

  def pick
    if logged_in?
      @user = current_user
      $tweets = @user.tweet.order("RANDOM()").order(tweet_id: :desc).limit(10)
    else
      redirect_to root_path
    end
  end

  def feed
    if logged_in?
      @user = current_user
      $all_tweets = @user.tweet.all
      $all_friends = @user.friend.all
      $tweets = @user.tweet.order(tweet_id: :desc).limit(100)

      @sent_min = $all_tweets.minimum("sentiment");
      @sent_max = $all_tweets.maximum("sentiment");
      @close_min = $all_friends.minimum("closeness");
      @close_max = $all_friends.maximum("closeness");
      @post_min = $all_friends.minimum("post_frequency");
      @post_max = $all_friends.maximum("post_frequency");
      @pop_min = $all_tweets.minimum("popularity");
      @pop_max = $all_tweets.maximum("popularity");
    else
      redirect_to root_path
    end
  end

  def filter
    # @TODO:
    # Tolerance should be normalized

    # Capture data as integer
    low = params[:low].to_i
    high = params[:high].to_i

    # Capture filter
    filter = params[:filter]

    # Set new high and low of that specific filter
    $filters[filter][0] = low
    $filters[filter][1] = high

    ## Fixes exclusive query to be inclusive
    #if ($filters["verified"][0] + $filters["verified"][1]) == 0
      #ver = 0
    #elsif ($filters["verified"][0] + $filters["verified"][1]) == 2
      #ver = 1
    #else
      #ver = [0,1]
    #end


    @real_tweets = $tweets.where(
      popularity: ($filters["popularity"][0]..$filters["popularity"][1]),
      poster_frequency: ($filters["poster_frequency"][0]..$filters["poster_frequency"][1]),
      sentiment: ($filters["sentiment"][0]..$filters["sentiment"][1]),
      closeness: ($filters["closeness"][0]..$filters["closeness"][1]),
      verified: ($filters["verified"][0]..$filters["verified"][1]),
    )

    @fake_tweets = $tweets.where(
      fake_popularity: ($filters["popularity"][0]..$filters["popularity"][1]),
      fake_poster_frequency: ($filters["poster_frequency"][0]..$filters["poster_frequency"][1]),
      fake_sentiment: ($filters["sentiment"][0]..$filters["sentiment"][1]),
      fake_closeness: ($filters["closeness"][0]..$filters["closeness"][1]),
      fake_verified: ($filters["verified"][0]..$filters["verified"][1]),
    )

  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end
end
