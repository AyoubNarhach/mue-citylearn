<?php

// User purchase status on courses
if(!function_exists('dtlms_get_user_course_purchase_status')) {
	function dtlms_get_user_course_purchase_status($course_id, $user_id) {

		$active_package_courses = dtlms_get_user_active_packages($user_id, 'courses');
		$active_package_courses = (is_array($active_package_courses) && !empty($active_package_courses)) ? $active_package_courses : array();

		$purchased_class_courses = dtlms_get_user_purchased_class_courses($user_id);
		$purchased_class_courses = (is_array($purchased_class_courses) && !empty($purchased_class_courses)) ? $purchased_class_courses : array();

		$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
		$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

		$assigned_courses = get_user_meta($user_id, 'assigned_courses', true);
		$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();

		$active_courses = array_merge($active_package_courses, $purchased_class_courses, $purchased_courses, $assigned_courses);	

		if(in_array($course_id, $active_courses)) {
			return true;
		}

		return false;

	}
}


// Course curriculum counts
if(!function_exists('dtlms_course_curriculum_counts')) {
	function dtlms_course_curriculum_counts($course_id, $return_total = false) {

		$lessons_counts = $quizzes_counts = $assignments_counts = 0;

		$course_curriculum = get_post_meta($course_id, 'course-curriculum', true);

        if(is_array($course_curriculum) && !empty($course_curriculum)) {
            foreach($course_curriculum as $curriculum) {
            	if(is_numeric($curriculum)) {

	                if(get_post_type($curriculum) == 'dtlms_lessons') {
	                	$lessons_counts++;
	                }

	                if(get_post_type($curriculum) == 'dtlms_quizzes') {
	                    $quizzes_counts++;
	                }

	                if(get_post_type($curriculum) == 'dtlms_assignments') {
	                	$assignments_counts++;
	                }

	                if(get_post_type($curriculum) == 'dtlms_lessons') {
		                $lesson_curriculum_counts = dtlms_course_lesson_curriculum_counts($curriculum);
		            } else {
		            	$lesson_curriculum_counts = '0|0|0';
		            }

		            $lesson_curriculum_counts = explode('|', $lesson_curriculum_counts);

		            $lessons_counts = $lessons_counts + $lesson_curriculum_counts[0];
		            $quizzes_counts = $quizzes_counts + $lesson_curriculum_counts[1];
		            $assignments_counts = $assignments_counts + $lesson_curriculum_counts[2];

	            }
            }
        }


        if($return_total) {
        	$output = ($lessons_counts+$quizzes_counts+$assignments_counts);
        } else {
        	$output = $lessons_counts.'|'.$quizzes_counts.'|'.$assignments_counts;
        }

		return $output;

	}
}

function dtlms_course_lesson_curriculum_counts($lesson_id) {

	$lessons_counts = $quizzes_counts = $assignments_counts = 0;

	$lesson_curriculum = get_post_meta ($lesson_id, 'lesson-curriculum', true);

	if(is_array($lesson_curriculum) && !empty($lesson_curriculum)) {
	    foreach($lesson_curriculum as $curriculum) {
	    	if(is_numeric($curriculum)) {    		

                if(get_post_type($curriculum) == 'dtlms_lessons') {
                	$lessons_counts++;
                }

                if(get_post_type($curriculum) == 'dtlms_quizzes') {
                    $quizzes_counts++;
                }

                if(get_post_type($curriculum) == 'dtlms_assignments') {
                	$assignments_counts++;
                }

	        }
	    }
	}

	return $lessons_counts.'|'.$quizzes_counts.'|'.$assignments_counts;

}

// Get course duration
if(!function_exists('dtlms_get_course_duration')) {
	function dtlms_get_course_duration($item_id, $style = '', $content_type = 'course') {

		if($content_type == 'others') {
			$curriculum_items = array ($item_id);
		} else {
			$curriculum_items = get_post_meta($item_id, 'course-curriculum', true);
		}

		$duration_in_seconds = 0;
        if(isset($curriculum_items) && is_array($curriculum_items)) {
            foreach($curriculum_items as $curriculum_item) {
            	if (is_numeric($curriculum_item)) { 
	            	$duration = get_post_meta ( $curriculum_item, 'duration', true );
	            	$duration = (isset($duration) && $duration > 0) ? $duration : 0;
	            	$duration_parameter = get_post_meta ( $curriculum_item, 'duration-parameter', true );
	            	$duration_parameter = (isset($duration_parameter) && $duration_parameter > 0) ? $duration_parameter : 0;
					$duration_in_seconds = ($duration * $duration_parameter) + $duration_in_seconds; 
					if($content_type == 'course' && get_post_type($curriculum_item) == 'dtlms_lessons') {
						$lesson_curriculum = get_post_meta($curriculum_item, 'lesson-curriculum', true);
						if(is_array($lesson_curriculum) && !empty($lesson_curriculum)) {
							foreach ($lesson_curriculum as $lesson_curriculum_item) {
								if (is_numeric($lesson_curriculum_item)) { 
					            	$duration = get_post_meta ( $lesson_curriculum_item, 'duration', true );
					            	$duration_parameter = get_post_meta ( $lesson_curriculum_item, 'duration-parameter', true );
									$duration_in_seconds = ($duration * $duration_parameter) + $duration_in_seconds; 
								}
							}
						}	
					}
				}
            }
        }

        $overall_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, $style);
	
		return $overall_duration;
		
	}
}

// Get course class details

if(!function_exists('dtlms_get_course_classes_lists')) {
	function dtlms_get_course_classes_lists($course_id) {
		
		$dtclasses = array( 'post_type' => 'dtlms_classes', 'fields' => 'ids' );
		$dtclasses_post = get_posts( $dtclasses );
		
		$class_ids = array();
		foreach($dtclasses_post as $dtclass) {
			
			$class_content_options_value = get_post_meta($dtclass, 'dtlms-class-content-options', true );
			
			if($class_content_options_value == 'course') {

				$class_courses = get_post_meta($dtclass, "dtlms-class-courses", true);
				if(!empty($class_courses)) {
					if(in_array($course_id, $class_courses)) {
						$class_ids[] = $dtclass;
					}
				}

			}
			
		}
		
		return $class_ids;
		
	}
}

if(!function_exists('dtlms_get_course_classes_details')) {
	function dtlms_get_course_classes_details($course_id, $options = 'existornot') {
		
		$out = '';
		
		$class_ids = dtlms_get_course_classes_lists($course_id);

		if($options == 'links') {

			foreach($class_ids as $class_id) {
				$out .= '<a href="'.get_permalink($class_id).'">'.get_the_title($class_id).'</a>, ';
			}

			return substr($out, 0, strlen($out) - 2);

		} else if($options == 'lists') {

			return $class_ids;

		} else {

			if(isset($class_ids) && !empty($class_ids)) {
				return true;
			} else {
				return false;
			}

		}
		
	}
}

add_action( 'wp_ajax_dtlms_start_course_initialize', 'dtlms_start_course_initialize' );
add_action( 'wp_ajax_nopriv_dtlms_start_course_initialize', 'dtlms_start_course_initialize' );
function dtlms_start_course_initialize() {

	$startcourse_nonce = $_POST['startcourse_nonce'];
	$course_id = $_POST['course_id'];
	$user_id = $_POST['user_id'];
	$author_id = $_POST['author_id'];

	if(isset($startcourse_nonce) && wp_verify_nonce($startcourse_nonce, 'start_course_'.$course_id.'_'.$user_id)) {

		$started_users = get_post_meta($course_id, 'started_users', true);
		$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array();
		array_push($started_users, $user_id);
		update_post_meta($course_id, 'started_users', array_unique($started_users));		

		$started_courses = get_user_meta($user_id, 'started_courses', true);
		$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();
		array_push($started_courses, $course_id);
		update_user_meta($user_id, 'started_courses', array_unique($started_courses));

		$current_timestamp = current_time( 'timestamp', 1 );

		// Create entry in gradings
		$user_info = get_userdata($user_id);

		//$title = $user_info->display_name.' - '.get_the_title($course_id);
		$title = get_the_title($course_id);

		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dtlms_gradings',
			'post_author' => $author_id,
		);
		
		$grade_post_id = wp_insert_post($grade_post);
		
		update_post_meta($grade_post_id, 'dtlms-course-id',  $course_id);
		update_post_meta($grade_post_id, 'dtlms-course-grade-id',  $grade_post_id );
		update_post_meta($grade_post_id, 'dtlms-user-id',  $user_id);
		update_post_meta($grade_post_id, 'grade-type', 'course' );
		update_post_meta($grade_post_id, 'started-timestamp', $current_timestamp );


		$curriculum_details = array ('started' => 1, 'started-timestamp' => $current_timestamp, 'grade-post-id' => $grade_post_id, 'curriculum' => array ());

		// For curriculum completion lock
		$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
		if($curriculum_completion_lock == 'true') {
			$course_curriculum = dtlms_get_course_numeric_curriculum($course_id);	
			if(is_array($course_curriculum) && !empty($course_curriculum)) {
				$course_curriculum_item = key($course_curriculum);
				
                if(get_post_type($course_curriculum_item) == 'dtlms_lessons') {
                	$free_item = get_post_meta ( $course_curriculum_item, 'free-lesson', true );
                }

                if(get_post_type($course_curriculum_item) == 'dtlms_quizzes') {
                    $free_item = get_post_meta ( $course_curriculum_item, 'free-quiz', true );
                }

                if(get_post_type($course_curriculum_item) == 'dtlms_assignments') {
                	$free_item = get_post_meta ( $course_curriculum_item, 'free-assignment', true );
                }

				if(!$free_item) {				
					$curriculum_details['next-curriculum-id'] = $course_curriculum_item;
					$curriculum_details['active-next-curriculum-id'] = $course_curriculum_item;
				} else {
					$next_curriculum_id = dtlms_get_course_next_curriculum_id($course_id, $course_curriculum_item, -1);
					$curriculum_details['next-curriculum-id'] = $next_curriculum_id;
					$curriculum_details['active-next-curriculum-id'] = $next_curriculum_id;					
				}
			}
		}
		update_user_meta($user_id, $course_id, $curriculum_details);

		// Notification & Mail - Start Course
		do_action('dtlms_poc_course_started', $course_id, $user_id); 

		// Notification & Mail - Drip Feed Content
		do_action('dtlms_poc_course_drip_content_agenda', $course_id, $user_id); 				

	}

	die();

}

