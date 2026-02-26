<?php

function dtlms_add_buddypress_tabs() {

	if(bp_is_my_profile()) {

		global $bp;
		
		bp_core_new_nav_item( array(
			'name'                  => 'LMS',
			'slug'                  => 'lms',
			'parent_url'            => $bp->displayed_user->domain,
			'parent_slug'           => $bp->profile->slug,
			'screen_function'       => 'dtlms_screen',			
			'position'              => 200,
			'default_subnav_slug'   => 'lms'
		) );

	}

}
add_action( 'bp_setup_nav', 'dtlms_add_buddypress_tabs', 100 );

function dtlms_screen() {
    add_action( 'bp_template_content', 'dtlms_screen_content' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function dtlms_screen_content() { 

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

?>