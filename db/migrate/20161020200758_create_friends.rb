class CreateFriends < ActiveRecord::Migration[5.0]
  def change
    create_table :friends do |t|
      t.string :nickname
      t.string :name
      t.string :image_url
      t.string :twitter_url
      t.integer :statuses_count
      t.integer :verified
      t.integer :followers_count
      t.string :location
      t.string :screen_name
      t.string :lang
      t.integer :friends_count
      t.string :description
      t.string :twitter_creation_date
      t.string :time_zone
      t.float :twitter_id
      t.references :users, foreign_key: true

      t.timestamps
    end
  end
end
