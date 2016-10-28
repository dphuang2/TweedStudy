class AddFakePosterFrequencyToTweets < ActiveRecord::Migration[5.0]
  def change
    add_column :tweets, :fake_poster_frequency, :float
  end
end
