<?php

// User purchase status on classes
if(!function_exists('dtlms_get_user_class_purchase_status')) {
	function dtlms_get_user_class_purchase_status($class_id, $user_id) {

		$active_package_classes = dtlms_get_user_active_packages($user_id, 'classes');
		$active_package_classes = (is_array($active_package_classes) && !empty($active_package_classes)) ? $active_package_classes : array();

		$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
		$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();

		$assigned_classes = get_user_meta($user_id, 'assigned_classes', true);
		$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();

		$active_classes = array_merge($active_package_classes, $purchased_classes, $assigned_classes);	

		if(in_array($class_id, $active_classes)) {
			return true;
		}

		return false;

	}
}


add_action( 'wp_ajax_dtlms_start_class_initialize', 'dtlms_start_class_initialize' );
add_action( 'wp_ajax_nopriv_dtlms_start_class_initialize', 'dtlms_start_class_initialize' );
function dtlms_start_class_initialize($class_id, $user_id, $author_id, $ajax_call = false) {

	$ajax_call = isset($_REQUEST['ajax_call']) ? $_REQUEST['ajax_call'] : false;
	if($ajax_call) {
		$startclass_nonce = isset($_REQUEST['startclass_nonce']) ? $_REQUEST['startclass_nonce'] : '';
		$class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : -1;
		$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : -1;
		$author_id = isset($_REQUEST['author_id']) ? $_REQUEST['author_id'] : -1;
	}

	if(($class_id > 0 && $user_id > 0) || ($ajax_call && isset($startclass_nonce) && wp_verify_nonce($startclass_nonce, 'start_class_'.$class_id.'_'.$user_id))) {

		$started_users = get_post_meta($class_id, 'started_users', true);
		$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array();
		array_push($started_users, $user_id);
		update_post_meta($class_id, 'started_users', array_unique($started_users));		

		$started_classes = get_user_meta($user_id, 'started_classes', true);
		$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();
		array_push($started_classes, $class_id);
		update_user_meta($user_id, 'started_classes', array_unique($started_classes));

		// Create entry in gradings
		$user_info = get_userdata($user_id);

		$title = get_the_title($class_id);

		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dtlms_gradings',
			'post_author' => $author_id,
		);
		
		$grade_post_id = wp_insert_post($grade_post);
		
		update_post_meta($grade_post_id, 'dtlms-class-id',  $class_id);
		update_post_meta($grade_post_id, 'dtlms-class-grade-id',  $grade_post_id );
		update_post_meta($grade_post_id, 'dtlms-user-id',  $user_id);
		update_post_meta($grade_post_id, 'grade-type', 'class' );

		$class_curriculum_details = array ('started' => 1, 'grade-post-id' => $grade_post_id);
		update_user_meta($user_id, $class_id, $class_curriculum_details);

	}	

	if($ajax_call) {
		die();
	}	

}

add_action( 'wp_ajax_dtlms_start_class_ajax_initialize', 'dtlms_start_class_ajax_initialize' );
add_action( 'wp_ajax_nopriv_dtlms_start_class_ajax_initialize', 'dtlms_start_class_ajax_initialize' );
function dtlms_start_class_ajax_initialize() {

	$startclass_nonce = isset($_REQUEST['startclass_nonce']) ? $_REQUEST['startclass_nonce'] : '';
	$class_id = isset($_REQUEST['class_id']) ? $_REQUEST['class_id'] : -1;
	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : -1;
	$author_id = isset($_REQUEST['author_id']) ? $_REQUEST['author_id'] : -1;

	if(isset($startclass_nonce) && wp_verify_nonce($startclass_nonce, 'start_class_'.$class_id.'_'.$user_id)) {

		$started_users = get_post_meta($class_id, 'started_users', true);
		$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array();
		array_push($started_users, $user_id);
		update_post_meta($class_id, 'started_users', array_unique($started_users));		

		$started_classes = get_user_meta($user_id, 'started_classes', true);
		$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();
		array_push($started_classes, $class_id);
		update_user_meta($user_id, 'started_classes', array_unique($started_classes));

		// Create entry in gradings
		$user_info = get_userdata($user_id);

		$title = get_the_title($class_id);

		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dtlms_gradings',
			'post_author' => $author_id,
		);
		
		$grade_post_id = wp_insert_post($grade_post);
		
		update_post_meta($grade_post_id, 'dtlms-class-id',  $class_id);
		update_post_meta($grade_post_id, 'dtlms-class-grade-id',  $grade_post_id );
		update_post_meta($grade_post_id, 'dtlms-user-id',  $user_id);
		update_post_meta($grade_post_id, 'grade-type', 'class' );

		$class_curriculum_details = array ('started' => 1, 'grade-post-id' => $grade_post_id);
		update_user_meta($user_id, $class_id, $class_curriculum_details);

	}	

	die();

}

