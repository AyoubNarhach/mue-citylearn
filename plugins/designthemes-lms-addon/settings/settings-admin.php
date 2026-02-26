<?php

function dtlms_settings_general_content() {
	
	$output = '';

	$class_title_singular = apply_filters( 'class_label', 'singular' );

   	$tabs = array ( 
				'dtlms_settings_general_settings' => esc_html__('General Settings', 'dtlms'),
				'dtlms_settings_course_settings' => esc_html__('Course Settings', 'dtlms'), 
				'dtlms_settings_class_settings' => sprintf( esc_html__('%1$s Settings', 'dtlms'), $class_title_singular ),
				'dtlms_settings_login_settings' => esc_html__('Login Settings', 'dtlms'), 
				'dtlms_settings_permalink_settings' => esc_html__('Permalink Settings', 'dtlms'), 
				'dtlms_settings_chart_settings' => esc_html__('Chart Settings', 'dtlms'), 			
    		);
    
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dtlms_settings_general_settings';

    $output .= '<h3 class="dtlms-custom-nav nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ) {
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			$output .= '<a class="nav-tab '.$class.'" href="?page=dtlms-settings-options&parenttab=dtlms_settings_general&tab='.$key.'">'.$tab.'</a>';
		}
    $output .= '</h3>';
  

    if($current == 'dtlms_settings_general_settings') {
	 	$output .= '<div class="dtlms-settings-general-container">';
			$output .= dtlms_get_general_settings_content();
		$output .= '</div>';
    } else if($current == 'dtlms_settings_course_settings') {
	 	$output .= '<div class="dtlms-settings-course-container">';
			$output .= dtlms_get_course_settings_content();
		$output .= '</div>';
    } else if($current == 'dtlms_settings_class_settings') {
	 	$output .= '<div class="dtlms-settings-class-container">';
			$output .= dtlms_get_class_settings_content();
		$output .= '</div>'; 			
    } else if($current == 'dtlms_settings_login_settings') {
	 	$output .= '<div class="dtlms-settings-login-container">';
			$output .= dtlms_get_login_settings_content();
		$output .= '</div>'; 
    } else if($current == 'dtlms_settings_permalink_settings') {
	 	$output .= '<div class="dtlms-settings-permalink-container">';
			$output .= dtlms_get_permalink_settings_content();
		$output .= '</div>';					
    } else if($current == 'dtlms_settings_chart_settings') {
	 	$output .= '<div class="dtlms-settings-chart-container">';
			$output .= dtlms_get_chart_settings_content();
		$output .= '</div>'; 				 	
    }

    echo $output;

}

function dtlms_settings_assigning_content() {
	
	$output = '';

	$class_title_singular = apply_filters( 'class_label', 'singular' );
	$class_title_plural = apply_filters( 'class_label', 'plural' );

   	$tabs = array ( 
				'dtlms_settings_courses_assign_students' => esc_html__('Course - Assign students', 'dtlms'), 
				'dtlms_settings_courses_assign_courses' => esc_html__('Course - Assign courses', 'dtlms'), 
				'dtlms_settings_classes_assign_students' => sprintf( esc_html__( '%1$s - Assign students', 'dtlms' ), $class_title_singular ),
				'dtlms_settings_classes_assign_classes' => sprintf( esc_html__( '%1$s - Assign %2$s', 'dtlms' ), $class_title_singular, strtolower($class_title_plural) ),
    		);
    
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dtlms_settings_courses_assign_students';

    $output .= '<h3 class="dtlms-custom-nav nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ) {
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			$output .= '<a class="nav-tab '.$class.'" href="?page=dtlms-settings-options&parenttab=dtlms_settings_assigning&tab='.$key.'">'.$tab.'</a>';
		}
    $output .= '</h3>';
  

    if($current == 'dtlms_settings_courses_assign_students') {
	 	$output .= '<div class="dtlms-settings-assigning-container">';
			$output .= dtlms_get_courses_assign_students_content();
		$output .= '</div>';   	
    } else if($current == 'dtlms_settings_courses_assign_courses') {
	 	$output .= '<div class="dtlms-settings-assigning-container">';
			$output .= dtlms_get_courses_assign_courses_content();
		$output .= '</div>';
    } else if($current == 'dtlms_settings_classes_assign_students') {
	 	$output .= '<div class="dtlms-settings-assigning-container">';
			$output .= dtlms_get_classes_assign_students_content();
		$output .= '</div>';
    } else if($current == 'dtlms_settings_classes_assign_classes') {
	 	$output .= '<div class="dtlms-settings-assigning-container">';
			$output .= dtlms_get_classes_assign_classes_content();
		$output .= '</div>';   				 	
    }

    echo $output;

}

