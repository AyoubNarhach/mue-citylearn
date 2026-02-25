<?php

require_once plugin_dir_path ( __FILE__ ) . '/settings-utils.php';
require_once plugin_dir_path ( __FILE__ ) . '/settings-general-utils.php';
require_once plugin_dir_path ( __FILE__ ) . '/settings-commission-utils.php';
require_once plugin_dir_path ( __FILE__ ) . '/settings-assigning-utils.php';
require_once plugin_dir_path ( __FILE__ ) . '/settings-poc-utils.php';
require_once plugin_dir_path ( __FILE__ ) . '/settings-import-utils.php';


function dtlms_settings_options() {
	
	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	if ( in_array( 'administrator', (array) $current_user->roles ) ) {
		$current = isset( $_GET['parenttab'] ) ? $_GET['parenttab'] : 'dtlms_settings_general';
	} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {
	    $current = isset( $_GET['parenttab'] ) ? $_GET['parenttab'] : 'dtlms_settings_overview';
	    if('true' ==  dtlms_option('general','allow-instructor-setcommission')) {
	    	$current = isset( $_GET['parenttab'] ) ? $_GET['parenttab'] : 'dtlms_settings_set_commission';
	    } else {
	    	$current = isset( $_GET['parenttab'] ) ? $_GET['parenttab'] : 'dtlms_settings_import';
	    }    
   	}	
	
	dtlms_get_settings_submenus($current);
	dtlms_get_settings_tab($current);
	
}		

function dtlms_get_settings_submenus($current) {

	$dtlms_settings = get_option('dtlms-settings');

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	if ( in_array( 'administrator', (array) $current_user->roles ) ) {
	    $tabs = array ( 
					'dtlms_settings_general' => esc_html__('General', 'dtlms'),
					'dtlms_settings_assigning' => esc_html__('Assigning', 'dtlms'), 
					'dtlms_settings_commission' => esc_html__('Commission', 'dtlms'), 
					'dtlms_settings_pointofcontact' => esc_html__('Point Of Contact', 'dtlms'),
					'dtlms_settings_import' => esc_html__('Import', 'dtlms'),
					'dtlms_settings_skin' => esc_html__('Skin', 'dtlms'), 
					'dtlms_settings_typography' => esc_html__('Typography', 'dtlms'), 
	    		);
   	} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {
	    $tabs = array ();
	    if('true' ==  dtlms_option('general','allow-instructor-setcommission')) {
	    	$tabs['dtlms_settings_set_commission'] = esc_html__('Set Commission', 'dtlms');
	    }
	    $tabs['dtlms_settings_import'] = esc_html__('Import', 'dtlms'); 
   	}	    
			
    echo '<h2 class="dtlms-custom-nav nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ) {
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			echo '<a class="nav-tab '.$class.'" href="?page=dtlms-settings-options&parenttab='.$key.'">'.$tab.'</a>';
		}
    echo '</h2>';

}

function dtlms_get_settings_tab($current) {

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;
	
	if ( in_array( 'administrator', (array) $current_user->roles ) ) {

		require_once plugin_dir_path ( __FILE__ ) . '/settings-admin.php';

		switch($current){
			case 'dtlms_settings_general': 
				dtlms_settings_general_content($current_user_id);
			break;
			case 'dtlms_settings_assigning':
				dtlms_settings_assigning_content();
			break;				
			case 'dtlms_settings_commission':
				dtlms_settings_commission_content();
			break;
			case 'dtlms_settings_pointofcontact':
				dtlms_settings_pointofcontact_content();
			break;
			case 'dtlms_settings_import':
				dtlms_settings_import_content();
			break;	
			case 'dtlms_settings_skin':
				dtlms_settings_skin_content();
			break;
			case 'dtlms_settings_typography':
				dtlms_settings_typography_content();
			break;											
			default:
				dtlms_settings_general_content($current_user_id);
			break;
		}

   	} else if ( in_array( 'instructor', (array) $current_user->roles ) ) {

		require_once plugin_dir_path ( __FILE__ ) . '/settings-instructor.php';

		switch($current){
			case 'dtlms_settings_set_commission':
				dtlms_settings_set_commission_content($current_user_id);
			break;
			case 'dtlms_settings_import':
				dtlms_settings_import_content();
			break;				
			default:
				dtlms_settings_overview_content($current_user_id);
			break;
		}
	    
   	}	
	
}

?>