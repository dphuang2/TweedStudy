module Compute
  @@happyWords = File.readlines("app/assets/words/happyWords.txt")
  @@happyWords = @@happyWords.map{ |word| word.delete "\n"}
  @@sadWords = File.readlines("app/assets/words/sadWords.txt")
  @@sadWords = @@sadWords.map{ |word| word.delete "\n"}
  @@words = Hash.new
  @@sadWords.each do |sadWord|
    @@words[sadWord] = -1
  end
  @@happyWords.each do |happyWord|
    @@words[happyWord] = 1
  end

  # For fake calculating
  @@sentimentArray = []
  @@popularityArray = []
  @@frequencyArray = []
  @@closenessArray = []
  @@verifiedArray = []

  def shuffle_frequency
    @@frequencyArray = @@frequencyArray.shuffle
  end
  def shuffle_verified
    @@verifiedArray = @@verifiedArray.shuffle
  end
  def shuffle_popularity
    @@popularityArray = @@popularityArray.shuffle
  end
  def shuffle_closeness
  end
  def shuffle_sentiment
    @@sentimentArray = @@sentimentArray.shuffle
  end

  def grab_fake_verified
    return @@verifiedArray.pop
  end
  def grab_fake_frequency
    return @@frequencyArray.pop
  end

  def grab_fake_popularity
    return @@popularityArray.pop
  end

  def grab_fake_sentiment
    return @@sentimentArray.pop
  end

  def compute_verified(friend)
    @@verifiedArray.push(friend.verified)
    return friend.verified
  end

  def compute_frequency(time, count)
    now = Time.now
    frequency = count/(now-time)
    @@frequencyArray.push(frequency)
    return frequency
  end

  def compute_closeness(friend)
    rank = 0 # Start rank at 0
    id = User.find_by(twitter_id: session[:twitter_id]).id
    messages = Message.where(user_id: id, sender_id: friend.id) # Collect messages

    # Missing:
    # newer friends are more distant
    # recent direct message

    #rank += (index <= (size/2)) ? -1 : 1 # If you are inserted into database more recently, then you are a newer friend so you are more distant

    case # If you have more followers, you are more distant
    when friend.followers_count > 100000
      rank -= 3
    when friend.followers_count > 10000
      rank -= 2
    when friend.followers_count > 1000
      rank -= 1
    else rank += 1
    end

    if messages.any?
      word_count = messages.inject(0) {|sum, hash| sum + hash[:word_count]} # sum of word count from this friend
      case # If you have exchanged a lot of DMS, you are closer
      when word_count > 1000
        rank += 10
      when word_count > 100
        rank += 5
      when word_count > 10
        rank += 2
      when word_count > 0
        rank += 1
      end

      avg_sentiment = messages.inject(0) {|sum, hash| sum + hash[:sentiment]}
      avg_sentiment /= messages.size
      case # If you have a positive average sentiment, you are closer and vica versa
      when avg_sentiment > 0
        rank += 1
      when avg_sentiment < 0
        rank -= 1
      end
    end

    rank += friend.verified ? -1 : 1 # Verified means more distant

  end

  def compute_popularity(retweet_count)
    @@popularityArray.push(retweet_count)
    return retweet_count
  end

  def compute_sentiment(text)
    # Format text into array
    text = text.gsub(/[^a-z0-9\s]/i, '').downcase
    tweet_array = text.split(" ")

    # Initialize sentiment to 0
    sentiment = 0

    tweet_array.each do |word|
      sentiment += @@words[word] if @@words.has_key?(word) 
    end

    @@sentimentArray.push(sentiment)

    return sentiment
  end
end
