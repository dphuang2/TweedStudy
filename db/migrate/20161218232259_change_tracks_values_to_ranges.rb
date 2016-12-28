class ChangeTracksValuesToRanges < ActiveRecord::Migration[5.0]
  def change
    rename_column :tracks, :sentiment, :sentiment_low
    add_column :tracks, :sentiment_high, :integer
    rename_column :tracks, :closeness, :closeness_low
    add_column :tracks, :closeness_high, :integer
    rename_column :tracks, :poster_frequency, :poster_frequency_low
    add_column :tracks, :poster_frequency_high, :integer
    rename_column :tracks, :celebrity, :celebrity_low
    add_column :tracks, :celebrity_high, :integer
    rename_column :tracks, :popularity, :popularity_low
    add_column :tracks, :popularity_high, :integer
    rename_column :tracks, :verified, :verified_low
    add_column :tracks, :verified_high, :integer
  end
end
