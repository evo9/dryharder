<?php

return array(

    'name'       => 'DryHarder',

    'driver'     => 'smtp',
    'host'       => 'smtp.mailgun.org',
    'port'       => 587,
    'from'       => array('address' => null, 'name' => null),
    'encryption' => 'tls',
    'username'   => null,
    'password'   => null,
    'sendmail'   => '/usr/sbin/sendmail -bs',
    'pretend'    => false,
    'self'       => 'victor@dryharder.me',

);
