<?php
	$update_string = '';
	$update = false;
	$TempDate = '';
	$expired = 0;
	$surpassed_by = '';
	if ( isset( $_POST['surpassed_by'] )) {
		$surpassed_by = intval($_POST['surpassed_by']);
	}

	# receiving a post of editing agreement, new or old
	if ( isset( $_POST['admin_post'] )) {
		$TempDate = new MyDate( intval( $_POST['year'] ), 
			intval( $_POST['month'] ), intval( $_POST['day'] ));

		if ( isset( $_POST['expired'] )) {
			$expired = 1;
		}

		$pub = false;
		if ( isset($_POST['world_public']) && $_POST['world_public'] == 'on') {
			$pub = true;
		}

		$Agrms = new Agreement(
			intval( $_POST['num'] ),
			mysql_real_escape_string( $_POST['title'] ), 
			mysql_real_escape_string( $_POST['summary'] ), 
			mysql_real_escape_string( $_POST['full'] ), 
			mysql_real_escape_string( $_POST['background'] ), 
			mysql_real_escape_string( $_POST['comments'] ), 
			mysql_real_escape_string( $_POST['processnotes'] ), 
			intval( $_POST['cid'] ),
			$TempDate, 
			$surpassed_by,
			$expired,
			$pub
		);
		$update = true;
	}
	elseif ( $num > 0 ) {
		# edit agreement, document number
		$Agrms = new Agreement( $num );
	}
	else {
		# first visit
		$Agrms = new Agreement( );
	}
	$Cmty = new Committee( $Agrms->cid );

	if ( isset( $_POST['save'] )) {
		$Agrms->save( $update );
	}
	elseif( isset( $_GET['delete'] )) {
		$Agrms->delete( $confirm_del );
	}
	else {
		if ( $num > 0 ) {
			$update_string = 
				'<input type="hidden" name="update" value="1" />' . "\n";
		}

		echo <<<EOHTML
			<h1>admin agreement entry tool</h1>
			<form action="?id=admin" method="post">
			<input type="hidden" name="doctype" value="agreement" />
			<input type="hidden" name="admin_post" value="1" />
			<input type="hidden" name="num" value="{$num}" />
			{$update_string}
EOHTML;

		echo $Agrms->Date->selectDate( ) .
			$Cmty->selectCommittee( $Agrms->cid );
		$Agrms->actionChoices( );
		$Agrms->display( 'form' );
		echo '<p><input type="submit" name="save" '.
			'value="save changes &rarr;" />' . "</p></form>\n";
	}

?>
