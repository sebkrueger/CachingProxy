# == Class: baseconfig
#
# Performs initial configuration tasks for all Vagrant boxes.
#
class baseconfig {

  package { ['less', 'curl', 'sysv-rc-conf',
             'virtualbox-guest-utils', 'virtualbox-guest-dkms', 'virtualbox-guest-x11', 'virtualbox-ose-guest-x11']:
    ensure => latest;
  }

  file {
    '/root/.bashrc':
    owner  => 'root',
    group  => 'root',
    mode   => '0644',
    source => 'puppet:///modules/baseconfig/bashrc';
  }

}