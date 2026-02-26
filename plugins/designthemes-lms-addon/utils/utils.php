<?php

// Theme Support
if (!function_exists('dtlms_features')) {

	function dtlms_features() {

		if ( function_exists( 'add_theme_support' ) ) {

			add_image_size( 'dtlms-420x330', 420, 330, true  );

			//add_image_size( 'dtlms-1920x600', 1920, 600, array ( 'center', 'top' )  ); // Course / Class - Single Page
			add_image_size( 'dtlms-960x640', 960, 640, true  ); // Course / Class -  Default - 2 Column
			add_image_size( 'dtlms-640x430', 640, 430, true  ); // Course / Class -  Default - 3 Column

			add_image_size( 'dtlms-960x581', 960, 581, true  ); // Course -  Type 9 - 2 Column
			add_image_size( 'dtlms-640x387', 640, 387, true  ); // Course -  Type 9 - 3 Column	

			add_image_size( 'dtlms-960x735', 960, 735, true  ); // Course -  Type 10 - 2 Column
			add_image_size( 'dtlms-640x490', 640, 490, true  ); // Course -  Type 10 - 3 Column						

		}

	}

	add_action('after_setup_theme', 'dtlms_features');

}

// Plugin default settings
if(!function_exists('dtlms_plugins_default_settings')) {
	function dtlms_plugins_default_settings() {

		$general_settings = array
		        (
					'instructor-singular-label'   => esc_html__('Instrutor', 'dtlms'),
					'instructor-plural-label'     => esc_html__('Instrutors', 'dtlms'),       
					'add-instructor-roleto-admin' => 'true',
					'backend-postperpage'         => '10',
					'frontend-postperpage'        => '10',
					'progressbar-color'           => '#81d742',
		        );

		$course_settings = array
		        (
					'enable-countdown-timer-course-startdate' => 'true',
					'contact-instructor-in-coursepage'        => 'true',
					'curriculum-visiblitiy'                   => '',
					'members-visiblitiy'                      => '',
					'events-visiblitiy'                       => '',
					'buddypress-group-visiblitiy'             => '',
					'news-visiblitiy'                         => '',
		        );		        

		$class_settings = array
		        (
					'class-title-singular'                   => esc_html__('Class', 'dtlms'),
					'class-title-plural'                     => esc_html__('Classes', 'dtlms'),  		        	
					'enable-countdown-timer-class-startdate' => 'true',
					'contact-instructor-in-classpage'        => 'true',
					'include-class-in-commission'            => 'true',
		        );	

		$chart_settings = array
		        (
		            'chart-colors' => array
		                (
		                    '#39ba8b',
		                    'rgba(224,35,139,0.82)',
		                    'rgba(49,234,185,0.79)',
		                    'rgba(84,81,19,0.78)',
		                    '#dd9933',
		                    'rgba(221,51,51,0.86)',
		                    'rgba(128,153,21,0.87)',
		                    'rgba(122,33,160,0.91)',
		                    'rgba(35,102,237,0.87)',
		                    '#164866',
		                    'rgba(129,215,66,0.93)',
		                    'rgba(238,238,34,0.82)'
		                ),
		            'shuffle-colors' => 'true',
		            'legend-position' => 'bottom'
		        );

		$dtlms_settings = array (
				'general' => $general_settings,
				'course' => $course_settings,
				'class' => $class_settings,
				'chart' => $chart_settings,
				'plugin-status' => 'activated-already'
			);

		return $dtlms_settings;

	}
}

// Plugin skin settings
if(!function_exists('dtlms_plugins_skin_settings')) {
	function dtlms_plugins_skin_settings() {

		$settings = array
		        (
					'primary-color'              => '#ffcc21',
					'secondary-color'            => '#40c4ff',
					'tertiary-color'             => '#002d62',
					'primary-alternate-color'    => '#000000',
					'secondary-alternate-color'  => '#ffffff',
					'tertiary-alternate-color'   => '',
					'quiztimer-foreground-color' => '#40c4ff',
					'quiztimer-background-color' => '#ffcc21',
		        );

		return $settings;

	}
}

// Retrieve General Options
if(!function_exists('dtlms_option')) {
	function dtlms_option($key1, $key2 = '') {

		$options = get_option('dtlms-settings');

		$output = '';

		if (is_array ( $options )) {
			if (array_key_exists ( $key1, $options )) {
				$output = $options [$key1];
				if (is_array ( $output ) && ! empty ( $key2 )) {
					$output = (array_key_exists ( $key2, $output ) && (! empty ( $output [$key2] ))) ? $output [$key2] : '';
				}
			} else {
				$output = $output;
			}
		}

		return $output;

	}
}