function dtlms_generate_class_startnprogress($class_id, $user_id) {

	$out = '';

	//$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);

	//if($class_content_options == 'course') {

		$user_class_status = dtlms_get_user_class_purchase_status($class_id, $user_id);

		$started_classes = get_user_meta($user_id, 'started_classes', true);
		$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();

		$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
		$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();	

		$completed_classes = get_user_meta($user_id, 'completed_classes', true);
		$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();	

		$course_data = get_post($class_id);
		$author_id = $course_data->post_author;

		$product = dtlms_get_product_object($class_id);

		$class_title_singular = apply_filters( 'class_label', 'singular' );
		$class_title_plural = apply_filters( 'class_label', 'plural' );	

		$out .= '<div class="dtlms-class-dynamic-section-holder">';

			$class_start_date = get_post_meta ( $class_id, 'dtlms-class-start-date', true );
			$class_startdate_timestamp = strtotime($class_start_date);
			$current_timestamp = current_time( 'timestamp', 1 );

			if($current_timestamp < $class_startdate_timestamp) {

				$out .= '<div class="dtlms-class-dynamic-section-startdate"><i class="fas fa-calendar-alt"></i>'.sprintf( esc_html__('%1$s starts on %2$s', 'dtlms'), $class_title_singular, '<strong>'.$class_start_date.'</strong>' ).'</div>';

				if('true' ==  dtlms_option('class','enable-countdown-timer-class-startdate')) {
					$countdown_date = dtlms_format_datetime($class_startdate_timestamp, 'm/d/Y H:i:s', true);
					$out .= dtlms_generate_countdown_html($countdown_date, -1, -1);	
				}

			} else if($user_class_status || ($user_id > 0 && !dtlms_check_item_has_price($product))) {

				if(in_array($class_id, $completed_classes)) {

					$class_curriculum_details = get_user_meta($user_id, $class_id, true);
					$class_grade_id = $class_curriculum_details['grade-post-id'];
					$user_percentage = get_post_meta($class_grade_id, 'user-percentage', true);
					$user_percentage = round($user_percentage, 2);

					$out .= '<div class="dtlms-class-result-overview">';
						$out .= '<p>'.sprintf( esc_html__('Your %1$s have been evaluated successfully. Please click below link to check the result.', 'dtlms'), strtolower($class_title_singular) ).'</p>';
						$out .= '<div class="dtlms-item-student-score-details">';
							$out .= esc_html__('Your Score', 'dtlms');
							$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
							$out .= '<div class="dtlms-item-overview-progressbar">';
								$out .= dtlms_generate_progressbar($user_percentage);
								$out .= '<span class="dtlms-item-percentage">'.$user_percentage.'%</span>';
							$out .= '</div>';
						$out .= '</div>';
						$out .= '<a href="#" class="dtlms-button dtlms-view-class-result filled small" data-classid="'.$class_id.'" data-userid="'.$user_id.'">'.esc_html__('View Results', 'dtlms').'</a>';
					$out .= '</div>';

				} else if(in_array($class_id, $submitted_classes)) {

					$out .= '<p>'.sprintf( esc_html__('Your %1$s have been submitted successfully for evaluation.', 'dtlms'), strtolower($class_title_singular) ).'</p>';

				} else if(in_array($class_id, $started_classes)) {

					$total_curriculum_count = $submitted_items_count = $completed_items_count = 0;

					$class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
					if(is_array($class_courses) && !empty($class_courses)) {

						$total_curriculum_count = count($class_courses);

						foreach($class_courses as $course_id) {

							// Submitted Items
							$submitted_args = array ( 
												'post_type' => 'dtlms_gradings',
												'meta_query'=>array(), 
											);	
							$submitted_args['meta_query'][] = array (
																	'key'     => 'dtlms-course-id',
																	'value'   => $course_id,
																	'compare' => '=='
																);
							$submitted_args['meta_query'][] = array (
																	'key'     => 'dtlms-user-id',
																	'value'   => $user_id,
																	'compare' => '=='
																);																			
							$submitted_args['meta_query'][] = array (
																	'key'     => 'grade-type',
																	'value'   => 'course',
																	'compare' => '=='
																);																	
							$submitted_args['meta_query'][] = array (
																	'key'     => 'submitted',
																	'value'   => '1',
																	'compare' => '=='
																);					
							$submitted_gradings = new WP_Query( $submitted_args );
							$submitted_gradings_count = $submitted_gradings->found_posts; 
							wp_reset_postdata();

							$submitted_items_count = $submitted_items_count + $submitted_gradings_count;


							// Completed Items
							$completed_args = array ( 
												'post_type' => 'dtlms_gradings',
												'meta_query'=>array(), 
											);	
							$completed_args['meta_query'][] = array (
																	'key'     => 'dtlms-course-id',
																	'value'   => $course_id,
																	'compare' => '=='
																);
							$completed_args['meta_query'][] = array (
																	'key'     => 'dtlms-user-id',
																	'value'   => $user_id,
																	'compare' => '=='
																);																			
							$completed_args['meta_query'][] = array (
																	'key'     => 'grade-type',
																	'value'   => 'course',
																	'compare' => '=='
																);																	
							$completed_args['meta_query'][] = array (
																	'key'     => 'graded',
																	'value'   => 'true',
																	'compare' => '=='
																);				
							$completed_gradings = new WP_Query( $completed_args );
							$completed_gradings_count = $completed_gradings->found_posts; 
							wp_reset_postdata();

							$completed_items_count = $completed_items_count + $completed_gradings_count;					

						}

					}

					$submitted_percentage = $completed_percentage = 0;
					if($total_curriculum_count > 0) {

						if($submitted_items_count > 0) {
							$submitted_percentage = round((($submitted_items_count/$total_curriculum_count)*100), 2);
						} else {
							$submitted_percentage = 0;
						}

						if($completed_items_count > 0) {
							$completed_percentage = round((($completed_items_count/$total_curriculum_count)*100), 2);
						} else {
							$completed_percentage = 0;
						}	

					}	
					
					$out .= '<div class="dtlms-item-progress-details-holder">
								<div class="dtlms-title">'.sprintf( esc_html__('%1$s Progress', 'dtlms'), $class_title_singular ).'</div>';
						$out .= '<div class="dtlms-item-student-submitted-item-details">';
							$out .= sprintf( esc_html__('Submitted %1$s / %2$s', 'dtlms'), '<span>'.$submitted_items_count, $total_curriculum_count.'</span>' );
							$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
							$out .= '<div class="dtlms-item-overview-progressbar">';
								$out .= dtlms_generate_progressbar($submitted_percentage);
								$out .= '<span class="dtlms-item-percentage">'.$submitted_percentage.'%</span>';
							$out .= '</div>';
						$out .= '</div>';					
						$out .= '<div class="dtlms-item-student-completed-item-details">';
							$out .= sprintf( esc_html__('Graded & Completed %1$s / %2$s', 'dtlms'), '<span>'.$completed_items_count, $total_curriculum_count.'</span>' );
							$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
							$out .= '<div class="dtlms-item-overview-progressbar">';
								$out .= dtlms_generate_progressbar($completed_percentage);
								$out .= '<span class="dtlms-item-percentage">'.$completed_percentage.'%</span>';
							$out .= '</div>';
						$out .= '</div>';	
					$out .= '</div>';		

					$out .= '<div class="dtlms-item-submit-button-holder">';
						$out .= '<div class="dtlms-item-submit-button">';

							$out .= '<a href="#" class="dtlms-button dtlms-submit-class-button large" data-submit-class-nonce="'.wp_create_nonce('submit_class_'.$class_id.'_'.$user_id).'" data-classid="'.$class_id.'" data-userid="'.$user_id.'" data-authorid="'.$author_id.'" data-totalcurriculumcount="'.$total_curriculum_count.'" data-submittedcurriculumcount="'.$submitted_items_count.'">'.sprintf( esc_html__('Submit %1$s', 'dtlms'), $class_title_singular ).'</a>';

				   		$out .= '</div>';
					$out .= '</div>';

				} else if($user_id > 0 && !dtlms_check_item_has_price($product)) {

					$out .= '<div class="dtlms-item-progress-details-holder">';
						$out .= '<div class="dtlms-item-progress-details">';
							$out .= '<a href="#" class="dtlms-button dtlms-start-class-button large" data-start-class-nonce="'.wp_create_nonce('start_class_'.$class_id.'_'.$user_id).'" data-classid="'.$class_id.'" data-userid="'.$user_id.'" data-authorid="'.$author_id.'">'.esc_html__('Start Class', 'dtlms').'</a>';
				   		$out .= '</div>';
					$out .= '</div>';

				}

			}

		$out .= '</div>';

	//}

    return $out;

}

add_action( 'wp_ajax_dtlms_submit_class_initialize', 'dtlms_submit_class_initialize' );
add_action( 'wp_ajax_nopriv_dtlms_submit_class_initialize', 'dtlms_submit_class_initialize' );
function dtlms_submit_class_initialize() {

	$submitclass_nonce = $_POST['submitclass_nonce'];
	$class_id = $_POST['class_id'];
	$user_id = $_POST['user_id'];
	$author_id = $_POST['author_id'];

	if(isset($submitclass_nonce) && wp_verify_nonce($submitclass_nonce, 'submit_class_'.$class_id.'_'.$user_id)) {

		$submitted_users = get_post_meta($class_id, 'submitted_users', true);
		$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
		array_push($submitted_users, $user_id);
		update_post_meta($class_id, 'submitted_users', array_unique($submitted_users));		

		$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
		$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();
		array_push($submitted_classes, $class_id);
		update_user_meta($user_id, 'submitted_classes', array_unique($submitted_classes));


		$class_curriculum_details = get_user_meta($user_id, $class_id, true);
		$class_grade_id = isset($class_curriculum_details['grade-post-id']) ? $class_curriculum_details['grade-post-id'] : -1;

		$class_curriculum_details['submitted'] = 1;
		update_user_meta($user_id, $class_id, $class_curriculum_details);

		update_post_meta($class_grade_id, 'submitted', 1 );


		// Notification & Mail
		do_action('dtlms_poc_class_submitted', $class_id, $user_id); 

	}

	die();

}

