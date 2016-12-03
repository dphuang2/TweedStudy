class AddMoreInfoToTweets < ActiveRecord::Migration[5.0]
  def change
    # aliased name to database
    add_column :tweets, :retweet_user_name, :string
    add_column :tweets, :user_name, :string
    # User URL to database
    add_column :tweets, :user_url, :string
  end
end