function dtlms_generate_course_startnprogress($course_id, $user_id) {

	$out = '';

	$user_course_status = dtlms_get_user_course_purchase_status($course_id, $user_id);

	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

	$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
	$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();	

	$completed_courses = get_user_meta($user_id, 'completed_courses', true);
	$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();

	$course_data = get_post($course_id);
	$author_id = $course_data->post_author;

	$product = dtlms_get_product_object($course_id);


	$show_startcourse_button = true;

	$course_start_date = get_post_meta ( $course_id, 'course-start-date', true );
	$course_startdate_timestamp = strtotime($course_start_date);
	$current_timestamp = current_time( 'timestamp', 1 );

	$course_prerequisite = get_post_meta ( $course_id, 'course-prerequisite', true );


	if($current_timestamp < $course_startdate_timestamp) {

		$out .= '<div class="dtlms-course-dynamic-section-holder dtlms-dynamic-section-notification dtlms-dynamic-section-counter">';

			$show_startcourse_button = false;

			$out .= '<div class="dtlms-course-dynamic-section-startdate"><i class="fas fa-calendar-alt"></i>'.sprintf( esc_html__('Course starts on %1$s', 'dtlms'), '<strong>'.$course_start_date.'</strong>' ).'</div>';

			if('true' ==  dtlms_option('course','enable-countdown-timer-course-startdate')) {
				$countdown_date = dtlms_format_datetime($course_startdate_timestamp, 'm/d/Y H:i:s', true);
				$out .= dtlms_generate_countdown_html($countdown_date, -1, -1);	
			}	
			
		$out .= '</div>';

	} else if($course_prerequisite > 0) {

		$out .= '<div class="dtlms-course-dynamic-section-holder dtlms-dynamic-section-notification">';

			$show_startcourse_button = false;

			$startcourse_button_label = '<p>'.sprintf( esc_html__('You have to complete the course %1$s before, to take this course.', 'dtlms'), '<a href="'.get_permalink($course_prerequisite).'">'.get_the_title($course_prerequisite).'</a>' ).'</p>';

			if('true' ==  dtlms_option('course','course-prerequisite-on-complete')) {
				if(in_array($course_prerequisite, $completed_courses)) {
					$show_startcourse_button = true;
					$startcourse_button_label = '';
				}
			} else {
				if(in_array($course_prerequisite, $submitted_courses)) {
					$show_startcourse_button = true;
					$startcourse_button_label = '';
				}
			}

			$out .= $startcourse_button_label;

		$out .= '</div>';
		
	}

	if($show_startcourse_button && ($user_course_status || ($user_id > 0 && !dtlms_check_item_has_price($product)))) {

		if(in_array($course_id, $completed_courses)) {

			$curriculum_details = get_user_meta($user_id, $course_id, true);
			$course_grade_id = $curriculum_details['grade-post-id'];
			$user_percentage = get_post_meta($course_grade_id, 'user-percentage', true);
			$user_percentage = round($user_percentage, 2);

			$out .= '<div class="dtlms-course-dynamic-section-holder">';
				$out .= '<div class="dtlms-course-result-overview">';
					$out .= '<p>'.esc_html__('Your course have been evaluated successfully. Please click the below link to check the result.', 'dtlms').'</p>';
					$out .= '<div class="dtlms-item-student-score-details">';
						$out .= esc_html__('Your Score', 'dtlms');
						$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
						$out .= '<div class="dtlms-item-overview-progressbar">';
							$out .= dtlms_generate_progressbar($user_percentage);
							$out .= '<span class="dtlms-item-percentage">'.$user_percentage.'%</span>';
						$out .= '</div>';
					$out .= '</div>';
					$out .= '<a href="#" class="dtlms-button dtlms-view-course-result filled small" data-courseid="'.$course_id.'" data-userid="'.$user_id.'">'.esc_html__('View Results', 'dtlms').'</a>';
					$out .= dtlms_generate_loader_html(false);
				$out .= '</div>';
			$out .= '</div>';

		} else if(in_array($course_id, $submitted_courses)) {

			$out .= '<div class="dtlms-course-dynamic-section-holder">';
				$out .= '<p>'.esc_html__('Your course have been submitted successfully for evaluation.', 'dtlms').'</p>';
			$out .= '</div>';

		} else if(in_array($course_id, $started_courses)) {

			$total_curriculum_count = dtlms_course_curriculum_counts($course_id, true);
			$curriculum_details = get_user_meta($user_id, $course_id, true);

			$submitted_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'grade-post-id', 0);
			$graded_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'completed', 0);

			$submitted_percentage = $graded_percentage = 0;
			if($total_curriculum_count > 0) {

				if($submitted_items_count > 0) {
					$submitted_percentage = round((($submitted_items_count/$total_curriculum_count)*100), 2);
				} else {
					$submitted_percentage = 0;
				}

				if($graded_items_count > 0) {
					$graded_percentage = round((($graded_items_count/$total_curriculum_count)*100), 2);
				} else {
					$graded_percentage = 0;
				}

			}		
			
			$out .= '<div class="dtlms-course-dynamic-section-holder dtlms-dynamic-section-course-progress">';

				$out .= '<div class="dtlms-item-progress-details-holder">
							<div class="dtlms-title">'.esc_html__('Course Progress', 'dtlms').'</div>';
					$out .= '<div class="dtlms-item-student-submitted-item-details">';
						$out .= sprintf( esc_html__('Submitted %1$s/%2$s', 'dtlms'), '<span>'.$submitted_items_count, $total_curriculum_count.'</span>' );
						$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
						$out .= '<div class="dtlms-item-overview-progressbar">';
							$out .= dtlms_generate_progressbar($submitted_percentage);
							$out .= '<span class="dtlms-item-percentage">'.$submitted_percentage.'%</span>';
						$out .= '</div>';
					$out .= '</div>';					
					$out .= '<div class="dtlms-item-student-completed-item-details">';
						$out .= sprintf( esc_html__('Graded & Completed %1$s/%2$s', 'dtlms'), '<span>'.$graded_items_count, $total_curriculum_count.'</span>' );
						$out .= '<label>( '.esc_html__('% Out of 100', 'dtlms').' )</label>';
						$out .= '<div class="dtlms-item-overview-progressbar">';
							$out .= dtlms_generate_progressbar($graded_percentage);
							$out .= '<span class="dtlms-item-percentage">'.$graded_percentage.'%</span>';
						$out .= '</div>';
					$out .= '</div>';	
				$out .= '</div>';		

				$out .= '<div class="dtlms-item-submit-button-holder">';
					$out .= '<div class="dtlms-item-submit-button">';

						$out .= '<a href="#" class="dtlms-button dtlms-submit-course-button large" data-submit-course-nonce="'.wp_create_nonce('submit_course_'.$course_id.'_'.$user_id).'" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-authorid="'.$author_id.'" data-totalcurriculumcount="'.$total_curriculum_count.'" data-submittedcurriculumcount="'.$submitted_items_count.'">'.esc_html__('Submit Course', 'dtlms').'</a>';

					$out .= '</div>';
				$out .= '</div>';

			$out .= '</div>';

		} else {

			if($show_startcourse_button) {

				$out .= '<div class="dtlms-course-dynamic-section-holder">';
					$out .= '<div class="dtlms-item-progress-details-holder">';
						$out .= '<div class="dtlms-item-progress-details">';
							$out .= '<a href="#" class="dtlms-button dtlms-start-course-button large" data-start-course-nonce="'.wp_create_nonce('start_course_'.$course_id.'_'.$user_id).'" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-authorid="'.$author_id.'">'.esc_html__('Start Course', 'dtlms').'</a>';
						$out .= '</div>';
					$out .= '</div>';
				$out .= '</div>';

			}

		}

	}

    return $out;

}

add_action( 'wp_ajax_dtlms_submit_course_initialize', 'dtlms_submit_course_initialize' );
add_action( 'wp_ajax_nopriv_dtlms_submit_course_initialize', 'dtlms_submit_course_initialize' );
function dtlms_submit_course_initialize() {

	$submitcourse_nonce = $_POST['submitcourse_nonce'];
	$course_id = $_POST['course_id'];
	$user_id = $_POST['user_id'];
	$author_id = $_POST['author_id'];

	if(isset($submitcourse_nonce) && wp_verify_nonce($submitcourse_nonce, 'submit_course_'.$course_id.'_'.$user_id)) {

		$submitted_users = get_post_meta($course_id, 'submitted_users', true);
		$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
		array_push($submitted_users, $user_id);
		update_post_meta($course_id, 'submitted_users', array_unique($submitted_users));		

		$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
		$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();
		array_push($submitted_courses, $course_id);
		update_user_meta($user_id, 'submitted_courses', array_unique($submitted_courses));


		$curriculum_details = get_user_meta($user_id, $course_id, true);
		$course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

		if($course_grade_id > 0) {

			$completed_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'completed', 0);

			update_post_meta($course_grade_id, 'completed-count', $completed_items_count);
			update_post_meta($course_grade_id, 'submitted', 1 );

			$curriculum_details['completed-count'] = $completed_items_count;
			$curriculum_details['submitted'] = 1;

			update_user_meta($user_id, $course_id, $curriculum_details);

		}

		// Notification & Mail
		do_action('dtlms_poc_course_submitted', $course_id, $user_id); 

	}

	die();

}