function dtlms_calculate_class_available_seats($class_id) {

	$class_enable_purchases = get_post_meta($class_id, 'dtlms-class-enable-purchases', true);
	$class_enable_registration = get_post_meta($class_id, 'dtlms-class-enable-registration', true);

	$class_capacity = get_post_meta($class_id, 'dtlms-class-capacity', true);
	$class_capacity = ($class_capacity != '') ? $class_capacity : 0;

	$seats_alloted = 0;
	if($class_enable_purchases == 'true') {
		$purchased_users = get_post_meta($class_id, 'purchased_users', true);
		$seats_alloted = (is_array($purchased_users) && !empty($purchased_users)) ? count($purchased_users) : 0;
	} else if($class_enable_registration == 'true') {
		$registered_users = get_post_meta($class_id, 'registered_users', true);
		$seats_alloted = (is_array($registered_users) && !empty($registered_users)) ? count($registered_users) : 0;
		$seats_alloted_anonymous = 0;
		if(dtlms_option('class','class-registration-without-login') == 'true') {
			$registered_users_anonymous = get_post_meta($class_id, 'registered_users_anonymous', true);
			$seats_alloted_anonymous = (is_array($registered_users_anonymous) && !empty($registered_users_anonymous)) ? count($registered_users_anonymous) : 0;			
		}
		$seats_alloted = $seats_alloted + $seats_alloted_anonymous;
	}

	if($seats_alloted > 0) {
		$available_seats = $class_capacity - $seats_alloted;
	} else {
		$available_seats = $class_capacity;
	}

	return $available_seats;

}


add_action( 'wp_ajax_dtlms_apply_onsite_class', 'dtlms_apply_onsite_class' );
add_action( 'wp_ajax_nopriv_dtlms_apply_onsite_class', 'dtlms_apply_onsite_class' );
function dtlms_apply_onsite_class() {

	$class_id = $_REQUEST['class_id'];
	$user_id = $_REQUEST['user_id'];

	$registered_users = get_post_meta($class_id, 'registered_users', true);
	$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array();
	$registered_users[$user_id] = array ();	
	update_post_meta($class_id, 'registered_users', $registered_users);		

	$registered_classes = get_user_meta($user_id, 'registered_classes', true);
	$registered_classes = (is_array($registered_classes) && !empty($registered_classes)) ? $registered_classes : array();
	array_push($registered_classes, $class_id);
	update_user_meta($user_id, 'registered_classes', array_unique($registered_classes));

	$seats_available = dtlms_calculate_class_available_seats($class_id);

	echo esc_html__('Applied', 'dtlms').'|'.$seats_available;

	die();

}

add_action( 'wp_ajax_dtlms_register_onsite_class', 'dtlms_register_onsite_class' );
add_action( 'wp_ajax_nopriv_dtlms_register_onsite_class', 'dtlms_register_onsite_class' );
function dtlms_register_onsite_class(){
	
	$class_title_singular = apply_filters( 'class_label', 'singular' );

	$class_id = $_REQUEST['class_id'];
	
	$output = '<div class="dtlms-class-registration-form-container">';

		$output .= '<div class="dtlms-class-registration-form-inner">';

			$output .= '<div class="dtlms-class-registration-form-holder">';

				$output .= '<div class="dtlms-title dtlms-registration-title"><h2><span>'.$class_title_singular.'<strong>'.esc_html__('Registration', 'dtlms').'</strong></span></h2></div>';
						
				$output .= '<form method="post" class="dtlms-class-registration-form" name="dtlms-class-registration-form">';
				
					$output .= '<div class="dtlms-column dtlms-one-half first">
									<input type="text" name="first_name" class="first_name" placeholder="'.esc_html('First Name', 'dtlms').'" value="" required />
								</div>
								<div class="dtlms-column dtlms-one-half">
									<input type="text" name="last_name" class="last_name" placeholder="'.esc_html('Last Name', 'dtlms').'" value="" />
								</div>

								<div class="dtlms-hr-invisible"></div>

								<div class="dtlms-column dtlms-one-half first">
									<input type="email" name="email" class="email" placeholder="'.esc_html('Email Id', 'dtlms').'" value="" required />
								</div>
								<div class="dtlms-column dtlms-one-half">
									<input type="text" name="dob" class="dob" placeholder="'.esc_html('DOB (ex. 01/01/2001)', 'dtlms').'" value="" required />
								</div>

								<div class="dtlms-hr-invisible"></div>

								<div class="dtlms-column dtlms-one-column first">
									<textarea name="message" class="message" placeholder="'.esc_html('Anything would you like to share with us ?', 'dtlms').'"></textarea>
								</div>
								<input type="hidden" name="class_id" class="class_id" value="'.$class_id.'" />		

								<button type="submit" class="dtlms-button dtlms-submit-registration-form small" name="dtlms-submit-registration-form">'.esc_html__('Register','dtlms').'</button>';
									
				$output .= '</form>';

			$output .= '</div>';

		$output .= '</div>';

	$output .= '</div>';

	$output .= '<div class="dtlms-class-registration-form-overlay"></div>';
	
	echo $output;
	
	die();
}

add_action( 'wp_ajax_dttheme_submit_class_registration_form', 'dttheme_submit_class_registration_form' );
add_action( 'wp_ajax_nopriv_dttheme_submit_class_registration_form', 'dttheme_submit_class_registration_form' );
function dttheme_submit_class_registration_form(){
	
	$class_id = $_REQUEST['class_id'];
	
	$class_disable_purchases_registration = get_post_meta($class_id, 'dtlms-class-disable-purchases-regsitration', true);
	
	$registered_users_anonymous = get_post_meta($class_id, 'registered_users_anonymous', true);
	$registered_users_anonymous = (is_array($registered_users_anonymous) && !empty($registered_users_anonymous)) ? $registered_users_anonymous : array();

	foreach($registered_users_anonymous as $registered_user_anonymous) {
		if($_POST['email'] == $registered_user_anonymous['email']) {
			echo 'invalid|'.esc_html__('Email id already exits!', 'dtlms');
			die();
		}
	}

	$registered_users = array();
	$registered_users[0]['first_name'] = $_POST['first_name'];
	$registered_users[0]['last_name'] = $_POST['last_name'];
	$registered_users[0]['email'] = $_POST['email'];
	$registered_users[0]['dob'] = $_POST['dob'];
	$registered_users[0]['message'] = $_POST['message'];
	
	$registered_users_anonymous = array_merge_recursive($registered_users_anonymous, $registered_users);

	update_post_meta ( $class_id, 'registered_users_anonymous', array_filter($registered_users_anonymous) );

	$seats_available = dtlms_calculate_class_available_seats($class_id);
	if($seats_available > 0) {
		echo 'false|'.$seats_available;
	} else if($seats_available <= 0 && $class_disable_purchases_registration != 'true') {
		echo 'false|0';
	} else {
		echo 'true|0';
	}

	die();

}


