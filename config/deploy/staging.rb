# role-based syntax
# ==================

# Defines a role with one or multiple servers. The primary server in each
# group is considered to be the first unless any  hosts have the primary
# property set. Specify the username and a domain or IP for the server.
# Don't use `:all`, it's a meta role.

role :dev, %w{deployer@dev.eu.clevvermail.com}
server 'dev.eu.clevvermail.com', user: 'deployer', roles: %w{dev}

set :branch, "develop"

set :ssh_options, {
    forward_agent: false,
    auth_methods: %w(publickey),
#    password: 'B8M7qIrXzAhkGSaYbx3m',
    user: 'deployer',
}