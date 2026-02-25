<?php

function dtlms_classregistrations_options() {

	$class_singular_label = apply_filters( 'class_label', 'singular' );
	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$output = '';

	$output .= '<div class="dtlms-classregistrations-container">';

		$output .= '<p class="dtlms-note">'.sprintf( esc_html__( 'Only onsite %1$s will be shown here.', 'dtlms' ), $class_plural_label ).'</p>';

		$output .= '<div class="dtlms-column dtlms-one-sixth first">';
			$output .= '<label>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).'</label>';
		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-five-sixth">';
		    $output .= '<select class="dtlms-classregistrations-classes" name="dtlms-classregistrations-classes" style="width:50%;" data-placeholder="'.sprintf( esc_html__( 'Choose %1$s ...', 'dtlms' ), $class_singular_label ).'" class="dtlms-chosen-select">';

				$output .= '<option value="-1">'.esc_html__('None', 'dtlms').'</option>';

				$class_args = array('posts_per_page' => -1, 'post_type' => 'dtlms_classes', 'orderby' => 'title', 'order' => 'DESC');
				$class_args['meta_query'][] = array(
								'key'     => 'dtlms-class-type',
								'value'   => 'onsite',
								'compare' => '='
								);
				
				$classes = get_posts( $class_args );
		        if ( count( $classes ) > 0 ) {
		            foreach ($classes as $class) {
						$class_id = $class->ID;
						$output .= '<option value="' . esc_attr( $class_id ) . '">' . esc_html( $class->post_title ) . '</option>';
					}
				}
				wp_reset_postdata();

		    $output .= '</select>';
		$output .= '</div>';

		$output .= '<div class="dtlms-hr-invisible"></div>';

		$output .= dtlms_generate_loader_html(true);

		$output .= '<div class="dtlms-classregistrations-classes-container"></div>';

	$output .= '</div>';

	echo $output;

}	