// Login form
if(!function_exists('dtlms_login_form')) {
	function dtlms_login_form() {

		$out = '<div class="dtlms-login-form-container">';

			$out .= '<div class="dtlms-login-form">';

				$out .= '<div class="dtlms-login-form-holder">';

					$out .= '<div class="dtlms-title dtlms-login-title"><h2><span>'.esc_html__('Welcome!', 'dtlms').'<strong>'.esc_html__('Login', 'dtlms').'</strong></span></h2></div>';
					$out .= wp_login_form(array ('echo' => false));
		    		$out .= '<p class="tpl-forget-pwd"><a href="'.wp_lostpassword_url( get_permalink() ).'">'.esc_html__('Forgot password ?','dtlms').'</a></p>';

	    		$out .= '</div>';

				if(dtlms_option('login','enable-facebook-login') == 'true' || dtlms_option('login','enable-googleplus-login') == 'true') {
					
					$out .= '<div class="dtlms-social-logins-container">';
					
						$out .= '<div class="dtlms-social-logins-divider">'.esc_html__('Or', 'dtlms').'</div>';
						
						if(dtlms_option('login','enable-facebook-login') == 'true') {

							if(!session_id()) {
							    session_start();
							}

							require_once  WP_PLUGIN_DIR.'/designthemes-lms-addon/apis/facebook/Facebook/autoload.php';

							$appId = dtlms_option('login','facebook-appid'); //Facebook App ID
							$appSecret = dtlms_option('login','facebook-appsecret'); // Facebook App Secret

							$fb = new Facebook\Facebook([
								'app_id' => $appId,
								'app_secret' => $appSecret,
								'default_graph_version' => 'v2.10',
							]);

							$helper = $fb->getRedirectLoginHelper();
							$permissions = ['email'];
							$loginUrl = $helper->getLoginUrl( site_url('wp-login.php') . '?dtLMSFacebookLogin=1', $permissions);

							$out .= '<a href="'.htmlspecialchars($loginUrl).'" class="dtlms-social-facebook-connect"><i class="fa fa-facebook"></i>'.esc_html__('Connect With Facebook', 'dtlms').'</a>';

						}
						
						if(dtlms_option('login','enable-google-login') == 'true') {

							$out .= '<a href="'.dtlms_google_login_url().'" class="dtlms-social-google-connect"><i class="fa fa-google"></i>'.esc_html__('Connect With Google', 'dtlms').'</a>';

						}
					
					$out .= '</div>';
					
				}

			$out .= '</div>';

		$out .= '</div>';

		$out .= '<div class="dtlms-login-form-overlay"></div>';
		

		return $out;

	}
}

add_action( 'wp_ajax_dtlms_show_login_form_popup', 'dtlms_show_login_form_popup' );
add_action( 'wp_ajax_nopriv_dtlms_show_login_form_popup', 'dtlms_show_login_form_popup' );
function dtlms_show_login_form_popup() {

	echo dtlms_login_form();

	die();

}

add_action( 'wp_ajax_dtlms_generate_curriculum_page_contents', 'dtlms_generate_curriculum_page_contents' );
add_action( 'wp_ajax_nopriv_dtlms_generate_curriculum_page_contents', 'dtlms_generate_curriculum_page_contents' );
function dtlms_generate_curriculum_page_contents() {

	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;	

	$course_id = $_REQUEST['course_id'];
	$parent_curriculum_id = $_REQUEST['parent_curriculum_id'];
	$curriculum_id = $_REQUEST['curriculum_id'];

    if(get_post_type($curriculum_id) == 'dtlms_lessons') {
    	dtlms_generate_lesson_page_contents($user_id, $course_id, $curriculum_id, $parent_curriculum_id);
    }

    if(get_post_type($curriculum_id) == 'dtlms_quizzes') {
        dtlms_generate_quiz_page_contents($user_id, $course_id, $curriculum_id, $parent_curriculum_id);
    }

    if(get_post_type($curriculum_id) == 'dtlms_assignments') {	
    	dtlms_generate_assignment_page_contents($user_id, $course_id, $curriculum_id, $parent_curriculum_id);
    }

}

function dtlms_get_completed_items_list($user_id, $course_id, $parent_curriculum_id) {

	$completed_items = array();

	$curriculum_details = get_user_meta($user_id, $course_id, true);

	if($parent_curriculum_id > 0) {

		$lesson_curriculums = isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum']) ? $curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'] : array();

		if(!empty($lesson_curriculums)) {
			foreach($lesson_curriculums as $lesson_curriculum_key => $lesson_curriculum) {
				if(isset($lesson_curriculum['completed']) && $lesson_curriculum['completed'] == 1) {
					array_push($completed_items, $lesson_curriculum_key);
				}					
			}
		}

	} else {

		$course_curriculums = isset($course_curriculums['curriculum']) ? $course_curriculums['curriculum'] : array();

		if(!empty($course_curriculums)) {
			foreach($course_curriculums as $course_curriculum_key => $course_curriculum) {
				if(isset($course_curriculum['completed']) && $course_curriculum['completed'] == 1) {
					array_push($completed_items, $course_curriculum_key);
				}

			}
		}

	}

   	return $completed_items;

}

function dtlms_allowed_filetypes() {

	$attachment_types = array(
		'jpg', 'gif', 'png', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'pps', 'ppsx', 'odt', 'xls', 'xlsx', 'mp3', 'm4a', 'ogg', 'wav', 'wma', 'mp4', 'm4v', 'mov', 'wmv', 'avi', 'mpg', 'ogv', '3gp', '3g2', 'flv', 'webm', 'apk', 'rar', 'zip'
	);

	return $attachment_types;
	
}

