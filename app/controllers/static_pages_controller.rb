class StaticPagesController < ApplicationController
  def home
    if logged_in?
      redirect_to '/pick'
    end
  end
end