/*
* Classes Listing
*/

function dtlms_classes_listing_search_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$class_title_singular_data = apply_filters( 'class_label', 'singular' );

		$class_title_singular = sprintf( esc_html__('Search %1$s', 'dtlms'), $class_title_singular_data );

		$dtlms_classes_search_text = isset($request['dtlms-classes-search-text']) ? $request['dtlms-classes-search-text'] : '';
		$output .= '<div class="dtlms-classes-search-filter">';
			$output .= '<input name="dtlms-classes-search-text" class="dtlms-classes-search-text" type="text" value="'.$dtlms_classes_search_text.'" placeholder="'.$class_title_singular.'" />';
		$output .= '</div>';

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}		

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
			$output .= '<div class="dtlms-classes-search-filter">';
				$output .= '<input name="dtlms-classes-search-text" class="dtlms-classes-search-text dtlms-without-ajax-load" type="text" value="" placeholder="'.esc_html__('Keywords', 'dtlms').'" />';
			$output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_classtype_field($request, $ajax_load, $column_cnt) {

	$output = '';

	$class_title_singular = apply_filters( 'class_label', 'singular' );

	if($ajax_load) {

		$classfilter_classtype = isset($_REQUEST['classfilter-classtype']) ? $_REQUEST['classfilter-classtype'] : 'all';
       	$output .= '<div class="dtlms-classes-classtype-filter">
                        <div class="dtlms-title">'.sprintf( esc_html__('%1$s Type', 'dtlms'), $class_title_singular ).'</div>
                        <ul>
                        	<li><input type="radio" name="classfilter-classtype" class="classfilter-classtype" value="all" id="classfilter-classtype-all" '.checked('all', $classfilter_classtype, false).' /><label for="classfilter-classtype-all">'.esc_html__('All', 'dtlms').'</label></li>
                            <li><input type="radio" name="classfilter-classtype" class="classfilter-classtype" value="default" id="classfilter-classtype-default" '.checked('default', $classfilter_classtype, false).' /><label for="classfilter-classtype-default">'.esc_html__('Default', 'dtlms').'</label></li>
                            <li><input type="radio" name="classfilter-classtype" class="classfilter-classtype" value="onsite" id="classfilter-classtype-onsite" '.checked('onsite', $classfilter_classtype, false).' /><label for="classfilter-classtype-onsite"">'.esc_html__('Onsite', 'dtlms').'</label></li>
                            <li><input type="radio" name="classfilter-classtype" class="classfilter-classtype" value="online" id="classfilter-classtype-online" '.checked('online', $classfilter_classtype, false).' /><label for="classfilter-classtype-online">'.esc_html__('Online', 'dtlms').'</label></li>';
            $output .= '</ul>';
        $output .= '</div>';        	    

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		    $output .= '<div class="dtlms-classes-classtype-filter">';
					        $output .= '<select class="classfilter-classtype dtlms-without-ajax-load dtlms-chosen-select" name="classfilter-classtype" data-placeholder="'.sprintf( esc_html__('%1$s Type', 'dtlms'), $class_title_singular ).'">';
		                        $output .= '<option value="all">'.esc_html__('All', 'dtlms').'</option>';
		                        $output .= '<option value="default">'.esc_html__('Default', 'dtlms').'</option>';
		                        $output .= '<option value="onsite">'.esc_html__('Onsite', 'dtlms').'</option>';
		                        $output .= '<option value="online">'.esc_html__('Online', 'dtlms').'</option>';				            
							$output .= '</select>';
		    $output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_instructor_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$classfilter_instructor = isset($request['classfilter-instructor']) ? $request['classfilter-instructor'] : array ();
       	$output .= '<div class="dtlms-classes-instructor-filter">
                        <div class="dtlms-title">'.esc_html__('Instructor', 'dtlms').'</div>
                        <ul>';
							$instructors = get_users ( array ('role' => 'instructor') );
					        if ( count( $instructors ) > 0 ) {
					            foreach ($instructors as $instructor) {
									$instructor_id = $instructor->data->ID;
					                $output .= '<li><input type="checkbox" name="classfilter-instructor" class="classfilter-instructor" value="'.$instructor_id.'" id="classfilter-instructor-'.$instructor_id.'" '.checked(in_array($instructor_id, $classfilter_instructor), true, false).' /><label for="classfilter-instructor-'.$instructor_id.'">'.$instructor->data->display_name.'</label></li>';
					            }
					        }
            $output .= '</ul>';
        $output .= '</div>';	    

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		    $output .= '<div class="dtlms-classes-instructor-filter">';
					        $output .= '<select class="classfilter-instructor dtlms-without-ajax-load dtlms-chosen-select" name="classfilter-instructor[]" data-placeholder="'.esc_html__('Instructor', 'dtlms').'" multiple>';
		                        $instructors = get_users ( array ('role' => 'instructor') );
		                        if ( count( $instructors ) > 0 ) {
		                            foreach($instructors as $instructor) {
		                            	$instructor_id = $instructor->data->ID;
		                            	$output .= '<option value="'.$instructor_id.'">'.$instructor->data->display_name.'</option>';
		                            }
		                        }				            
							$output .= '</select>';
		    $output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_cost_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$classfilter_cost = isset($_REQUEST['classfilter-cost']) ? $_REQUEST['classfilter-cost'] : 'all';
       	$output .= '<div class="dtlms-classes-cost-filter">
                        <div class="dtlms-title">'.esc_html__('Cost', 'dtlms').'</div>
                        <ul>
                            <li><input type="radio" name="classfilter-cost" class="classfilter-cost " value="all" id="classfilter-cost-all" '.checked('all', $classfilter_cost, false).' /><label for="classfilter-cost-all">'.esc_html__('All', 'dtlms').'</label></li>
                            <li><input type="radio" name="classfilter-cost" class="classfilter-cost" value="free" id="classfilter-cost-free" '.checked('free', $classfilter_cost, false).' /><label for="classfilter-cost-free"">'.esc_html__('Free', 'dtlms').'</label></li>
                            <li><input type="radio" name="classfilter-cost" class="classfilter-cost" value="paid" id="classfilter-cost-paid" '.checked('paid', $classfilter_cost, false).' /><label for="classfilter-cost-paid">'.esc_html__('Paid', 'dtlms').'</label></li>';
            $output .= '</ul>';
        $output .= '</div>';        	    

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		    $output .= '<div class="dtlms-classes-cost-filter">';
					        $output .= '<select class="classfilter-cost dtlms-without-ajax-load dtlms-chosen-select" name="classfilter-cost" data-placeholder="'.esc_html__('Cost', 'dtlms').'">';
		                        $output .= '<option value="all">'.esc_html__('All', 'dtlms').'</option>';
		                        $output .= '<option value="free">'.esc_html__('Free', 'dtlms').'</option>';
		                        $output .= '<option value="paid">'.esc_html__('Paid', 'dtlms').'</option>';				            
							$output .= '</select>';
		    $output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_startdate_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$classfilter_date = isset($request['classfilter-date']) ? $request['classfilter-date'] : '';
	   	$output .= '<div class="dtlms-classes-date-filter">
	                    <div class="dtlms-title">'.esc_html__('Start Date :', 'dtlms').'</div>
	                    <div class="dtlms-classes-date-filter-holder">
	                    	<input type="text" name="classfilter-date" class="classfilter-date dtlms-datepicker" placeholder="'.esc_html__('Start Date', 'dtlms').'" value="'.$classfilter_date.'" readonly />
	                    </div>
	                </div>';            

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		   	$output .= '<div class="dtlms-classes-date-filter">
		   					<div class="dtlms-classes-date-filter-holder">
		                    	<input type="text" name="classfilter-date" class="classfilter-date dtlms-datepicker dtlms-without-ajax-load" placeholder="'.esc_html__('Start Date', 'dtlms').'" value="" readonly />
		                    </div>
		                </div>';
		$output .= '</div>'; 

	}

	return $output;

}