function dtlms_get_upload_size() {
	
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));	

	$upload_size = min($max_upload, $max_post);
	
	return $upload_size;

}


function dtlms_get_pass_percentage($curriculum, $with_symbol = false) {

	$pass_percentage = '';

	if(get_post_type($curriculum) == 'dtlms_lessons') {
		$pass_percentage = get_post_meta ($curriculum, 'lesson-pass-percentage', true);
	}

	if(get_post_type($curriculum) == 'dtlms_quizzes') {
	    $pass_percentage = get_post_meta ($curriculum, 'quiz-pass-percentage', true);
	}

	if(get_post_type($curriculum) == 'dtlms_assignments') {
		$pass_percentage = get_post_meta ($curriculum, 'assignment-pass-percentage', true);
	}

	if($with_symbol && $pass_percentage != '') {
		$pass_percentage = $pass_percentage.'%';
	}

	return $pass_percentage;

}


function dtlms_generate_progressbar($percentage) {
	
	$progressbar_color = (dtlms_option('general','progressbar-color') != '') ? dtlms_option('general','progressbar-color') : 'rgb(155, 189, 60)';

	$percentage = round($percentage, 2);
	
	$out = '<div class="dtlms-progressbar">
				<div data-value="'.$percentage.'" style="background-color: '.$progressbar_color.';" class="dtlms-bar">
					<div class="dtlms-bar-text"><span>'.$percentage.'%</span></div>
				</div>
			</div>';
	
	return $out;
	
}

function dtlms_insert_parent_grade_post($course_id, $course_grade_id, $user_id, $curriculum_id) {

	$title = get_the_title($curriculum_id);

	$lesson_id = $quiz_id = $assignment_id = -1;

    if(get_post_type($curriculum_id) == 'dtlms_lessons') {
    	$lesson_id = $curriculum_id;
    	$grade_type = 'lesson';
    } else if(get_post_type($curriculum_id) == 'dtlms_quizzes') {
        $quiz_id = $curriculum_id;
        $grade_type = 'quiz';
    } else if(get_post_type($curriculum_id) == 'dtlms_assignments') {
    	$assignment_id = $curriculum_id;
    	$grade_type = 'assignment';
    }

	$grade_post = array(
		'post_title' => $title,
		'post_status' => 'publish',
		'post_type' => 'dtlms_gradings',
		'post_author' => $author_id,
		'post_parent' => $course_grade_id
	);
	
	$grade_post_id = wp_insert_post( $grade_post );

	update_post_meta ( $grade_post_id, 'dtlms-course-id',  $course_id );
	update_post_meta ( $grade_post_id, 'dtlms-course-grade-id',  $course_grade_id );
	update_post_meta ( $grade_post_id, 'dtlms-user-id',  $user_id );
	update_post_meta ( $grade_post_id, 'dtlms-lesson-id',  $lesson_id );
	update_post_meta ( $grade_post_id, 'dtlms-quiz-id',  $quiz_id );
	update_post_meta ( $grade_post_id, 'dtlms-assignment-id',  $assignment_id );
	update_post_meta ( $grade_post_id, 'dtlms-parent-curriculum-id',  -1 );	
	update_post_meta ( $grade_post_id, 'grade-type',  $grade_type );

	update_post_meta ( $grade_post_id, 'temp-grade-post-id',  $grade_post_id );

	if($grade_type == 'quiz') {
		update_post_meta ( $grade_post_id, 'user-attempts',  0 );	
	}
		
	$curriculum_details = get_user_meta($user_id, $course_id, true);
	$curriculum_details['curriculum'][$curriculum_id]['temp-grade-post-id'] = $grade_post_id;

	update_user_meta($user_id, $course_id, $curriculum_details);			

	return $grade_post_id;

}

function dtlms_retrieve_curriculum_post_datas($curriculum_id, $type = 'name') {

	$output = '';

    if(get_post_type($curriculum_id) == 'dtlms_lessons') {
    	if($type == 'name') {
    		$output = esc_html__('Lesson', 'dtlms');
    	} else if($type == 'class') {
    	    $output = 'lesson';    		
    	} else if($type == 'maxmark') {
    		$output = get_post_meta($curriculum_id, 'lesson-maximum-mark', true);
    	}
    }

    if(get_post_type($curriculum_id) == 'dtlms_quizzes') {
    	if($type == 'name') {
       	 	$output = esc_html__('Quiz', 'dtlms');
     	} else if($type == 'class') {
    	    $output = 'quiz';        	 	
    	} else if($type == 'maxmark') {
    		$output = get_post_meta($curriculum_id, 'quiz-total-grade', true);       	 	
       	}
    }

    if(get_post_type($curriculum_id) == 'dtlms_assignments') {
    	if($type == 'name') {
    		$output = esc_html__('Assignment', 'dtlms');
    	} else if($type == 'class') {
    	    $output = 'assignment';      		
    	} else if($type == 'maxmark') {
    		$output = get_post_meta($curriculum_id, 'assignment-maximum-mark', true);    		
    	}
    }

    return $output;

}

