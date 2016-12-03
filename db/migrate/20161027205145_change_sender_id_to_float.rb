class ChangeSenderIdToFloat < ActiveRecord::Migration[5.0]
  def change
    change_column :messages, :sender_id, :float
  end
end
