# == Define: enmode
#
# Enables an Apache module.
#
define apache::enmode() {
  file { "/etc/apache2/mods-enabled/${name}":
    ensure => link,
    target => "/etc/apache2/mods-available/${name}",
    require => Package['apache2'],
    notify => Service['apache2'];
  }
}