class AddCelebrityAttributeToFriends < ActiveRecord::Migration[5.0]
  def change
    add_column :friends, :celebrity, :integer
    add_column :friends, :fake_celebrity, :integer
  end
end
