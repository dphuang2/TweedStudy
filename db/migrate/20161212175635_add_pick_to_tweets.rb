class AddPickToTweets < ActiveRecord::Migration[5.0]
  def change
      add_column :tweets, :pick, :string
  end
end