function dtlms_classes_listing_display_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$classfilter_display = isset($_REQUEST['classfilter-display-default']) ? $_REQUEST['classfilter-display-default'] : 'grid';
		if($classfilter_display == 'grid') {
			$grid_class = 'active';
			$list_class = '';
		} else if($classfilter_display == 'list') {
			$grid_class = '';
			$list_class = 'active';
		}
       	$output .= '<div class="dtlms-classes-display-filter">
                        <a class="dtlms-classes-display-type grid '.$grid_class.'" data-displaytype="grid"><span></span>'.esc_html__('Grid', 'dtlms').'</a>
                        <a class="dtlms-classes-display-type list '.$list_class.'" data-displaytype="list"><span></span>'.esc_html__('List', 'dtlms').'</a>
                    </div>';


	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
	       	$output .= '<div class="dtlms-classes-display-filter">
					        <select class="classfilter-display dtlms-without-ajax-load dtlms-chosen-select" name="classfilter-display" data-placeholder="'.esc_html__('Display Type', 'dtlms').'">
					        	<option value="grid">'.esc_html__( 'Grid', 'dtlms' ).'</option>
					            <option value="list">'.esc_html__( 'List', 'dtlms' ).'</option>
							</select>';
	        $output .= '</div>';
	    $output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_orderby_field($request, $ajax_load, $column_cnt) {

	$output = '';

	$class_title_plural = apply_filters( 'class_label', 'plural' );

	if($ajax_load) {

		$classfilter_orderby = isset($_REQUEST['classfilter-orderby']) ? $_REQUEST['classfilter-orderby'] : '';
       	$output .= '<div class="dtlms-classes-orderby-filter">
                        <label>'.esc_html__('Order by :', 'dtlms').'</label>
				        <select class="classfilter-orderby" name="classfilter-orderby" data-placeholder="'.esc_html__('Select Order', 'dtlms').'">
				        	<option value="" '.selected('', $classfilter_orderby, false).'>'.esc_html__( 'Select Order', 'dtlms' ).'</option>
				        	<option value="recent-classes" '.selected('recent-classes', $classfilter_orderby, false).'>'.sprintf( esc_html__('Recent %1$s', 'dtlms'), $class_title_plural ).'</option>
				            <option value="highest-rated" '.selected('highest-rated', $classfilter_orderby, false).'>'.esc_html__( 'Highest Rated', 'dtlms' ).'</option>
				            <option value="most-members" '.selected('most-members', $classfilter_orderby, false).'>'.esc_html__( 'Most Members', 'dtlms' ).'</option>
				            <option value="alphabetical" '.selected('alphabetical', $classfilter_orderby, false).'>'.esc_html__( 'Alphabetical', 'dtlms' ).'</option>
						</select>';
        $output .= '</div>';

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$classfilter_orderby = isset($_REQUEST['classfilter-orderby']) ? $_REQUEST['classfilter-orderby'] : '';
		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
	       	$output .= '<div class="dtlms-classes-orderby-filter">
					        <select class="classfilter-orderby dtlms-without-ajax-load dtlms-chosen-select" name="classfilter-orderby" data-placeholder="'.esc_html__('Select Order', 'dtlms').'">
					        	<option value="recent-classes">'.sprintf( esc_html__('Recent %1$s', 'dtlms'), $class_title_plural ).'</option>
					            <option value="highest-rated">'.esc_html__( 'Highest Rated', 'dtlms' ).'</option>
					            <option value="most-members">'.esc_html__( 'Most Members', 'dtlms' ).'</option>
					            <option value="alphabetical">'.esc_html__( 'Alphabetical', 'dtlms' ).'</option>
							</select>';
	        $output .= '</div>';
        $output .= '</div>';

	}

	return $output;

}

