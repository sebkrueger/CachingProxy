# == Class: apache
#
# Installs packages for Apache2, enables modules, and sets config files.
#
class apache {

  package { ['apache2']:
    ensure => present;
  }

  service { 'apache2':
    ensure => running,
    enable => true,    # Start always
    require => Package['apache2'],
  }

  apache::conf { ['apache2.conf']: }
  apache::enmode { ['expires.load', 'rewrite.load', 'autoindex.load', 'autoindex.conf']: }
  apache::dismode { ['userdir.load', 'userdir.conf',
                     'negotiation.load', 'negotiation.conf',
                     'setenvif.load', 'setenvif.conf',
                     'env.load']: }

}