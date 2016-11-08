class AddFakeVerified < ActiveRecord::Migration[5.0]
  def change
      add_column :tweets, :fake_verified, :integer
  end
end