function dtlms_parse_array_and_count_particular_key($original_array, $key_to_find, $counter = 0) {

    foreach ($original_array as $key => $value) {
        if (is_array($value)) {
            $counter = dtlms_parse_array_and_count_particular_key($value, $key_to_find, $counter);
        } else {
			if($key == $key_to_find) {
				$counter++;	
			}             
        }
    }

    return $counter;

}

/*
function dtlms_parse_array_and_group_by_key($array) {

	$output = array();
	foreach($array as $array_item) {

	    $array_key = key($array_item);
	    $array_value = current($array_item);

	    if(!isset($output[$array_key])) {
	        $output[$array_key] = array();
	    }
	    foreach($array_value as $value) {
	    	$output[$array_key][] = $value;	
	    }

	}

   	return $output;

}*/

function dtlms_check_user_package_is_active($user_id, $package_id) {

	if($user_id > 0) {

		$purchased_packages = get_user_meta($user_id, 'purchased_packages', true);
		$expirydate_timestamp = isset($purchased_packages[$package_id]['expiry-date']) ? $purchased_packages[$package_id]['expiry-date'] : 'NA';

		if($expirydate_timestamp != 'NA') {
			$current_timestamp = strtotime(current_time(get_option('date_format')));
			if($current_timestamp <= $expirydate_timestamp) {
				return true;
			}
		}

	}

    return false;

}

function dtlms_get_course_packages($course_id) {

	$packages_list = array ();

	if($course_id > 0) {

        $args = array (
                    'post_type'=> 'dtlms_packages',
                    'numberposts'=> -1,
                    'suppress_filters'  => FALSE
                );
        $post_types = get_posts($args);

        foreach($post_types as $post_type) {
        	$package_id = $post_type->ID;
			$courses_included = get_post_meta($package_id, 'courses-included', true);
			$courses_included = (is_array($courses_included) && !empty($courses_included)) ? $courses_included : array (); 
			if(in_array($course_id, $courses_included)) {
				array_push($packages_list, $package_id);
			}
        }

	}

    return $packages_list;

}

function dtlms_get_user_active_packages($user_id, $item_type = 'all') {

	$package_items_list = array ();

	if($user_id > 0) {

        $purchased_packages = get_user_meta($user_id, 'purchased_packages', true);
        if(is_array($purchased_packages) && !empty($purchased_packages)) {
	        foreach($purchased_packages as $purchased_package_key => $purchased_package) {
	        	if(dtlms_check_user_package_is_active($user_id, $purchased_package_key)) {
	        		if($item_type == 'classes' || $item_type == 'all') {
			 			$classes_included = get_post_meta($purchased_package_key, 'classes-included', true);
						$classes_included = (is_array($classes_included) && !empty($classes_included)) ? $classes_included : array ();
		        		$package_items_list = array_merge_recursive($package_items_list, $classes_included);
	        		}
	        		if($item_type == 'courses' || $item_type == 'all') {
			 			$courses_included = get_post_meta($purchased_package_key, 'courses-included', true);
						$courses_included = (is_array($courses_included) && !empty($courses_included)) ? $courses_included : array ();
		        		$package_items_list = array_merge_recursive($package_items_list, $courses_included);
		        	}
	        	}
	        }
	    }

	}

    return array_unique($package_items_list);

}

function dtlms_get_user_purchased_class_courses($user_id) {

	$class_courses_list = array ();

	if($user_id > 0) {

        $purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
        if(is_array($purchased_classes) && !empty($purchased_classes)) {
	        foreach($purchased_classes as $purchased_class_key => $purchased_class) {
	        	$class_courses = get_post_meta($purchased_class, 'dtlms-class-courses', true);
	        	if(is_array($class_courses) && !empty($class_courses)) {
	        		$class_courses_list = array_merge_recursive($class_courses_list, $class_courses);
	        	}
	        }
	    }

        $assigned_classes = get_user_meta($user_id, 'assigned_classes', true);
        if(is_array($assigned_classes) && !empty($assigned_classes)) {
	        foreach($assigned_classes as $assigned_class_key => $assigned_class) {
	        	$class_courses = get_post_meta($assigned_class, 'dtlms-class-courses', true);
	        	if(is_array($class_courses) && !empty($class_courses)) {
	        		$class_courses_list = array_merge_recursive($class_courses_list, $class_courses);
	        	}
	        }
	    }

	}

    return array_unique($class_courses_list);

}

function dtlms_get_course_numeric_curriculum($course_id) {

	$course_numeric_curriculum = array ();

	$course_curriculum = get_post_meta($course_id, 'course-curriculum', true);

	if(is_array($course_curriculum) && !empty($course_curriculum)) {
		foreach ($course_curriculum as $course_curriculum_item) {
			if (is_numeric($course_curriculum_item)) {
				$course_numeric_curriculum[$course_curriculum_item] = array ();
				if(get_post_type($course_curriculum_item) == 'dtlms_lessons') {
					$lesson_curriculum = get_post_meta($course_curriculum_item, 'lesson-curriculum', true);
					if(is_array($lesson_curriculum) && !empty($lesson_curriculum)) {
						foreach ($lesson_curriculum as $lesson_curriculum_item) {
							if (is_numeric($lesson_curriculum_item)) { 
								$course_numeric_curriculum[$course_curriculum_item][] = $lesson_curriculum_item;
							}
						}
					}	
				}	
			} 
		}
	}

	return $course_numeric_curriculum;

}

