<?php

$anchorsData = array();

//var_dump($anchorCloudStatData);

if( $anchorCloudStatData && count( $anchorCloudStatData ) ):

	foreach($anchorCloudStatData as $anchorStats){
		$anchorsData[] = $anchorStats['anchor'] . ' ['.$anchorStats['count'].']('.round( $anchorStats['percent'] ).'%)';
	}
	
	echo implode( ', <br />', $anchorsData );
	
endif;
?>
<h2></h2>
<div>
</div>