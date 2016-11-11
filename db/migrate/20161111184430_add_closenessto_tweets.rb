class AddClosenesstoTweets < ActiveRecord::Migration[5.0]
  def change
      add_column :tweets, :closeness, :integer
      add_column :tweets, :fake_closeness, :integer
  end
end
