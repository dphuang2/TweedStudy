require 'twitter'

client = Twitter::REST::Client.new do |config|
  config.consumer_key        = "VplV7u5kB7QEJXMPQwJbudQAH"
  config.consumer_secret     = "ZmCI2RCZa9xYqaXuCRmxmsAvSQQ1wlG6Y1NR9dmPxDwXXqjoYe"
  config.access_token        = "3197793925-bIGyf4opaxAGEXS5xfMsm17ByXWB1gZtXZ4J91E"
  config.access_token_secret = "to5bBBGBN4df3rwy1RetrAJCYeHiM9QKjl6uiDLNc2UIt"
end

number = 1

friends = client.friends
friends.each do |friend|
    puts friend.name
end
puts friends

=begin
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
=end

=begin
     n# Get all tweets
     ndef collect_with_max_id(collection=[], max_id=nil, &block)
     n  response = yield(max_id)
     n  collection += response
     n  response.empty? ? collection.flatten : collect_with_max_id(collection, response.last.id - 1, &block)
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
     ndef get_all_messages(client)
     n  collect_with_max_id do |max_id|
     n    options = {count: 200}
     n    options[:max_id] = max_id unless max_id.nil?
     n    client.direct_messages(options)
     n  end
     nend
=end

=begin
     nmessages = client.direct_messages
     n
     nputs messages.first.text
=end

=begin
     nmessages = get_all_messages(client)
     nmessages.first.text
=end

=begin
     ntweets = get_all_tweets(client)
     n
     nputs tweets.count
=end