function dtlms_classes_listing_content($classes_listing_options) {

	$output = '';

	$class_carousel_attributes = $class_listing_attributes = array ();
	$holder_class = $container_class = $class_carousel_attributes_string = $class_listing_attributes_string = '';

	$ajax_load = true;
	if($classes_listing_options['listing-output-page'] != '') {
		$ajax_load = false;
	}

	$disable_all_filters = false;
	$enable_fullwidth = false;	

	if($classes_listing_options['class'] != '') {
		$holder_class .= ' '.$classes_listing_options['class'];
	}
		
	if($ajax_load) {

		if($classes_listing_options['disable-all-filters'] == 'true') {
			$disable_all_filters = true;
		}	

		if($classes_listing_options['enable-fullwidth'] == 'true') {
			$enable_fullwidth = true;
		}		

		if($classes_listing_options['enable-carousel'] == 'true') {

			array_push($class_carousel_attributes, 'data-enablecarousel="true"');
			array_push($class_carousel_attributes, 'data-carouseleffect="'.$classes_listing_options['carousel-effect'].'"');
			array_push($class_carousel_attributes, 'data-carouselautoplay="'.$classes_listing_options['carousel-autoplay'].'"');
			array_push($class_carousel_attributes, 'data-carouselslidesperview="'.$classes_listing_options['carousel-slidesperview'].'"');
			array_push($class_carousel_attributes, 'data-carouselloopmode="'.$classes_listing_options['carousel-loopmode'].'"');
			array_push($class_carousel_attributes, 'data-carouselmousewheelcontrol="'.$classes_listing_options['carousel-mousewheelcontrol'].'"');
			array_push($class_carousel_attributes, 'data-carouselbulletpagination="'.$classes_listing_options['carousel-bulletpagination'].'"');
			array_push($class_carousel_attributes, 'data-carouselarrowpagination="'.$classes_listing_options['carousel-arrowpagination'].'"');
			array_push($class_carousel_attributes, 'data-carouselspacebetween="'.$classes_listing_options['carousel-spacebetween'].'"');

			$container_class .= ' swiper-wrapper';

		} else {

			array_push($class_listing_attributes, 'data-enablecarousel="false"');

			if($classes_listing_options['apply-isotope'] == 'true') {
				$container_class .= ' dtlms-apply-isotope';
			}

		}

		$class_carousel_attributes_string = '';
		if(!empty($class_carousel_attributes)) {
			$class_carousel_attributes_string = implode(' ', $class_carousel_attributes);
		}


		array_push($class_listing_attributes, 'data-postperpage="'.$classes_listing_options['post-per-page'].'"');
		array_push($class_listing_attributes, 'data-columns="'.$classes_listing_options['columns'].'"');
		array_push($class_listing_attributes, 'data-applyisotope="'.$classes_listing_options['apply-isotope'].'"');

		if($classes_listing_options['disable-all-filters'] == 'true') {
			array_push($class_listing_attributes, 'data-disablefilters="true"');
		} else {
			array_push($class_listing_attributes, 'data-disablefilters="false"');
		}

		array_push($class_listing_attributes, 'data-defaultfilter="'.$classes_listing_options['default-filter'].'"');

		$display_type = 'grid';
		if($classes_listing_options['default-display-type']) {
			$display_type = $classes_listing_options['default-display-type'];
		}		
		array_push($class_listing_attributes, 'data-defaultdisplaytype="'.$display_type.'"');
		array_push($class_listing_attributes, 'data-classitemids="'.$classes_listing_options['class-item-ids'].'"');
		array_push($class_listing_attributes, 'data-instructorids="'.$classes_listing_options['instructor-ids'].'"');
		array_push($class_listing_attributes, 'data-enablefullwidth="'.$classes_listing_options['enable-fullwidth'].'"');
		array_push($class_listing_attributes, 'data-type="'.$classes_listing_options['type'].'"');

		if(!empty($class_listing_attributes)) {
			$class_listing_attributes_string = implode(' ', $class_listing_attributes);
		}

		if(isset($_REQUEST['classfilter-display']) && $_REQUEST['classfilter-display'] != '') {
			$container_display_type = $_REQUEST['classfilter-display'];
			$_REQUEST['classfilter-display-default'] = $_REQUEST['classfilter-display'];
		} else {
			$container_display_type = $display_type;
			$_REQUEST['classfilter-display-default'] = $display_type;
		}

	} else {

		$holder_class .= ' dtlms-without-ajax-load';
		$disable_all_filters = false;
		$container_display_type = '';

	}


	$output .= '<div class="dtlms-classes-listing-holder '.$container_display_type.' '.$holder_class.'" '.$class_listing_attributes_string.' '.$class_carousel_attributes_string.'>';


			if($ajax_load) {
				if(!$disable_all_filters && !$enable_fullwidth) {
					$output .= '<div class="dtlms-column dtlms-one-third first">';
				}
			} else {
				$output .= '<form name="dtlmsClassesListingSearchForm" action="'.get_permalink($classes_listing_options['listing-output-page']).'" method="post">';
			}	

				if(!$disable_all_filters) {

					$output .= '<div class="dtlms-classes-listing-filters">';

								$column_cnt = 0;
								if($classes_listing_options['enable-search-filter'] == 'true') {
									$output .= dtlms_classes_listing_search_field($_REQUEST, $ajax_load, $column_cnt);
									$column_cnt++;
								}

				 				if($classes_listing_options['enable-classtype-filter'] == 'true') {
				 					$output .= dtlms_classes_listing_classtype_field($_REQUEST, $ajax_load, $column_cnt);
				 					$column_cnt++;
				                } 

				  				if($classes_listing_options['enable-instructor-filter'] == 'true') {
				  					$output .= dtlms_classes_listing_instructor_field($_REQUEST, $ajax_load, $column_cnt);
				  					$column_cnt++;
				                }

				  				if($classes_listing_options['enable-cost-filter'] == 'true') {
				  					if($column_cnt == 3) {
				  						$column_cnt = 0;
				  					}
				  					$output .= dtlms_classes_listing_cost_field($_REQUEST, $ajax_load, $column_cnt);
				  					$column_cnt++;
				                }

				  				if($classes_listing_options['enable-date-filter'] == 'true') {
				  					if($column_cnt == 3) {
				  						$column_cnt = 0;
				  					}			  					
				  					$output .= dtlms_classes_listing_startdate_field($_REQUEST, $ajax_load, $column_cnt);
				  					$column_cnt++;
				                }	

								if(!$ajax_load) {

									if($classes_listing_options['enable-display-filter'] == 'true') {
					  					if($column_cnt == 3) {
					  						$column_cnt = 0;
					  					}									
										$output .= dtlms_classes_listing_display_field($_REQUEST, $ajax_load, $column_cnt);
										$column_cnt++;
								    }

									if($classes_listing_options['enable-orderby-filter'] == 'true') {
					  					if($column_cnt == 3) {
					  						$column_cnt = 0;
					  					}									
										$output .= dtlms_classes_listing_orderby_field($_REQUEST, $ajax_load, $column_cnt);
										$column_cnt++;
								    }

								}

				    $output .= '</div>';

				}

			if($ajax_load) {

				if(!$disable_all_filters) {

					if(!$enable_fullwidth) {

						$output .= '</div>';
						$output .= '<div class="dtlms-column dtlms-two-third">';

					}

					if($classes_listing_options['enable-display-filter'] == 'true' || $classes_listing_options['enable-orderby-filter'] == 'true') {

						$output .= '<div class="dtlms-classes-listing-rightside-filter">';

							if($classes_listing_options['enable-display-filter'] == 'true') {
								$output .= dtlms_classes_listing_display_field($_REQUEST, $ajax_load, 0);
			                }

							if($classes_listing_options['enable-orderby-filter'] == 'true') {
								$output .= dtlms_classes_listing_orderby_field($_REQUEST, $ajax_load, 0);
			                }

			            $output .= '</div>';

			        }

			    }

				    if($classes_listing_options['enable-carousel'] == 'true') {
				    	$output .= '<div class="dtlms-classes-swiper-listing" '.$class_carousel_attributes_string.'>';
				    }

					    $output .= '<div class="dtlms-classes-listing-containers '.$container_display_type.' '.$container_class.'">'.dtlms_generate_loader_html(false).'</div>';

					if($classes_listing_options['enable-carousel'] == 'true') {

						if($classes_listing_options['carousel-bulletpagination'] == 'true' || $classes_listing_options['carousel-arrowpagination'] == 'true') {
							$output .= '<div class="dtlms-swiper-pagination-holder">';
								if($classes_listing_options['carousel-bulletpagination'] == 'true') {
									$output .= '<div class="dtlms-swiper-bullet-pagination"></div>';	
								}
								if($classes_listing_options['carousel-arrowpagination'] == 'true') {				
									$output .= '<div class="dtlms-swiper-arrow-pagination">';
										$output .= '<a href="#" class="dtlms-swiper-arrow-prev">'.esc_html__('Prev', 'dtlms').'</a>';
										$output .= '<a href="#" class="dtlms-swiper-arrow-next">'.esc_html__('Next', 'dtlms').'</a>';
									$output .= '</div>';
								}						
							$output .= '</div>';
						}

						$output .= '</div>';

					}

				if(!$disable_all_filters && !$enable_fullwidth) {
				    $output .= '</div>';
				}

		    } else {

		    	$class_title_plural = apply_filters( 'class_label', 'plural' );

				$output .= '<input type="submit" name="dtlms-classes-listing-searchform-submit" class="dtlms-classes-listing-searchform-submit" value="'.sprintf( esc_html__(' Search %1$s', 'dtlms'), $class_title_plural ).'" />';

				$output .= '</form>';

			}

	$output .= '</div>';

    return $output;

}


