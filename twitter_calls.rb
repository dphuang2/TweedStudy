require 'twitter'

client = Twitter::REST::Client.new do |config|
  config.consumer_key        = "XDMrnx4b7Gdu6fMepQxGC4tfS"
  config.consumer_secret     = "ZDXy8Bs63UJqqn6E30gRmeZZrNGoPXSNXN9U8xdKUn5lpHHkFy"
  config.access_token        = "3197793925-ut8lvK9T86EiEMzqgyNmvqGaDwgYnasYTRCLE1T"
  config.access_token_secret = "wsfbhpI4Nh9FnX4HWUJqF2IrdOGuJawRCXvFgGOAk7WyE"
end

client.search("to:justinbieber marry me", result_type: "recent").take(3).collect do |tweet|
  puts "#{tweet.user.screen_name}: #{tweet.text}"
end

tweet = client.home_timeline.first
puts tweet.full_text
puts tweet.uri
puts tweet.created_at
puts tweet.favorite_count
puts tweet.filter_level
puts tweet.in_reply_to_screen_name

