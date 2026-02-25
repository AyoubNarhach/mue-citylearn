<?php

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'DTLmsDefault' ) ) {

	class DTLmsDefault {

		function __construct() {

			add_filter( 'body_class', array( $this, 'dtlms_default_body_class' ), 20 );

			add_action( 'wp_enqueue_scripts', array( $this, 'dtlms_default_enqueue_styles' ), 104 );

			add_action( 'dtlms_before_main_content', array( $this, 'dtlms_default_before_main_content' ), 10 );
			add_action( 'dtlms_after_main_content', array( $this, 'dtlms_default_after_main_content' ), 10 );

			add_action( 'dtlms_before_content', array( $this, 'dtlms_default_before_content' ), 10 );
			add_action( 'dtlms_after_content', array( $this, 'dtlms_default_after_content' ), 10 );

		}
	
		function dtlms_default_body_class( $classes ) {

			return $classes;

		}

		function dtlms_default_enqueue_styles() {

			wp_enqueue_style ( 'dtlms-default', plugins_url ('designthemes-lms-addon') . '/css/themes/default.css' );

		} 

		function dtlms_default_before_main_content() {	

			echo '';

		}

		function dtlms_default_after_main_content() {

			echo '';

		}

		function dtlms_default_before_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {
			} else {
				global $post;
				echo '<article id="post-'.$post->ID.'" class="'.implode(' ', get_post_class()).'">';				
			}

		}

		function dtlms_default_after_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {
			} else {
				echo '</article>';				
			}
			
		}

	}

	new DTLmsDefault();

}

?>