<?php
	$MainNav['home'] = 'Home';

	$sql = 'select cid, cmty from committees where parent=cid order by cid';
	$CInfo = my_getInfo( $G_DEBUG, $HDUP, $sql );
	$Cmtys = array( );
	foreach( $CInfo as $i=>$Info )
	{ $Cmtys[$Info['cid']] = $Info['cmty']; }

	$sql = 'select * from committees where cid!=parent order by cid';
	$SubInfo = my_getInfo( $G_DEBUG, $HDUP, $sql );
	$SubCmtys = array( );
	foreach( $SubInfo as $i=>$Info )
	{ $SubCmtys[$Info['parent']][$Info['cid']] = $Info['cmty']; }
?>
