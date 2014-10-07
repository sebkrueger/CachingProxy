# == Class: apache_vhosts
#
# Adds and enables virtual hosts. Sets up /var/www symlinks.
#
class apache_vhosts {

  file { '/var/www':
    ensure => directory,
    owner => 'vagrant',
    group => 'vagrant',
    mode => '0777';
  }

  apache_vhosts::vhost { ['cachingproxy']: }
}