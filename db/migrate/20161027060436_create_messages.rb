class CreateMessages < ActiveRecord::Migration[5.0]
  def change
    create_table :messages do |t|
      t.text :text
      t.references :user, foreign_key: true
      t.integer :sender_id
      t.string :sender_name
      t.string :sent_date
      t.integer :sentiment
      t.integer :word_count
      t.timestamps
    end
  end
end
