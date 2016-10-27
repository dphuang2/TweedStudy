class User < ApplicationRecord
  has_many :tweet
  has_many :friend
  
  def self.find_or_create_from_auth_hash(auth)
    where(:twitter_id => auth[:uid]).first || create_from_omniauth(auth)
  end
  def self.create_from_omniauth(auth)
    create! do |user|
      user.nickname = auth[:info][:nickname]
      user.name = auth[:info][:name]
      user.image_url = auth[:info][:image]
      user.twitter_url = auth[:info][:urls][:Twitter]
      user.statuses_count = auth[:extra][:raw_info][:statuses_count]
      user.verified = auth[:verified]
      user.followers_count = auth[:extra][:raw_info][:followers_count]
      user.location = auth[:extra][:raw_info][:location]
      user.screen_name = auth[:extra][:raw_info][:screen_name]
      user.lang = auth[:extra][:raw_info][:lang]
      user.friends_count = auth[:extra][:raw_info][:friends_count]
      user.description = auth[:extra][:raw_info][:description]
      user.twitter_creation_date = auth[:extra][:raw_info][:created_at]
      user.time_zone = auth[:extra][:raw_info][:time_zone]
      user.twitter_id = auth[:uid]
    end
  end
end
