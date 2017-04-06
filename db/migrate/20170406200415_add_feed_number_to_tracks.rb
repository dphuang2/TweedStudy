class AddFeedNumberToTracks < ActiveRecord::Migration[5.0]
  def change
    add_column :tracks, :feed, :integer
  end
end