// Social Sharer
function dtlms_generate_course_social_share($course_id, $type) {

	$out = '';

	$socialshare_items = get_post_meta($course_id, 'socialshare-items', true);
	if(!empty($socialshare_items)) {

		$add_class = '';
		if($type == 'type3') {
			$add_class = 'with-color';
		} else if($type == 'type4') {
			$add_class = 'with-color with-circle';
		}
		
		$out .= '<div class="dtlms-courses-share-holder">';
			if($type == 'type1') {
				$out .= '<span>'.esc_html__('Social Share', 'dtlms').'</span>';
			} else if($type == 'type5') {
				$out .= '<div class="dtlms-title">'.esc_html__('Share this Course: ', 'dtlms').'</div>';
			} else {
				$out .= '<div class="dtlms-title">'.esc_html__('Social Share', 'dtlms').'</div>';
			}
			$out .= '<ul class="dtlms-courses-share-list '.esc_attr( $add_class ).'">';

						$sstitle = get_the_title($course_id);
						$ssurl = get_permalink($course_id);

						if(in_array('facebook', $socialshare_items)) {
							$out .= '<li> <a href="//www.facebook.com/sharer.php?u='.$ssurl.'&amp;t='.urlencode($sstitle).'" title="facebook" target="_blank"> <span class="fab fa-facebook-f"></span>  </a> </li>';
						}
						if(in_array('delicious', $socialshare_items)) {
							$out .= '<li> <a href="//del.icio.us/post?url='.$ssurl.'&amp;title='.urlencode($sstitle).'" title="delicious" target="_blank"> <span class="fa fa-delicious"></span>  </a> </li>';
						}
						if(in_array('digg', $socialshare_items)) {
							$out .= '<li> <a href="//digg.com/submit?phase=2&amp;url='.$ssurl.'&amp;title='.urlencode($sstitle).'" title="digg" target="_blank"> <span class="fa fa-digg"></span>  </a> </li>';
						}
						if(in_array('stumbleupon', $socialshare_items)) {
							$out .= '<li> <a href="//www.stumbleupon.com/submit?url='.$ssurl.'&amp;title='.urlencode($sstitle).'" title="stumbleupon" target="_blank"> <span class="fab fa-stumbleupon"></span>  </a> </li>';
						}
						if(in_array('twitter', $socialshare_items)) {
							$out .= '<li> <a href="//twitter.com/home/?status='.$ssurl.':'.urlencode($sstitle).'" title="twitter" target="_blank"> <span class="fab fa-twitter"></span>  </a> </li>';
						}
						if(in_array('googleplus', $socialshare_items)) {
							$out .= '<li> <a href="//plus.google.com/share?url='.$ssurl.'" title="googleplus" target="_blank" onclick="javascript:window.open(this.href,\"\",\"menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600\");return false;"> <span class="fab fa-google-plus-g"></span>  </a> </li>';
						}
						if(in_array('linkedin', $socialshare_items)) {
							$out .= '<li> <a href="//www.linkedin.com/shareArticle?mini=true&amp;title='.urlencode($sstitle).'&amp;url='.$ssurl.'" title="linkedin" target="_blank"> <span class="fab fa-linkedin-in"></span>  </a> </li>';
						}
						if(in_array('pinterest', $socialshare_items)) {
							$media = isset($portfolio_settings['items'][0]) ? $portfolio_settings['items'][0] : ''; 
							$out .= '<li> <a href="//pinterest.com/pin/create/button/?url='.$ssurl.'&amp;media='.$media.'" title="pinterest" target="_blank"> <span class="fab fa-pinterest"></span>  </a> </li>'; 
						}

		  $out .= '</ul>
			</div>';
		
	}

	return $out;

}

// Course curriculum
function dtlms_generate_course_curriculum($user_id, $course_id, $duration_type, $item_id, $page_layout, $preview_curriculum = false) {

	$output = '';

	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

	$purchase_status = dtlms_get_user_course_purchase_status($course_id, $user_id);

	$product = dtlms_get_product_object($course_id);				
	$woo_price = dtlms_get_item_price_html($product);

	$free_course = false;
	if($woo_price == '') {
		$free_course = true;
	}


	$section_open = false;

	$curriculum_details = get_user_meta($user_id, $course_id, true);

	$course_curriculum = get_post_meta($course_id, 'course-curriculum', true);

	$allnumberic_curriculum = false;
	if ( is_array($course_curriculum) && !empty($course_curriculum) ) {
		if ( count( $course_curriculum ) === count( array_filter( $course_curriculum, 'is_numeric' ) ) ) {
			$allnumberic_curriculum = true;
		}
	}

	// Drip Feed
	$drip_feed = get_post_meta($course_id, 'drip-feed', true);


	// Curriculum completion lock
	$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
	$curriculum_completion_lock_class = '';
	if($curriculum_completion_lock == 'true') {
		$curriculum_completion_lock_class = 'with-completion-lock';
	}
	
	$output .= '<div class="dtlms-course-curriculum-toggle-group-holder">';

	if(isset($course_curriculum) && is_array($course_curriculum)) {

		if($allnumberic_curriculum) {
        	$output .= '<div class="dtlms-toggle-group-set">
		        			<div class="dtlms-toggle-content" style="display: block;">
		        				<div class="block">
		        					<ul class="dtlms-curriculum-list '.$curriculum_completion_lock_class.'">';
		}

		$i = 0;
	    foreach($course_curriculum as $curriculum) {

	    	if(is_numeric($curriculum)) {

	    		$item_li_class = '';

				// Item type
    			$free_item = false;
    			$lesson_id = -1;
    			$item_type = '';
                if(get_post_type($curriculum) == 'dtlms_lessons') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-file-o"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-lesson', true );
                	$lesson_id = $curriculum;
                }
                if(get_post_type($curriculum) == 'dtlms_quizzes') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-puzzle-piece"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-quiz', true );
                }
                if(get_post_type($curriculum) == 'dtlms_assignments') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-file-text"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-assignment', true );
                }

                if($free_item) {
                	$item_li_class .= ' preview-item';
                }

                if(in_array($course_id, $started_courses)) {
                	$item_li_class .= ' purchased-item';
                }

				// Curriculum completion lock
				if($curriculum_completion_lock == 'true') {

					if($user_id > 0) {

						if($purchase_status || $free_course) {

							if(in_array($course_id, $started_courses)) {

								$disable_lock = false;
								$active_next_curriculum_item = isset($curriculum_details['active-next-curriculum-id']) ? $curriculum_details['active-next-curriculum-id'] : -1;
								if($active_next_curriculum_item == $curriculum || (isset($curriculum_details['curriculum'][$curriculum]['grade-post-id']) && $curriculum_details['curriculum'][$curriculum]['grade-post-id'] != '')) {
									$disable_lock = true;
								}

				           		if(!$preview_curriculum && !$free_item) {
				           			if($disable_lock) {
				           				$item_li_class .= ' unlocked';
				           			} else {
					           			$item_li_class .= ' locked';
				           			}
				           		}

				           	} else {
				           		if(!$preview_curriculum && !$free_item) {
					           		$item_li_class .= ' locked';
					           	}
				           	}

			           	} else {
			           		if(!$preview_curriculum && !$free_item) {
				           		$item_li_class .= ' locked';
				           	}
			           	}

		           	} else {

		           		if(!$preview_curriculum && !$free_item) {
			           		$item_li_class .= ' locked';
			           	}

		           	}					

				}

				if($drip_feed == 'true') {

					if($user_id > 0) {

						if(in_array($course_id, $started_courses)) {
							$drip_feed_enable = dtlms_course_drip_feed_check($course_id, $curriculum, $user_id);
							if($drip_feed_enable != 'true') {
								$item_li_class .= ' drip-locked';
							}
						} else {
							if(!$preview_curriculum && !$free_item) {
								$item_li_class .= ' drip-locked';
							}
						}
						
		           	} else {

		           		if(!$preview_curriculum && !$free_item) {
			           		$item_li_class .= ' drip-locked';
			           	}
		           		
		           	}
		           					
				}

    			if($curriculum == $item_id) {
    				$item_li_class .= ' active';
    			}          		

	    		$output .= '<li class="'.$item_li_class.'">';

					if($page_layout != 'type5') {
						$output .= $item_type;
					}
              		
	                $completed = (isset($curriculum_details['curriculum'][$curriculum]['completed']) && $curriculum_details['curriculum'][$curriculum]['completed'] == 1) ? true : false;
	                $completed_status = '';
	                if($completed) {
	                	$completed_status = '<span class="dtlms-completed"><span class="fa fa-check"></span></span>';
	                }

               		if($preview_curriculum) {

               			$output .= '<div class="dtlms-curriculum-meta-title">'.get_the_title($curriculum).'</div>';

           			} else if($free_item) {

           				$output .= '<div class="dtlms-curriculum-meta-title"><a href="#" onclick="return false;" data-courseid="'.$course_id.'" data-parentcurriculumid="-1"  data-curriculumid="'.$curriculum.'">'.get_the_title($curriculum).$completed_status.'</a></div>';

               		} else if(in_array($course_id, $started_courses)) {

	                	$output .= '<div class="dtlms-curriculum-meta-title"><a href="#" onclick="return false;" data-courseid="'.$course_id.'" data-parentcurriculumid="-1"  data-curriculumid="'.$curriculum.'">'.get_the_title($curriculum).$completed_status.'</a></div>';

	                } else {

	                	$output .= '<div class="dtlms-curriculum-meta-title">'.get_the_title($curriculum).'</div>';

	                }

					if($page_layout == 'type5') {
						$output .= $item_type;
					}

	                $output .= '<div class="dtlms-curriculum-meta-items">';

		                if($free_item) {
		                	$output .= '<div class="dtlms-curriculum-meta-preview">'.esc_html__('Free', 'dtlms').'</div>';
		                }
		                
		                $curriculum_duration = dtlms_get_course_duration($curriculum, 'style4', 'others');
		                if($curriculum_duration != 0) {
		                    $output .= '<div class="dtlms-curriculum-meta-duration">';
			                    if($item_id == -1 && $page_layout != 'type5') {
				                    $output .= esc_html__( 'Duration : ' );
				            	}
			                    $output .= '<span>'. $curriculum_duration.'</span>';
		                    $output .= '</div>';
		                }

	                $output .= '</div>';

	                $output .= dtlms_generate_course_lesson_curriculum($user_id, $lesson_id, $course_id, $started_courses, $duration_type, $item_id, $preview_curriculum, $purchase_status, $free_course, $page_layout);

	            $output .= '</li>';

	            $i++;

	        } else {

	        	if($section_open) {
	        		$output .= '</ul></div></div></div>';
	        	}

	        	$output .= '<div class="dtlms-toggle-group-set">
			        			<h5 class="dtlms-toggle active"><a href="#">'.esc_html($curriculum).'</a></h5>
			        			<div class="dtlms-toggle-content" style="display: block;">
			        				<div class="block">
			        					<ul class="dtlms-curriculum-list '.$curriculum_completion_lock_class.'">';

	        	$section_open = true;

	        }
	        
	    }

		if($section_open || $allnumberic_curriculum) {
			$output .= '</ul></div></div></div>';
		}
	}

	$output .= '</div>';

	return $output;

}

