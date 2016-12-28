class RenameTracksPosterFrequencyColumn < ActiveRecord::Migration[5.0]
  def change
    rename_column :tracks, :poster_ster_requency, :poster_frequency
  end
end
