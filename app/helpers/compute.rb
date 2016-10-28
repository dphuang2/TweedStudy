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

  def grab_fake_popularity
    @@popularityArray = @@popularityArray.shuffle
    return @@popularityArray.pop
  end

  def grab_fake_sentiment
    @@sentimentArray = @@sentimentArray.shuffle
    return @@sentimentArray.pop
  end

  def compute_frequency(time, count)
    now = Time.now
    frequency = (now-time)/count
    @@frequencyArray.push(frequency)
    return frequency
  end

  def compute_closeness(friend)
    rank = 0 # Start rank at 0
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
