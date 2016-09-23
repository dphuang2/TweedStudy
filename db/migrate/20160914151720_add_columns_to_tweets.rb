class AddColumnsToTweets < ActiveRecord::Migration[5.0]
  def change
    # Retweet user information (User that tweet was retweeted from)
    add_column :tweets, :retweet_user_screen_name, :string
    add_column :tweets, :retweet_user_profile_img, :string
    add_column :tweets, :retweet_user_url, :string
    # User information (User who is posting)
    add_column :tweets, :user_profile_img_url, :string
    add_column :tweets, :user_screen_name, :string
    # Entities
    add_column :tweets, :media_url, :string
    add_column :tweets, :hashtags, :string
    # Metadata
    add_column :tweets, :retweet_count, :integer
    add_column :tweets, :favorite_count, :integer
    add_column :tweets, :tweet_id, :float
    add_column :tweets, :tweet_created_at, :string

    # Calculated data
    add_column :tweets, :popularity, :integer
    add_column :tweets, :poster_frequency, :integer
    # Fake data
    add_column :tweets, :fake_popularity, :integer
    add_column :tweets, :fake_poster_frequency, :integer

    # Rename string column to text column
    rename_column :tweets, :string, :text
  end
end