function dtlms_generate_course_lesson_curriculum($user_id, $lesson_id, $course_id, $started_courses, $duration_type, $item_id, $preview_curriculum, $purchase_status, $free_course, $page_layout) {

	$output = '';


	$section_open = false;

	$curriculum_details = get_user_meta($user_id, $course_id, true);
	$lesson_curriculum = get_post_meta($lesson_id, 'lesson-curriculum', true);


	$allnumberic_curriculum = false;
	if ( is_array($lesson_curriculum) && !empty($lesson_curriculum) ) {
		if ( count( $lesson_curriculum ) === count( array_filter( $lesson_curriculum, 'is_numeric' ) ) ) {
			$allnumberic_curriculum = true;
		}
	}

	// Drip Feed
	$drip_feed = get_post_meta($course_id, 'drip-feed', true);


	if(isset($lesson_curriculum) && is_array($lesson_curriculum)) {

		if($allnumberic_curriculum) {
        	$output .= '<div class="dtlms-toggle-group-set">
		        			<div class="dtlms-toggle-content" style="display: block;">
		        				<div class="block">
		        					<ul class="dtlms-curriculum-list">';
		}

	    foreach($lesson_curriculum as $curriculum) {
	    	if(is_numeric($curriculum)) {

	    		$item_li_class = '';

				// Curriculum item type
    			$free_item = false;
    			$item_type = '';
                if(get_post_type($curriculum) == 'dtlms_lessons') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-file-o"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-lesson', true );
                }
                if(get_post_type($curriculum) == 'dtlms_quizzes') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-puzzle-piece"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-quiz', true );
                }
                if(get_post_type($curriculum) == 'dtlms_assignments') {
                	$item_type .= '<div class="dtlms-curriculum-meta-icon"><span class="fa fa-file-text"></span></div>';
                	$free_item = get_post_meta ( $curriculum, 'free-assignment', true );
                }

                if($free_item) {
                	$item_li_class .= ' preview-item';
                }
                
                if(in_array($course_id, $started_courses)) {
                	$item_li_class .= ' purchased-item';
                }

				// Curriculum completion lock
				$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
				if($curriculum_completion_lock == 'true') {

					if($user_id > 0) {

						if($purchase_status || $free_course) {

							if(in_array($course_id, $started_courses)) {

								$disable_lock = false;

								$active_next_curriculum_item = isset($curriculum_details['active-next-curriculum-id']) ? $curriculum_details['active-next-curriculum-id'] : -1;
								if($active_next_curriculum_item == $curriculum || (isset($curriculum_details['curriculum'][$lesson_id]['curriculum'][$curriculum]['grade-post-id']) && $curriculum_details['curriculum'][$lesson_id]['curriculum'][$curriculum]['grade-post-id'] != '')) {
									$disable_lock = true;
								}

				           		if(!$preview_curriculum && !$free_item) {
				           			if($disable_lock) {
				           				$item_li_class .= ' unlocked';
				           			} else {
					           			$item_li_class .= ' locked';
					           		}
				           		}

				           	} else {
				           		if(!$preview_curriculum && !$free_item) {
					           		$item_li_class .= ' locked';
					           	}
				           	}


			           	} else {
			           		if(!$preview_curriculum && !$free_item) {
				           		$item_li_class .= ' locked';	
				           	}
			           	}

		           	} else {

		           		if(!$preview_curriculum && !$free_item) {
			           		$item_li_class .= ' locked';	
			           	}

		           	}		           		

				}

				if($drip_feed == 'true') {
					if($user_id > 0) {

						if(in_array($course_id, $started_courses)) {
							$drip_feed_enable = dtlms_course_drip_feed_check($course_id, $curriculum, $user_id);
							if($drip_feed_enable != 'true') {
								$item_li_class .= ' drip-locked';
							}
						} else {
							if(!$preview_curriculum && !$free_item) {
								$item_li_class .= ' drip-locked';
							}							
						}

		           	} else {

						if(!$preview_curriculum && !$free_item) {
							$item_li_class .= ' drip-locked';
						}
		           		
		           	}						
				}

    			if($curriculum == $item_id) {
    				$item_li_class .= ' active';
    			}          		

	    		$output .= '<li class="'.$item_li_class.'">';

					if($page_layout == 'type5') {
						$output .= $item_type;
					}

	                $completed = (isset($curriculum_details['curriculum'][$lesson_id]['curriculum'][$curriculum]['completed']) && $curriculum_details['curriculum'][$lesson_id]['curriculum'][$curriculum]['completed'] == 1) ? true : false;
	                $completed_status = '';
	                if($completed) {
	                	$completed_status = '<span class="dtlms-completed"><span class="fa fa-check"></span></span>';
	                }

               		if($preview_curriculum) {

               			$output .= '<div class="dtlms-curriculum-meta-title">'.get_the_title($curriculum).'</div>';

               		} else if($free_item) {

               			$output .= '<div class="dtlms-curriculum-meta-title"><a href="#" onclick="return false;" data-courseid="'.$course_id.'" data-parentcurriculumid="'.$lesson_id.'"  data-curriculumid="'.$curriculum.'">'.get_the_title($curriculum).$completed_status.'</a></div>';

               		} else if(in_array($course_id, $started_courses)) {

	                	$output .= '<div class="dtlms-curriculum-meta-title"><a href="#" onclick="return false;" data-courseid="'.$course_id.'" data-parentcurriculumid="'.$lesson_id.'"  data-curriculumid="'.$curriculum.'">'.get_the_title($curriculum).$completed_status.'</a></div>';

	                } else {

	                	$output .= '<div class="dtlms-curriculum-meta-title">'.get_the_title($curriculum).'</div>';

	                }

					if($page_layout == 'type5') {
						$output .= $item_type;
					}

	                $output .= '<div class="dtlms-curriculum-meta-items">';

		                if($free_item) {
		                	$output .= '<div class="dtlms-curriculum-meta-preview">'.esc_html__('Free', 'dtlms').'</div>';
		                }
		                
		                $curriculum_duration = dtlms_get_course_duration($curriculum, 'style4', 'others');
		                if($curriculum_duration != 0) {
		                    $output .= '<div class="dtlms-curriculum-meta-duration">';
			                    if($item_id == -1 && $page_layout != 'type5') {
				                    $output .= esc_html__( 'Duration : ' );
				            	}
			                    $output .= '<span>'. $curriculum_duration.'</span>';
		                    $output .= '</div>';		                    
		                }

	                $output .= '</div>';

	            $output .= '</li>';

	        } else {

	        	if($section_open) {
	        		$output .= '</ul></div></div></div>';
	        	}

	        	$output .= '<div class="dtlms-toggle-group-set">
			        			<h5 class="dtlms-toggle active"><a href="#">'.esc_html($curriculum).'</a></h5>
			        			<div class="dtlms-toggle-content" style="display: block;">
			        				<div class="block">
			        					<ul class="dtlms-curriculum-list">';

	        	$section_open = true;

	        }
	    }
		if($section_open || $allnumberic_curriculum) {
			$output .= '</div></div></div>';
		}
	}

	return $output;

}


/*
* Courses Listing
*/

function dtlms_courses_listing_search_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$dtlms_courses_search_text = isset($request['dtlms-courses-search-text']) ? $request['dtlms-courses-search-text'] : '';
		$output .= '<div class="dtlms-courses-search-filter">';
			$output .= '<input name="dtlms-courses-search-text" class="dtlms-courses-search-text" type="text" value="'.$dtlms_courses_search_text.'" placeholder="'.esc_html__('Search Course', 'dtlms').'" />';
		$output .= '</div>';

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}		

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
			$output .= '<div class="dtlms-courses-search-filter">';
				$output .= '<input name="dtlms-courses-search-text" class="dtlms-courses-search-text dtlms-without-ajax-load" type="text" value="" placeholder="'.esc_html__('Keywords', 'dtlms').'" />';
			$output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_courses_listing_category_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_category = isset($request['coursefilter-category']) ? $request['coursefilter-category'] : array ();
	    $output .= '<div class="dtlms-courses-category-filter">';
	                     $output .= '<div class="dtlms-title">'.esc_html__('Course Categories', 'dtlms').'</div>';
	                     $output .= '<ul>';
	                        $cats = get_categories('taxonomy=course_category&hide_empty=1');
	                        if(isset($cats)) { 
	                            foreach($cats as $cat) {
	                                $output .= '<li>
	                                				<input type="checkbox" name="coursefilter-category" class="coursefilter-category" value="'.$cat->term_id.'" id="coursefilter-category-'.$cat->term_id.'" '.checked(in_array($cat->term_id, $coursefilter_category), true, false).' />
	                                				<label for="coursefilter-category-'.$cat->term_id.'">'.$cat->name.'</label>
	                                			</li>';
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
		    $output .= '<div class="dtlms-courses-category-filter">';
					        $output .= '<select class="coursefilter-category dtlms-without-ajax-load dtlms-chosen-select" name="coursefilter-category[]" data-placeholder="'.esc_html__('Categories', 'dtlms').'" multiple>';
		                        $cats = get_categories('taxonomy=course_category&hide_empty=1');
		                        if(isset($cats)) { 
		                            foreach($cats as $cat) {
		                            	$output .= '<option value="'.$cat->term_id.'">'.$cat->name.'</option>';
		                            }
		                        }				            
							$output .= '</select>';
		    $output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_courses_listing_instructor_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_instructor = isset($request['coursefilter-instructor']) ? $request['coursefilter-instructor'] : array ();
       	$output .= '<div class="dtlms-courses-instructor-filter">
                        <div class="dtlms-title">'.esc_html__('Instructor', 'dtlms').'</div>
                        <ul>';
							$instructors = get_users ( array ('role' => 'instructor') );
					        if ( count( $instructors ) > 0 ) {
					            foreach ($instructors as $instructor) {
									$instructor_id = $instructor->data->ID;
					                $output .= '<li><input type="checkbox" name="coursefilter-instructor" class="coursefilter-instructor" value="'.$instructor_id.'" id="coursefilter-instructor-'.$instructor_id.'" '.checked(in_array($instructor_id, $coursefilter_instructor), true, false).' /><label for="coursefilter-instructor-'.$instructor_id.'">'.$instructor->data->display_name.'</label></li>';
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
		    $output .= '<div class="dtlms-courses-instructor-filter">';
					        $output .= '<select class="coursefilter-instructor dtlms-without-ajax-load dtlms-chosen-select" name="coursefilter-instructor[]" data-placeholder="'.esc_html__('Instructor', 'dtlms').'" multiple>';
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

function dtlms_courses_listing_cost_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_cost = isset($_REQUEST['coursefilter-cost']) ? $_REQUEST['coursefilter-cost'] : 'all';
       	$output .= '<div class="dtlms-courses-cost-filter">
                        <div class="dtlms-title">'.esc_html__('Cost', 'dtlms').'</div>
                        <ul>
                            <li><input type="radio" name="coursefilter-cost" class="coursefilter-cost " value="all" id="coursefilter-cost-all" '.checked('all', $coursefilter_cost, false).' /><label for="coursefilter-cost-all">'.esc_html__('All', 'dtlms').'</label></li>
                            <li><input type="radio" name="coursefilter-cost" class="coursefilter-cost" value="free" id="coursefilter-cost-free" '.checked('free', $coursefilter_cost, false).' /><label for="coursefilter-cost-free"">'.esc_html__('Free', 'dtlms').'</label></li>
                            <li><input type="radio" name="coursefilter-cost" class="coursefilter-cost" value="paid" id="coursefilter-cost-paid" '.checked('paid', $coursefilter_cost, false).' /><label for="coursefilter-cost-paid">'.esc_html__('Paid', 'dtlms').'</label></li>';
            $output .= '</ul>';
        $output .= '</div>';        	    

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		    $output .= '<div class="dtlms-courses-cost-filter">';
					        $output .= '<select class="coursefilter-cost dtlms-without-ajax-load dtlms-chosen-select" name="coursefilter-cost" data-placeholder="'.esc_html__('Cost', 'dtlms').'">';
		                        $output .= '<option value="all">'.esc_html__('All', 'dtlms').'</option>';
		                        $output .= '<option value="free">'.esc_html__('Free', 'dtlms').'</option>';
		                        $output .= '<option value="paid">'.esc_html__('Paid', 'dtlms').'</option>';				            
							$output .= '</select>';
		    $output .= '</div>';
		$output .= '</div>';

	}

	return $output;

}

function dtlms_courses_listing_startdate_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_date = isset($request['coursefilter-date']) ? $request['coursefilter-date'] : '';
	   	$output .= '<div class="dtlms-courses-date-filter">
	                    <div class="dtlms-title">'.esc_html__('Start Date :', 'dtlms').'</div>
	                    <div class="dtlms-courses-date-filter-holder">
	                    	<input type="text" name="coursefilter-date" class="coursefilter-date dtlms-datepicker" placeholder="'.esc_html__('Start Date', 'dtlms').'" value="'.$coursefilter_date.'" readonly />
	                    </div>
	                </div>';            

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
		   	$output .= '<div class="dtlms-courses-date-filter">';
		   		$output .= '<div class="dtlms-courses-date-filter-holder">';
		            $output .= '<input type="text" name="coursefilter-date" class="coursefilter-date dtlms-datepicker dtlms-without-ajax-load" placeholder="'.esc_html__('Start Date', 'dtlms').'" value="" readonly />';
		        $output .= '</div>';
		    $output .= '</div>';
		$output .= '</div>'; 

	}

	return $output;

}

