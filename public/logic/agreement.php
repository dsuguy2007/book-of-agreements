<?php
	if ( $PUBLIC_USER ) {
		echo <<<EOHTML
			<img src="/display/images/tango/32x32/actions/go-previous.png" class="tango" alt="previous" />
			<a href="?id=agreement">Back to listing</a>
EOHTML;
	}
	$Agrms->display( 'document' );
?>
