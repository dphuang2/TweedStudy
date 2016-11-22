Rails.application.config.middleware.use OmniAuth::Builder do
  #provider :twitter, Rails.application.secrets.twitter_api_key, Rails.application.secrets.twitter_api_secret
  provider :twitter, "VplV7u5kB7QEJXMPQwJbudQAH", "ZmCI2RCZa9xYqaXuCRmxmsAvSQQ1wlG6Y1NR9dmPxDwXXqjoYe"
end