function dtlms_courses_listing_display_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_display = isset($_REQUEST['coursefilter-display-default']) ? $_REQUEST['coursefilter-display-default'] : 'grid';
		if($coursefilter_display == 'grid') {
			$grid_class = 'active';
			$list_class = '';
		} else if($coursefilter_display == 'list') {
			$grid_class = '';
			$list_class = 'active';
		}
       	$output .= '<div class="dtlms-courses-display-filter">
                        <a class="dtlms-courses-display-type grid '.$grid_class.'" data-displaytype="grid"><span></span>'.esc_html__('Grid', 'dtlms').'</a>
                        <a class="dtlms-courses-display-type list '.$list_class.'" data-displaytype="list"><span></span>'.esc_html__('List', 'dtlms').'</a>
                    </div>';


	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
	       	$output .= '<div class="dtlms-courses-display-filter">
					        <select class="coursefilter-display dtlms-without-ajax-load dtlms-chosen-select" name="coursefilter-display" data-placeholder="'.esc_html__('Display Type', 'dtlms').'">
					        	<option value="grid">'.esc_html__( 'Grid', 'dtlms' ).'</option>
					            <option value="list">'.esc_html__( 'List', 'dtlms' ).'</option>
							</select>';
	        $output .= '</div>';
	    $output .= '</div>';

	}

	return $output;

}

function dtlms_courses_listing_orderby_field($request, $ajax_load, $column_cnt) {

	$output = '';

	if($ajax_load) {

		$coursefilter_orderby = isset($_REQUEST['coursefilter-orderby']) ? $_REQUEST['coursefilter-orderby'] : '';
       	$output .= '<div class="dtlms-courses-orderby-filter">
                        <label>'.esc_html__('Order by :', 'dtlms').'</label>
				        <select class="coursefilter-orderby" name="coursefilter-orderby" data-placeholder="'.esc_html__('Select Order', 'dtlms').'">
				        	<option value="" '.selected('', $coursefilter_orderby, false).'>'.esc_html__( 'Select Order', 'dtlms' ).'</option>
				        	<option value="recent-courses" '.selected('recent-courses', $coursefilter_orderby, false).'>'.esc_html__( 'Recent Courses', 'dtlms' ).'</option>
				            <option value="highest-rated" '.selected('highest-rated', $coursefilter_orderby, false).'>'.esc_html__( 'Highest Rated', 'dtlms' ).'</option>
				            <option value="most-members" '.selected('most-members', $coursefilter_orderby, false).'>'.esc_html__( 'Most Members', 'dtlms' ).'</option>
				            <option value="alphabetical" '.selected('alphabetical', $coursefilter_orderby, false).'>'.esc_html__( 'Alphabetical', 'dtlms' ).'</option>
						</select>';
        $output .= '</div>';

	} else {

		$first_class = '';
		if($column_cnt == 0) {
			$first_class = 'first';	
		}

		$coursefilter_orderby = isset($_REQUEST['coursefilter-orderby']) ? $_REQUEST['coursefilter-orderby'] : '';
		$output .= '<div class="dtlms-column dtlms-one-third '.$first_class.'">';
	       	$output .= '<div class="dtlms-courses-orderby-filter">
					        <select class="coursefilter-orderby dtlms-without-ajax-load dtlms-chosen-select" name="coursefilter-orderby" data-placeholder="'.esc_html__('Select Order', 'dtlms').'">
					        	<option value="recent-courses">'.esc_html__( 'Recent Courses', 'dtlms' ).'</option>
					            <option value="highest-rated">'.esc_html__( 'Highest Rated', 'dtlms' ).'</option>
					            <option value="most-members">'.esc_html__( 'Most Members', 'dtlms' ).'</option>
					            <option value="alphabetical">'.esc_html__( 'Alphabetical', 'dtlms' ).'</option>
							</select>';
	        $output .= '</div>';
        $output .= '</div>';

	}

	return $output;

}

