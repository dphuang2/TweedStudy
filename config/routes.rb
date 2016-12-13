Rails.application.routes.draw do
  # Authentication
  get '/auth/:provider/callback', to: 'sessions#create'
  delete '/logout', to: 'sessions#destroy'
  get '/logout', to: 'sessions#destroy'

  # Content
  get '/pick', to: 'sessions#pick'
  get '/feed', to: 'sessions#feed'
  get '/reset', to: 'sessions#reset'
  get '/filter/:filter', to: 'sessions#filter'

  root 'static_pages#home'
end
