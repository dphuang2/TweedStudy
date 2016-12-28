class AddCelebrityAttributeToTweets < ActiveRecord::Migration[5.0]
  def change
    add_column :tweets, :celebrity, :integer
    add_column :tweets, :fake_celebrity, :integer
  end
end
