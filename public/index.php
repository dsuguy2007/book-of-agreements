<?php
	session_start();

	// if the file exists, then use it to pull in the users list. Otherwise,
	// ignore it so it can be created.
	if (file_exists('config.php')) {
		require_once('config.php');
	}

	$authenticated = false;
	if ( isset( $_SESSION['logged_in'] ) && $_SESSION['logged_in'] == 1 ) {
		$authenticated = true;

		if ( isset( $_GET['login'] ) && $_GET['login'] == 0 ) {
			$authenticated = false;
			unset($_SERVER['PHP_AUTH_USER']); 
			$_SESSION['logged_in'] = 0;
		}
	}
	elseif ( isset( $_GET['login'] ) && $_GET['login'] == 1 ) {
		$authenticated = attempt_login();	
	}

	function attempt_login()
	{
		global $Basic_Auth_Users;
		global $PUBLIC_USER;

		if ( !isset($_SERVER['PHP_AUTH_USER'] )) {
			open_login_window();
			exit;
		}
		
		// check the password
		if ($Basic_Auth_Users[$_SERVER['PHP_AUTH_USER']] == 
				sha1( $_SERVER['PHP_AUTH_PW'] )) {
			$_SESSION['logged_in'] = 1;
			$PUBLIC_USER = FALSE;
			return TRUE;
		}
		else {
			open_login_window();
			exit;
		}
	}

	function open_login_window()
	{
		global $basic_auth_realm;
		header( "WWW-Authenticate: Basic realm=\"$basic_auth_realm\"" );
		header( 'HTTP/1.0 401 Unauthorized' );
		echo 'You hit the Cancel button, please try again, or ' .
			'<a href="?">go back</a>';
	}

	require_once( 'main.php' );
?>