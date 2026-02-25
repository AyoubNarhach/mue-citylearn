<?php

function dtlms_generate_lesson_page_contents($user_id, $course_id, $lesson_id, $parent_curriculum_id) {

	$lesson_data = get_post($lesson_id);
	$author_id = $lesson_data->post_author;

	$lesson_title = get_the_title($lesson_id);
	$lesson_permalink = get_permalink($lesson_id);
	
	$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
	$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

	$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
	$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();	

	$curriculum_details = get_user_meta($user_id, $course_id, true);

	$drip_feed_enable = dtlms_course_drip_feed_check($course_id, $lesson_id, $user_id);
							
	if($parent_curriculum_id > 0) {
		$curriculum_status = (isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['completed']) && $curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['completed'] == 1) ? true : false;			
	} else {
		$curriculum_status = (isset($curriculum_details['curriculum'][$lesson_id]['completed']) && $curriculum_details['curriculum'][$lesson_id]['completed'] == 1) ? true : false;
	}

	if( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes') ) {
		WPBMap::addAllMappedShortcodes();
	}

	$output = '';

	$output .= '<div id="dtlms-course-curriculum-popup" class="dtlms-course-curriculum-popup-lesson">';

				$curriculum_image_url = '';
				if(has_post_thumbnail($lesson_id)) {
					$image_url = wp_get_attachment_image_src(get_post_thumbnail_id($lesson_id), 'full');
					$curriculum_image_url = 'style="background-image:url('.esc_url($image_url[0]).');"';
				} 

				$output .= '<div class="dtlms-course-curriculum-popup-header" '.$curriculum_image_url.'>';

					$output .= '<div class="dtlms-curriculum-intro">';
					
							$output .= '<div class="dtlms-column dtlms-one-column first">';								

								$output .= '<div class="dtlms-curriculum-intro-details">';

									$output .= '<h2>'.$lesson_title.'</h2>';

									$output .= '<div class="dtlms-curriculum-intro-details-meta">';

										$duration = get_post_meta ( $lesson_id, 'duration', true );
										$duration_parameter = get_post_meta ( $lesson_id, 'duration-parameter', true );
										$duration_in_seconds = ($duration * $duration_parameter); 

										$curriculum_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, 'style4');

										$output .= '<span class="dtlms-curriculum-duration">'.$curriculum_duration.'</span>';

										if($curriculum_status) {
											$output .= '<span class="dtlms-completed">'.esc_html__('Completed', 'dtlms').'</span>';
										} else if(in_array($course_id, $submitted_courses)) {
											$output .= '<span class="dtlms-underevaluation">'.esc_html__('Under Evaluation', 'dtlms').'</span>';
										} else if (in_array($course_id, $started_courses)) {

											if($drip_feed_enable == 'true') {

												// Open the next locked curriculum item
												$next_curriculum_id = -1;
												$enable_next_curriculum = 'false';
												$free_item = get_post_meta ( $lesson_id, 'free-lesson', true );
												if(!$free_item) {
													$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
													if($curriculum_completion_lock == 'true') {
														$next_curriculum_id = dtlms_get_course_next_curriculum_id($course_id, $lesson_id, $parent_curriculum_id);
														$enable_next_curriculum = 'true';
													}
												}

												$output .= '<a href="#" class="dtlms-button dtlms-lesson-complete-button small" data-complete-nonce="'.wp_create_nonce('complete_lesson_'.$lesson_id.'_'.$user_id).'" data-courseid="'.$course_id.'" data-lessonid="'.$lesson_id.'" data-userid="'.$user_id.'" data-authorid="'.$author_id.'" data-parentcurriculumid="'.$parent_curriculum_id.'" data-nextcurriculumid="'.$next_curriculum_id.'" data-enablenextcurriculum="'.$enable_next_curriculum.'">'.esc_html__('Mark As Complete', 'dtlms').'</a>';

												$output .= '<span class="dtlms-completed hidden">'.esc_html__('Completed', 'dtlms').'</span>';

											}

										}

									$output .= '</div>';

								$output .= '</div>';

							$output .= '</div>';

					$output .= '</div>';

					$output .= '<div class="dtlms-refresh-course-curriculum"></div>';
					$output .= '<div class="dtlms-close-course-curriculum-popup"></div>';		

				$output .= '</div>';	

				$output .= '<div class="dtlms-course-curriculum-popup-container">';

					$output .= '<div class="dtlms-column dtlms-one-fifth first">';

						$output .= '<div class="dtlms-curriculum-details">';
									
							$output .= '<div class="dtlms-curriculum-detailed-links">';
								$output .= dtlms_generate_course_curriculum($user_id, $course_id, 'style3', false, $lesson_id, '');
							$output .= '</div>';

						$output .= '</div>';	

					$output .= '</div>';

					$output .= '<div class="dtlms-column dtlms-four-fifth">';

						$output .= '<div class="dtlms-curriculum-content-holder">';

							$output .= '<div class="dtlms-lesson-details-container">';

								if($drip_feed_enable == 'true') {

									$lesson_video = get_post_meta($lesson_id, 'lesson-video', true);
									if(isset($lesson_video) && $lesson_video != '') {
										$output .= '<div class="dtlms-lesson-video">';
											if(wp_oembed_get( $lesson_video ) != '') {
												$output .= wp_oembed_get($lesson_video); 
											} else {
												$output .= wp_video_shortcode(array('src' => $lesson_video));
											}
					                    $output .= '</div>';
									}

									$output .= do_shortcode(apply_filters( 'the_content', $lesson_data->post_content ));

								} else {

									$drip_date = dtlms_format_datetime($drip_feed_enable, get_option('date_format').' '.get_option('time_format'), false);
									$output .= sprintf( esc_html__('This lesson will be available on %1$s', 'dtlms'), '<strong>'.$drip_date.'</strong>' );

									//$countdown_date = dtlms_format_datetime($drip_feed_enable, 'm/d/Y H:i:s', false);
									$countdown_date = dtlms_format_datetime($drip_feed_enable, get_option('date_format').' '.get_option('time_format'), false);
									$output .= dtlms_generate_countdown_html($countdown_date, $lesson_id, $parent_curriculum_id);

								}

							$output .= '</div>';	

						$output .= '</div>';					

					$output .= '</div>';

					$output .= dtlms_generate_loader_html(false);

				$output .= '</div>';	

	$output .= '</div>';

	echo $output;

	die();

}


