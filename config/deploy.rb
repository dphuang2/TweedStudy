# config valid only for current version of Capistrano
lock '3.7.0'

set :application, 'TweedStudy'
set :repo_url, 'https://github.com/dphuang2/TweedStudy.git'
set :passenger_restart_with_touch, true

set :deploy_to, '/home/deploy/TweedStudy'

set :linked_files, %w{config/database.yml config/secrets.yml}
set :linked_dirs, %w{log tmp/pids tmp/cache tmp/sockets vendor/bundle public/system}
namespace :deploy do

  desc 'Restart application'
  task :restart do
    on roles(:app), in: :sequence, wait: 5 do
      # Your restart mechanism here, for example:
      # execute :touch, release_path.join('tmp/restart.txt')
    end
  end

  after :publishing, :restart

  after :restart, :clear_cache do
    on roles(:web), in: :groups, limit: 3, wait: 10 do
       #within release_path do
         #execute :crontab, "-r"
         #execute :whenever, "-w"
       #end
    end
  end

end

