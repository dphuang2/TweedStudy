Rails.application.routes.draw do
  # Authentication
  get '/auth/:provider/callback', to: 'sessions#create'
  delete '/logout', to: 'sessions#destroy'
  get '/logout', to: 'sessions#destroy'

  # Content
  get '/feed', to: 'sessions#feed'
  get '/filter/:filter', to: 'sessions#filter'

  root 'static_pages#home'
end