function dtlms_get_course_next_curriculum_id($course_id, $item_id, $parent_curriculum_id) {

	$next_curriculum_id = -1;

	$curriculum_items = dtlms_get_course_numeric_curriculum($course_id);

	if(is_array($curriculum_items[$item_id]) && !empty($curriculum_items[$item_id])) {
		$next_curriculum_id = $curriculum_items[$item_id][0];
	} else {
		if($parent_curriculum_id > 0) {
			$sub_curriculum_items = $curriculum_items[$parent_curriculum_id];
			if(isset($sub_curriculum_items[array_search($item_id, $sub_curriculum_items)+1])) {
				$next_curriculum_id = $sub_curriculum_items[array_search($item_id, $sub_curriculum_items)+1];
			} else {
				$curriculum_item_keys = array_keys($curriculum_items);
				$curent_item_position = array_search($parent_curriculum_id, $curriculum_item_keys);
				if($curent_item_position >= 0) {
					$next_item_position = $curent_item_position+1;				
					$next_curriculum_id = $curriculum_item_keys[$next_item_position];
				}
			}
		} else {
			$curriculum_item_keys = array_keys($curriculum_items);
			$curent_item_position = array_search($item_id, $curriculum_item_keys);
			if($curent_item_position >= 0) {
				$next_item_position = $curent_item_position+1;				
				$next_curriculum_id = $curriculum_item_keys[$next_item_position];
			}					
		}
	}

	if(dtlms_check_curriculum_item_is_free($next_curriculum_id)) {
		$next_curriculum_id = dtlms_get_course_next_curriculum_id($course_id, $next_curriculum_id, $parent_curriculum_id);
	}

	return $next_curriculum_id;

}

function dtlms_check_curriculum_item_is_free($curriculum) {

	if(get_post_type($curriculum) == 'dtlms_lessons') {
		$free_item = get_post_meta ( $curriculum, 'free-lesson', true );
	}
	if(get_post_type($curriculum) == 'dtlms_quizzes') {
		$free_item = get_post_meta ( $curriculum, 'free-quiz', true );
	}
	if(get_post_type($curriculum) == 'dtlms_assignments') {
		$free_item = get_post_meta ( $curriculum, 'free-assignment', true );
	}
	if($free_item) {
		return true;
	}

	return false;

}

function dtlms_get_course_numeric_curriculum_ids($course_id) {

	$course_numeric_curriculum = array ();

	$course_curriculum = get_post_meta($course_id, 'course-curriculum', true);

	if(is_array($course_curriculum) && !empty($course_curriculum)) {
		foreach ($course_curriculum as $course_curriculum_item) {
			if (is_numeric($course_curriculum_item)) {
				$course_numeric_curriculum[] = $course_curriculum_item;
				if(get_post_type($course_curriculum_item) == 'dtlms_lessons') {
					$lesson_curriculum = get_post_meta($course_curriculum_item, 'lesson-curriculum', true);
					if(is_array($lesson_curriculum) && !empty($lesson_curriculum)) {
						foreach ($lesson_curriculum as $lesson_curriculum_item) {
							if (is_numeric($lesson_curriculum_item)) { 
								$course_numeric_curriculum[] = $lesson_curriculum_item;
							}
						}
					}	
				}	
			} 
		}
	}

	return $course_numeric_curriculum;

}

function dtlms_get_course_curriculum_sectionwise($course_id) {

	$course_curriculum_sectionwise = array ();

	$course_curriculum = get_post_meta($course_id, 'course-curriculum', true);

	if(is_array($course_curriculum) && !empty($course_curriculum)) {
		$course_curriculum_key = '';
		foreach ($course_curriculum as $course_curriculum_item) {
			if (!is_numeric($course_curriculum_item)) {
				$course_curriculum_key = $course_curriculum_item;
			} else {
				if($course_curriculum_key != '') {
					$course_curriculum_sectionwise[$course_curriculum_key][] = $course_curriculum_item;
					if(get_post_type($course_curriculum_item) == 'dtlms_lessons') {
						$lesson_curriculum = get_post_meta($course_curriculum_item, 'lesson-curriculum', true);
						if(is_array($lesson_curriculum) && !empty($lesson_curriculum)) {
							foreach ($lesson_curriculum as $lesson_curriculum_item) {
								if (is_numeric($lesson_curriculum_item)) { 
									$course_curriculum_sectionwise[$course_curriculum_key][] = $lesson_curriculum_item;
								}
							}
						}	
					}	
				}			
			}
		}
	}

	return $course_curriculum_sectionwise;

}

