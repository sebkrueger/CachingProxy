# create a new run stage to ensure apt-get stuff it the first we run
stage { 'bootstrap':
    before => Stage['pre']
}

# create a new run stage to ensure certain modules are included first
stage { 'pre':
    before => Stage['main'];
}

stage { 'last': }
# Run Stage last after Stage main
Stage['main'] -> Stage['last']

class { 'aptget':
    stage => 'bootstrap';
}

# add the baseconfig module to the new 'pre' run stage
class { 'baseconfig':
    stage => 'pre';
}

# In Mainstage run all Modules not defined in other stages

# add some modules that need lamp stack
class { 'mountstartup':
    stage => 'last';
}

# set defaults for file ownership/permissions
File {
    owner => 'root',
    group => 'root',
    mode => '0644',
}

include aptget, baseconfig, apache, apache_vhosts, php, composer, mountstartup