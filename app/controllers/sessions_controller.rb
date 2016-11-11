class SessionsController < ApplicationController
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
    redirect_to '/feed'
  end

  def destroy
    log_out if logged_in?
    redirect_to root_path
  end

  def feed
    if logged_in?
      @user = current_user
      $tweets = @user.tweet.order(tweet_id: :desc).limit(100);
    else
      redirect_to root_path
    end
  end

  def filter
      # @TODO:
      # Tolerance should be normalized
      @user = current_user

      low = params[:low].to_i
      high = params[:high].to_i
      filter = params[:filter]

      @real_tweets = $tweets.select {|tweet| tweet[filter] >= low && tweet[filter] <= high}
      @fake_tweets = $tweets.select {|tweet| tweet["fake_#{filter}"] >= low && tweet["fake_#{filter}"] <= high}
  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end
end