function dtlms_courses_listing_content($courses_listing_options) {

	$output = '';

	$course_carousel_attributes = $course_listing_attributes = array ();
	$holder_class = $container_class = $course_carousel_attributes_string = $course_listing_attributes_string = '';

	$ajax_load = true;
	if($courses_listing_options['listing-output-page'] != '') {
		$ajax_load = false;
	}

	$disable_all_filters = false;
	$enable_fullwidth = false;

	if($courses_listing_options['class'] != '') {
		$holder_class .= ' '.$courses_listing_options['class'];
	}
		
	if($ajax_load) {
		
		if($courses_listing_options['disable-all-filters'] == 'true') {
			$disable_all_filters = true;
		}		
		
		if($courses_listing_options['enable-fullwidth'] == 'true') {
			$enable_fullwidth = true;
		}	

		if($courses_listing_options['enable-carousel'] == 'true') {

			array_push($course_carousel_attributes, 'data-enablecarousel="true"');
			array_push($course_carousel_attributes, 'data-carouseleffect="'.$courses_listing_options['carousel-effect'].'"');
			array_push($course_carousel_attributes, 'data-carouselautoplay="'.$courses_listing_options['carousel-autoplay'].'"');
			array_push($course_carousel_attributes, 'data-carouselslidesperview="'.$courses_listing_options['carousel-slidesperview'].'"');
			array_push($course_carousel_attributes, 'data-carouselloopmode="'.$courses_listing_options['carousel-loopmode'].'"');
			array_push($course_carousel_attributes, 'data-carouselmousewheelcontrol="'.$courses_listing_options['carousel-mousewheelcontrol'].'"');
			array_push($course_carousel_attributes, 'data-carouselbulletpagination="'.$courses_listing_options['carousel-bulletpagination'].'"');
			array_push($course_carousel_attributes, 'data-carouselarrowpagination="'.$courses_listing_options['carousel-arrowpagination'].'"');
			array_push($course_carousel_attributes, 'data-carouselspacebetween="'.$courses_listing_options['carousel-spacebetween'].'"');

			$container_class .= ' swiper-wrapper';

		} else {

			array_push($course_listing_attributes, 'data-enablecarousel="false"');

			if($courses_listing_options['apply-isotope'] == 'true') {
				$container_class .= ' dtlms-apply-isotope';
			}

		}

		if(!empty($course_carousel_attributes)) {
			$course_carousel_attributes_string = implode(' ', $course_carousel_attributes);
		}
		

		array_push($course_listing_attributes, 'data-postperpage="'.$courses_listing_options['post-per-page'].'"');
		array_push($course_listing_attributes, 'data-columns="'.$courses_listing_options['columns'].'"');
		array_push($course_listing_attributes, 'data-showauthordetails="'.$courses_listing_options['show-author-details'].'"');
		array_push($course_listing_attributes, 'data-applyisotope="'.$courses_listing_options['apply-isotope'].'"');
		array_push($course_listing_attributes, 'data-enablecategoryisotopefilter="'.$courses_listing_options['enable-category-isotope-filter'].'"');

		if($courses_listing_options['disable-all-filters'] == 'true') {
			array_push($course_listing_attributes, 'data-disablefilters="true"');
		} else {
			array_push($course_listing_attributes, 'data-disablefilters="false"');
		}

		array_push($course_listing_attributes, 'data-defaultfilter="'.$courses_listing_options['default-filter'].'"');

		$display_type = 'grid';
		if($courses_listing_options['default-display-type']) {
			$display_type = $courses_listing_options['default-display-type'];
		}		
		array_push($course_listing_attributes, 'data-defaultdisplaytype="'.$display_type.'"');
		array_push($course_listing_attributes, 'data-courseitemids="'.$courses_listing_options['course-item-ids'].'"');
		array_push($course_listing_attributes, 'data-coursecategoryids="'.$courses_listing_options['course-category-ids'].'"');
		array_push($course_listing_attributes, 'data-instructorids="'.$courses_listing_options['instructor-ids'].'"');
		array_push($course_listing_attributes, 'data-enablefullwidth="'.$courses_listing_options['enable-fullwidth'].'"');
		array_push($course_listing_attributes, 'data-type="'.$courses_listing_options['type'].'"');
		array_push($course_listing_attributes, 'data-showdescription="'.$courses_listing_options['show-description'].'"');
		array_push($course_listing_attributes, 'data-hidelabels="'.$courses_listing_options['hide-labels'].'"');
		array_push($course_listing_attributes, 'data-hidecategory="'.$courses_listing_options['hide-category'].'"');
		array_push($course_listing_attributes, 'data-hideaddtocartbutton="'.$courses_listing_options['hide-addtocartbutton'].'"');
		array_push($course_listing_attributes, 'data-class="'.$courses_listing_options['class'].'"');

		if(!empty($course_listing_attributes)) {
			$course_listing_attributes_string = implode(' ', $course_listing_attributes);
		}

		if(isset($_REQUEST['coursefilter-display']) && $_REQUEST['coursefilter-display'] != '') {
			$container_display_type = $_REQUEST['coursefilter-display'];
			$_REQUEST['coursefilter-display-default'] = $_REQUEST['coursefilter-display'];
		} else {
			$container_display_type = $display_type;
			$_REQUEST['coursefilter-display-default'] = $display_type;
		}

	} else {

		$holder_class .= ' dtlms-without-ajax-load';
		$disable_all_filters = false;
		$container_display_type = '';

	}

	$output .= '<div class="dtlms-courses-listing-holder '.$container_display_type.' '.$holder_class.'" '.$course_listing_attributes_string.' '.$course_carousel_attributes_string.'>';

			if($ajax_load) {
				if(!$disable_all_filters && !$enable_fullwidth) {
					$output .= '<div class="dtlms-column dtlms-one-third first">';
				}
			} else {
				$output .= '<form name="dtlmsCoursesListingSearchForm" action="'.get_permalink($courses_listing_options['listing-output-page']).'" method="post">';
			}			

			if(!$disable_all_filters) {

				$output .= '<div class="dtlms-courses-listing-filters">';

							$column_cnt = 0;
							if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-search-filter'] == 'true') {
								$output .= dtlms_courses_listing_search_field($_REQUEST, $ajax_load, $column_cnt);
								$column_cnt++;
							}

			 				if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-category-filter'] == 'true') {
			 					$output .= dtlms_courses_listing_category_field($_REQUEST, $ajax_load, $column_cnt);
			 					$column_cnt++;
			                } 

			  				if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-instructor-filter'] == 'true') {
			  					$output .= dtlms_courses_listing_instructor_field($_REQUEST, $ajax_load, $column_cnt);
			  					$column_cnt++;
			                }

			  				if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-cost-filter'] == 'true') {
			  					if($column_cnt == 3) {
			  						$column_cnt = 0;
			  					}
			  					$output .= dtlms_courses_listing_cost_field($_REQUEST, $ajax_load, $column_cnt);
			  					$column_cnt++;
			                }

			  				if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-date-filter'] == 'true') {
			  					if($column_cnt == 3) {
			  						$column_cnt = 0;
			  					}			  					
			  					$output .= dtlms_courses_listing_startdate_field($_REQUEST, $ajax_load, $column_cnt);
			  					$column_cnt++;
			                }	

							if(!$ajax_load) {

								if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-display-filter'] == 'true') {
				  					if($column_cnt == 3) {
				  						$column_cnt = 0;
				  					}									
									$output .= dtlms_courses_listing_display_field($_REQUEST, $ajax_load, $column_cnt);
									$column_cnt++;
							    }

								if($courses_listing_options['disable-all-filters'] != 'true' && $courses_listing_options['enable-orderby-filter'] == 'true') {
				  					if($column_cnt == 3) {
				  						$column_cnt = 0;
				  					}									
									$output .= dtlms_courses_listing_orderby_field($_REQUEST, $ajax_load, $column_cnt);
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

					if($courses_listing_options['enable-display-filter'] == 'true' || $courses_listing_options['enable-orderby-filter'] == 'true') {

						$output .= '<div class="dtlms-courses-listing-rightside-filter">';

							if($courses_listing_options['enable-display-filter'] == 'true') {
								$output .= dtlms_courses_listing_display_field($_REQUEST, $ajax_load, 0);
			                }

							if($courses_listing_options['enable-orderby-filter'] == 'true') {
								$output .= dtlms_courses_listing_orderby_field($_REQUEST, $ajax_load, 0);
			                }

			            $output .= '</div>';

			        }

			    }

				    if($courses_listing_options['enable-carousel'] == 'true') {
				    	$output .= '<div class="dtlms-courses-swiper-listing" '.$course_carousel_attributes_string.'>';
				    }

					    $output .= '<div class="dtlms-courses-listing-containers '.$container_display_type.' '.$container_class.'">'.dtlms_generate_loader_html(false).'</div>';

					if($courses_listing_options['enable-carousel'] == 'true') {

						if($courses_listing_options['carousel-bulletpagination'] == 'true' || $courses_listing_options['carousel-arrowpagination'] == 'true') {
							$output .= '<div class="dtlms-swiper-pagination-holder">';
								if($courses_listing_options['carousel-bulletpagination'] == 'true') {
									$output .= '<div class="dtlms-swiper-bullet-pagination"></div>';	
								}
								if($courses_listing_options['carousel-arrowpagination'] == 'true') {				
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

				$output .= '<input type="submit" name="dtlms-courses-listing-searchform-submit" class="dtlms-courses-listing-searchform-submit" value="'.esc_html__('Search Courses', 'dtlms').'" />';

				$output .= '</form>';

			}

	$output .= '</div>';


    return $output;

}


add_action( 'wp_ajax_dtlms_generate_courses_listing', 'dtlms_generate_courses_listing' );
add_action( 'wp_ajax_nopriv_dtlms_generate_courses_listing', 'dtlms_generate_courses_listing' );
function dtlms_generate_courses_listing() {

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

	$show_author_details = $_REQUEST['show_author_details'];

	if(isset($_REQUEST['display_type']) && $_REQUEST['display_type'] != '') {
		$display_type = $_REQUEST['display_type'];
	} else {
		$display_type = isset($_REQUEST['default_display_type']) ? $_REQUEST['default_display_type'] : 'grid';
	}

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'type1';
	$show_description = (isset($_REQUEST['show_description']) && $_REQUEST['show_description'] == 'true') ? true : false;
	$hide_labels = (isset($_REQUEST['hide_labels']) && $_REQUEST['hide_labels'] == 'true') ? true : false;
	$hide_category = (isset($_REQUEST['hide_category']) && $_REQUEST['hide_category'] == 'true') ? true : false;
	$hide_addtocartbutton = (isset($_REQUEST['hide_addtocartbutton']) && $_REQUEST['hide_addtocartbutton'] == 'true') ? true : false;
	

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
			if($columns == 2 && $type == 'type6') {
				$column_class = 'dtlms-column dtlms-one-half';
			} else {
				$column_class = 'dtlms-column dtlms-one-column';
			}
		}
		if($enable_fullwidth != 'true' && $display_type == 'grid' && $disable_filters == 'false' && $columns == 3) {
			$column_class = 'dtlms-column dtlms-one-half';
		}		
	}

	$class = $_REQUEST['class'];

	$category = array ();

	$output = '';

	$args = array ( 
				'offset' => $offset, 
				'paged' => $current_page ,
				'posts_per_page' => $post_per_page, 
				'post_type' => 'dtlms_courses',
				'meta_query'=>array(), 
				'tax_query'=>array(),
				'post_status'=>'publish'
			);

	if($disable_filters != 'true') {

		$search_text = isset($_REQUEST['search_text']) ? $_REQUEST['search_text'] : '';
		$order_by = $_REQUEST['order_by'];
		$category = isset($_REQUEST['category']) ? $_REQUEST['category'] : ''; 
		$instructor = isset($_REQUEST['instructor']) ? $_REQUEST['instructor'] : '';
		$cost_type = $_REQUEST['cost_type'];
		$cost_type = isset($cost_type[0]) ? $cost_type[0] : '';
		$start_date = $_REQUEST['start_date'];
		$start_date = (isset($start_date) && $start_date != '') ? $start_date : '';


		// Search Filter
		if($search_text != '') {
			$args['s'] = $search_text;
		}

		// OrderBy Filter
		if($order_by == 'recent-courses') {

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

		// Category Filter
		if(!empty($category)) {
			$args['tax_query'][] = array ( 
										'taxonomy' => 'course_category',
										'field' => 'id',
										'terms' => $category,
										'operator' => 'IN'
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
										'key'     => 'course-start-date-compare-format',
										'value'   => $date_compare_format,
										'compare' => '>='
									);
		}

	} else {

		$default_filter = $_REQUEST['default_filter'];
		$course_item_ids = $_REQUEST['course_item_ids'];
		$course_category_ids = $_REQUEST['course_category_ids'];
		$category = $course_category_ids;
		$instructor_ids = $_REQUEST['instructor_ids'];

		// Course Item Ids Filter
		if($course_item_ids != '') {
			$course_item_ids_arr = explode(',', $course_item_ids);
			$args['post__in'] = $course_item_ids_arr;
		}	

		// Default Filters
		if($default_filter == 'upcoming-courses') {

			$args['meta_query'][] = array (
										'key'     => 'course-start-date-compare-format',
										'value'   => current_time('Ymd'),
										'compare' => '>=',
										'type' => 'DATE'
									);	

		} else if($default_filter == 'recent-courses') {

			$args['orderby'] = 'date';

		} else if($default_filter == 'highest-rated-courses') {

			$args['orderby'] = 'meta_value_num';
			$args['meta_key'] = 'average-ratings';

		} else if($default_filter == 'most-membered-courses') {

			$args['orderby'] = 'meta_value';
			$args['meta_key'] = 'purchased_users';

		} else if($default_filter == 'paid-courses') {

			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => 0,
										'type'    => 'numeric',
										'compare' => '>'
									);

		} else if($default_filter == 'free-courses') {

			$args['meta_query'][] = array (
										'key'     => '_regular_price',
										'value'   => '',
										'compare' => '='
									);

		}

		// Category Filter
		if($course_category_ids != '') {
			$course_category_ids_arr = explode(',', $course_category_ids);
			$args['tax_query'][] = array ( 
										'taxonomy' => 'course_category',
										'field' => 'id',
										'terms' => $course_category_ids_arr,
										'operator' => 'IN'
									);
		}		

		// Instructor Filter
		if(!empty($instructor_ids)) {
			$instructor_ids_arr = explode(',', $instructor_ids);
			$args['author__in'] = $instructor_ids_arr;
		}	

	}


	$apply_isotope = $_REQUEST['apply_isotope'];
	$enable_category_isotope_filter = $_REQUEST['enable_category_isotope_filter'];

	if($apply_isotope == 'true') {
		if($enable_category_isotope_filter == 'true') {

			if(empty($category)):
				$categories = get_categories('taxonomy=course_category&hide_empty=1');			
			else:
				$c = array ('taxonomy' => 'course_category', 'hide_empty' => 1,'include' => $category);
				$categories = get_categories($c);			
			endif;

			if( is_array($categories) && !empty($categories) ) :
		        $output .= '<div class="dtlms-courses-listing-isotope-filter">
			        			<a href="#" class="active-sort" title="" data-filter=".all-sort">'.esc_html__('All','dtlms').'</a>';
			            		foreach( $categories as $category ):
			                		 $output .= '<a href="#" data-filter=".'.esc_attr($category->category_nicename).'-sort">'.esc_html($category->cat_name).'</a>';
			                	endforeach;
		        $output .= '</div>';
			endif;

		}

	}

		
	$data_listing_attributes = array ();
	$data_listing_attributes['column'] = $columns;
	$data_listing_attributes['column_class'] = $column_class;
	$data_listing_attributes['carousel_class'] = $carousel_class;
	$data_listing_attributes['display_type'] = $display_type;
	$data_listing_attributes['show_author_details'] = $show_author_details;
	$data_listing_attributes['apply_isotope'] = $apply_isotope;
	$data_listing_attributes['enable_category_isotope_filter'] = $enable_category_isotope_filter;
	$data_listing_attributes['type'] = $type;
	$data_listing_attributes['show_description'] = $show_description;
	$data_listing_attributes['hide_labels'] = $hide_labels;
	$data_listing_attributes['hide_category'] = $hide_category;
	$data_listing_attributes['hide_addtocartbutton'] = $hide_addtocartbutton;
	$data_listing_attributes['class'] = $class;

	$courses_query = new WP_Query( $args );

	if ( $courses_query->have_posts() ) :

		if($apply_isotope == 'true'):
			$output .= '<div class="dtlms-courses-listing-items">';
				$output .= '<div class="grid-sizer '.$column_class.'"></div>';
		endif;
		
			$i = 1;
			while ( $courses_query->have_posts() ) : 
				$courses_query->the_post();

				if($enable_carousel == 'true') {
					$first_class = '';
				} else {
					if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
					if($i == $columns) { $i = 1; } else { $i = $i + 1; }				
				}

				$data_listing_attributes['first_class'] = $first_class;

				$output .= dtlms_course_data_listing($user_id, $data_listing_attributes);

			endwhile;
			wp_reset_postdata();

		if($apply_isotope == 'true'):
			$output .= '</div>';
		endif;

	else :

		$output .= '<div class="dtlms-courses-listing-norecords">'.esc_html__('No records found!', 'dtlms').'</div>';

	endif;

	if($enable_carousel != 'true'):
		$output .= dtlms_course_listing_pagination($courses_query, $current_page);
	endif;

	$output .= dtlms_generate_loader_html(false);
		
	echo $output;

	die();

}

