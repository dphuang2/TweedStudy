class Tweet < ApplicationRecord
  belongs_to :user
  validates :tweet_id, uniqueness: true # To make sure no more than one copy of tweet is saved to database

  def retweet?
    !self.retweet_user_screen_name.nil?
  end

end
