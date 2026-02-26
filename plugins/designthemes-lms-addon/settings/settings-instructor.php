<?php

function dtlms_settings_overview_content($user_id) {
	
	$output = '';	
	$output .= '<div class="dtlms-settings-overview-container">';
	$output .= '</div>';

	echo $output;

}

function dtlms_settings_set_commission_content($user_id) {
	
	$output = '';

	$output .= '<div class="dtlms-settings-set-commission-container">';
		$output .= dtlms_setcom_load_instructor_courses($user_id);
	$output .= '</div>';

	echo $output;

}

function dtlms_settings_import_content() {
	
	$output = '';

	$output .= dtlms_settings_load_import_content();

    echo $output;

}

?>