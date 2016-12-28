class AddVerifiedToTracks < ActiveRecord::Migration[5.0]
  def change
    add_column :tracks, :verified, :integer
  end
end
