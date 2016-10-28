module GetAll
  def collect_with_max_id(collection=[], max_id=nil, &block)
    response = yield(max_id)
    collection += response
    response.empty? ? collection.flatten : collect_with_max_id(collection, response.last.id - 1, &block)
  end

  # Functions to cursor through hometimeline
  def collect_with_cursor(collection=[], cursor=nil, &block)
    response = yield(cursor)
    next_cursor = response.next_cursor
    response = response.to_a
    collection += response
    response.empty? ? collection.flatten : collect_with_cursor(collection, next_cursor, &block)
  end

  def get_all_friends(client)
    collect_with_cursor do |cursor|
      options = {count: 10}
      options[:cursor] = cursor unless cursor.nil?
      client.friends
    end
  end

  def get_few_tweets(client)
    options = {count: 20, include_rts: true}
    client.home_timeline(options)
  end

  def get_all_messages(client)
    collect_with_max_id do |max_id|
      options = {count: 200}
      options[:max_id] = max_id unless max_id.nil?
      client.direct_messages(options)
    end
  end

  def get_all_tweets(user, client)
    collect_with_max_id do |max_id|
      options = {count: 200, include_rts: true}
      options[:max_id] = max_id unless max_id.nil?
      #client.home_timeline(options)
      client.user_timeline(user, options)
    end
  end

end
