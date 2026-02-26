<?php 
add_action( 'vc_before_init', 'dtlms_package_details_vc_map' );

function dtlms_package_details_vc_map() {

	vc_map( array(
		"name" => esc_html__( 'Package Details', 'dtlms' ),
		"base" => "dtlms_package_details",
		"icon" => "dtlms_package_details",
		"category" => DTLMSADDON_DASHBOARD_TITLE,
		'description' => esc_html__('To list overall details of packages.', 'dtlms'),	
	) );

}
?>