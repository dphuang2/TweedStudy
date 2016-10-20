class RemoveColumnsFromTweets < ActiveRecord::Migration[5.0]
  def change
      remove_column :tweets, :poster_frequency, :integer
      remove_column :tweets, :fake_poster_frequency, :integer
  end
end
