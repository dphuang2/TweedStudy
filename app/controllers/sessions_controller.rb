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
      config.consumer_key = Rails.application.secrets.twitter_api_key
      config.consumer_secret = Rails.application.secrets.twitter_api_secret
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
      $tweets = @user.tweet.order(tweet_id: :desc).limit(100)

      @sent_min = $tweets.minimum("sentiment");
      @sent_max = $tweets.maximum("sentiment");
      @close_min = $tweets.minimum("closeness");
      @close_max = $tweets.maximum("closeness");
      @post_min = $tweets.minimum("poster_frequency");
      @post_max = $tweets.maximum("poster_frequency");
      @pop_min = $tweets.minimum("popularity");
      @pop_max = $tweets.maximum("popularity");
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

    # Fixes exclusive query to be inclusive
    if ($filters["verified"][0] + $filters["verified"][1]) == 0
      ver = 0
    elsif ($filters["verified"][0] + $filters["verified"][1]) == 2
      ver = 1
    else
      ver = [0,1]
    end


    @real_tweets = $tweets.where(
      popularity: ($filters["popularity"][0]...$filters["popularity"][1]),
      poster_frequency: ($filters["poster_frequency"][0]...$filters["poster_frequency"][1]),
      sentiment: ($filters["sentiment"][0]...$filters["sentiment"][1]),
      closeness: ($filters["closeness"][0]...$filters["closeness"][1]),
      verified: ver,
    )

    @fake_tweets = $tweets.where(
      fake_popularity: ($filters["popularity"][0]...$filters["popularity"][1]),
      fake_poster_frequency: ($filters["poster_frequency"][0]...$filters["poster_frequency"][1]),
      fake_sentiment: ($filters["sentiment"][0]...$filters["sentiment"][1]),
      fake_closeness: ($filters["closeness"][0]...$filters["closeness"][1]),
      fake_verified: ver,
    )

  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end
end