add_action( 'wp_ajax_dtlms_generate_classes_listing', 'dtlms_generate_classes_listing' );
add_action( 'wp_ajax_nopriv_dtlms_generate_classes_listing', 'dtlms_generate_classes_listing' );
function dtlms_generate_classes_listing() {

	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;

	$disable_filters = $_REQUEST['disable_filters'];
	$enable_fullwidth = $_REQUEST['enable_fullwidth'];

	$enable_carousel = $_REQUEST['enable_carousel'];
	$carousel_class = '';
	if($enable_carousel == 'true') {
		$carousel_class = 'swiper-slide';
	}

	$post_per_page = $_REQUEST['post_per_page'];
	$columns = $_REQUEST['columns'];

	if(isset($_REQUEST['display_type']) && $_REQUEST['display_type'] != '') {
		$display_type = $_REQUEST['display_type'];
	} else {
		$display_type = isset($_REQUEST['default_display_type']) ? $_REQUEST['default_display_type'] : 'grid';
	}

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'type1';

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	if($enable_carousel == 'true') {
		$column_class = '';
		$post_per_page = -1;
	} else {
		if($columns == 3) {
			$column_class = 'dtlms-column dtlms-one-third';
		} else if($columns == 2) {
			$column_class = 'dtlms-column dtlms-one-half';
		} else {
			$column_class = 'dtlms-column dtlms-one-column';
		}
		if($display_type == 'list') {
			$column_class = 'dtlms-column dtlms-one-column';
		}
		if($enable_fullwidth != 'true' && $display_type == 'grid' && $disable_filters == 'false' && $columns == 3) {
			$column_class = 'dtlms-column dtlms-one-half';
		}		
	}

	$category = array ();

	$output = '';

	$args = array ( 
				'offset' => $offset, 
				'paged' => $current_page ,
				'posts_per_page' => $post_per_page, 
				'post_type' => 'dtlms_classes',
				'meta_query'=>array(), 
				'tax_query'=>array(),
				'post_status'=>'publish'
			);

	if($disable_filters != 'true') {

		$search_text = $_REQUEST['search_text'];
		$order_by = $_REQUEST['order_by'];
		$class_type = $_REQUEST['class_type'];
		$class_type = isset($class_type[0]) ? $class_type[0] : '';
		$instructor = $_REQUEST['instructor'];
		$cost_type = $_REQUEST['cost_type'];
		$cost_type = isset($cost_type[0]) ? $cost_type[0] : '';
		$start_date = $_REQUEST['start_date'];
		$start_date = (isset($start_date) && $start_date != '') ? $start_date : '';
		

		// Search Filter
		if($search_text != '') {
			$args['s'] = $search_text;
		}

		// OrderBy Filter
		if($order_by == 'recent-classes') {

			$args['orderby'] = 'date';

		} else if($order_by == 'highest-rated') {

			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'average-ratings';

		} else if($order_by == 'most-members') {

			$args['orderby'] = 'meta_value';
			$args['meta_key'] = 'purchased_users';

		} else if($order_by == 'alphabetical') {

			$args['orderby'] = 'title';
			$args['order'] = 'ASC';

		}


		// Class Type Filter
		if($class_type == 'onsite') {
			
			$args['meta_query'][] = array (
										'key'     => 'dtlms-class-type',
										'value'   => 'onsite',
										'compare' => '='
									);
							
		} else if($class_type == 'online') {
			
			$args['meta_query'][] = array (
										'key'     => 'dtlms-class-type',
										'value'   => 'online',
										'compare' => '='
									);

		} else if($class_type == 'default') {
			
			$args['meta_query'][] = array (
										'key'     => 'dtlms-class-type',
										'value'   => 'default',
										'compare' => '='
									);			
			
		} else if($class_type == 'all') {
			
			$args['meta_query'][] = array (
										'key'     => 'dtlms-class-type',
										'value'   => array ( 'onsite', 'online', 'default' ),
										'compare' => 'IN'
									);

		}


		// Instructor Filter
		if(!empty($instructor)) {
			$args['author__in'] = $instructor;
		}

		// Cost Filter
		if($cost_type == 'paid') {
			
			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => 0,
										'type'    => 'numeric',
										'compare' => '>'
									);
							
		} else if($cost_type == 'free') {
			
			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => '',
										'compare' => '='
									);
							
		} 

		// Date Filter
		if($start_date != '') {
			$date_compare_format = date('Ymd', strtotime($start_date));
			$args['meta_query'][] = array (
										'key'     => 'class-start-date-compare-format',
										'value'   => $date_compare_format,
										'compare' => '>='
									);

			$args['meta_query'][] = array (
										'key'     => 'dtlms-class-type',
										'value'   => 'onsite',
										'compare' => '='
									);			
		}

	} else {

		$default_filter = $_REQUEST['default_filter'];
		$class_item_ids = $_REQUEST['class_item_ids'];
		$instructor_ids = $_REQUEST['instructor_ids'];

		// Class Item Ids Filter
		if($class_item_ids != '') {
			$class_item_ids_arr = explode(',', $class_item_ids);
			$args['post__in'] = $class_item_ids_arr;
		}

		// Default Filters
		if($default_filter == 'upcoming-classes') {

			$args['meta_query'][] = array (
										'key'     => 'class-start-date-compare-format',
										'value'   => current_time('Ymd'),
										'compare' => '>=',
										'type' => 'DATE'
									);	

		} else if($default_filter == 'recent-classes') {

			$args['orderby'] = 'date';

		} else if($default_filter == 'highest-rated-classes') {

			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'average-ratings';

		} else if($default_filter == 'most-membered-classes') {

			$args['orderby'] = 'meta_value';
			$args['meta_key'] = 'purchased_users';

		} else if($default_filter == 'paid-classes') {

			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => 0,
										'type'    => 'numeric',
										'compare' => '>'
									);

		} else if($default_filter == 'free-classes') {

			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => '',
										'compare' => '='
									);

		}

		// Instructor Filter
		if(!empty($instructor_ids)) {
			$instructor_ids_arr = explode(',', $instructor_ids);
			$args['author__in'] = $instructor_ids_arr;
		}

	}

	$apply_isotope = $_REQUEST['apply_isotope'];

	$data_listing_attributes = array ();
	$data_listing_attributes['column'] = $columns;
	$data_listing_attributes['column_class'] = $column_class;
	$data_listing_attributes['carousel_class'] = $carousel_class;
	$data_listing_attributes['display_type'] = $display_type;
	$data_listing_attributes['apply_isotope'] = $apply_isotope;
	$data_listing_attributes['type'] = $type;

					
	$classes_query = new WP_Query( $args );

	if ( $classes_query->have_posts() ) :

		if($apply_isotope == 'true'):
			$output .= '<div class="dtlms-classes-listing-items">';
				$output .= '<div class="grid-sizer '.$column_class.'"></div>';
		endif;

		$i = 1;
		while ( $classes_query->have_posts() ) : 
			$classes_query->the_post();

			if($enable_carousel == 'true') {
				$first_class = '';
			} else {
				if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
				if($i == $columns) { $i = 1; } else { $i = $i + 1; }				
			}

			$data_listing_attributes['first_class'] = $first_class;

			$output .= dtlms_class_data_listing($user_id, $data_listing_attributes);

		endwhile;
		wp_reset_postdata();

		if($apply_isotope == 'true'):
			$output .= '</div>';
		endif;

	else :

		$output .= '<div class="dtlms-classes-listing-norecords">'.esc_html__('No records found!', 'dtlms').'</div>';

	endif;

	if($enable_carousel != 'true'):
		$output .= dtlms_class_listing_pagination($classes_query, $current_page);
	endif;

	$output .= dtlms_generate_loader_html(false);	

	echo $output;

	die();

}

