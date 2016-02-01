<?php

return array(

    'driver'     => 'smtp',
    'host'       => 'smtp.gmail.com',
    'port'       => 587,
    'from'       => array('address' => null, 'name' => null),
    'encryption' => 'tls',
    'username'   => 'info@dryharder.me',
    'password'   => '',
    'sendmail'   => '/usr/sbin/sendmail -bs',
    'pretend'    => false,
    'infobox'    => 'info@dryharder.me',

);
