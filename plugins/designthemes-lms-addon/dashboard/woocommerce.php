<?php

class DtLms_My_Account_Endpoint {

	public static $endpoint = 'lms';

	public function __construct() {

		add_action( 'init', array( $this, 'dtlms_add_endpoints' ) );

		add_filter( 'woocommerce_account_menu_items', array ( $this, 'dtlms_add_endpoint_in_menu' ) );
		add_action( 'woocommerce_account_' . self::$endpoint .  '_endpoint', array ( $this, 'dtlms_endpoint_content' ) );
		
	}


	public function dtlms_add_endpoints() {

		add_rewrite_endpoint( self::$endpoint, EP_ROOT | EP_PAGES );
		flush_rewrite_rules();
		
	}

	public function dtlms_add_endpoint_in_menu( $items ) {

		$new_items = array ();
		$new_items['lms'] = esc_html__( 'LMS', 'dtlms' );

		$modified_items = dtlms_append_to_array( $items, $new_items, 'edit-account' );

		return $modified_items;

	}

	public function dtlms_endpoint_content() {

		$current_user = wp_get_current_user();
		$current_user_id = $current_user->ID;

		if ( in_array( 'administrator', (array) $current_user->roles ) ) {
			$admin_dashboard_page = dtlms_option('general','admin-dashboard-content');
			if($admin_dashboard_page != '') {
				$admin_dashboard_page_post = get_post($admin_dashboard_page); 
				$admin_dashboard_content = $admin_dashboard_page_post->post_content;			
				echo do_shortcode($admin_dashboard_content);
			} else {
				echo dtlms_get_administrator_dashboard($current_user_id);	
			}
		} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {
			$instructor_dashboard_page = dtlms_option('general','instructor-dashboard-content');
			if($instructor_dashboard_page != '') {
				$instructor_dashboard_page_post = get_post($instructor_dashboard_page); 
				$instructor_dashboard_content = $instructor_dashboard_page_post->post_content;				
				echo do_shortcode($instructor_dashboard_content);
			} else {			
				echo dtlms_get_instructor_dashboard($current_user_id);
			}
		} else if ( in_array( 'student', (array) $current_user->roles ) ) {
			$student_dashboard_page = dtlms_option('general','student-dashboard-content');
			if($student_dashboard_page != '') {
				$student_dashboard_page_post = get_post($student_dashboard_page); 
				$student_dashboard_content = $student_dashboard_page_post->post_content;			
				echo do_shortcode($student_dashboard_content);
			} else {				
		   		echo dtlms_get_student_dashboard($current_user_id); 
		   	}
		}

	}

}

new DtLms_My_Account_Endpoint();