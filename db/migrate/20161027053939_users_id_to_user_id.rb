class UsersIdToUserId < ActiveRecord::Migration[5.0]
  def change
      remove_index :friends, :users_id
      rename_column :friends, :users_id, :user_id
      add_index :friends, :user_id
  end
end
