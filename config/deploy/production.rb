# role-based syntax
# ==================

# Defines a role with one or multiple servers. The primary server in each
# group is considered to be the first unless any  hosts have the primary
# property set. Specify the username and a domain or IP for the server.
# Don't use `:all`, it's a meta role.


role :node2, %w{deployer@node2.eu.clevvermail.com}
server 'node2.eu.clevvermail.com', user: 'deployer', roles: %w{node2}

role :node1, %w{deployer@node1.eu.clevvermail.com}
server 'node1.eu.clevvermail.com', user: 'deployer', roles: %w{node1}


set :ssh_options, {
    forward_agent: false,
    auth_methods: %w(publickey),
#    password: 'B8M7qIrXzAhkGSaYbx3m',
    user: 'deployer',
}