function dtlms_course_data_listing($user_id, $data_listing_attributes) {

	$output = '';

	$course_id = get_the_ID();
	$course_title = get_the_title();
	$course_permalink = get_permalink();

	extract($data_listing_attributes);


	$average_rating = get_post_meta($course_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$display_type = $display_type.'-item';

	$item_classes = array ('dtlms-courselist-item-wrapper');
	array_push($item_classes, $column_class, $carousel_class, $display_type, $type);
	if($first_class != '') {
		array_push($item_classes, $first_class);
	}

	$author_id = get_the_author_meta( 'ID');

	if($apply_isotope == 'true') {
		if($enable_category_isotope_filter == 'true') {
			array_push($item_classes, 'all-sort');
			$item_categories = get_the_terms( $course_id, 'course_category' );
			if(is_object($item_categories) || is_array($item_categories)) {
				foreach ($item_categories as $category) {
					array_push($item_classes, $category->slug.'-sort');
				}
			}
		}
	}

	//

	$product = dtlms_get_product_object($course_id);				
	$woo_price = dtlms_get_item_price_html($product);

	$free_course = false;
	if($woo_price == '') {
		$free_course = true;
	}


	$active_package_courses = dtlms_get_user_active_packages($user_id, 'courses');
	$active_package_courses = (is_array($active_package_courses) && !empty($active_package_courses)) ? $active_package_courses : array();

	$purchased_class_courses = dtlms_get_user_purchased_class_courses($user_id);
	$purchased_class_courses = (is_array($purchased_class_courses) && !empty($purchased_class_courses)) ? $purchased_class_courses : array();

	$assigned_courses = get_user_meta($user_id, 'assigned_courses', true);
	$assigned_courses = (is_array($assigned_courses) && !empty($assigned_courses)) ? $assigned_courses : array();

	$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
	$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

	$purchased_paid_course = false;
	if(in_array($course_id, $active_package_courses) || in_array($course_id, $purchased_class_courses) || in_array($course_id, $assigned_courses) || in_array($course_id, $purchased_courses)) {
		$purchased_paid_course = true;
	}


	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

	$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
	$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();	

	$completed_courses = get_user_meta($user_id, 'completed_courses', true);
	$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();

	//

	$output .= '<div class="'.implode(' ', get_post_class($item_classes, $course_id)).'">';
	
		if($type == 'type1') {

			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
				if(!$hide_labels) {
					$output .= dtlms_course_listing_certificatenbadge($course_id);
				}
			$output .= '</div>';

			$output .= '<div class="dtlms-courselist-details">';
				$output .= '<div class="dtlms-courselist-details-inner">';
					$output .= '<div class="dtlms-courselist-metadata-holder">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= '<div class="dtlms-courselist-metadata">';
							if(!$hide_category) {
								$output .= dtlms_course_listing_tags($course_id, true);
							}
							$output .= dtlms_course_listing_curriculum_count($course_id);
							$output .= dtlms_course_listing_rating($course_id, 'type2');
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';
				if($show_description) {
					$output .= dtlms_course_listing_description($course_id);
				}
				$output .= '<div class="dtlms-courselist-bottom-section">';
					$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
					}
					$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type2') {

			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
			$output .= '</div>';

			$output .= '<div class="dtlms-courselist-details">';
				$output .= '<div class="dtlms-courselist-details-inner">';
					$output .= '<div class="dtlms-courselist-metadata-holder">';
						$output .= dtlms_course_listing_featured($course_id);
						if(!$hide_labels) {
							$output .= dtlms_course_listing_certificatenbadge($course_id);
						}
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
						$output .= dtlms_course_listing_rating($course_id, 'type3');
					$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="dtlms-courselist-bottom-section">';
					$output .= dtlms_course_listing_duration($course_id, '', 'style3');
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
					}
					$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
				$output .= '</div>';				
			$output .= '</div>';

		} else if($type == 'type3') {

			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
				if(!$hide_labels) {
					$output .= dtlms_course_listing_certificatenbadge($course_id);	
				}		
			$output .= '</div>';

			if($display_type == 'list-item') {

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= '</div>';
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= dtlms_course_listing_rating($course_id, 'type3');
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= dtlms_course_listing_duration($course_id, 'type2', 'style3');
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';	
					$output .= '<div class="dtlms-courselist-metadata-holder">';
						if($show_author_details == 'true') {
							$output .= dtlms_course_listing_author($course_id, '');
						}
					$output .= '</div>';
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);	
					}								
				$output .= '</div>';

			} else {

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, '');
							}
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= '</div>';
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
						$output .= dtlms_course_listing_rating($course_id, 'type3');
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= dtlms_course_listing_duration($course_id, 'type2', 'style3');
						if(!$hide_addtocartbutton) {
							$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);	
						}
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';				
				$output .= '</div>';

			}		

		} else if($type == 'type4') {

			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
				if(!$hide_labels) {
					$output .= dtlms_course_listing_certificatenbadge($course_id);	
				}	
			$output .= '</div>';

			$output .= '<div class="dtlms-courselist-details">';
				$output .= '<div class="dtlms-courselist-details-inner">';
					$output .= '<div class="dtlms-courselist-metadata-holder">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);					
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
					$output .= '</div>';		
				$output .= '</div>';
				$output .= '<div class="dtlms-courselist-bottom-section">';
					$output .= '<div class="dtlms-courselist-bottom-left-section">';
						$output .= '<div class="dtlms-courselist-metadata">';
							if(!$hide_category) {
								$output .= dtlms_course_listing_tags($course_id, true);
							}
						$output .= '</div>';
						$output .= dtlms_course_listing_rating($course_id, 'type3');
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-right-section">';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';
					$output .= dtlms_course_listing_metadata($course_id);
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);	
					}
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type5') {

			if($display_type == 'list-item') {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);
					}
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');				
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= '</div>';
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, 'type2');
							}
						$output .= '</div>';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							$output .= dtlms_course_listing_duration($course_id, 'type2', 'style3');
							$output .= dtlms_course_listing_curriculum_count($course_id);
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							$output .= dtlms_course_listing_rating($course_id, 'type3');
						$output .= '</div>';
					$output .= '</div>';
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
					}
				$output .= '</div>';

			} else {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);
					}
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
					$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= '</div>';
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, 'type2');
							}
						$output .= '</div>';
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
						if(!$hide_addtocartbutton) {
							$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
						}
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							$output .= dtlms_course_listing_duration($course_id, 'type2', 'style3');
							$output .= dtlms_course_listing_curriculum_count($course_id);
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							$output .= dtlms_course_listing_rating($course_id, 'type3');
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';	

			}

		} else if($type == 'type6') {

			if($display_type == 'list-item') {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);	
					}
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');			
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						if(!$hide_category) {				
							$output .= dtlms_course_listing_tags($course_id, false);
						}
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, 'type6');
							}
						$output .= '</div>';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';
				$output .= '</div>';

			} else {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);	
					}
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');			
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);	
						if(!$hide_category) {			
							$output .= dtlms_course_listing_tags($course_id, false);
						}
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_excerpt_max_charlength($course_id, 70);
						}
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, 'type6');
							}
						$output .= '</div>';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);	
					}
				$output .= '</div>';

			}	

		} else if($type == 'type7') {

			$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
					$output .= dtlms_course_listing_featured($course_id);
					$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
				$output .= '</div>';
				$output .= '<div class="dtlms-courselist-metadata-badgenhours">';
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);	
					}
					$output .= dtlms_course_listing_duration($course_id, '', 'style3');
				$output .= '</div>';	
			$output .= '</div>';

			$output .= '<div class="dtlms-courselist-details">';
				$output .= '<div class="dtlms-courselist-details-inner">';
					if(!$hide_category) {
						$output .= dtlms_course_listing_tags($course_id, false);
					}
					$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
					if($show_description) {
						$output .= dtlms_course_listing_description($course_id);
					}
					$output .= dtlms_course_listing_rating($course_id, 'type3');
				$output .= '</div>';
				$output .= '<div class="dtlms-courselist-bottom-section">';
					$output .= '<div class="dtlms-courselist-bottom-left-section">';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-right-section">';
						if(!$hide_addtocartbutton) {
							$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
						}
					$output .= '</div>';
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type8') {

			$output .= '<div class="dtlms-courselist-thumb">';
				$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
				if(!$hide_labels) {
					$output .= dtlms_course_listing_certificatenbadge($course_id);	
				}
				if($display_type != 'list-item') {
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
				}		
			$output .= '</div>';

			$output .= '<div class="dtlms-courselist-details">';
				$output .= '<div class="dtlms-courselist-details-inner">';
					if($display_type == 'list-item') {
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);	
							$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
						$output .= '</div>';				
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, '');
							}
						$output .= '</div>';						
					} else {
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, '');
							}
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
							$output .= dtlms_course_listing_featured($course_id);
							$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);	
						$output .= '</div>';				
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}	
						if(!$hide_addtocartbutton) {
							$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
						}
					}
				$output .= '</div>';
				$output .= '<div class="dtlms-courselist-bottom-section">';
					$output .= '<div class="dtlms-courselist-bottom-left-section">';
						$output .= '<div class="dtlms-courselist-metadata">';
							$output .= dtlms_course_listing_students_enrolled($course_id);
							$output .= dtlms_course_listing_rating($course_id, '');
						$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-right-section">';
						$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						if($display_type == 'list-item') {
							if(!$hide_addtocartbutton) {
								$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
							}
						}
					$output .= '</div>';
				$output .= '</div>';				
			$output .= '</div>';

		} else if($type == 'type9') {

			if($display_type == 'list-item') {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);			
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');
					if(!$hide_labels) {	
						$output .= dtlms_course_listing_certificatenbadge($course_id);
					}
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							$output .= dtlms_course_listing_rating($course_id, 'type2');
							$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							if(!$hide_addtocartbutton) {
								$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
							}
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';	

			} else {

				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);			
					$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
					if(!$hide_labels) {
						$output .= dtlms_course_listing_certificatenbadge($course_id);
					}
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							$output .= dtlms_course_listing_rating($course_id, 'type2');
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						$output .= '</div>';
					$output .= '</div>';
					if(!$hide_addtocartbutton) {
						$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
					}
				$output .= '</div>';

			}

		} else if($type == 'type10') {

			if($display_type == 'list-item') {

				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
					$output .= '</div>';

					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							if(!$hide_addtocartbutton) {
								$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
							}
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						$output .= '</div>';
					$output .= '</div>';					
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';	
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= '<div class="dtlms-courselist-metadata">';
							if(!$hide_category) {
								$output .= dtlms_course_listing_tags($course_id, false);
							}				
							$output .= dtlms_course_listing_curriculum_count($course_id);
						$output .= '</div>';
						$output .= dtlms_course_listing_rating($course_id, 'type5');
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, '');
							}
						$output .= '</div>';	
						if(!$hide_labels) {
							$output .= dtlms_course_listing_certificatenbadge($course_id);	
						}
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}				
					$output .= '</div>';
				$output .= '</div>';

			} else {

				$output .= dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, 'archive');	
				$output .= '<div class="dtlms-courselist-thumb">';
					$output .= dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class);
					$output .= '<div class="dtlms-courselist-metadata-featurednpurchase">';
						$output .= dtlms_course_listing_featured($course_id);
						$output .= dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses);
					$output .= '</div>';
					$output .= dtlms_course_listing_rating($course_id, 'type5');
				$output .= '</div>';

				$output .= '<div class="dtlms-courselist-details">';
					$output .= '<div class="dtlms-courselist-details-inner">';
						$output .= '<div class="dtlms-courselist-metadata-holder">';
							if($show_author_details == 'true') {
								$output .= dtlms_course_listing_author($course_id, '');
							}
						$output .= '</div>';	
						if(!$hide_labels) {
							$output .= dtlms_course_listing_certificatenbadge($course_id);		
						}		
						$output .= dtlms_course_listing_title($course_id, $course_title, $course_permalink);
						$output .= '<div class="dtlms-courselist-metadata">';
							if(!$hide_category) {
								$output .= dtlms_course_listing_tags($course_id, false);
							}				
							$output .= dtlms_course_listing_curriculum_count($course_id);
						$output .= '</div>';
						if($show_description) {
							$output .= dtlms_course_listing_description($course_id);
						}				
					$output .= '</div>';
					$output .= '<div class="dtlms-courselist-bottom-section">';
						$output .= '<div class="dtlms-courselist-bottom-left-section">';
							if(!$hide_addtocartbutton) {
								$output .= dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses);
							}
						$output .= '</div>';
						$output .= '<div class="dtlms-courselist-bottom-right-section">';
							$output .= dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price);
						$output .= '</div>';
					$output .= '</div>';
				$output .= '</div>';

			}																									

		}

	$output .= '</div>';


	return $output;

}

