class AddStaticFeedToUsers < ActiveRecord::Migration[5.0]
  def change
    add_column :users, :real_feed_position, :string
    add_column :users, :fake_feed_position, :string
  end
end
