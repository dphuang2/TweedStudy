class StaticPagesController < ApplicationController
  def home
    if logged_in?
      redirect_to '/feed'
    end
  end
end
