<?php 
add_action( 'vc_before_init', 'dtlms_certificate_details_vc_map' );

function dtlms_certificate_details_vc_map() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );

	vc_map( array(
		"name" => esc_html__( 'Certificate Details', 'dtlms' ),
		"base" => "dtlms_certificate_details",
		"icon" => "dtlms_certificate_details",
		"category" => DTLMSADDON_TITLE,
		"params" => array(

			// Item Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Item Type','dtlms'),
				'param_name' => 'item_type',
				'value' => array( 
							esc_html__('Student Name','dtlms') => 'student_name',
							sprintf( esc_html__( 'Item Name ( %1$s or Course )', 'dtlms' ), $class_singular_label ) => 'item_name',
							esc_html__('Student Percent','dtlms') => 'student_percent',
							esc_html__('Date','dtlms') => 'date'
						),
				'std' => 'student_name'
			),	

		)
	) );

}
?>