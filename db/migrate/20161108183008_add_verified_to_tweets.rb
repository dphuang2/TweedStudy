class AddVerifiedToTweets < ActiveRecord::Migration[5.0]
  def change
      add_column :tweets, :verified, :integer
  end
end
