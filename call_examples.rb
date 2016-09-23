require 'twitter'

client = Twitter::REST::Client.new do |config|
  config.consumer_key        = "XDMrnx4b7Gdu6fMepQxGC4tfS"
  config.consumer_secret     = "ZDXy8Bs63UJqqn6E30gRmeZZrNGoPXSNXN9U8xdKUn5lpHHkFy"
  config.access_token        = "3197793925-ut8lvK9T86EiEMzqgyNmvqGaDwgYnasYTRCLE1T"
  config.access_token_secret = "wsfbhpI4Nh9FnX4HWUJqF2IrdOGuJawRCXvFgGOAk7WyE"
end

#client.search("to:justinbieber marry me", result_type: "recent").take(3).collect do |tweet|
  #puts "#{tweet.user.screen_name}: #{tweet.text}"
#end

# Get one tweet
options = {
  count: 1,
}
tweets = client.home_timeline options
puts tweets.count
tweet = tweets.first
puts tweet.inspect
puts tweet.media
tweet.media.each do |m|
  puts m.inspect
  puts m.attrs
end
puts "full_text: "+tweet.full_text
puts "text: "+tweet.text
puts tweet.uri
puts tweet.created_at
puts tweet.favorite_count
puts tweet.filter_level
puts tweet.in_reply_to_screen_name

# Get all tweets
def collect_with_max_id(count=0, collection=[], max_id=nil, &block)
  if count==2
    return collection.flatten
  end
  response = yield(max_id)
  collection += response
  response.empty? ? collection.flatten : collect_with_max_id( count+=1, collection, response.last.id - 1, &block)
end

def get_all_tweets(client)
  collect_with_max_id do |max_id|
    options = {count: 200, include_rts: true}
    options[:max_id] = max_id unless max_id.nil?
    client.home_timeline
  end
end

tweets = get_all_tweets(client)

puts tweets.count
