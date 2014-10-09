# == Class: mountstartup
#
#  Because one of the last things vagrant do, is to mount the shared folders, some demons like
#  apache2 and mysql won't able to start in bootprocess. As a workaround, this script here
#  listen to the udev event for foldermount, and start the demons
#
class mountstartup {

  package { 'screen':
    ensure => installed;
  }

  file {
    '/etc/udev/rules.d/50-vagrant-mount.rules':
      require => Package['screen'],
      owner  => 'root',
      group  => 'root',
      mode   => '0644',
      source => 'puppet:///modules/mountstartup/50-vagrant-mount.rules';
  }

}