function dtlms_settings_commission_content() {
	
	$output = '';

   	$tabs = array ( 
				'dtlms_settings_set_commission' => esc_html__('Set Commission', 'dtlms'), 
				'dtlms_settings_pay_commission' => esc_html__('Pay Commission', 'dtlms'), 
    		);
    
	$current = isset( $_GET['tab'] ) ? $_GET['tab'] : 'dtlms_settings_set_commission';

    $output .= '<h3 class="dtlms-custom-nav nav-tab-wrapper">';
		foreach( $tabs as $key => $tab ) {
			$class = ( $key == $current ) ? 'nav-tab-active' : '';
			$output .= '<a class="nav-tab '.$class.'" href="?page=dtlms-settings-options&parenttab=dtlms_settings_commission&tab='.$key.'">'.$tab.'</a>';
		}
    $output .= '</h3>';
  

    if($current == 'dtlms_settings_set_commission') {
		$output .= dtlms_get_set_commission_content();
    } else if($current == 'dtlms_settings_pay_commission') {
		$output .= dtlms_get_pay_commission_content();
    }

    echo $output;

}

function dtlms_settings_pointofcontact_content() {
	
	$output = '';

	$poc_settings = get_option('dtlms-poc-settings');

	$instructor_singular_label = apply_filters( 'instructor_label', 'singular' );

	global $dtlms_point_of_contacts;

	$output .= '<div class="dtlms-settings-poc-container">';

		$output .= '<form name="formPocSettings" class="formPocSettings" method="post">';

			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__('Email Template Page', 'dtlms').'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';

					$poc_email_template = ( isset($poc_settings['poc-email-template']) && '' !=  $poc_settings['poc-email-template'] ) ? $poc_settings['poc-email-template'] : '';
		            $output .= '<select id="poc-email-template" name="dtlms-poc-settings[poc-email-template]">';
						$pages = get_pages(); 
						$output .= '<option value="">'.esc_html__('Default', 'dtlms').'</option>';					
						foreach ( $pages as $page ) {
							$output .= '<option value="'.$page->ID.'" '.selected( $page->ID, $poc_email_template, false ).'>';
							$output .= $page->post_title;
							$output .= '</option>';
						}
					$output .= '</select>'; 
		            $output .= '<p class="dtlms-note">'.esc_html__('You can choose template page here.', 'dtlms').'</p>';

				$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Email Subject Prefix', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $poc_email_subject_prefix = ( isset($poc_settings['poc-email-subject-prefix']) && '' !=  $poc_settings['poc-email-subject-prefix'] ) ? $poc_settings['poc-email-subject-prefix'] : '';
		            $output .= '<input id="poc-email-subject-prefix" name="dtlms-poc-settings[poc-email-subject-prefix]" type="text" value="'.$poc_email_subject_prefix.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('If you wish you can have email subject prefix here.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';				
	

			foreach($dtlms_point_of_contacts as $point_of_contact) {

				$output .= '<div class="dtlms-settings-options-holder">';

					$output .= '<div class="dtlms-column dtlms-one-fifth first">';
						$output .= '<label>'.$point_of_contact['label'].'</label>';
					$output .= '</div>';
					$output .= '<div class="dtlms-column dtlms-four-fifth">';
						$output .= '<div class="dtlms-column dtlms-one-column first">';
							$output .= '<div class="dtlms-column dtlms-one-fifth first">';
								$output .= '<label>'.esc_html__('Student', 'dtlms').'</label>';
							$output .= '</div>';
							$output .= '<div class="dtlms-column dtlms-four-fifth">';
								$output .= '<div class="dtlms-column dtlms-one-fourth first">';
									$output .= '<label>'.esc_html__('Notification', 'dtlms').'</label>';
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
									if(isset($point_of_contact['disable']) && $point_of_contact['disable'] == 'notification') {
										$output .= '-';
									} else {
					                    $checked = ( isset($poc_settings[$point_of_contact['name']]['student']['notification']) && 'true' ==  $poc_settings[$point_of_contact['name']]['student']['notification'] ) ? ' checked="checked"' : '';
					                    $switchclass = ( isset($poc_settings[$point_of_contact['name']]['student']['notification']) && 'true' ==  $poc_settings[$point_of_contact['name']]['student']['notification'] ) ? 'checkbox-switch-on' :'checkbox-switch-off';
							            $output .= '<div data-for="'.$point_of_contact['name'].'-student-notification" class="dtlms-checkbox-switch '.$switchclass.'"></div>';
							            $output .= '<input id="'.$point_of_contact['name'].'-student-notification" class="hidden" type="checkbox" name="dtlms-poc-settings['.$point_of_contact['name'].'][student][notification]" value="true" '.$checked.' />';
							        }
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
									$output .= '<label>'.esc_html__('Email', 'dtlms').'</label>';
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
				                    $checked = ( isset($poc_settings[$point_of_contact['name']]['student']['email']) && 'true' ==  $poc_settings[$point_of_contact['name']]['student']['email'] ) ? ' checked="checked"' : '';
				                    $switchclass = ( isset($poc_settings[$point_of_contact['name']]['student']['email']) && 'true' ==  $poc_settings[$point_of_contact['name']]['student']['email'] ) ? 'checkbox-switch-on' :'checkbox-switch-off';
						            $output .= '<div data-for="'.$point_of_contact['name'].'-student-email" class="dtlms-checkbox-switch '.$switchclass.'"></div>';
						            $output .= '<input id="'.$point_of_contact['name'].'-student-email" class="hidden" type="checkbox" name="dtlms-poc-settings['.$point_of_contact['name'].'][student][email]" value="true" '.$checked.' />';
								$output .= '</div>';					
							$output .= '</div>';
						$output .= '</div>';
						$output .= '<div class="dtlms-column dtlms-one-column first">';
							$output .= '<div class="dtlms-column dtlms-one-fifth first">';
								$output .= '<label>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $instructor_singular_label ).'</label>';
							$output .= '</div>';
							$output .= '<div class="dtlms-column dtlms-four-fifth">';
								$output .= '<div class="dtlms-column dtlms-one-fourth first">';
									$output .= '<label>'.esc_html__('Notification', 'dtlms').'</label>';
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
									if(isset($point_of_contact['disable']) && $point_of_contact['disable'] == 'notification') {
										$output .= '-';
									} else {
					                    $checked = ( isset($poc_settings[$point_of_contact['name']]['instructor']['notification']) && 'true' ==  $poc_settings[$point_of_contact['name']]['instructor']['notification'] ) ? ' checked="checked"' : '';
					                    $switchclass = ( isset($poc_settings[$point_of_contact['name']]['instructor']['notification']) && 'true' ==  $poc_settings[$point_of_contact['name']]['instructor']['notification'] ) ? 'checkbox-switch-on' :'checkbox-switch-off';				                    
							            $output .= '<div data-for="'.$point_of_contact['name'].'-instructor-notification" class="dtlms-checkbox-switch '.$switchclass.'"></div>';
							            $output .= '<input id="'.$point_of_contact['name'].'-instructor-notification" class="hidden" type="checkbox" name="dtlms-poc-settings['.$point_of_contact['name'].'][instructor][notification]" value="true" '.$checked.' />';
							        }
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
									$output .= '<label>'.esc_html__('Email', 'dtlms').'</label>';
								$output .= '</div>';
								$output .= '<div class="dtlms-column dtlms-one-fourth">';
				                    $checked = ( isset($poc_settings[$point_of_contact['name']]['instructor']['email']) && 'true' ==  $poc_settings[$point_of_contact['name']]['instructor']['email'] ) ? ' checked="checked"' : '';
				                    $switchclass = ( isset($poc_settings[$point_of_contact['name']]['instructor']['email']) && 'true' ==  $poc_settings[$point_of_contact['name']]['instructor']['email'] ) ? 'checkbox-switch-on' :'checkbox-switch-off';
						            $output .= '<div data-for="'.$point_of_contact['name'].'-instructor-email" class="dtlms-checkbox-switch '.$switchclass.'"></div>';
						            $output .= '<input id="'.$point_of_contact['name'].'-instructor-email" class="hidden" type="checkbox" name="dtlms-poc-settings['.$point_of_contact['name'].'][instructor][email]" value="true" '.$checked.' />';
								$output .= '</div>';
							$output .= '</div>';			
						$output .= '</div>';
					$output .= '</div>';

				$output .= '</div>';

			}

			$output .= '<p class="dtlms-note">'.esc_html__('Make sure "BuddyPress" plugin is activated for notification concept.', 'dtlms').'</p>';

			$output .= '<div class="dtlms-poc-settings-response-holder"></div>';

			$output .= '<a href="#" class="dtlms-button dtlms-save-poc-settings small">'.esc_html__('Save Settings', 'dtlms').'</a>';

		$output .= '</form>';

	$output .= '</div>';

	echo $output;

}

function dtlms_settings_import_content() {
	
	$output = '';

	$output .= dtlms_settings_load_import_content();

    echo $output;

}

function dtlms_settings_skin_content() {
	
	$output = '';

	$skin_settings = get_option('dtlms-skin-settings');

	$primary_color = ( isset($skin_settings['primary-color']) && '' !=  $skin_settings['primary-color'] ) ? $skin_settings['primary-color'] : '';
	$secondary_color = ( isset($skin_settings['secondary-color']) && '' !=  $skin_settings['secondary-color'] ) ? $skin_settings['secondary-color'] : '';
	$tertiary_color = ( isset($skin_settings['tertiary-color']) && '' !=  $skin_settings['tertiary-color'] ) ? $skin_settings['tertiary-color'] : '';

	$primary_alternate_color = ( isset($skin_settings['primary-alternate-color']) && '' !=  $skin_settings['primary-alternate-color'] ) ? $skin_settings['primary-alternate-color'] : '';
	$secondary_alternate_color = ( isset($skin_settings['secondary-alternate-color']) && '' !=  $skin_settings['secondary-alternate-color'] ) ? $skin_settings['secondary-alternate-color'] : '';
	$tertiary_alternate_color = ( isset($skin_settings['tertiary-alternate-color']) && '' !=  $skin_settings['tertiary-alternate-color'] ) ? $skin_settings['tertiary-alternate-color'] : '';

	$quiztimer_foreground_color = ( isset($skin_settings['quiztimer-foreground-color']) && '' !=  $skin_settings['quiztimer-foreground-color'] ) ? $skin_settings['quiztimer-foreground-color'] : '#81164e';
	$quiztimer_background_color = ( isset($skin_settings['quiztimer-background-color']) && '' !=  $skin_settings['quiztimer-background-color'] ) ? $skin_settings['quiztimer-background-color'] : '#e2d6c1';


	$output .= '<form name="formSkinSettings" class="formSkinSettings" method="post">';

		$output .= '<p class="dtlms-note">'.esc_html__('Following colors will be used as default colors for "DesignThemes LMS Addon".', 'dtlms').'</p>';
		$output .= '<div class="dtlms-clear"></div>';

		$output .= '<div class="dtlms-column dtlms-one-third first">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Primary Color', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[primary-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$primary_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose primary color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-one-third">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Secondary Color', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[secondary-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$secondary_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose secondary color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-one-third">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Tertiary Color', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[tertiary-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$tertiary_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose tertiary color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-hr-invisible"></div>';

		$output .= '<div class="dtlms-column dtlms-one-third first">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Primary Color - Alternate', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[primary-alternate-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$primary_alternate_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose primary alternate color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-one-third">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Secondary Color - Alternate', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[secondary-alternate-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$secondary_alternate_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose secondary alternate color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-one-third">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Tertiary Color - Alternate', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[tertiary-alternate-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$tertiary_alternate_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose tertiary alternate color module skin.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-hr-invisible"></div>';		

		$output .= '<div class="dtlms-column dtlms-one-third first">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Quiz Timer - Foreground Color', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[quiztimer-foreground-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$quiztimer_foreground_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose quiz timer foreground color.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-one-third">';
			$output .= '<div class="dtlms-settings-options-holder">';
				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Quiz Timer - Background Color', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
		            $output .= '<input name="dtlms-skin-settings[quiztimer-background-color]" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$quiztimer_background_color.'" />';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose quiz timer background color.', 'dtlms').'</p>';
				$output .= '</div>';
			$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="dtlms-hr-invisible"></div>';	
		
		$output .= '<div class="dtlms-skin-settings-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-skin-settings small">'.esc_html__('Save Settings', 'dtlms').'</a>';

	$output .= '</form>';

    echo $output;

}

function dtlms_settings_typography_content() {

	$google_fonts = dtlms_fonts();

    $title_font_family = dtlms_option('typography', 'title-font-family');

    $output = '';

	$output .= '<div class="dtlms-settings-typography-container">';

		$output .= '<form name="formOptionSettings" class="formOptionSettings" method="post">';

			$output .= '<div class="dtlms-settings-options-holder">';

				$output .= '<div class="dtlms-column dtlms-one-fifth first">';
					$output .= '<label>'.esc_html__( 'Title Font Family', 'dtlms' ).'</label>';
				$output .= '</div>';
				$output .= '<div class="dtlms-column dtlms-four-fifth">';
				    $output .= '<select id="title-font-family" name="dtlms[typography][title-font-family]">';

				    	$output .= '<option value="">'.esc_html__('Default', 'dtlms').'</option>';		
				    	
						# System fonts
						$output .= '<optgroup label="'. esc_html__('System', 'dtlms') .'">';
						foreach ( $google_fonts['system'] as $font ) {
							$output .= '<option value="'. $font .'"'.selected($title_font_family, $font, false).'>'. $font .'</option>';
						}
						$output .= '</optgroup>';

						# Google fonts | all
						$output .= '<optgroup label="'. esc_html__('Google Fonts', 'dtlms') .'">';
						foreach ( $google_fonts['all'] as $font ) {
							$output .= '<option value="'. $font .'"'.selected($title_font_family, $font, false).'>'. $font .'</option>';
						}
						$output .= '</optgroup>'; 

					$output .= '</select>';
		            $output .= '<p class="dtlms-note">'.esc_html__('Choose title font family here.', 'dtlms').'</p>';
				$output .= '</div>';

			$output .= '</div>';

			$output .= '<div class="dtlms-option-settings-response-holder"></div>';

			$output .= '<a href="#" class="dtlms-button dtlms-save-options-settings small" data-settings="typography">'.esc_html__('Save Settings', 'dtlms').'</a>';

		$output .= '</form>';

	$output .= '</div>';

    echo $output;

}

?>