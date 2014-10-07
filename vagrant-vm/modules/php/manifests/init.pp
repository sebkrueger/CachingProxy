# == Class: php
#
# Installs PHP5 and necessary modules. Sets config files.
#
class php {

  package { ['php5-common',
             'php5-cli',
             'libapache2-mod-php5',
             'php-apc',
             'php5-intl',
             'php5-xdebug'
    ]:
    ensure => latest;
  }

  file {
    '/etc/php5/apache2':
      ensure => directory,
      before => File ['/etc/php5/apache2/php.ini'];

    '/etc/php5/apache2/php.ini':
       source => 'puppet:///modules/php/apache2-php.ini',
       require => Package['php5-common'];

    '/etc/php5/cli':
       ensure => directory,
       before => File ['/etc/php5/cli/php.ini'];

    '/etc/php5/cli/php.ini':
      source => 'puppet:///modules/php/cli-php.ini',
      require => Package['php5-cli'];

    '/etc/php5/mods-available':
      ensure => directory,
      before => File ['/etc/php5/mods-available/apc.ini'];

    '/etc/php5/mods-available/apc.ini':
      source => 'puppet:///modules/php/apc.ini',
      require => Package['php-apc'];
  }
}