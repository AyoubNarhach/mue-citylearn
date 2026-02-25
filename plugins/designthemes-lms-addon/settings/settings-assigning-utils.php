<?php

if(!function_exists('dtlms_get_courses_assign_students_content')) {

	function dtlms_get_courses_assign_students_content() {

		$output = '';

		$output .= '<div class="dtlms-settings-assign-students-container">';

			$output .= '<div class="dtlms-column dtlms-one-fifth first">';

				$output .= '<label>'.esc_html__('Choose course', 'dtlms').'</label>';

			$output .= '</div>';


			$output .= '<div class="dtlms-column dtlms-four-fifth">';

			    $output .= '<select class="dtlms-assigning-students" name="dtlms-assigning-students" data-placeholder="'.esc_html__('Choose Course ...', 'dtlms').'" class="dtlms-chosen-select">';

					$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';

					$args = array (
					    'post_type'         =>  'dtlms_courses',
					    'posts_per_page' 	=> -1
				    );

					$args['meta_query'][] = array (
												'key'     => '_regular_price',
												'value'   => 0,
												'type'    => 'numeric',
												'compare' => '>'
											);

				    $courses = get_posts($args);

				    if(isset($courses) && !empty($courses)) {
				    	foreach( $courses as $course ) {

				    		$course_id = $course->ID;
				    		$output .= '<option value="' . esc_attr( $course_id ) . '"' . selected( $course_id, '', false ) . '>' . get_the_title($course_id) . '</option>';

				    	}
				    }

			    $output .= '</select>';

			$output .= '</div>';

			$output .= dtlms_generate_loader_html(false);

			$output .= '<div class="dtlms-assign-studentstocourse-container"></div>';


		$output .= '</div>';

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_assigning_load_students_data', 'dtlms_assigning_load_students_data' );
add_action( 'wp_ajax_nopriv_dtlms_assigning_load_students_data', 'dtlms_assigning_load_students_data' );
function dtlms_assigning_load_students_data() {

	$course_id = $_REQUEST['course_id'];

	$output = '';

	if($course_id != '') {

		// Pagination script Start
		$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
		$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
		$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

		if($dashboard_function_call != '') {
			$function_call = $dashboard_function_call;
		} else {
			$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_assigning_load_students_data';
		}

		if($dashboard_output_div != '') {
			$output_div = $dashboard_output_div;
		} else {
			$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-assign-studentstocourse-container';
		}
		// Pagination script End


		$output .= '<form name="formAssignStudents" class="formAssignStudents" method="post">';

			$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Student', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Purchased', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Assigned', 'dtlms').'</th>
							</tr>';

				$students = get_users ( array ('role' => 'student' ) );	
				$students_filtered = array_slice($students, $offset, $post_per_page, true);			
				if(is_array($students_filtered) && !empty($students_filtered)) {

					$i = $offset+1;
					foreach ( $students_filtered as $student ) {
						setup_postdata( $student );

						$student_id = $student->data->ID;

		                $purchase_checked = '';
		                $purchase_switchclass = 'checkbox-switch-off';

						$active_package_courses = dtlms_get_user_active_packages($student_id, 'courses');
						$active_package_courses = (is_array($active_package_courses) && !empty($active_package_courses)) ? $active_package_courses : array();	                
						$purchased_courses = get_user_meta($student_id, 'purchased_courses', true);
						if((is_array($purchased_courses) && in_array($course_id, $purchased_courses)) || (is_array($active_package_courses) && in_array($course_id, $active_package_courses))) {
		                    $purchase_checked = 'checked="checked"';
		                    $purchase_switchclass = 'checkbox-switch-on';
						}

		                $assigned_checked = '';
		                $assigned_switchclass = 'checkbox-switch-off';
						$assigned_courses = get_user_meta($student_id, 'assigned_courses', true);
						if(is_array($assigned_courses) && in_array($course_id, $assigned_courses)) {
		                    $assigned_checked = 'checked="checked"';
		                    $assigned_switchclass = 'checkbox-switch-on';
						}				

						$output .= '<tr>
										<td>'.$i.'</td>
										<td>'.get_the_author_meta('display_name', $student_id).'</td>
										<td>'
											.'<div class="dtlms-checkbox-switch disabled '.$purchase_switchclass.'"></div>'.
										'</td>
										<td>'
											.'<div data-for="assign-students-to-course-'.$student_id.'" class="dtlms-checkbox-switch '.$assigned_switchclass.'"></div>'
											.'<input id="assign-students-to-course-'.$student_id.'" class="assign-students-to-course hidden" type="checkbox" name="assign-students-to-course" value="'.$student_id.'" '.$assigned_checked.' />'.
										'</td>
									</tr>';

						$i++;

					}

				} else {
					$output .= '<tr>
									<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
								</tr>';			
				}

			$output .= '</table>';

		$output .= '</form>';


		$output .= '<div class="dtlms-assign-students-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-assign-students-settings small" data-courseid="'.$course_id.'">'.esc_html__('Assign', 'dtlms').'</a>';	


		wp_reset_postdata();

		// Pagination script Start
		$students_count = count($students);
		$max_num_pages = ceil($students_count / $post_per_page);

		$item_ids['course_id'] = $course_id;

		$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
		// Pagination script End

	} else {

		$output .= esc_html__('Please choose course!', 'dtlms');		

	}

	echo $output;

	die();

}

add_action( 'wp_ajax_dtlms_save_assign_students_settings', 'dtlms_save_assign_students_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_assign_students_settings', 'dtlms_save_assign_students_settings' );
function dtlms_save_assign_students_settings() {

	$course_id = $_REQUEST['course_id'];
	$student_ids = is_array($_REQUEST['student_ids']) && !empty($_REQUEST['student_ids']) ? $_REQUEST['student_ids'] : array ();

	$output = '';

	if($course_id != '') {

		$students = get_users ( array ('role' => 'student') );
        if ( count( $students ) > 0 ) {
            foreach ($students as $student) {		

            	$student_id = $student->data->ID;

            	if(in_array($student_id, $student_ids)) {
					$assigned_courses = get_user_meta($student_id, 'assigned_courses', true);
					$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();
					array_push($assigned_courses, $course_id);
					update_user_meta($student_id, 'assigned_courses', array_unique($assigned_courses));

					// Notification & Mail
					do_action('dtlms_poc_course_assigned', $course_id, $student_id);					
            	} else {
					$assigned_courses = get_user_meta($student_id, 'assigned_courses', true);
					$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();
					if(in_array($course_id, $assigned_courses)) {
					    unset($assigned_courses[array_search($course_id, $assigned_courses)]);
					}				
					update_user_meta($student_id, 'assigned_courses', array_unique($assigned_courses));
            	}

			}
		}

		update_post_meta($course_id, 'assigned_users', array_unique($student_ids));	

		$output .= esc_html__('Students assigned successfully!', 'dtlms');

	} else {

		$output .= esc_html__('Something went wrong!', 'dtlms');

	}
	
	echo $output;

	die();

}


if(!function_exists('dtlms_get_courses_assign_courses_content')) {

	function dtlms_get_courses_assign_courses_content() {

		$output = '';

		$output .= '<div class="dtlms-settings-assign-courses-container">';

			$output .= '<div class="dtlms-column dtlms-one-fifth first">';

				$output .= '<label>'.esc_html__('Choose student', 'dtlms').'</label>';

			$output .= '</div>';


			$output .= '<div class="dtlms-column dtlms-four-fifth">';

			    $output .= '<select class="dtlms-assigning-courses" name="dtlms-assigning-courses" data-placeholder="'.esc_html__('Choose Student ...', 'dtlms').'" class="dtlms-chosen-select">';

					$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';

					$students = get_users ( array ('role' => 'student') );
			        if ( count( $students ) > 0 ) {
			            foreach ($students as $student) {

							$student_id = $student->data->ID;

				    		$output .= '<option value="' . esc_attr( $student_id ) . '"' . selected( $student_id, '', false ) . '>' . esc_html( $student->data->display_name ) . '</option>';

				    	}
				    }

			    $output .= '</select>';

			$output .= '</div>';

			$output .= dtlms_generate_loader_html(false);

			$output .= '<div class="dtlms-assign-coursestostudent-container"></div>';


		$output .= '</div>';

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_assigning_load_courses_data', 'dtlms_assigning_load_courses_data' );
add_action( 'wp_ajax_nopriv_dtlms_assigning_load_courses_data', 'dtlms_assigning_load_courses_data' );
function dtlms_assigning_load_courses_data() {

	$student_id = $_REQUEST['student_id'];

	$output = '';

	if($student_id != '') {

		// Pagination script Start
		$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
		$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
		$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

		if($dashboard_function_call != '') {
			$function_call = $dashboard_function_call;
		} else {
			$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_assigning_load_courses_data';
		}

		if($dashboard_output_div != '') {
			$output_div = $dashboard_output_div;
		} else {
			$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-assign-coursestostudent-container';
		}
		// Pagination script End

		$output .= '<form name="formAssignCourses" class="formAssignCourses" method="post">';

			$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Course', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Purchased', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Assigned', 'dtlms').'</th>
							</tr>';

				$args = array (
							'offset' => $offset,
							'paged' => $current_page,
							'posts_per_page' => $post_per_page, 				
						    'post_type' =>  'dtlms_courses',
					    );
				$args['meta_query'][] = array (
											'key'     => '_regular_price',
											'value'   => 0,
											'type'    => 'numeric',
											'compare' => '>'
										);

			    $courses = get_posts($args);
			    if(isset($courses) && !empty($courses)) {
			    	$i = $offset+1;
			    	foreach( $courses as $course ) {

			    		$course_id = $course->ID;

		                $purchase_checked = '';
		                $purchase_switchclass = 'checkbox-switch-off';

						$active_package_courses = dtlms_get_user_active_packages($student_id, 'courses');
						$active_package_courses = (is_array($active_package_courses) && !empty($active_package_courses)) ? $active_package_courses : array();	                
						$purchased_users = get_post_meta($course_id, 'purchased_users', true);
						if((is_array($purchased_users) && in_array($student_id, $purchased_users)) || (is_array($active_package_courses) && in_array($course_id, $active_package_courses))) {
		                    $purchase_checked = 'checked="checked"';
		                    $purchase_switchclass = 'checkbox-switch-on';
						}

		                $assigned_checked = '';
		                $assigned_switchclass = 'checkbox-switch-off';
						$assigned_users = get_post_meta($course_id, 'assigned_users', true);
						if(is_array($assigned_users) && in_array($student_id, $assigned_users)) {
		                    $assigned_checked = 'checked="checked"';
		                    $assigned_switchclass = 'checkbox-switch-on';
						}				

						$output .= '<tr>
										<td>'.$i.'</td>
										<td>'.get_the_title($course_id).'</td>
										<td>'
											.'<div class="dtlms-checkbox-switch disabled '.$purchase_switchclass.'"></div>'.
										'</td>
										<td>'
											.'<div data-for="assign-courses-to-student-'.$course_id.'" class="dtlms-checkbox-switch '.$assigned_switchclass.'"></div>'
											.'<input id="assign-courses-to-student-'.$course_id.'" class="assign-courses-to-student hidden" type="checkbox" name="assign-courses-to-student" value="'.$course_id.'" '.$assigned_checked.' />'.
										'</td>
									</tr>';

						$i++;

					}

				} else {
					$output .= '<tr>
									<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
								</tr>';			
				}

			$output .= '</table>';

		$output .= '</form>';


		$output .= '<div class="dtlms-assign-courses-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-assign-courses-settings small" data-studentid="'.$student_id.'">'.esc_html__('Assign', 'dtlms').'</a>';


		wp_reset_postdata();


		// Pagination script Start
		$total_post_args = array ( 
						'posts_per_page' => -1, 
						'post_type'=> 'dtlms_courses'
					);
		$total_post_courses = get_posts( $total_post_args );
		wp_reset_postdata();

		$courses_post_count = count($total_post_courses);
		$max_num_pages = ceil($courses_post_count / $post_per_page);

		$item_ids['student_id'] = $student_id;

		$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
		// Pagination script End

	} else {

		$output .= esc_html__('Please choose student', 'dtlms');

	}


	echo $output;

	die();

}

add_action( 'wp_ajax_dtlms_save_assign_courses_settings', 'dtlms_save_assign_courses_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_assign_courses_settings', 'dtlms_save_assign_courses_settings' );
function dtlms_save_assign_courses_settings() {

	$student_id = $_REQUEST['student_id'];
	$course_ids = is_array($_REQUEST['course_ids']) && !empty($_REQUEST['course_ids']) ? $_REQUEST['course_ids'] : array ();

	$output = '';

	if($student_id != '') {

		$args = array (
		    'post_type'         =>  'dtlms_courses',
		    'posts_per_page' 	=> -1
	    );

	    $courses = get_posts($args);

	    if(isset($courses) && !empty($courses)) {
	    	foreach( $courses as $course ) {

	    		$course_id = $course->ID;

            	if(in_array($course_id, $course_ids)) {

					$assigned_users = get_post_meta($course_id, 'assigned_users', true);
					$assigned_users = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array();
					array_push($assigned_users, $student_id);
					update_post_meta($course_id, 'assigned_users', array_unique($assigned_users));

					// Notification & Mail
					do_action('dtlms_poc_course_assigned', $course_id, $student_id);  					

            	} else {

					$assigned_users = get_post_meta($course_id, 'assigned_users', true);
					$assigned_users = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array();
					if(in_array($student_id, $assigned_users)) {
					    unset($assigned_users[array_search($student_id, $assigned_users)]);
					}				
					update_post_meta($course_id, 'assigned_users', array_unique($assigned_users));

            	}         	

			}
		}

		update_user_meta($student_id, 'assigned_courses', array_unique($course_ids));	

		$output .= esc_html__('Courses assigned successfully!', 'dtlms');

	} else {

		$output .= esc_html__('Something went wrong!', 'dtlms');

	}
	
	echo $output;

	die();

}

// Classes

if(!function_exists('dtlms_get_classes_assign_students_content')) {

	function dtlms_get_classes_assign_students_content() {

		$class_title_singular = apply_filters( 'class_label', 'singular' );
		$class_title_plural = apply_filters( 'class_label', 'plural' );

		$output = '';

		$output .= '<div class="dtlms-settings-assign-students-container">';

			$output .= '<div class="dtlms-column dtlms-one-fifth first">';

				$output .= '<label>'.sprintf( esc_html__( 'Choose %1$s', 'dtlms' ), $class_title_singular ).'</label>';

			$output .= '</div>';


			$output .= '<div class="dtlms-column dtlms-four-fifth">';

			    $output .= '<select class="dtlms-assigning-classes-students" name="dtlms-assigning-classes-students" data-placeholder="'.sprintf( esc_html__( 'Choose %1$s...', 'dtlms' ), $class_title_singular ).'" class="dtlms-chosen-select">';

					$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';

					$args = array (
					    'post_type'         =>  'dtlms_classes',
					    'posts_per_page' 	=> -1
				    );
					$args['meta_query'][] = array (
												'key'     => '_regular_price',
												'value'   => 0,
												'type'    => 'numeric',
												'compare' => '>'
											);

				    $classes = get_posts($args);

				    if(isset($classes) && !empty($classes)) {
				    	foreach( $classes as $class ) {

				    		$class_id = $class->ID;
				    		$output .= '<option value="' . esc_attr( $class_id ) . '"' . selected( $class_id, '', false ) . '>' . get_the_title($class_id) . '</option>';

				    	}
				    }

			    $output .= '</select>';

			$output .= '</div>';

			$output .= dtlms_generate_loader_html(false);

			$output .= '<div class="dtlms-assign-studentstoclass-container"></div>';


		$output .= '</div>';

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_assigning_classes_load_students_data', 'dtlms_assigning_classes_load_students_data' );
add_action( 'wp_ajax_nopriv_dtlms_assigning_classes_load_students_data', 'dtlms_assigning_classes_load_students_data' );
function dtlms_assigning_classes_load_students_data() {

	$class_id = $_REQUEST['class_id'];

	$output = '';

	if($class_id != '') {

		// Pagination script Start
		$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
		$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
		$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

		if($dashboard_function_call != '') {
			$function_call = $dashboard_function_call;
		} else {
			$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_assigning_classes_load_students_data';
		}

		if($dashboard_output_div != '') {
			$output_div = $dashboard_output_div;
		} else {
			$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-assign-studentstoclass-container';
		}
		// Pagination script End

		$output .= '<form name="formAssignStudents" class="formAssignStudents" method="post">';

			$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Student', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Purchased', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Assigned', 'dtlms').'</th>
							</tr>';

				$students = get_users ( array ('role' => 'student' ) );	
				$students_filtered = array_slice($students, $offset, $post_per_page, true);				
				if(is_array($students_filtered) && !empty($students_filtered)) {

					$i = $offset+1;
					foreach ( $students_filtered as $student ) {
						setup_postdata( $student );

						$student_id = $student->data->ID;

		                $purchase_checked = '';
		                $purchase_switchclass = 'checkbox-switch-off';

						$active_package_classes = dtlms_get_user_active_packages($student_id, 'classes');
						$active_package_classes = (is_array($active_package_classes) && !empty($active_package_classes)) ? $active_package_classes : array();	                
						$purchased_classes = get_user_meta($student_id, 'purchased_classes', true);
						if((is_array($purchased_classes) && in_array($class_id, $purchased_classes)) || (is_array($active_package_classes) && in_array($class_id, $active_package_classes))) {
		                    $purchase_checked = 'checked="checked"';
		                    $purchase_switchclass = 'checkbox-switch-on';
						}

		                $assigned_checked = '';
		                $assigned_switchclass = 'checkbox-switch-off';
						$assigned_classes = get_user_meta($student_id, 'assigned_classes', true);
						if(is_array($assigned_classes) && in_array($class_id, $assigned_classes)) {
		                    $assigned_checked = 'checked="checked"';
		                    $assigned_switchclass = 'checkbox-switch-on';
						}				

						$output .= '<tr>
										<td>'.$i.'</td>
										<td>'.get_the_author_meta('display_name', $student_id).'</td>
										<td>'
											.'<div class="dtlms-checkbox-switch disabled '.$purchase_switchclass.'"></div>'.
										'</td>
										<td>'
											.'<div data-for="assign-students-to-class-'.$student_id.'" class="dtlms-checkbox-switch '.$assigned_switchclass.'"></div>'
											.'<input id="assign-students-to-class-'.$student_id.'" class="assign-students-to-class hidden" type="checkbox" name="assign-students-to-class" value="'.$student_id.'" '.$assigned_checked.' />'.
										'</td>
									</tr>';

						$i++;

					}

				} else {
					$output .= '<tr>
									<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
								</tr>';			
				}

			$output .= '</table>';

		$output .= '</form>';


		$output .= '<div class="dtlms-assign-classes-students-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-assign-classes-students-settings small" data-classid="'.$class_id.'">'.esc_html__('Assign', 'dtlms').'</a>';


		wp_reset_postdata();

		// Pagination script Start
		$students_count = count($students);
		$max_num_pages = ceil($students_count / $post_per_page);

		$item_ids['class_id'] = $class_id;

		$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
		// Pagination script End

	} else {

		$output .= esc_html__('Please choose class!', 'dtlms');

	}

	echo $output;

	die();

}

add_action( 'wp_ajax_dtlms_save_assign_classes_students_settings', 'dtlms_save_assign_classes_students_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_assign_classes_students_settings', 'dtlms_save_assign_classes_students_settings' );
function dtlms_save_assign_classes_students_settings() {

	$class_id = $_REQUEST['class_id'];
	$student_ids = is_array($_REQUEST['student_ids']) && !empty($_REQUEST['student_ids']) ? $_REQUEST['student_ids'] : array ();

	$output = '';

	if($class_id != '') {

		$students = get_users ( array ('role' => 'student') );
        if ( count( $students ) > 0 ) {
            foreach ($students as $student) {		

            	$student_id = $student->data->ID;

            	if(in_array($student_id, $student_ids)) {
					$assigned_classes = get_user_meta($student_id, 'assigned_classes', true);
					$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();
					array_push($assigned_classes, $class_id);
					update_user_meta($student_id, 'assigned_classes', array_unique($assigned_classes));

					// Start class for user
					$class_data = get_post($class_id);
					$author_id = $class_data->post_author;					
					dtlms_start_class_initialize($class_id, $student_id, $author_id, false);

					// Notification & Mail
					do_action('dtlms_poc_class_assigned', $class_id, $student_id);					
            	} else {
					$assigned_classes = get_user_meta($student_id, 'assigned_classes', true);
					$assigned_classes = (is_array($assigned_classes) && !empty($assigned_classes)) ? $assigned_classes : array();
					if(in_array($class_id, $assigned_classes)) {
					    unset($assigned_classes[array_search($class_id, $assigned_classes)]);
					}				
					update_user_meta($student_id, 'assigned_classes', array_unique($assigned_classes));
            	}

			}
		}

		update_post_meta($class_id, 'assigned_users', array_unique($student_ids));	

		$output .= esc_html__('Students assigned successfully!', 'dtlms');

	} else {

		$output .= esc_html__('Something went wrong!', 'dtlms');

	}
	
	echo $output;

	die();

}


if(!function_exists('dtlms_get_classes_assign_classes_content')) {

	function dtlms_get_classes_assign_classes_content() {

		$output = '';

		$output .= '<div class="dtlms-settings-assign-courses-container">';

			$output .= '<div class="dtlms-column dtlms-one-fifth first">';

				$output .= '<label>'.esc_html__('Choose student', 'dtlms').'</label>';

			$output .= '</div>';


			$output .= '<div class="dtlms-column dtlms-four-fifth">';

			    $output .= '<select class="dtlms-assigning-classes-classes" name="dtlms-assigning-classes-classes" data-placeholder="'.esc_html__('Choose Student ...', 'dtlms').'" class="dtlms-chosen-select">';

					$output .= '<option value="">'.esc_html__('None', 'dtlms').'</option>';

					$students = get_users ( array ('role' => 'student') );
			        if ( count( $students ) > 0 ) {
			            foreach ($students as $student) {

							$student_id = $student->data->ID;

				    		$output .= '<option value="' . esc_attr( $student_id ) . '"' . selected( $student_id, '', false ) . '>' . esc_html( $student->data->display_name ) . '</option>';

				    	}
				    }

			    $output .= '</select>';

			$output .= '</div>';

			$output .= dtlms_generate_loader_html(false);

			$output .= '<div class="dtlms-assign-classestostudent-container"></div>';

		$output .= '</div>';

		return $output;

	}

}

add_action( 'wp_ajax_dtlms_assigning_classes_load_classes_data', 'dtlms_assigning_classes_load_classes_data' );
add_action( 'wp_ajax_nopriv_dtlms_assigning_classes_load_classes_data', 'dtlms_assigning_classes_load_classes_data' );
function dtlms_assigning_classes_load_classes_data() {

	$student_id = $_REQUEST['student_id'];

	$output = '';

	if($student_id != '') {

		// Pagination script Start
		$ajax_call = (isset($_REQUEST['ajax_call']) && $_REQUEST['ajax_call'] == true) ? true : false;
		$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
		$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
		$backend_postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
		$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : $backend_postperpage;

		if($dashboard_function_call != '') {
			$function_call = $dashboard_function_call;
		} else {
			$function_call = (isset($_REQUEST['function_call']) && $_REQUEST['function_call'] != '') ? $_REQUEST['function_call'] : 'dtlms_assigning_classes_load_classes_data';
		}

		if($dashboard_output_div != '') {
			$output_div = $dashboard_output_div;
		} else {
			$output_div = (isset($_REQUEST['output_div']) && $_REQUEST['output_div'] != '') ? $_REQUEST['output_div'] : 'dtlms-assign-classestostudent-container';
		}
		// Pagination script End

		$class_title_plural = apply_filters( 'class_label', 'plural' );

		$output .= '<form name="formAssignClasses" class="formAssignClasses" method="post">';

			$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">
							<tr>
								<th scope="col">'.esc_html__('#', 'dtlms').'</th>
								<th scope="col">'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_title_plural ).'</th>
								<th scope="col">'.esc_html__('Purchased', 'dtlms').'</th>
								<th scope="col">'.esc_html__('Assigned', 'dtlms').'</th>
							</tr>';

				$args = array (
					'offset' => $offset,
					'paged' => $current_page,
					'posts_per_page' => $post_per_page, 				
				    'post_type' => 'dtlms_classes',
			    );
				$args['meta_query'][] = array (
											'key'     => '_regular_price',
											'value'   => 0,
											'type'    => 'numeric',
											'compare' => '>'
										);
				
			    $classes = get_posts($args);
			    if(isset($classes) && !empty($classes)) {
			    	$i = 1;
			    	foreach( $classes as $class ) {

			    		$class_id = $class->ID;

		                $purchase_checked = '';
		                $purchase_switchclass = 'checkbox-switch-off';

						$active_package_classes = dtlms_get_user_active_packages($student_id, 'classes');
						$active_package_classes = (is_array($active_package_classes) && !empty($active_package_classes)) ? $active_package_classes : array();	                
						$purchased_users = get_post_meta($class_id, 'purchased_users', true);
						if((is_array($purchased_users) && in_array($student_id, $purchased_users)) || (is_array($active_package_classes) && in_array($class_id, $active_package_classes))) {
		                    $purchase_checked = 'checked="checked"';
		                    $purchase_switchclass = 'checkbox-switch-on';
						}

		                $assigned_checked = '';
		                $assigned_switchclass = 'checkbox-switch-off';
						$assigned_users = get_post_meta($class_id, 'assigned_users', true);
						if(is_array($assigned_users) && in_array($student_id, $assigned_users)) {
		                    $assigned_checked = 'checked="checked"';
		                    $assigned_switchclass = 'checkbox-switch-on';
						}				

						$output .= '<tr>
										<td>'.$i.'</td>
										<td>'.get_the_title($class_id).'</td>
										<td>'
											.'<div class="dtlms-checkbox-switch disabled '.$purchase_switchclass.'"></div>'.
										'</td>
										<td>'
											.'<div data-for="assign-classes-to-student-'.$class_id.'" class="dtlms-checkbox-switch '.$assigned_switchclass.'"></div>'
											.'<input id="assign-classes-to-student-'.$class_id.'" class="assign-classes-to-student hidden" type="checkbox" name="assign-classes-to-student" value="'.$class_id.'" '.$assigned_checked.' />'.
										'</td>
									</tr>';

						$i++;

					}

				} else {
					$output .= '<tr>
									<td colspan="4">'.esc_html__('No records found!', 'dtlms').'</td>
								</tr>';			
				}

			$output .= '</table>';

		$output .= '</form>';

		$output .= '<div class="dtlms-assign-classes-classes-response-holder"></div>';

		$output .= '<a href="#" class="dtlms-button dtlms-save-assign-classes-classes-settings small" data-studentid="'.$student_id.'">'.esc_html__('Assign', 'dtlms').'</a>';


		wp_reset_postdata();

		// Pagination script Start
		$total_post_args = array ( 
						'posts_per_page' => -1, 
						'post_type'=> 'dtlms_classes'
					);	
		$total_post_classes = get_posts( $total_post_args );
		wp_reset_postdata();

		$classes_post_count = count($total_post_classes);
		$max_num_pages = ceil($classes_post_count / $post_per_page);

		$item_ids['student_id'] = $student_id;

		$output .= dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids);
		// Pagination script End

	} else {

		$output .= esc_html__('Please choose student!' , 'dtlms');
		
	}

	echo $output;

	die();

}

add_action( 'wp_ajax_dtlms_save_assign_classes_classes_settings', 'dtlms_save_assign_classes_classes_settings' );
add_action( 'wp_ajax_nopriv_dtlms_save_assign_classes_classes_settings', 'dtlms_save_assign_classes_classes_settings' );
function dtlms_save_assign_classes_classes_settings() {

	$student_id = $_REQUEST['student_id'];
	$class_ids = is_array($_REQUEST['class_ids']) && !empty($_REQUEST['class_ids']) ? $_REQUEST['class_ids'] : array ();

	$output = '';

	if($student_id != '') {

		$class_plural_label = apply_filters( 'class_label', 'plural' );

		$args = array (
		    'post_type'         =>  'dtlms_classes',
		    'posts_per_page' 	=> -1
	    );

	    $classes = get_posts($args);

	    if(isset($classes) && !empty($classes)) {
	    	foreach( $classes as $class ) {

	    		$class_id = $class->ID;

            	if(in_array($class_id, $class_ids)) {

					$assigned_users = get_post_meta($class_id, 'assigned_users', true);
					$assigned_users = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array();
					array_push($assigned_users, $student_id);
					update_post_meta($class_id, 'assigned_users', array_unique($assigned_users));

					// Start class for user
					$class_data = get_post($class_id);
					$author_id = $class_data->post_author;					
					dtlms_start_class_initialize($class_id, $student_id, $author_id, false);
					
					// Notification & Mail
					do_action('dtlms_poc_class_assigned', $class_id, $student_id);  					

            	} else {

					$assigned_users = get_post_meta($class_id, 'assigned_users', true);
					$assigned_users = (is_array($assigned_users) && !empty($assigned_users)) ? $assigned_users : array();
					if(in_array($student_id, $assigned_users)) {
					    unset($assigned_users[array_search($student_id, $assigned_users)]);
					}				
					update_post_meta($class_id, 'assigned_users', array_unique($assigned_users));

            	}         	

			}
		}

		update_user_meta($student_id, 'assigned_classes', array_unique($class_ids));	

		$output .= sprintf( esc_html__( '%1$s assigned successfully!', 'dtlms' ), $class_plural_label );

	} else {

		$output .= esc_html__('Something went wrong!', 'dtlms');

	}
	
	echo $output;

	die();

}

?>