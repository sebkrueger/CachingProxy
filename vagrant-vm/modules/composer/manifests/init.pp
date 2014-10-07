# == Class: composer
#
# Adds composer support
#
class composer {

  exec { 'composer_install':
    command => 'curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer',
    path => '/usr/bin:/usr/sbin',
    unless => "test -f /usr/local/bin/composer",
    require => Package['curl', 'php5-cli'],
  }

}