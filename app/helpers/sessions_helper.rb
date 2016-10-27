module SessionsHelper
  require 'rails_autolink'
  require 'certified' # This verified your SSL certificate (it seems to be a problem on Windows)
  include SaveData

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
  

end