function dtlms_convert_seconds_to_readable_format($seconds, $style) {

	$format = '';

	if($seconds > 0) {

		$dtF = new DateTime;
		$dtT = clone $dtF;
		$dtT->modify("+$seconds seconds");
		$diff = $dtF->diff($dtT);

		$readable_format = array ('years' => $diff->y, 'months' => $diff->m, 'days' => $diff->d, 'hours' => $diff->h, 'minutes' => $diff->i, 'seconds' => $diff->s);


		if($style == 'style4') {

			if($readable_format['years'] > 0) {
				$format .= $readable_format['years'].' yr ';
			}
			if($readable_format['months'] > 0) {
				$format .= $readable_format['months'].' mth ';
			}
			if($readable_format['days'] > 0) {
				$format .= $readable_format['days'].' d ';
			}
			if($readable_format['hours'] > 0) {
				$format .= $readable_format['hours'].' hrs ';
			}
			if($readable_format['minutes'] > 0) {
				$format .= $readable_format['minutes'].' mins ';
			}
			if($readable_format['seconds'] > 0) {
				$format .= $readable_format['seconds'].' secs ';
			}

		} else if($style == 'style3') {

			if($readable_format['years'] > 0) {
				$format .= $readable_format['years'].'y ';
			}
			if($readable_format['months'] > 0) {
				$format .= $readable_format['months'].'m ';
			}
			if($readable_format['days'] > 0) {
				$format .= $readable_format['days'].'d ';
			}
			if($readable_format['hours'] > 0) {
				$format .= $readable_format['hours'].'h ';
			}
			if($readable_format['minutes'] > 0) {
				$format .= $readable_format['minutes'].'m ';
			}
			if($readable_format['seconds'] > 0) {
				$format .= $readable_format['seconds'].'s ';
			}

		} else if($style == 'style2') {
			
			if($readable_format['years'] > 0) {
				$format .= $readable_format['years'].' : ';
			}
			if($readable_format['months'] > 0) {
				$format .= $readable_format['months'].' : ';
			}
			if($readable_format['days'] > 0) {
				$format .= $readable_format['days'].' : ';
			}
			if($readable_format['hours'] > 0) {
				$format .= $readable_format['hours'].' : ';
			}
			if($readable_format['minutes'] > 0) {
				$format .= $readable_format['minutes'].' : ';
			}
			if($readable_format['seconds'] > 0) {
				$format .= $readable_format['seconds'].' : ';
			}

		} else {
			
			if($readable_format['years'] > 0) {
				$format .= $readable_format['years'].' years ';
			}
			if($readable_format['months'] > 0) {
				$format .= $readable_format['months'].' months ';
			}
			if($readable_format['days'] > 0) {
				$format .= $readable_format['days'].' days ';
			}
			if($readable_format['hours'] > 0) {
				$format .= $readable_format['hours'].' hours ';
			}
			if($readable_format['minutes'] > 0) {
				$format .= $readable_format['minutes'].' minutes ';
			}
			if($readable_format['seconds'] > 0) {
				$format .= $readable_format['seconds'].' seconds ';
			}

		}

	}

	return $format;

}

function dtlms_generate_countdown_html($date, $item_id, $parent_curriculum_id) {

	$output = '';

	$gmt_offset = get_option('gmt_offset');

	$output .= '<div class="dtlms-countdown-holder" data-date="'.$date.'" data-offset="'.$gmt_offset.'" data-curriculumid="'.$item_id.'" data-parentcurriculumid="'.$parent_curriculum_id.'">';
		$output .= '<div class="dtlms-countdown-wrapper">';
			$output .= '<div class="dtlms-countdown-icon-wrapper">';
				$output .= '<div class="dtlms-countdown-number days">00</div>';
			$output .= '</div>';
			$output .= '<h3 class="dtlms-countdown-title">'.esc_html__('Days', 'dtlms').'</h3>';
		$output .= '</div>';
		$output .= '<div class="dtlms-countdown-wrapper">';
			$output .= '<div class="dtlms-countdown-icon-wrapper">';
				$output .= '<div class="dtlms-countdown-number hours">00</div>';
			$output .= '</div>';
			$output .= '<h3 class="dtlms-countdown-title">'.esc_html__('Hours', 'dtlms').'</h3>';
		$output .= '</div>';
		$output .= '<div class="dtlms-countdown-wrapper">';
			$output .= '<div class="dtlms-countdown-icon-wrapper">';
				$output .= '<div class="dtlms-countdown-number minutes">00</div>';
			$output .= '</div>';
			$output .= '<h3 class="dtlms-countdown-title">'.esc_html__('Minutes', 'dtlms').'</h3>';
		$output .= '</div>';
		$output .= '<div class="dtlms-countdown-wrapper last">';
			$output .= '<div class="dtlms-countdown-icon-wrapper">';
				$output .= '<div class="dtlms-countdown-number seconds">00</div>';
			$output .= '</div>';
			$output .= '<h3 class="dtlms-countdown-title">'.esc_html__('Seconds', 'dtlms').'</h3>';
		$output .= '</div>';
	$output .= '</div>';

	return $output;

}


function dtlms_format_datetime($unixTime, $format, $without_timezone = false) {

	if($without_timezone == true) {

		$date = new DateTime( "@$unixTime" );

		return $date->format($format);

	} else {

		$timezone = get_option('timezone_string');
		if($timezone == '') {
			$timezone = get_option('gmt_offset');
			$timezone = str_replace($timezone, 'UTC', '');
			$timezone = str_replace($timezone, ':', '');
		}

		if($timezone != '') {

			$UTC = new DateTimeZone("UTC");
			$newTZ = new DateTimeZone($timezone);
			$date = new DateTime( "@$unixTime", $UTC );
			$date->setTimezone( $newTZ );

			return $date->format($format);

		} else {

			$UTC = new DateTimeZone("UTC");
			$date = new DateTime( "@$unixTime", $UTC );

			return $date->format($format);

		}

	}

	return false;

}

