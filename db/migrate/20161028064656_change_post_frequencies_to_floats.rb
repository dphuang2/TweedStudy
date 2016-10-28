class ChangePostFrequenciesToFloats < ActiveRecord::Migration[5.0]
  def change
    change_column :friends, :fake_post_frequency, :float
    change_column :friends, :post_frequency, :float
  end
end
