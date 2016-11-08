class AddFakeVerifiedToFriends < ActiveRecord::Migration[5.0]
  def change
      add_column :friends, :fake_verified, :integer
  end
end
