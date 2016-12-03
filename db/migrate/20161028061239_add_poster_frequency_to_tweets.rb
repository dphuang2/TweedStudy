class AddPosterFrequencyToTweets < ActiveRecord::Migration[5.0]
  def change
    add_column :tweets, :poster_frequency, :float
  end
end
