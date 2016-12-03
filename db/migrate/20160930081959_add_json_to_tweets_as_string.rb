class AddJsonToTweetsAsString < ActiveRecord::Migration[5.0]
  def change
    add_column :tweets, :complete_json, :string
  end
end
