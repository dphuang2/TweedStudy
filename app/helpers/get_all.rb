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
        options = {count: 100, include_rts: true}
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
            client.home_timeline(options)
            #client.user_timeline(user, options)
        end
    end

    # When authorized from Twitter, grab followed users
    def twitter_followed_users(client)
      @twitter_followed_users ||= begin
        # Returns pages of 5k
        friend_ids = begin
          client.friend_ids.to_a
        rescue Twitter::Error::TooManyRequests => e
          sleepy_time = e.rate_limit.reset_in + 1
          Rails.logger.debug("friend_ids - sleeping #{sleepy_time}")
          retry
        end

        # Returns pages of 100 - better than 20 at a time with friends/list
        following = begin
          client.users(friend_ids).to_a
        rescue Twitter::Error::TooManyRequests => e
          sleepy_time = e.rate_limit.reset_in + 1
          Rails.logger.debug("following - sleeping #{sleepy_time}")
          sleep sleepy_time
          retry
        end

        following
      end
    end

end
