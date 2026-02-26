<?php

function dtportfolio_generate_skin_colors() {

	$output = '';

	$use_predefined_skin = (int) get_theme_mod( 'use-predefined-skin', elearni_defaults('use-predefined-skin') );
	$primary_color = get_theme_mod('primary-color',elearni_defaults('primary-color'));
	$secondary_color = get_theme_mod('secondary-color',elearni_defaults('secondary-color'));
	$tertiary_color = get_theme_mod('tertiary-color',elearni_defaults('tertiary-color'));
	
	if( empty( $use_predefined_skin ) ) {

		$output .= '.dtportfolio-item .dtportfolio-image-overlay .links a:hover, .dtportfolio-item .dtportfolio-image-overlay a:hover, .dtportfolio-fullpage-carousel .dtportfolio-fullpage-carousel-content a:hover, .dtportfolio-item.dtportfolio-hover-modern-title .dtportfolio-image-overlay .links a:hover, .dtportfolio-swiper-pagination-holder .dtportfolio-swiper-playpause:hover { color:'.$primary_color.'}';	

		$output .= '.dtportfolio-swiper-pagination-holder .swiper-pagination-bullet-active { background:'.$primary_color.'}';	

	}

	return $output;

}

?>