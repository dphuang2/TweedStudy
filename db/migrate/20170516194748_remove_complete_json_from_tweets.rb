class RemoveCompleteJsonFromTweets < ActiveRecord::Migration[5.0]
  def change
    remove_column :tweets, :complete_json, :string
  end
end
