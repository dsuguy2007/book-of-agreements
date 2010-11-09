<?php
	$HOST_EMAIL = ''; // add your email here
	define('AUDIT_CONTACT', ''); // add your email
	define('FROM_ADDRESS', ''); // add your email
	define('SITE_NAME', ''); // site name which appears in the basic auth

	$Basic_Auth_Users = array( 
		'user' => '', // enter sha1 password
		'guest' => '' // enter sha1 password
	);
	$Admin_Users = array( 'user' );
	$basic_auth_realm = SITE_NAME;

	$HDUP = array(
		'database'=>'', // database name
		'host'=>'localhost',
		'user'=>'', // database username
		'password'=>'' // database password
	);
	$admin_string = ''; // enter sha1 password
?>