function dtlms_class_data_listing($user_id, $data_listing_attributes) {

	$output = '';

	$class_id = get_the_ID();
	$class_title = get_the_title();
	$class_permalink = get_permalink();

	extract($data_listing_attributes);


	$average_rating = get_post_meta($class_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$display_type = $display_type.'-item';

	$item_classes = array ('dtlms-classlist-item-wrapper');
	array_push($item_classes, $column_class, $carousel_class, $display_type, $type);
	if($first_class != '') {
		array_push($item_classes, $first_class);
	}

	$class_type = get_post_meta($class_id, 'dtlms-class-type', true);
	if($class_type != '') {
		array_push($item_classes, $class_type);
	}

	$class_featured = get_post_meta($class_id, 'dtlms-class-featured', true);

	$seats_available = dtlms_calculate_class_available_seats($class_id);
	if($seats_available < 0) {
		$seats_available = 0;
	}	

	$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);
	$class_start_date = get_post_meta($class_id, 'dtlms-class-start-date', true);

	$product = dtlms_get_product_object($class_id);				
	$woo_price = dtlms_get_item_price_html($product);

	$free_class = false;
	if($woo_price == '') {
		$free_class = true;
	}

	$active_package_classes = dtlms_get_user_active_packages($user_id, 'classes');
	$active_package_classes = (is_array($active_package_classes) && !empty($active_package_classes)) ? $active_package_classes : array();

	$assigned_classes = get_user_meta($user_id, 'assigned_classes', true);
	$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();

	$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
	$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();

	$purchased_paid_class = false;
	if(in_array($class_id, $active_package_classes) || in_array($class_id, $assigned_classes) || in_array($class_id, $purchased_classes)) {
		$purchased_paid_class = true;
	}

	$started_classes = get_user_meta($user_id, 'started_classes', true);
	$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();

	$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
	$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();

	$completed_classes = get_user_meta($user_id, 'completed_classes', true);
	$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();


	$output .= '<div class="'.implode(' ', get_post_class($item_classes, $class_id)).'">';

		if($type == 'type1') {

			$output .= '<div class="dtlms-classlist-thumb">';
				$output .= dtlms_class_listing_thumb($class_id, $class_title, $class_permalink, $display_type, $column);
				$output .= dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'archive');
				$output .= dtlms_class_listing_single_class_type($class_id, $class_type);
				$output .= dtlms_class_listing_single_certificatenbadge($class_id);
			$output .= '</div>';
			$output .= '<div class="dtlms-classlist-details">';
				$output .= dtlms_class_listing_single_featured($class_id);
				$output .= dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes);
				$output .= dtlms_class_listing_total_courses($class_id, 'type1');
				$output .= dtlms_class_listing_title($class_id, $class_title, $class_permalink);
				$output .= dtlms_class_listing_description($class_id);
				$output .= dtlms_class_listing_author($class_id);
			$output .= '</div>';
			

		} else if($type == 'type2') {

			$output .= '<div class="dtlms-classlist-thumb">';
				$output .= dtlms_class_listing_thumb($class_id, $class_title, $class_permalink, $display_type, $column);
				$output .= dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'archive');
				$output .= dtlms_class_listing_single_class_type($class_id, $class_type);
				$output .= dtlms_class_listing_total_courses($class_id, $type);
				$output .= dtlms_class_listing_single_certificatenbadge($class_id);
			$output .= '</div>';
			$output .= '<div class="dtlms-classlist-details">';
				$output .= dtlms_class_listing_single_featured($class_id);
				$output .= dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes);
				$output .= dtlms_class_listing_title($class_id, $class_title, $class_permalink);
				$output .= dtlms_class_listing_review($class_id, $type);
				if($class_content_options == 'course') {
					$class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
					if(is_array($class_courses) && !empty($class_courses)) {
						$output .= '<div class="dtlms-classlist-description">';
							$output .= '<ul>';
								foreach($class_courses as $class_course) {
									$output .= '<li>'.get_the_title($class_course).'</li>';
								}
							$output .= '</ul>';
						$output .= '</div>';
					}
				} else {
					$output .= dtlms_class_listing_description($class_id);
				}
				$output .= '<div class="dtlms-classlist-bottom-section">';
					$output .= '<div class="dtlms-classlist-bottom-section-left">';
						$output .= dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price);
					$output .= '</div>';
					$output .= '<div class="dtlms-classlist-bottom-section-right">';
						$output .= '<a href="'.esc_url($class_permalink).'">'.esc_html__('View Details', 'dtlms').'</a>';
					$output .= '</div>';					
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type3') {

			$output .= '<div class="dtlms-classlist-thumb">';
				$output .= dtlms_class_listing_thumb($class_id, $class_title, $class_permalink, $display_type, $column);
				$output .= dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, 'archive');
				$output .= dtlms_class_listing_single_class_type($class_id, $class_type);
				$output .= dtlms_class_listing_single_certificatenbadge($class_id);
			$output .= '</div>';
			$output .= '<div class="dtlms-classlist-details">';
				$output .= dtlms_class_listing_single_featured($class_id);
				$output .= dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes);
				$output .= dtlms_class_listing_title($class_id, $class_title, $class_permalink);
				$output .= dtlms_class_listing_total_courses($class_id, $type);
				$output .= dtlms_class_listing_author($class_id);
				$output .= dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price);
				$output .= '<div class="dtlms-classlist-bottom-section">';
					$output .= '<div class="dtlms-classlist-bottom-section-left">';
						$output .= dtlms_class_listing_review($class_id, $type);
					$output .= '</div>';
					$output .= '<div class="dtlms-classlist-bottom-section-right">';
						$output .= '<a href="'.esc_url($class_permalink).'">'.esc_html__('View Details', 'dtlms').'</a>';
					$output .= '</div>';					
				$output .= '</div>';
			$output .= '</div>';

		}

	$output .= '</div>';


	return $output;

}

function dtlms_class_listing_pagination($dtlms_wpquery, $current_page) {

	$output = '';
	$total_posts = $dtlms_wpquery->found_posts;

	if($dtlms_wpquery->max_num_pages > 1) {

		$pages = ($dtlms_wpquery->max_num_pages) ? $dtlms_wpquery->max_num_pages : 1;
		
		$output .= '<div class="dtlms-pagination dtlms-ajax-pagination">';
				
			if($current_page > 1) {
				$output .= '<div class="prev-post"><a href="#" data-currentpage="'.$current_page.'"><span class="fa fa-caret-left"></span>&nbsp;'.esc_html__('Prev', 'dtlms').'</a></div>';
			}

			$output .= paginate_links ( array (
						  'base' 		 => '#',
						  'format' 		 => '',
						  'current' 	 => $current_page,
						  'type'     	 => 'list',
						  'end_size'     => 2,
						  'mid_size'     => 3,
						  'prev_next'    => false,
						  'total' 		 => $dtlms_wpquery->max_num_pages 
					  ) );

			if ($current_page < $pages) {
				$output .= '<div class="next-post"><a href="#" data-currentpage="'.$current_page.'">'.esc_html__('Next', 'dtlms').'&nbsp;<span class="fa fa-caret-right"></span></a></div>';
			}

		$output .= '</div>';

    }

    return $output;

}


?>