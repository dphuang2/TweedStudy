class CreateTracks < ActiveRecord::Migration[5.0]
  def change
    create_table :tracks do |t|
      t.string :twitter_id
      t.string :screen_name
      t.integer :sentiment
      t.integer :closeness
      t.integer :poster_ster_requency
      t.integer :celebrity
      t.integer :popularity

      t.timestamps
    end
  end
end
