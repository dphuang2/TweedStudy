class AddFrequencyAndClosenessToFriends < ActiveRecord::Migration[5.0]
  def change
    add_column :friends, :post_frequency, :int
    add_column :friends, :fake_post_frequency, :int
    add_column :friends, :closeness, :int
    add_column :friends, :fake_closeness, :int
  end
end
