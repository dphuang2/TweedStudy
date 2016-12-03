class ChangeTwitterIdToDouble < ActiveRecord::Migration[5.0]
  def change
    change_column :users, :twitter_id, :float
  end
end
