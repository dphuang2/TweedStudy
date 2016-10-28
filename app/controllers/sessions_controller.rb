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
    save_friends @user, client
    save_messages @user, client
    save_tweets @user, client
    redirect_to '/feed'
  end

  def destroy
    log_out if logged_in?
    redirect_to root_path
  end

  def feed
    if logged_in?
      @user = User.find_by(:twitter_id => session[:twitter_id])
      @tweets = @user.tweet.order(tweet_id: :desc).limit(20)
    else
      redirect_to root_path
    end
  end

  def filter
      @user = User.find_by(:twitter_id => session[:twitter_id])
      @tweets = @user.tweet.order("#{params[:filter]} DESC").limit(20)
      @fake_or_not = params[:filter].start_with? ("fake")
  end

  protected
  def auth_hash
    request.env['omniauth.auth']
  end
end
