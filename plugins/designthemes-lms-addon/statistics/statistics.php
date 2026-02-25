<?php

require_once plugin_dir_path ( __FILE__ ) . '/statistics-utils.php';


function dtlms_statistics_options() {
	
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dtlms_statistics_overview';
	
	dtlms_get_statistics_submenus($current);
	dtlms_get_statistics_tab($current);
	
}		

function dtlms_get_statistics_submenus($current) {

	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	if ( in_array( 'administrator', (array) $current_user->roles ) ) {

		$instructor_label = apply_filters( 'instructor_label', 'plural' );

	    $tabs = array ( 
					'dtlms_statistics_overview' => esc_html__('Overview', 'dtlms'), 
					'dtlms_statistics_courses' => esc_html__('Courses', 'dtlms'), 
					'dtlms_statistics_classes' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ),
					'dtlms_statistics_packages' => esc_html__('Packages', 'dtlms'), 
					'dtlms_statistics_instructors' => sprintf( esc_html__('%1$s', 'dtlms'), $instructor_label ), 
					'dtlms_statistics_students' => esc_html__('Students', 'dtlms'), 
	    		);

   	} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {

	    $tabs = array ( 
					'dtlms_statistics_overview' => esc_html__('Overview', 'dtlms'), 
					'dtlms_statistics_mycourses' => esc_html__('Courses', 'dtlms'),
					'dtlms_statistics_myclasses' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ),
					'dtlms_statistics_commissions' => esc_html__('Commissions', 'dtlms'), 
	    		);

   	}	    
			
    echo '<h2 class="dtlms-custom-nav nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ) {
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$class.'" href="?page=dtlms-statistics-options&tab='.$key.'">'.$tab.'</a>';
		}
    echo '</h2>';

}

function dtlms_get_statistics_tab($current) {

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;
	
	if ( in_array( 'administrator', (array) $current_user->roles ) ) {

		require_once plugin_dir_path ( __FILE__ ) . '/statistics-admin.php';

		switch($current){
			case 'dtlms_statistics_overview': 
				dtlms_statistics_overview_content($current_user_id);
			break;
			case 'dtlms_statistics_courses':
				dtlms_statistics_courses_content($current_user_id);
			break;
			case 'dtlms_statistics_classes':
				dtlms_statistics_classes_content($current_user_id);
			break;			
			case 'dtlms_statistics_instructors':
				dtlms_statistics_instructors_content($current_user_id);
			break;
			case 'dtlms_statistics_packages':
				dtlms_statistics_packages_content($current_user_id);
			break;			
			case 'dtlms_statistics_students':
				dtlms_statistics_students_content($current_user_id);
			break;					
			default:
				dtlms_statistics_overview_content($current_user_id);
			break;
		}

   	} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {

		require_once plugin_dir_path ( __FILE__ ) . '/statistics-instructor.php';

		switch($current){
			case 'dtlms_statistics_overview': 
				dtlms_statistics_overview_content($current_user_id);
			break;
			case 'dtlms_statistics_mycourses':
				dtlms_statistics_mycourses_content($current_user_id);
			break;
			case 'dtlms_statistics_myclasses':
				dtlms_statistics_myclasses_content($current_user_id);
			break;			
			case 'dtlms_statistics_commissions':
				dtlms_statistics_commissions_content($current_user_id);
			break;			
			default:
				dtlms_statistics_overview_content($current_user_id);
			break;
		}
	    
   	}	
	
}

?>