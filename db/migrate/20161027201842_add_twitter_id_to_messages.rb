class AddTwitterIdToMessages < ActiveRecord::Migration[5.0]
  def change
    add_column :messages, :twitter_id, :float
  end
end
