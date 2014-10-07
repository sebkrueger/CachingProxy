# == Class: aptget
#
# Performs only apt get update
#
class aptget {

  exec { 'apt-get update':
    command => '/usr/bin/apt-get update',
  }

}