global $dtlms_allowed_html_tags;
$dtlms_allowed_html_tags = array(
	'a' => array('class' => array(), 'href' => array(), 'title' => array(), 'target' => array()),
	'abbr' => array('title' => array()),
	'address' => array(),
	'area' => array('shape' => array(), 'coords' => array(), 'href' => array(), 'alt' => array()),
	'article' => array(),
	'aside' => array(),
	'audio' => array('autoplay' => array(), 'controls' => array(), 'loop' => array(), 'muted' => array(), 'preload' => array(), 'src' => array()),
	'b' => array(),
	'base' => array('href' => array(), 'target' => array()),
	'bdi' => array(),
	'bdo' => array('dir' => array()), 
	'blockquote' => array('cite' => array()), 
	'br' => array(),
	'button' => array('autofocus' => array(), 'disabled' => array(), 'form' => array(), 'formaction' => array(), 'formenctype' => array(), 'formmethod' => array(), 'formnovalidate' => array(), 'formtarget' => array(), 'name' => array(), 'type' => array(), 'value' => array()),
	'canvas' => array('height' => array(), 'width' => array()),
	'caption' => array('align' => array()),
	'cite' => array(),
	'code' => array(),
	'col' => array(),
	'colgroup' => array(),
	'datalist' => array('id' => array()),
	'dd' => array(),
	'del' => array('cite' => array(), 'datetime' => array()),
	'details' => array('open' => array()),
	'dfn' => array(),
	'dialog' => array('open' => array()),
	'div' => array('class' => array(), 'id' => array(), 'align' => array()),
	'dl' => array(),
	'dt' => array(),
	'em' => array(),
	'embed' => array('height' => array(), 'src' => array(), 'type' => array(), 'width' => array()),
	'fieldset' => array('disabled' => array(), 'form' => array(), 'name' => array()),
	'figcaption' => array(),
	'figure' => array(),
	'form' => array('accept' => array(), 'accept-charset' => array(), 'action' => array(), 'autocomplete' => array(), 'enctype' => array(), 'method' => array(), 'name' => array(), 'novalidate' => array(), 'target' => array(), 'id' => array(), 'class' => array()),
	'h1' => array('class' => array()), 'h2' => array('class' => array()), 'h3' => array('class' => array()), 'h4' => array('class' => array()), 'h5' => array('class' => array()), 'h6' => array('class' => array()),
	'hr' => array(), 
	'i' => array('class' => array()), 
	'iframe' => array('name' => array(), 'seamless' => array(), 'src' => array(), 'srcdoc' => array(), 'width' => array()),
	'img' => array('alt' => array(), 'crossorigin' => array(), 'height' => array(), 'ismap' => array(), 'src' => array(), 'usemap' => array(), 'width' => array()),
	'input' => array('align' => array(), 'alt' => array(), 'autocomplete' => array(), 'autofocus' => array(), 'checked' => array(), 'disabled' => array(), 'form' => array(), 'formaction' => array(), 'formenctype' => array(), 'formmethod' => array(), 'formnovalidate' => array(), 'formtarget' => array(), 'height' => array(), 'list' => array(), 'max' => array(), 'maxlength' => array(), 'min' => array(), 'multiple' => array(), 'name' => array(), 'pattern' => array(), 'placeholder' => array(), 'readonly' => array(), 'required' => array(), 'size' => array(), 'src' => array(), 'step' => array(), 'type' => array(), 'value' => array(), 'width' => array(), 'id' => array(), 'class' => array()),
	'ins' => array('cite' => array(), 'datetime' => array()),
	'label' => array('for' => array(), 'form' => array()),
	'legend' => array('align' => array()), 
	'li' => array('type' => array(), 'value' => array(), 'class' => array()),
	'link' => array('crossorigin' => array(), 'href' => array(), 'hreflang' => array(), 'media' => array(), 'rel' => array(), 'sizes' => array(), 'type' => array()),
	'main' => array(), 
	'map' => array('name' => array()), 
	'mark' => array(), 
	'menu' => array('label' => array(), 'type' => array()),
	'menuitem' => array('checked' => array(), 'command' => array(), 'default' => array(), 'disabled' => array(), 'icon' => array(), 'label' => array(), 'radiogroup' => array(), 'type' => array()),
	'meta' => array('charset' => array(), 'content' => array(), 'http-equiv' => array(), 'name' => array()),
	'object' => array('form' => array(), 'height' => array(), 'name' => array(), 'type' => array(), 'usemap' => array(), 'width' => array()),
	'ol' => array('class' => array(), 'reversed' => array(), 'start' => array(), 'type' => array()),
	'p' => array('class' => array()), 
	'q' => array('cite' => array()), 
	'section' => array(), 
	'select' => array('autofocus' => array(), 'disabled' => array(), 'form' => array(), 'multiple' => array(), 'name' => array(), 'required' => array(), 'size' => array()),
	'small' => array(), 
	'source' => array('media' => array(), 'src' => array(), 'type' => array()),
	'span' => array('class' => array()), 
	'strong' => array(),
	'style' => array('media' => array(), 'scoped' => array(), 'type' => array()),
	'sub' => array(),
	'sup' => array(),
	'table' => array('sortable' => array()), 
	'tbody' => array(), 
	'td' => array('colspan' => array(), 'headers' => array()),
	'textarea' => array('autofocus' => array(), 'cols' => array(), 'disabled' => array(), 'form' => array(), 'maxlength' => array(), 'name' => array(), 'placeholder' => array(), 'readonly' => array(), 'required' => array(), 'rows' => array(), 'wrap' => array()),
	'tfoot' => array(),
	'th' => array('abbr' => array(), 'colspan' => array(), 'headers' => array(), 'rowspan' => array(), 'scope' => array(), 'sorted' => array()),
	'thead' => array(), 
	'time' => array('datetime' => array()), 
	'title' => array(), 
	'tr' => array(), 
	'track' => array('default' => array(), 'kind' => array(), 'label' => array(), 'src' => array(), 'srclang' => array()), 
	'u' => array(), 
	'ul' => array('class' => array()), 
	'var' => array(), 
	'video' => array('autoplay' => array(), 'controls' => array(), 'height' => array(), 'loop' => array(), 'muted' => array(), 'muted' => array(), 'poster' => array(), 'preload' => array(), 'src' => array(), 'width' => array()), 
	'wbr' => array(), 
);

