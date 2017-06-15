
# config valid only for current version of Capistrano
lock '3.4.1'

set :application, 'clevvermail_webapp'
set :repo_url, 'git@github.com:ClevverMail/webapp.git'
set :scm, :git

set :pty, true

set :format, :pretty

set :deploy_to, '/var/www/clevvermail_webapp'

# Default value for keep_releases is 5
set :keep_releases, 5

before :deploy, "deploy:set_cache_permissions"

namespace :deploy do
  task :link_upload_directories do     
  	on roles :all do
  	    # execute "mkdir -p #{release_path}/uploads"
  	    # execute "cp -vpr #{release_path}/uploads #{shared_path}/uploads"
  	    # execute "rm -rf #{release_path}/uploads"
  	    execute "ln -s #{shared_path}/uploads #{release_path}/uploads"
  	end
  end  
  task :set_cache_permissions do
    on roles :all do
      execute :sudo, "chmod -R g+w #{releases_path}/*/system/virtualpost/cache/"
  	end
  end
  task :remove_static_dir do
    on roles :all do
    	execute "rm -rf #{release_path}/database/"
      execute "rm -rf #{release_path}/system/virtualpost/logs"
  	end
  end
  task :link_shared_directories do     
  	on roles :all do
  		execute "ln -s #{shared_path}/data #{release_path}/data"
      execute "ln -s #{shared_path}/logs #{release_path}/system/virtualpost/logs"
  	end
  end    

  after :deploy, "deploy:remove_static_dir"
  after :deploy, :link_shared_directories
  after :deploy, "deploy:link_upload_directories"
end
