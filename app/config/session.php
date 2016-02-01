<?php

return array(

	'driver'          => 'file',
	'lifetime'        => 260000,
	'expire_on_close' => false,
	'files'           => storage_path() . '/sessions',
	'connection'      => null,
	'table'           => 'sessions',
	'lottery'         => array(2, 100),
	'cookie'          => 'dhid',
	'path'            => '/',
	'domain'          => '.dryharder.me',
	'secure'          => false,

);
