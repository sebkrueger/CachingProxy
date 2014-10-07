# == Define: dismode
#
# Disables an Apache module.
#
define apache::dismode() {
  file { "/etc/apache2/mods-enabled/${name}":
    ensure => absent,
    require => Package['apache2'],
    notify => Service['apache2'];
  }
}