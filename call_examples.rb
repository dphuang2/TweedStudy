require 'twitter'

client = Twitter::REST::Client.new do |config|
  config.consumer_key        = "XDMrnx4b7Gdu6fMepQxGC4tfS"
  config.consumer_secret     = "ZDXy8Bs63UJqqn6E30gRmeZZrNGoPXSNXN9U8xdKUn5lpHHkFy"
  config.access_token        = "3197793925-ut8lvK9T86EiEMzqgyNmvqGaDwgYnasYTRCLE1T"
  config.access_token_secret = "wsfbhpI4Nh9FnX4HWUJqF2IrdOGuJawRCXvFgGOAk7WyE"
end
number = 1
friends = client.friends
friends.each do |friend|
    puts friend.name
end
=begin
     n
     n#client.search("to:justinbieber marry me", result_type: "recent").take(3).collect do |tweet|
     n  #puts "#{tweet.user.screen_name}: #{tweet.text}"
     n#end
     n
     n# Get one tweet
     noptions = {
     n  count: 1,
     n}
     ntweets = client.home_timeline options
     nputs tweets.count
     ntweet = tweets.first
     nputs tweet.inspect
     nputs tweet.media
     ntweet.media.each do |m|
     n  puts m.inspect
     n  puts m.attrs
     nend
     nputs "full_text: "+tweet.full_text
     nputs "text: "+tweet.text
     nputs tweet.uri
     nputs tweet.created_at
     nputs tweet.favorite_count
     nputs tweet.filter_level
     nputs tweet.in_reply_to_screen_name
     n
     n# Get all tweets
     ndef collect_with_max_id(count=0, collection=[], max_id=nil, &block)
     n  if count==2
     n    return collection.flatten
     n  end
     n  response = yield(max_id)
     n  collection += response
     n  response.empty? ? collection.flatten : collect_with_max_id( count+=1, collection, response.last.id - 1, &block)
     nend
     n
     ndef get_all_tweets(client)
     n  collect_with_max_id do |max_id|
     n    options = {count: 200, include_rts: true}
     n    options[:max_id] = max_id unless max_id.nil?
     n    client.home_timeline
     n  end
     nend
     n
     ntweets = get_all_tweets(client)
     n
     nputs tweets.count
=end
