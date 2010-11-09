<?php
	if ( !isset( $_GET['num'] )) {
		$max = 100;
		$show = '';
		$show_exp = false;
		if ( !$PUBLIC_USER && isset( $_GET['show'] )) {
			if ( $_GET['show'] == 'expired' ) {
				$show = 'expired';
			}
			elseif ( $_GET['show'] == 'surpassed' ) {
				$show = 'surpassed';
			}
		}
		require_once( 'logic/pagevars/all_agreements_v.php' );
	}
	else
	{
		$num = intval( $_GET['num'] );
		$Date = new MyDate( ); 
		$Agrms = new Agreement( $num );
		$Cmty = new Committee( );
		$title .= ": {$Agrms->title} [Agreement]";

		$body = 'logic/agreement.php';
	}
?>