function dtlms_course_listing_pagination($dtlms_wpquery, $current_page) {

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

function dtlms_course_drip_feed_check($course_id, $item_id, $user_id) {

	$drip_feed = get_post_meta($course_id, 'drip-feed', true);

	if($drip_feed == 'true') {

		$curriculum_details = get_user_meta($user_id, $course_id, true);
		$started_timestamp = isset($curriculum_details['started-timestamp']) ? $curriculum_details['started-timestamp'] : '';

		if($started_timestamp != '') {
				
			$drip_content_type = get_post_meta ( $course_id, 'drip-content-type', true );
			$drip_duration_type = get_post_meta ( $course_id, 'drip-duration-type', true );
			$drip_duration = get_post_meta ( $course_id, 'drip-duration', true );
			$drip_duration_parameter = get_post_meta ( $course_id, 'drip-duration-parameter', true );

			if($drip_content_type == 'curriculum') {

				if($drip_duration_type == 'dynamic') {

					$curriculum_items = dtlms_get_course_numeric_curriculum_ids($course_id);
					$curriculum_number = array_search($item_id, $curriculum_items);

					$curriculum_items = array_slice($curriculum_items, 0, $curriculum_number);

					$duration_to_add = 0;
					foreach($curriculum_items as $curriculum_item) {

						$duration = get_post_meta ( $curriculum_item, 'duration', true );
						$duration_parameter = get_post_meta ( $curriculum_item, 'duration-parameter', true );

						$duration_to_add = $duration_to_add + ($duration * $duration_parameter);

					}

				} else {
					$curriculum_items = dtlms_get_course_numeric_curriculum_ids($course_id);
					$curriculum_number = array_search($item_id, $curriculum_items);

					$duration_to_add = ($drip_duration * $drip_duration_parameter * $curriculum_number);
				}

				if($duration_to_add > 0) {

					$current_timestamp = current_time( 'timestamp', 1 );
					$curriculum_timestamp = strtotime('+'.$duration_to_add.' seconds', $started_timestamp);

					if($current_timestamp >= $curriculum_timestamp) {

						return 'true';

					} else {

						return $curriculum_timestamp;

					}

				}

			}

			if($drip_content_type == 'section') {

				$curriculum_sectionwise = dtlms_get_course_curriculum_sectionwise($course_id);
				$curriculum_sectionwise_keys = array_keys($curriculum_sectionwise);

				$curriculum_key = '';
				if(is_array($curriculum_sectionwise) && !empty($curriculum_sectionwise)) {
					foreach($curriculum_sectionwise as $curriculum_section_key => $curriculum_section) {
						if(in_array($item_id, $curriculum_section)) {
							$curriculum_key = $curriculum_section_key;
							break;
						}
					}
				}

				if($curriculum_key != '') {

					$section_key = array_search ($curriculum_key, $curriculum_sectionwise_keys);

					if($drip_duration_type == 'dynamic') {

						$curriculum_sections = array_slice($curriculum_sectionwise_keys, 0, $section_key);

						$duration_to_add = 0;
						foreach($curriculum_sections as $curriculum_section) {
							foreach($curriculum_sectionwise[$curriculum_section] as $curriculum_section_item) {
								$duration = get_post_meta ( $curriculum_section_item, 'duration', true );
								$duration_parameter = get_post_meta ( $curriculum_section_item, 'duration-parameter', true );

								$duration_to_add = $duration_to_add + ($duration * $duration_parameter);
							}
						}

					} else {

						$duration_to_add = ($drip_duration * $drip_duration_parameter * $section_key);

					}

					if($duration_to_add > 0) {

						$current_timestamp = current_time( 'timestamp', 1 );
						$curriculum_timestamp = strtotime('+'.$duration_to_add.' seconds', $started_timestamp);

						if($current_timestamp >= $curriculum_timestamp) {

							return 'true';

						} else {

							return $curriculum_timestamp;

						}

					}

				}

			}

		}

	}

	return 'true';

}

function dtlms_calculate_course_available_seats($course_id, $capacity) {

	$purchased_users = get_post_meta($course_id, 'purchased_users', true);
	$seats_alloted = (is_array($purchased_users) && !empty($purchased_users)) ? count($purchased_users) : 0;

	if($seats_alloted > 0) {
		$available_seats = $capacity - $seats_alloted;
	} else {
		$available_seats = $capacity;
	}

	return $available_seats;

}

function dtlms_check_course_items_visibility($item, $course_id, $user_id) {

	if($item == 'curriculum') {
		$item_visibility = dtlms_option('course', 'curriculum-visiblitiy');
	} else if($item == 'members') {
		$item_visibility = dtlms_option('course', 'members-visiblitiy');
	} else if($item == 'events') {
		$item_visibility = dtlms_option('course', 'events-visiblitiy');
	} else if($item == 'buddypressgroup') {
		$item_visibility = dtlms_option('course', 'buddypress-group-visiblitiy');
	} else if($item == 'news') {
		$item_visibility = dtlms_option('course', 'news-visiblitiy');						
	}


	if($item_visibility == '') {

		return true;

	} if($item_visibility == 'logged-in-users') {

		if(is_user_logged_in()) {
			return true;
		}

	} else if($item_visibility == 'purchased-users') {

		$purchased_users = get_post_meta($course_id, 'purchased_users', true);
		$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array ();

		if(in_array($user_id, $purchased_users)) {
			return true;
		}

	} else if($item_visibility == 'instructors-and-administrators') {

		if($user_id > 0) {

			$current_user = get_userdata($user_id);

			if ( in_array( 'administrator', (array) $current_user->roles ) || in_array( 'instructor', (array) $current_user->roles ) ) {
				return true;
			}

		}

	}

	return false;

}

?>