<?php

// Save General Options
add_action( 'wp_ajax_dtlms_save_options_settings', 'dtlms_save_options_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_options_settings', 'dtlms_save_options_settings' );
function dtlms_save_options_settings() {

	$dtlms_settings_options = $_REQUEST;

	$settings = $dtlms_settings_options['settings'];

	$dtlms_settings = get_option('dtlms-settings');

	$dtlms_settings[$settings] = $dtlms_settings_options['dtlms'][$settings];

	if (get_option('dtlms-settings') != $dtlms_settings) {
		if (update_option('dtlms-settings', $dtlms_settings)) {
			echo esc_html__('Options have been updated successfully!', 'dtlms');
		}
	} else {
		echo esc_html__('No changes done!', 'dtlms');
	}

	if('true' ==  dtlms_option('general', 'add-instructor-roleto-admin')) {

		$administrators = get_users ( array ('role' => 'administrator') );
        if ( count( $administrators ) > 0 ) {
            foreach ($administrators as $administrator) {
				$administrator_id = $administrator->data->ID;
		    	$admin_user = new WP_User( $administrator_id );
		        $admin_user->add_role( 'instructor' );                
            }
        }

	} else {

		$administrators = get_users ( array ('role' => 'administrator') );
        if ( count( $administrators ) > 0 ) {
            foreach ($administrators as $administrator) {
				$administrator_id = $administrator->data->ID;
		    	$admin_user = new WP_User( $administrator_id );
		        $admin_user->remove_role( 'instructor' );                
            }
        }

	}

	die();

}


if(!function_exists('dtlms_get_instructor_label')) {
	function dtlms_get_instructor_label($label_type) {
	
	    if($label_type == 'singular') {
	    	$label = (dtlms_option('general','instructor-singular-label') != '') ? dtlms_option('general','instructor-singular-label') : esc_html__('Instructor', 'dtlms');
	    }

	    if($label_type == 'plural') {
	    	$label = (dtlms_option('general','instructor-plural-label') != '') ? dtlms_option('general','instructor-plural-label') : esc_html__('Instructors', 'dtlms');	
	    } 

	    return $label;

	}
	add_filter( 'instructor_label', 'dtlms_get_instructor_label', 10, 1 );
}


if(!function_exists('dtlms_get_class_label')) {
	function dtlms_get_class_label($label_type) {
	
	    if($label_type == 'singular') {
	    	$label = (dtlms_option('class','class-title-singular') != '') ? dtlms_option('class','class-title-singular') : esc_html__('Class', 'dtlms');
	    }

	    if($label_type == 'plural') {
	    	$label = (dtlms_option('class','class-title-plural') != '') ? dtlms_option('class','class-title-plural') : esc_html__('Classes', 'dtlms');	
	    } 

	    return $label;

	}
	add_filter( 'class_label', 'dtlms_get_class_label', 10, 1 );
}


// Save Skin Settings
add_action( 'wp_ajax_dtlms_save_skin_settings', 'dtlms_save_skin_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_skin_settings', 'dtlms_save_skin_settings' );
function dtlms_save_skin_settings() {

	$dtlms_skin_settings = $_REQUEST['dtlms-skin-settings'];

	update_option('dtlms-skin-settings', $dtlms_skin_settings);

	echo esc_html__('"Skin" settings have been updated successfully!', 'dtlms');

	die();

}
?>