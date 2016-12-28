Rails.application.routes.draw do
  # Authentication
  get '/auth/:provider/callback', to: 'sessions#create'
  delete '/logout', to: 'sessions#destroy'
  get '/logout', to: 'sessions#destroy'

  # Content
  get '/pick', to: 'sessions#pick'
  get '/feed1', to: 'sessions#feed1'
  get '/feed2', to: 'sessions#feed2'
  get '/reset', to: 'sessions#reset'
  get '/filter/:filter', to: 'sessions#filter'

  root 'static_pages#home'
end