add_action( 'wp_ajax_dtlms_complete_the_lesson', 'dtlms_complete_the_lesson' );
add_action( 'wp_ajax_nopriv_dtlms_complete_the_lesson', 'dtlms_complete_the_lesson' );
function dtlms_complete_the_lesson() {

	$nonce = $_POST['complete_nonce'];
	$course_id = $_POST['course_id'];
	$lesson_id = $_POST['lesson_id'];
	$user_id = $_POST['user_id'];
	$author_id = $_POST['author_id'];
	$parent_curriculum_id = $_POST['parent_curriculum_id'];
	$next_curriculum_id = $_POST['next_curriculum_id'];

	if(isset($nonce) && wp_verify_nonce($nonce, 'complete_lesson_'.$lesson_id.'_'.$user_id)) {

		$curriculum_details = get_user_meta($user_id, $course_id, true);
		if(isset($curriculum_details['curriculum'][$lesson_id]['temp-grade-post-id']) && $curriculum_details['curriculum'][$lesson_id]['temp-grade-post-id'] > 0) {
			$lesson_grade_id = $curriculum_details['curriculum'][$lesson_id]['temp-grade-post-id'];
			unset($curriculum_details['curriculum'][$lesson_id]['temp-grade-post-id']);
			$curriculum_details['curriculum'][$lesson_id]['grade-post-id'] = $lesson_grade_id;
			delete_post_meta($lesson_grade_id, 'temp-grade-post-id');
		} else {		
			$lesson_grade_id = (isset($curriculum_details['curriculum'][$lesson_id]['grade-post-id']) && $curriculum_details['curriculum'][$lesson_id]['grade-post-id'] > 0) ? $curriculum_details['curriculum'][$lesson_id]['grade-post-id'] : -1;
		}

		$course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

		if($lesson_grade_id < 0) {

			if($parent_curriculum_id > 0) {
				if(isset($curriculum_details['curriculum'][$parent_curriculum_id]['grade-post-id']) && $curriculum_details['curriculum'][$parent_curriculum_id]['grade-post-id'] != '') {
					$parent_grade_id = $curriculum_details['curriculum'][$parent_curriculum_id]['grade-post-id'];
				} else if(isset($curriculum_details['curriculum'][$parent_curriculum_id]['temp-grade-post-id']) && $curriculum_details['curriculum'][$parent_curriculum_id]['temp-grade-post-id'] != '') {
					$parent_grade_id = $curriculum_details['curriculum'][$parent_curriculum_id]['temp-grade-post-id'];
				}
			} else {
				$parent_grade_id = $course_grade_id;
			}

			if($parent_grade_id == '') {
				$parent_grade_id = dtlms_insert_parent_grade_post($course_id, $course_grade_id, $user_id, $parent_curriculum_id, $author_id);
				$curriculum_details = get_user_meta($user_id, $course_id, true);
			}		

			$title = get_the_title($lesson_id);
			
			$grade_post = array(
				'post_title' => $title,
				'post_status' => 'publish',
				'post_type' => 'dtlms_gradings',
				'post_author' => $author_id,
				'post_parent' => $parent_grade_id
			);
			
			$lesson_grade_id = wp_insert_post( $grade_post );
			
			update_post_meta ( $lesson_grade_id, 'dtlms-course-id',  $course_id );
			update_post_meta ( $lesson_grade_id, 'dtlms-course-grade-id',  $course_grade_id );
			update_post_meta ( $lesson_grade_id, 'dtlms-user-id',  $user_id );
			update_post_meta ( $lesson_grade_id, 'dtlms-lesson-id',  $lesson_id );
			update_post_meta ( $lesson_grade_id, 'dtlms-quiz-id',  -1 );
			update_post_meta ( $lesson_grade_id, 'dtlms-assignment-id',  -1 );
			update_post_meta ( $lesson_grade_id, 'dtlms-parent-curriculum-id',  $parent_curriculum_id );
			update_post_meta ( $lesson_grade_id, 'grade-type',  'lesson' );

			// Update user meta field
			if($parent_curriculum_id > 0) {
				$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['grade-post-id'] = $lesson_grade_id;
			} else {
				$curriculum_details['curriculum'][$lesson_id]['grade-post-id'] = $lesson_grade_id;
			}

			// Update the next locked curriculum item
			$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
			if($curriculum_completion_lock == 'true') {
				if($next_curriculum_id > 0) {
					$curriculum_details['next-curriculum-id'] = $next_curriculum_id;
					$curriculum_details['active-next-curriculum-id'] = $next_curriculum_id;
				}
			}

		}


		// evaluate lesson
		$lesson_maximum_mark = get_post_meta ( $lesson_id, 'lesson-maximum-mark', true );
		if($lesson_maximum_mark == '') {
			$lesson_maximum_mark = 100;
		}

		update_post_meta ( $lesson_grade_id, 'marks-obtained', $lesson_maximum_mark );
		update_post_meta ( $lesson_grade_id, 'marks-obtained-percentage', 100 );
		update_post_meta ( $lesson_grade_id, 'graded', 'true' );

		// Update user meta field
		if($parent_curriculum_id > 0) {			
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['completed'] = 1;
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['marks-obtained'] = $lesson_maximum_mark;
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]['marks-obtained-percentage'] = 100;
		} else {
			$curriculum_details['curriculum'][$lesson_id]['completed'] = 1;
			$curriculum_details['curriculum'][$lesson_id]['marks-obtained'] = $lesson_maximum_mark;
			$curriculum_details['curriculum'][$lesson_id]['marks-obtained-percentage'] = 100;
		}	

		$completed_count = isset($curriculum_details['completed-count']) ? $curriculum_details['completed-count'] : 0;
		$completed_count = $completed_count + 1;
		$curriculum_details['completed-count'] = $completed_count;	

		update_post_meta($course_grade_id, 'completed-count', $completed_count);				
		
		update_user_meta($user_id, $course_id, $curriculum_details);		
			
	}

	die();

}
?>