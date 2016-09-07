class AddColumnsToUser < ActiveRecord::Migration[5.0]
  def change
    rename_column :users, :user_id, :nickname
    add_column :users, :name, :string
    add_column :users, :image_url, :string
    add_column :users, :twitter_url, :string
    add_column :users, :statuses_count, :integer
    add_column :users, :verified, :integer
    add_column :users, :followers_count, :integer
    add_column :users, :location, :string
    add_column :users, :screen_name, :string
    add_column :users, :lang, :string
    add_column :users, :friends_count, :integer
    add_column :users, :description, :string
    add_column :users, :twitter_creation_date, :string
    add_column :users, :time_zone, :string
    add_column :users, :twitter_id, :integer
  end
end
