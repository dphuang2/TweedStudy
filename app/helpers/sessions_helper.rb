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
end
