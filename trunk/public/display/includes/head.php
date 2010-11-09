<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title><?= $title; ?></title>
	<link rel="stylesheet" href="display/styles/default.css" type="text/css" />
<?php
	global $print_version;
	if ( $print_version ) {
		echo "\t" . '<link rel="stylesheet" href="display/styles/print.css" type="text/css" />' . "\n";
	}
	
	if ( file_exists( $js )) {
		echo <<<EOHTML
	<script type="text/javascript" src="/{$js}"></script>
EOHTML;
	}
?>

	<meta name="google-site-verification" content="g6Fg9AWOfsIvEGzT682MCUKNkYNRDVSH1bnmor4VEzU"/>
</head>
