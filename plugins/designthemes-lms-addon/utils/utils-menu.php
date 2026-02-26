<?php

// Register Instructor and Student Menus
$menu_locations = array ();
if(dtlms_option('general', 'enable-instructor-menu') == 'true') {
	$menu_locations['instructor-menu'] = esc_html__('Instructor Menu', 'dtlms');
}
if(dtlms_option('general', 'enable-student-menu') == 'true') {
	$menu_locations['student-menu'] = esc_html__('Student Menu', 'dtlms');
}

if(!empty($menu_locations)) {
	register_nav_menus( $menu_locations );
}

// Replace default menu location with instructor and student menu locations
function dtlms_replace_default_menu_location ($args) {
	
	$current_user = wp_get_current_user();

	if ( in_array( 'instructor', (array) $current_user->roles ) ) {
		if( has_nav_menu('instructor-menu') ) {
			$args['theme_location'] = 'instructor-menu';
		}
	} else if ( in_array( 'student', (array) $current_user->roles ) ) {
		if( has_nav_menu('student-menu') ) {
			$args['theme_location'] = 'student-menu';
		}
	}

	return $args;

}
add_filter( 'wp_nav_menu_args', 'dtlms_replace_default_menu_location' );

?>