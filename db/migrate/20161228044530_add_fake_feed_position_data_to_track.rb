class AddFakeFeedPositionDataToTrack < ActiveRecord::Migration[5.0]
  def change
    add_column :tracks, :real_feed_position, :string
    add_column :tracks, :fake_feed_position, :string
  end
end
