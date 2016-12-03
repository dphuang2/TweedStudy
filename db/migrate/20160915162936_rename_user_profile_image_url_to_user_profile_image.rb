class RenameUserProfileImageUrlToUserProfileImage < ActiveRecord::Migration[5.0]
  def change
    rename_column :tweets, :user_profile_img_url, :user_profile_img
  end
end