add_action( 'wp_ajax_dtlms_load_class_registration_details', 'dtlms_load_class_registration_details' );
add_action( 'wp_ajax_nopriv_dtlms_load_class_registration_details', 'dtlms_load_class_registration_details' );
function dtlms_load_class_registration_details($init_load) {
	
	$output = '';

	$class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : '';
	
	if($class_id > 0) {

		$registered_users = get_post_meta($class_id, 'registered_users', true);
		$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array ();
		$registered_users_count = count($registered_users);

		$registered_users_anonymous = get_post_meta($class_id, 'registered_users_anonymous', true);
		$registered_users_anonymous = (is_array($registered_users_anonymous) && !empty($registered_users_anonymous)) ? $registered_users_anonymous : array ();
		$registered_users_anonymous_count = count($registered_users_anonymous);
		

		$class_start_date = get_post_meta($class_id, 'dtlms-class-start-date', true);
		if($class_start_date != '') {
			$class_start_date_label = date(get_option('date_format'), strtotime($class_start_date));
		} else {
			$class_start_date_label = esc_html__('Date not chosen', 'dtlms');
		}

		$class_capacity = get_post_meta($class_id, 'dtlms-class-capacity', true);

		$seats_available = ($class_capacity - $registered_users_count - $registered_users_anonymous_count);

		
		$output .= '<div class="dtlms-class-details-container">
						<ul>
							<li><label>'.esc_html__('Start Date', 'dtlms').'</label> : '.$class_start_date_label.'</li>
							<li><label>'.esc_html__('Capacity', 'dtlms').'</label> : '.$class_capacity.'</li>
							<li><label>'.esc_html__('Applied ( Users regsitered with our site already )', 'dtlms').'</label> : '.$registered_users_count.'</li>
							<li><label>'.esc_html__('Registered', 'dtlms').'</label> : '.$registered_users_anonymous_count.'</li>
							<li><label>'.esc_html__('Available', 'dtlms').'</label> : '.$seats_available.'</li>
						</ul>
					</div>';


		$output .= '<h3>'.esc_html__('Registered Users', 'dtlms').'</h3>';
		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
					  <tr>
						<th scope="col">'.esc_html__('#', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Name', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Email', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Option', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Certificate', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Badge', 'dtlms').'</th>						
					  </tr>';
		
						$i = 0;
						if(isset($registered_users) && !empty($registered_users)) {
							
							foreach($registered_users as $registered_user_key => $registered_user) {
											
				                $registered_users_certificate_checked = '';
				                $registered_users_certificate_switchclass = 'checkbox-switch-off';
								if($registered_user['certificate'] == 'approved') {
				                    $registered_users_certificate_checked = 'checked="checked"';
				                    $registered_users_certificate_switchclass = 'checkbox-switch-on';
								}	

				                $registered_users_badge_checked = '';
				                $registered_users_badge_switchclass = 'checkbox-switch-off';
								if($registered_user['badge'] == 'approved') {
				                    $registered_users_badge_checked = 'checked="checked"';
				                    $registered_users_badge_switchclass = 'checkbox-switch-on';
								}

								$output .= '<tr>
												<td>'.($i+1).'</td>
												<td>'.get_the_author_meta('display_name', $registered_user_key).'</td>
												<td>'.get_the_author_meta('email', $registered_user_key).'</td>
												<td><a href="'.get_edit_user_link($registered_user_key['user_id']).'">'.esc_html('View', 'dtlms').'</a></td>
												<td>'
													.'<div data-for="approve-registered-users-certificate-'.$registered_user_key.'" class="dtlms-checkbox-switch '.$registered_users_certificate_switchclass.'"></div>'
													.'<input id="approve-registered-users-certificate-'.$registered_user_key.'" class="approve-registered-users-certificate hidden" type="checkbox" name="approve-registered-users-certificate" value="'.$registered_user_key.'" '.$registered_users_certificate_checked.' />'.
												'</td>
												<td>'
													.'<div data-for="approve-registered-users-badge-'.$registered_user_key.'" class="dtlms-checkbox-switch '.$registered_users_badge_switchclass.'"></div>'
													.'<input id="approve-registered-users-badge-'.$registered_user_key.'" class="approve-registered-users-badge hidden" type="checkbox" name="approve-registered-users-badge" value="'.$registered_user_key.'" '.$registered_users_badge_checked.' />'.
												'</td>												
											</tr>';
									
								$i++;	
								
							}
							
						}
						
						if($i == 0) {
							$output .= '<tr><td colspan="6">'.esc_html__('No Records Found!', 'dtlms').'</td></tr>';
						}
		
		$output .= '</table>';

		$output .= '<div class="dtlms-class-registration-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-class-registration-settings small" data-classid="'.$class_id.'">'.esc_html__('Save', 'dtlms').'</a>';		

		
		$output .= '<h3>'.esc_html__('Registered Users - Anonymous', 'dtlms').'</h3>';
		$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
					  <tr>
						<th scope="col">'.esc_html__('#', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Name', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Email', 'dtlms').'</th>
						<th scope="col">'.esc_html__('DOB', 'dtlms').'</th>
						<th scope="col">'.esc_html__('Message', 'dtlms').'</th>
					  </tr>';
		
						$i = 0;
						if(isset($registered_users_anonymous) && !empty($registered_users_anonymous)) {
							
							foreach($registered_users_anonymous as $registered_user_anonymous) {						

								$output .= '<tr>
												<td>'.($i+1).'</td>
												<td>'.$registered_user_anonymous['first_name'].' '.$registered_user_anonymous['last_name'].'</td>
												<td>'.$registered_user_anonymous['email'].'</td>
												<td>'.$registered_user_anonymous['dob'].'</td>
												<td>'.$registered_user_anonymous['message'].'</td>
											</tr>';
									
								$i++;	
								
							}
							
						}
						
						if($i == 0) {
							$output .= '<tr><td colspan="7">'.esc_html__('No Records Found!', 'dtlms').'</td></tr>';
						}
		
		$output .= '</table>';

	} else {
		
		$output .= esc_html__('Please select class!', 'dtlms');
		
	}
	
	echo $output;

	die();
	
}

add_action( 'wp_ajax_dtlms_save_class_registration_settings', 'dtlms_save_class_registration_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_class_registration_settings', 'dtlms_save_class_registration_settings' );
function dtlms_save_class_registration_settings() {

	$class_id = $_REQUEST['class_id'];
	$registered_users_certificate = is_array($_REQUEST['registered_users_certificate']) && !empty($_REQUEST['registered_users_certificate']) ? $_REQUEST['registered_users_certificate'] : array ();
	$registered_users_badge = is_array($_REQUEST['registered_users_badge']) && !empty($_REQUEST['registered_users_badge']) ? $_REQUEST['registered_users_badge'] : array ();

	// Updating registered user
	$registered_users = get_post_meta($class_id, 'registered_users', true);
	$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array ();

	if(isset($registered_users) && !empty($registered_users)) {
		foreach($registered_users as $registered_user_key => $registered_user) {
			if(in_array($registered_user_key, $registered_users_certificate)) {
				$registered_users[$registered_user_key]['certificate'] = 'approved';
			} else {
				unset($registered_users[$registered_user_key]['certificate']);
			}
			if(in_array($registered_user_key, $registered_users_badge)) {
				$registered_users[$registered_user_key]['badge'] = 'approved';
			} else {
				unset($registered_users[$registered_user_key]['badge']);				
			}
		}
	}

	update_post_meta($class_id, 'registered_users', $registered_users);
	
	echo esc_html__('Options saved successfully!', 'dtlms');

	die();

}

?>