function dtlms_wp_kses($content) {
	global $dtlms_allowed_html_tags;
	$data = wp_kses($content, $dtlms_allowed_html_tags);
	return $data;
}


function dtlms_ajax_pagination($max_num_pages, $current_page, $function_call, $output_div, $item_ids) {

	$output = '';

	if($max_num_pages > 1) {

		$instructor_id = $class_id = $course_id = $student_id = $user_id = -1;
		$commission_content = 'course';
		if(isset($item_ids['instructor_id']) && $item_ids['instructor_id'] != '') {
			$instructor_id = $item_ids['instructor_id'];
		}
		if(isset($item_ids['class_id']) && $item_ids['class_id'] != '') {
			$class_id = $item_ids['class_id'];
		}
		if(isset($item_ids['course_id']) && $item_ids['course_id'] != '') {
			$course_id = $item_ids['course_id'];
		}
		if(isset($item_ids['student_id']) && $item_ids['student_id'] != '') {
			$student_id = $item_ids['student_id'];
		}
		if(isset($item_ids['user_id']) && $item_ids['user_id'] != '') {
			$user_id = $item_ids['user_id'];
		}		
		if(isset($item_ids['commission_content']) && $item_ids['commission_content'] != '') {
			$commission_content = $item_ids['commission_content'];
		}		

		if($output_div == 'dtlms-course-result-curriculum-container' || $output_div == 'dtlms-class-result-curriculum-container') {
			$postperpage = (dtlms_option('general','frontend-postperpage') != '') ? dtlms_option('general','frontend-postperpage') : 10;
		} else {
			$postperpage = (dtlms_option('general','backend-postperpage') != '') ? dtlms_option('general','backend-postperpage') : 10;
		}
		
		
		$output .= '<div class="dtlms-pagination dtlms-ajax-pagination" data-postperpage="'.$postperpage.'" data-functioncall="'.$function_call.'" data-outputdiv="'.$output_div.'" data-instructorid="'.$instructor_id.'" data-classid="'.$class_id.'" data-courseid="'.$course_id.'" data-studentid="'.$student_id.'" data-userid="'.$user_id.'" data-commissioncontent="'.$commission_content.'">';
				
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
						  'total' 		 => $max_num_pages 
					  ) );

			if ($current_page < $max_num_pages) {
				$output .= '<div class="next-post"><a href="#" data-currentpage="'.$current_page.'">'.esc_html__('Next', 'dtlms').'&nbsp;<span class="fa fa-caret-right"></span></a></div>';
			}

		$output .= '</div>';

    }

    return $output;

}

function dtlms_append_to_array( $items, $new_items, $after ) {

    // Search for the item position and +1 since is after the selected item key.
    $position = array_search( $after, array_keys( $items ) ) + 1;

    // Insert the new item.
    $array = array_slice( $items, 0, $position, true );
    $array += $new_items;
    $array += array_slice( $items, $position, count( $items ) - $position, true );

    return $array;
    
}

function dtlms_generate_loader_html($add_first_class = true) {

	$add_first_class_html = '';
	if($add_first_class) {
		$add_first_class_html .= 'class="first"';
	}

	$output = '<div id="dtlms-ajax-load-image" '.$add_first_class_html.' style="display:none;">
					<div class="dtlms-loader-inner">
						<div class="dtlms-loading"></div>
						<div class="dtlms-pad">
							<div class="dtlms-line dtlms-line1"></div>
							<div class="dtlms-line dtlms-line2"></div>
							<div class="dtlms-line dtlms-line3"></div>
						</div>
					</div>
				</div>';

    return $output;
    
}


?>