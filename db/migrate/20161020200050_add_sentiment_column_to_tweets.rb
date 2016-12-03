class AddSentimentColumnToTweets < ActiveRecord::Migration[5.0]
  def change
    add_column :tweets, :sentiment, :integer
    add_column :tweets, :fake_sentiment, :integer
  end
end
