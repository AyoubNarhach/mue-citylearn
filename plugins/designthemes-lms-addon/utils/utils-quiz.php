<?php

function dtlms_generate_quiz_page_contents($user_id, $course_id, $quiz_id, $parent_curriculum_id) {

	$quiz_data = get_post($quiz_id);
	$author_id = $quiz_data->post_author;

	$quiz_title = get_the_title($quiz_id);
	$quiz_permalink = get_permalink($quiz_id);

	$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
	$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();

	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();

	$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
	$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();	

	$quiz_subtitle = get_post_meta($quiz_id, 'quiz-subtitle', true);

	$curriculum_details = get_user_meta($user_id, $course_id, true);

	if($parent_curriculum_id > 0) {
		$curriculum_status = (isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['completed']) && $curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['completed'] == 1) ? true : false;			
	} else {
		$curriculum_status = (isset($curriculum_details['curriculum'][$quiz_id]['completed']) && $curriculum_details['curriculum'][$quiz_id]['completed'] == 1) ? true : false;
	}	

	if( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes') ) {
		WPBMap::addAllMappedShortcodes();
	}

	$output = '';

	$output .= '<div id="dtlms-course-curriculum-popup" class="dtlms-course-curriculum-popup-quiz">';

				$curriculum_image_url = '';
				if(has_post_thumbnail($quiz_id)) {
					$image_url = wp_get_attachment_image_src(get_post_thumbnail_id($quiz_id), 'full');
					$curriculum_image_url = 'style="background-image:url('.esc_url($image_url[0]).');"';
				} 

				$output .= '<div class="dtlms-course-curriculum-popup-header" '.$curriculum_image_url.'>';

					$output .= '<div class="dtlms-curriculum-intro">';

								$output .= '<div class="dtlms-column dtlms-one-column first">';									
													
									$output .= '<div class="dtlms-curriculum-intro-details">';

										$output .= '<h2>'.$quiz_title.'</h2>';

										$output .= '<div class="dtlms-curriculum-intro-details-meta">';

											$duration = get_post_meta ( $quiz_id, 'duration', true );
											$duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
											$duration_in_seconds = ($duration * $duration_parameter); 

											$curriculum_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, 'style4');

											$output .= '<span class="dtlms-curriculum-duration">'.$curriculum_duration.'</span>';

											if($curriculum_status) {
												$output .= '<span class="dtlms-completed">'.esc_html__('Completed', 'dtlms').'</span>';
											} else if(in_array($course_id, $submitted_courses)) {
												$output .= '<span class="dtlms-underevaluation">'.esc_html__('Under Evaluation', 'dtlms').'</span>';												
											}

										$output .= '</div>';

									$output .= '</div>';

									if($quiz_subtitle != '') {
										$output .= '<h3>'.$quiz_subtitle.'</h3>';
									}								

								$output .= '</div>';

					$output .= '</div>';

					$output .= '<div class="dtlms-refresh-course-curriculum"></div>';
					$output .= '<div class="dtlms-close-course-curriculum-popup"></div>';

				$output .= '</div>';

				$output .= '<div class="dtlms-course-curriculum-popup-container">';

					$output .= '<div class="dtlms-column dtlms-one-fifth first">';

						$output .= '<div class="dtlms-curriculum-details">';

							$output .= '<div class="dtlms-curriculum-detailed-links">';
								$output .= dtlms_generate_course_curriculum($user_id, $course_id, 'style3', false, $quiz_id, '');
							$output .= '</div>';

						$output .= '</div>';	

					$output .= '</div>';

					$output .= '<div class="dtlms-column dtlms-four-fifth">';

						$output .= '<div class="dtlms-curriculum-content-holder">';
									
							$output .= '<div class="dtlms-quiz-details-container">';

								$drip_feed_enable = dtlms_course_drip_feed_check($course_id, $quiz_id, $user_id);

								if($drip_feed_enable == 'true') {

									$output .= do_shortcode(apply_filters( 'the_content', $quiz_data->post_content ));

									$course_curriculum = get_user_meta($user_id, $course_id, true);
									if($parent_curriculum_id > 0) {
										$grade_id = isset($course_curriculum['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id']) ? $course_curriculum['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id'] : -1;
									} else {
										$grade_id = isset($course_curriculum['curriculum'][$quiz_id]['grade-post-id']) ? $course_curriculum['curriculum'][$quiz_id]['grade-post-id'] : -1;
									}
									

									if($grade_id > 0) {
										$total_attempts = get_post_meta($grade_id, 'user-attempts', true);
										if($total_attempts != '' && $total_attempts > 0) {
											$quiz_auto_evaluation = get_post_meta($quiz_id, 'quiz-auto-evaluation', true); 
											$quiz_graded = get_post_meta($grade_id, 'graded', true); 
											if($quiz_auto_evaluation == 'true' || $quiz_graded == 'true') {

												$output .= '<div class="dtlms-hr-invisible"></div>';
												$output .= '<a href="#" class="dtlms-button dtlms-view-quiz-result filled large" data-quizid="'.$quiz_id.'" data-gradeid="'.$grade_id.'">'.esc_html__('View Quiz Results', 'dtlms').'</a>';

											}
										}
									}


									$output .= '<ul class="dtlms-quiz-features-list">';

										$quiz_retakes = get_post_meta($quiz_id, 'quiz-retakes', true);
										if($quiz_retakes != '' && $quiz_retakes > 0) {
											$output .= '<li><label><i class="fab fa-gg"></i>'.esc_html__('Total Attempts Allowed', 'dtlms').'</label> <span> '.$quiz_retakes.'</span> </li>';
										}

										$user_attempts = get_post_meta($grade_id, 'user-attempts', true);
										$user_attempts = ($user_attempts > 0) ? $user_attempts : 0;

										$attempts_remaning = ($quiz_retakes - $user_attempts);

										if($attempts_remaning != '' && $attempts_remaning > 0) {
											$output .= '<li><label><i class="fab fa-gg-circle"></i>'.esc_html__('Total Attempts Remaining', 'dtlms').'</label> <span> '.$attempts_remaning.'</span> </li>';
										}


										$duration = get_post_meta ( $quiz_id, 'duration', true );
										$duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
										$duration_in_seconds = ($duration * $duration_parameter); 

										if($duration_in_seconds != '') {
											$quiz_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, '');
											$output .= '<li><label><i class="fa fa-clock-o"></i>'.esc_html__('Duration', 'dtlms').'</label> <span> '.$quiz_duration.'</span> </li>';
										}


								        $question_type = get_post_meta($quiz_id, 'quiz-question-type', true);
								        if($question_type == 'add-categories') {
								        	$quiz_categories = get_post_meta($quiz_id, 'quiz-categories', true); 
								        	if(is_array($quiz_categories) && !empty($quiz_categories)) {
								        		$category_list = '';
								        		foreach($quiz_categories as $quiz_category) {
								        			$category_list .= get_term($quiz_category)->name.',';
								        		}
								        		$category_list = rtrim($category_list, ',');
								        		$output .= '<li><label><i class="fa fa-futbol-o"></i>'.esc_html__('Question Categories', 'dtlms').'</label> <span> '.$category_list.'</span> </li>';
								        	}
								        	$question_negative_grade = get_post_meta($quiz_id, 'quiz-categories-negative-grade', true); 
								        	$quiz_categories_questions = get_post_meta($quiz_id, 'quiz-categories-questions', true);   
								        	if(is_array($quiz_categories_questions) && !empty($quiz_categories_questions)) {
								        		$total_questions = array_sum($quiz_categories_questions);
								        	}
								        } else {
								        	$question_negative_grade = get_post_meta($quiz_id, 'quiz-question-negative-grade', true); 
								        	$quiz_question = get_post_meta($quiz_id, 'quiz-question', true); 
								        	$total_questions = count($quiz_question);
								        }

			
										if($total_questions != '') {
											$output .= '<li><label><i class="fa fa-futbol-o"></i>'.esc_html__('Total Questions', 'dtlms').'</label> <span> '.$total_questions.'</span> </li>';
										}

										$quiz_total_grade = get_post_meta($quiz_id, 'quiz-total-grade', true); 
										if($quiz_total_grade != '') {
											$output .= '<li><label><i class="fa fa-users"></i>'.esc_html__('Total Marks', 'dtlms').'</label> <span> '.$quiz_total_grade.'</span> </li>';
										}

										$quiz_pass_percentage = get_post_meta($quiz_id, 'quiz-pass-percentage', true); 
										if($quiz_pass_percentage != '') {
											$output .= '<li><label><i class="fa fa-sun-o"></i>'.esc_html__('Pass Percentage', 'dtlms').'</label> <span> '.$quiz_pass_percentage.'</span> </li>';
										}

										
										if(!empty($question_negative_grade)) {
											$output .= '<li><label><i class="fa fa-sun-o"></i>'.esc_html__('Includes Negative Grade', 'dtlms').'</label> <span> '.esc_html__('true', 'dtlms').'</span> </li>';
										}

										$quiz_auto_evaluation = get_post_meta($quiz_id, 'quiz-auto-evaluation', true); 
										if($quiz_auto_evaluation != '') {
											$output .= '<li><label><i class="fa fa-user-plus"></i>'.esc_html__('Auto Evaluation', 'dtlms').'</label> <span> '.$quiz_auto_evaluation.'</span> </li>';
										}

									$output .= '</ul>';

									if(!$curriculum_status) {

										if (in_array($course_id, $started_courses) && !in_array($course_id, $submitted_courses)) {

											$quiz_retakes = get_post_meta($quiz_id, 'quiz-retakes', true);
											$quiz_retakes = ($quiz_retakes != '') ? $quiz_retakes : 1;

											$user_attempts = get_post_meta($grade_id, 'user-attempts', true);
											$user_attempts = ($user_attempts != '' ) ? $user_attempts : 0;

											if($user_attempts >= $quiz_retakes) {
												
												$output .= '<div class="dtlms-info-box">'.esc_html__('You have crossed the number of retakes allowed for this quiz, so you can\'t retake this quiz.', 'dtlms').'</div>';

											} else {

												$duration = get_post_meta ( $quiz_id, 'duration', true );
												$duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
												$duration_in_seconds = ($duration * $duration_parameter); 

												if($duration_in_seconds != '') {
													$quiz_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, '');
													$output .= '<div class="dtlms-info-box">';
														$output .= '<strong>'.esc_html__('Note: ', 'dtlms').'</strong>';
														$output .= sprintf(esc_html__('You have to complete the quiz in %s. Timer will be triggered once you press the "Start Quiz" button.', 'dtlms'), $quiz_duration);
													$output .= '</div>';
												}									

												$output .= '<a class="dtlms-button large filled" id="dtlms-start-quiz" onclick="return false;" data-startquiz-nonce="'.wp_create_nonce('start_quiz_'.$quiz_id.'_'.$user_id).'" data-courseid="'.$course_id.'" data-userid="'.$user_id.'"  data-lessonid="-1" data-quizid="'.$quiz_id.'" data-assignmentid="-1" data-authorid="'.$author_id.'" data-parentcurriculumid="'.$parent_curriculum_id.'">'.esc_html__('Start Quiz','dtlms').'</a>';

											}

										}

									}

								} else {

									$drip_date = dtlms_format_datetime($drip_feed_enable, get_option('date_format').' '.get_option('time_format'), false);
									$output .= sprintf( esc_html__('This quiz will be available on %1$s', 'dtlms'), '<strong>'.$drip_date.'</strong>' );

									//$countdown_date = dtlms_format_datetime($drip_feed_enable, 'm/d/Y H:i:s', false);
									$countdown_date = dtlms_format_datetime($drip_feed_enable, get_option('date_format').' '.get_option('time_format'), false);
									$output .= dtlms_generate_countdown_html($countdown_date, $quiz_id, $parent_curriculum_id);

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

add_action( 'wp_ajax_dtlms_start_quiz', 'dtlms_start_quiz' );
add_action( 'wp_ajax_nopriv_dtlms_start_quiz', 'dtlms_start_quiz' );
function dtlms_start_quiz() {

	global $post;

	$startquiz_nonce = $_POST['startquiz_nonce'];
	$course_id = $_POST['course_id'];
	$lesson_id = $_POST['lesson_id'];
	$quiz_id = $_POST['quiz_id'];
	$assignment_id = $_POST['assignment_id'];
	$user_id = $_POST['user_id'];
	$author_id = $_POST['author_id'];
	$parent_curriculum_id = $_POST['parent_curriculum_id'];

	if(isset($startquiz_nonce) && wp_verify_nonce($startquiz_nonce, 'start_quiz_'.$quiz_id.'_'.$user_id)) {
			
		$out = '';
				
		$quiz_questions_onebyone = get_post_meta($quiz_id, 'quiz-questions-onebyone', true);
		$quiz_markasgraded_in_autoevaluation = get_post_meta( $quiz_id, 'quiz-markasgraded-in-autoevaluation', true );
				
		$out .= '<div class="dtlms-column dtlms-three-fourth first">';
		
			$out .= '<form method="post" class="formQuiz" name="formQuiz" autocomplete="off">';
			
				$out .= '<div class="dtlms-questions-list-container dtlms-quiz-underprogess">';
								
					$out .= '<div class="dtlms-questions-list">';

						$quiz_question_type = get_post_meta($quiz_id, 'quiz-question-type', true);
					
						if($quiz_question_type == 'add-categories') {

							$quiz_question = $quiz_question_grade = $quiz_question_category_title = array ();

							$quiz_categories = get_post_meta($quiz_id, 'quiz-categories', true);						
							$quiz_categories_questions = get_post_meta($quiz_id, 'quiz-categories-questions', true);
							$quiz_categories_grade = get_post_meta($quiz_id, 'quiz-categories-grade', true);
							$quiz_categories_negative_grade = get_post_meta($quiz_id, 'quiz-categories-negative-grade', true);

							$i = 0;
							foreach($quiz_categories as $quiz_category) {

								$args = array ( 
											'posts_per_page' => $quiz_categories_questions[$i], 
											'post_type' => 'dtlms_questions',
											'fields' => 'ids',
										);

								$args['tax_query'][] = array ( 
															'taxonomy' => 'question_category',
															'field' => 'id',
															'terms' => $quiz_category,
															'operator' => 'IN'
														);

								$quiz_randomize_questions = get_post_meta($quiz_id, 'quiz-randomize-questions', true); 
								if($quiz_randomize_questions == 'true') {
									$args['orderby'] = 'rand';
								}


								$questions_query = new WP_Query( $args );
								$questions_count = count($questions_query->posts);
								$quiz_question_catwise = $questions_query->posts;
								
								wp_reset_postdata();											


								//$quiz_question_category_title[] = '';
								for($j = 0; $j < $questions_count; $j++) {
									$quiz_question_grade[] = $quiz_categories_grade[$i];
									if(isset($quiz_categories_negative_grade[$i]) && $quiz_categories_negative_grade[$i] != '') {
										$quiz_question_negative_grade[] = $quiz_categories_negative_grade[$i];
									} else {
										$quiz_question_negative_grade[] = 0;
									}
									
									if($j == 0) {
										$quiz_question_category_title[] = get_term($quiz_category)->name;
									} else {
										$quiz_question_category_title[] = '';
									}
								}

								$quiz_question = array_merge_recursive($quiz_question, $quiz_question_catwise);							

								$i++;

							}

						} else {

							$quiz_question = get_post_meta($quiz_id, 'quiz-question', true);						
							$quiz_question_grade = get_post_meta($quiz_id, 'quiz-question-grade', true); 
							$quiz_question_negative_grade = get_post_meta($quiz_id, 'quiz-question-negative-grade', true);

							$quiz_randomize_questions = get_post_meta($quiz_id, 'quiz-randomize-questions', true); 
							if($quiz_randomize_questions == 'true') {
								shuffle($quiz_question);
							}

						}	

						$dtlms_question_ids	= implode(',', $quiz_question);
						$dtlms_question_grades	= implode(',', $quiz_question_grade);
						$dtlms_question_negative_grades	= implode(',', $quiz_question_negative_grade);


						$question_total = count($quiz_question);					
							
						$i = 0;
						foreach($quiz_question as $question_id) {
						
							global $post;
							$post = get_post( $question_id );

							$style = '';
							$add_class = '';
							if($quiz_questions_onebyone == 'true') {
								if($i > 0) {
									$style = 'style="display:none;"';
								}
								$add_class = 'dtlms-questions-oneatatime';
							}

							$question_type = get_post_meta($question_id, 'question-type', true);

							/*if($quiz_questions_onebyone != 'true') {
								if($quiz_question_category_title[$i] != '') {
									$slug_highlighter = str_replace(' ', '-', strtolower($quiz_question_category_title[$i]));
									$out .= '<div class="dtlms-question-category-title '.$slug_highlighter.'" id="'.$slug_highlighter.'">'.$quiz_question_category_title[$i].'</div>';
								}
							}*/

							$answer_hint = get_post_meta($question_id, 'answer-hint', true);

							if( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes') ) {
								WPBMap::addAllMappedShortcodes();
							}

							$out .= '<div class="dtlms-question dtlms-question-'.($i+1).' '.esc_attr($add_class).'" '.do_shortcode($style).'>';

								$out .= '<div class="dtlms-question-title-container">';
									$out .= '<div class="dtlms-question-title">';
									$out .= '<div class="dtlms-question-title-counter">'.esc_html__( 'Question', 'dtlms' ).' '.($i+1);
										$out .= '<div class="dtlms-mark"><span>'.$quiz_question_grade[$i].'</span>'.esc_html__('Mark(s)', 'dtlms').'</div>';
									$out .= '</div>';
									$out .= do_shortcode($post->post_content).'</div>';
								$out .= '</div>';	
							
								if($answer_hint != '') {
									$out .= '<div class="dtlms-answer-hint"><span>'.esc_html__('Answer Hint', 'dtlms').'</span><p>'.$answer_hint.'</p></div>';
								}

								$out .= dtlms_generate_quiz_questions($question_id, $question_type, '', 'default');
								
								$out .= '<input type="hidden" name="dtlms-current-question-id" id="dtlms-current-question-id" value="'.$question_id.'" />';

								$out .= '<div class="dtlms-hr-invisible"></div>';
								
							$out .= '</div>';

							wp_reset_postdata();		
							
							$i++;
							
						}
						
						
						$hide_complete_btn = '';
						if($quiz_questions_onebyone == 'true') {
							
							$quiz_correctanswer_and_answerexplanation = get_post_meta($quiz_id, 'quiz-correctanswer-and-answerexplanation', true);
							
							$out .= '<div id="dtlms-answer-holder"></div>';
							
							$out .= '<input type="hidden" name="dtlms-current-question-number" id="dtlms-current-question-number" value="1" />';
							$out .= '<input type="hidden" name="dtlms-total-questions" id="dtlms-total-questions" value="'.$question_total.'" />';
							$out .= '<input type="hidden" name="dtlms-correctanswer-and-answerexplanation" id="dtlms-correctanswer-and-answerexplanation" value="'.$quiz_correctanswer_and_answerexplanation.'" />';
							
							$hide_next_btn = $hide_submit_btn = '';
							if($quiz_correctanswer_and_answerexplanation == 'true') {
								$hide_next_btn = 'hidden';
							} else {
								$hide_submit_btn = 'hidden';
							}
							
							$out .= '<a class="dtlms-button small filled '.$hide_submit_btn.'" name="submit_question" id="dtlms-submit-question" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-lessonid="'.$lesson_id.'" data-quizid="'.$quiz_id.'" data-assignmentid="'.$assignment_id.'" data-authorid="'.$author_id.'">'.esc_html__('Submit Question','dtlms').'</a>';
							$out .= '<a class="dtlms-button small filled '.$hide_next_btn.'" name="next_question" id="dtlms-next-question" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-lessonid="'.$lesson_id.'" data-quizid="'.$quiz_id.'" data-assignmentid="'.$assignment_id.'" data-authorid="'.$author_id.'">'.esc_html__('Next Question','dtlms').'</a>';
							
							$hide_complete_btn = 'hidden';
							
						}
							
						$out .= '<input type="hidden" name="dt_question_type" id="dt_question_type" value="'.$question_type.'" />';
						$out .= '<input type="hidden" name="dtlms-question-ids" id="dtlms-question-ids" value="'.$dtlms_question_ids.'" />';
						$out .= '<input type="hidden" name="dtlms-question-grades" id="dtlms-question-grades" value="'.$dtlms_question_grades.'" />';
						$out .= '<input type="hidden" name="dtlms-question-negative-grades" id="dtlms-question-negative-grades" value="'.$dtlms_question_negative_grades.'" />';

						// Open the next locked curriculum item
						$next_curriculum_id = -1;
						$enable_next_curriculum = 'false';

						$free_item = get_post_meta ( $quiz_id, 'free-quiz', true );
						if(!$free_item) {
							$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
							if($curriculum_completion_lock == 'true') {
								$next_curriculum_id = dtlms_get_course_next_curriculum_id($course_id, $quiz_id, $parent_curriculum_id);

								$open_curriculum_on_submission = get_post_meta($course_id, 'open-curriculum-on-submission', true);
								if($open_curriculum_on_submission == 'true') {
									$enable_next_curriculum = 'true';
								} else {
									$quiz_auto_evaluation = get_post_meta ($quiz_id, 'quiz-auto-evaluation', true);
									$quiz_markasgraded_in_autoevaluation = get_post_meta( $quiz_id, 'quiz-markasgraded-in-autoevaluation', true );
									if($quiz_auto_evaluation == 'true' && $quiz_auto_evaluation == 'true') {
										$enable_next_curriculum = 'true';
									}
								}
							}
						}

						$out .= '<a class="dtlms-button small filled '.$hide_complete_btn.'" name="complete_quiz" id="dtlms-complete-quiz" data-courseid="'.$course_id.'" data-userid="'.$user_id.'" data-lessonid="'.$lesson_id.'" data-quizid="'.$quiz_id.'" data-assignmentid="'.$assignment_id.'" data-authorid="'.$author_id.'" data-markasgraded="'.$quiz_markasgraded_in_autoevaluation.'" data-parentcurriculumid="'.$parent_curriculum_id.'" data-nextcurriculumid="'.$next_curriculum_id.'" data-enablenextcurriculum="'.$enable_next_curriculum.'">'.esc_html__('Complete Quiz','dtlms').'</a>';
			
					$out .= '</div>';
				
				$out .= '</div>';
			
			$out .= '</form>';
		
		$out .= '</div>';
		
		$out .= '<div class="dtlms-column dtlms-one-fourth">';
		
			$out .= '<div class="dtlms-quiz-sidebar" id="dtlms-quiz-sidebar">';
			
				$out .= '<div class="dtlms-timer-container">';
				

					$duration = get_post_meta ( $quiz_id, 'duration', true );
					$duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
					$duration_in_seconds = ($duration * $duration_parameter); 

					if($duration_in_seconds > 0) {
						$quiz_duration = dtlms_convert_seconds_to_readable_format($duration_in_seconds, 'style2');
						$out .= '<h4><span class="fa fa-clock-o"></span>'.esc_html__('Time Remaining', 'dtlms').'</h4>';
						$out .= '<div class="dtlms-quiz-timer dtlms-start" data-time="'.$duration_in_seconds.'">
									<div class="dtlms-timer" data-timer="'.$duration_in_seconds.'"></div>
									<div class="dtlms-countdown">'.$quiz_duration.'</div>  
									<div class="dtlms-countdown-label">
										<span class="dtlms-mins">'.esc_html__('MINS', 'dtlms').'</span>
										<span class="dtlms-secs">'.esc_html__('SECS', 'dtlms').'</span>
									</div>
								</div>';
					}
								
				$out .= '</div>';	

				
				$quiz_questions_counter = get_post_meta($quiz_id, 'quiz-questions-counter', true);
				
				if($quiz_questions_onebyone == 'true' && $quiz_questions_counter == 'true') {
					
					$out .= '<div class="dtlms-question-counter-holder">';
						$out .= '<h4>'.esc_html__('Question Counter', 'dtlms').'</h4>';
						$out .= '<div class="dtlms-question-counter-container">';
							$out .= '<span class="dtlms-current-question">1</span>';
							$out .= '<span class="dtlms-question-sep">';
							$out .= '<span class="dtlms-total-question">'.$question_total.'</span>';
						$out .= '</div>';
					$out .= '</div>';
				
				}
				
				$quiz_pass_percentage = get_post_meta ( $quiz_id, "quiz-pass-percentage",true);
				if(isset($quiz_pass_percentage) && $quiz_pass_percentage != '') {
					$out .= '<div class="dtlms-warning-box">';
					$out .= sprintf( esc_html__('You require %s to pass this quiz!', 'dtlms'), $quiz_pass_percentage.'%' );
					$out .= '</div>';	
				}
			
			$out .= '</div>';		
		
		$out .= '</div>';						
		
		echo $out;
					
	}

	die();

}


function dtlms_generate_quiz_questions($question_id, $question_type, $user_answer, $location) {

	$user_answer_op = '';
	if($location == 'useranswer' && $user_answer != '') {
		$user_answer_op = $user_answer;
		$user_answer = str_replace(array("\\"), "", $user_answer);
	}

	$out = '<div class="dtlms-quiz-questions-container">';

		$out .= '<div class="dtlms-quiz-questions">';
		
			if($question_type == 'multiple-choice') {
				
				$multichoice_answers = get_post_meta($question_id, 'multichoice-answers', true);
				
				if(isset($multichoice_answers) && is_array($multichoice_answers)) {
					$out .= '<ul>'; 
						$j = 1;
						foreach($multichoice_answers as $answer) {

							$checked_attr = $disabled_attr = $id_attr = $name_attr = $for_attr = '';
							if($location == 'useranswer' || $location == 'correctanswer') {
								$disabled_attr = 'disabled="disabled"';
								$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
							} else {
								$id_attr = 'id="dtlms-question-'.$question_id.'-option-'.$j.'"';
								$for_attr = 'for="dtlms-question-'.$question_id.'-option-'.$j.'"';
								$name_attr = 'name="dtlms-question-'.$question_id.'"';							
							}

							if($location == 'useranswer') {
								if($user_answer == $answer) { 
									$checked_attr = 'checked="checked"';
								}	
							} else if($location == 'correctanswer') {
								$correct_answer = get_post_meta($question_id, 'multichoice-correct-answer', true);
								if($correct_answer == $answer) { 
									$checked_attr = 'checked="checked"';
								}	
							}					

							$out .= '<li>';
								$out .= '<div class="dtlms-quiz-answers-container">';
									$out .= '<input type="radio" value="'.htmlentities($answer, ENT_QUOTES).'" '.$id_attr.' '.$name_attr.' '.$checked_attr.'  '.$disabled_attr.'/><label '.$for_attr.'>'.do_shortcode($answer).'</label>';
								$out .= '</div>';
							$out .= '</li>';

							$j++;

						}
					$out .= '</ul>';
				}
			
			} else if($question_type == 'multiple-choice-image') {
				
				$multichoice_image_answers = get_post_meta($question_id, 'multichoice-image-answers', true);
				
				if(isset($multichoice_image_answers) && is_array($multichoice_image_answers)) {
					$out .= '<ul class="dtlms-question-image-options">'; 
						$j = 1;
						foreach($multichoice_image_answers as $answer) {

							$checked_attr = $disabled_attr = $id_attr = $name_attr = $li_class = '';
							if($location == 'useranswer' || $location == 'correctanswer') {
								$disabled_attr = 'disabled="disabled"';
								$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
							} else {
								$id_attr = 'id="dtlms-question-'.$question_id.'-option-'.$j.'"';
								$name_attr = 'name="dtlms-question-'.$question_id.'"';							
							}

							if($location == 'useranswer') {
								if($user_answer == $answer) { 
									$checked_attr = 'checked="checked"';
									$li_class = 'class="selected"';
								}
							} else if($location == 'correctanswer') {
								$correct_answer = get_post_meta($question_id, 'multichoice-image-correct-answer', true);
								if($correct_answer == $answer) { 
									$checked_attr = 'checked="checked"';
									$li_class = 'class="selected"';
								}	
							}

							$out .= '<li '.$li_class.'>';
								$out .= '<div class="dtlms-quiz-answers-container">';
									$out .= '<img src="'.esc_url($answer).'" />';
									$out .= '<input type="radio" value="'.esc_url($answer).'" '.$id_attr.' '.$name_attr.' '.$checked_attr.' '.$disabled_attr.' class="multichoice-image hidden" />';
								$out .= '</div>';
							$out .= '</li>';

							$j++;

						}
					$out .= '</ul>';
				}
			
			} else if($question_type == 'multiple-correct') {
				
				$multicorrect_answers = get_post_meta ( $question_id, 'multicorrect-answers', TRUE );
				
				if(isset($multicorrect_answers) && is_array($multicorrect_answers)) {
					$out .= '<ul>'; 
						$j = 1;
						foreach($multicorrect_answers as $answer) {

							$checked_attr = $disabled_attr = $id_attr = $name_attr = $for_attr = '';
							if($location == 'useranswer' || $location == 'correctanswer') {
								$disabled_attr = 'disabled="disabled"';
								$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
							} else {
								$id_attr = 'id="dtlms-question-'.$question_id.'-option-'.$j.'"';
								$for_attr = 'for="dtlms-question-'.$question_id.'-option-'.$j.'"';
								$name_attr = 'name="dtlms-question-'.$question_id.'[]"';							
							}

							if($location == 'useranswer') {
								if(is_array($user_answer) && in_array($answer, $user_answer)) { 
									$checked_attr = 'checked="checked"';
								}	
							} else if($location == 'correctanswer') {
								$correct_answer = get_post_meta($question_id, 'multicorrect-correct-answer', true);
								if(is_array($correct_answer) && in_array($answer, $correct_answer)) { 
									$checked_attr = 'checked="checked"';
								}	
							}

							$out .= '<li>';
								$out .= '<div class="dtlms-quiz-answers-container">';
									$out .= '<input type="checkbox" value="'.htmlentities($answer, ENT_QUOTES).'" '.$id_attr.' '.$name_attr.' '.$checked_attr.' '.$disabled_attr.' /><label '.$for_attr.'>'.do_shortcode($answer).'</label>';
								$out .= '</div>';
							$out .= '</li>';

							$j++;

						}
					$out .= '</ul>';
				}
			
			} else if($question_type == 'boolean') {

				$true_attr = $false_attr = $disabled_attr = $id_attr = $name_attr = $for_attr = '';
				if($location == 'useranswer' || $location == 'correctanswer') {
					$disabled_attr = 'disabled="disabled"';
					$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
				} else {
					$id_true_attr = 'id="dtlms-question-'.$question_id.'-true"';
					$for_true_attr = 'for="dtlms-question-'.$question_id.'-true"';
					$id_false_attr = 'id="dtlms-question-'.$question_id.'-false"';
					$for_false_attr = 'for="dtlms-question-'.$question_id.'-false"';				
					$name_attr = 'name="dtlms-question-'.$question_id.'"';							
				}

				if($location == 'useranswer') {
					$user_answer = strtolower(trim($user_answer));
					if($user_answer == 'true') {
						$true_attr = 'checked="checked"'; 
						$false_attr = ''; 
					} elseif($user_answer == 'false') {
						$true_attr = ''; 
						$false_attr = 'checked="checked"';
					}
				} else if($location == 'correctanswer') {
					$correct_answer = get_post_meta($question_id, 'boolean-answer', true);
					$correct_answer = strtolower(trim($correct_answer));
					if($correct_answer == 'true') {
						$true_attr = 'checked="checked"'; 
						$false_attr = ''; 
					} elseif($correct_answer == 'false') {
						$true_attr = ''; 
						$false_attr = 'checked="checked"';
					}	
				}

				$out .= '<div class="dtlms-boolean">';
					$out .= '<span><input type="radio" value="true" '.$id_true_attr.' '.$name_attr.' '.$true_attr.' '.$disabled_attr.' /><label '.$for_true_attr.'>'.esc_html__('True', 'dtlms').'</label></span>';
					$out .= '<span><input type="radio" value="false" '.$id_false_attr.' '.$name_attr.' '.$false_attr.' '.$disabled_attr.' /><label '.$for_false_attr.'>'.esc_html__('False', 'dtlms').'</label></span>';
				$out .= '</div>';			
				
			} else if($question_type == 'gap-fill') {

				$text_before_gap = get_post_meta ( $question_id, 'text-before-gap', TRUE );
				$text_before_gap = !empty($text_before_gap) ? $text_before_gap : '';
				$text_after_gap = get_post_meta ( $question_id, 'text-after-gap', TRUE );
				$text_after_gap = !empty($text_after_gap) ? $text_after_gap : '';
				
				$disabled_attr = $id_attr = $name_attr = '';
				if($location == 'useranswer' || $location == 'correctanswer') {
					$disabled_attr = 'disabled="disabled"';
					$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
				} else {
					$id_attr = 'id="dtlms-question-'.$question_id.'-option"';
					$name_attr = 'name="dtlms-question-'.$question_id.'"';							
				}

				$value_attr = 'value=""';
				if($location == 'useranswer') {
					$value_attr = 'value="'.$user_answer_op.'"';
				} else if($location == 'correctanswer') {
					$correct_answer = get_post_meta($question_id, 'gap', true);
					$value_attr = 'value="'.$correct_answer.'"';	
				}

				$out .= '<div class="dtlms-gapfill">';
					$out .= $text_before_gap.' <input type="text" '.$value_attr.' '.$id_attr.' '.$name_attr.' '.$disabled_attr.' class="dtlms-gap" /> '.$text_after_gap;
				$out .= '</div>';	
			
			} else if($question_type == 'single-line') {
					
				$disabled_attr = $id_attr = $name_attr = '';
				if($location == 'useranswer' || $location == 'correctanswer') {
					$disabled_attr = 'disabled="disabled"';
					$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
				} else {
					$id_attr = 'id="dtlms-question-'.$question_id.'-option"';
					$name_attr = 'name="dtlms-question-'.$question_id.'"';							
				}

				$value_attr = 'value=""';
				if($location == 'useranswer') {
					$value_attr = 'value="'.$user_answer_op.'"';
				} else if($location == 'correctanswer') {
					$correct_answer = get_post_meta($question_id, 'singleline-answer', true);
					$value_attr = 'value="'.$correct_answer.'"';	
				}

				$out .= '<input type="text" '.$value_attr.' '.$id_attr.' '.$name_attr.' '.$disabled_attr.' />';			

			} else if($question_type == 'multi-line') {

				$disabled_attr = $id_attr = $name_attr = '';
				if($location == 'useranswer' || $location == 'correctanswer') {
					$disabled_attr = 'disabled="disabled"';
					$name_attr = 'name="dtlms-question-'.$question_id.'-'.$location.'-disabled"';							
				} else {
					$id_attr = 'id="dtlms-question-'.$question_id.'-option"';
					$name_attr = 'name="dtlms-question-'.$question_id.'"';							
				}

				$user_answer_data = '';
				if($location == 'useranswer') {
					$user_answer_data = str_replace(array("<br>", "<br />"), "", $user_answer_op);
				} else if($location == 'correctanswer') {
					$correct_answer = get_post_meta($question_id, 'multiline-answer', true);
					$user_answer_data = str_replace(array("<br>", "<br />"), "", $correct_answer);	
				}

				$out .= '<textarea '.$id_attr.' '.$name_attr.' '.$disabled_attr.'>'.$user_answer_data.'</textarea>';
				
			}
		
		$out .= '</div>';

	$out .= '</div>';

	return $out;

}


add_action( 'wp_ajax_dtlms_show_answers_with_explanation', 'dtlms_show_answers_with_explanation' );
add_action( 'wp_ajax_nopriv_dtlms_show_answers_with_explanation', 'dtlms_show_answers_with_explanation' );
function dtlms_show_answers_with_explanation() {
	
	$course_id = $_REQUEST['course_id'];
	$quiz_id = $_REQUEST['quiz_id'];
	$question_id = $_REQUEST['question_id'];
	$user_id = $_REQUEST['user_id'];
	$author_id = $_REQUEST['author_id'];
	
	$question_type = get_post_meta($question_id, 'question-type', true);
	$user_answer = isset($_POST['dtlms-question-'.$question_id]) ? $_POST['dtlms-question-'.$question_id] : '';
	
	if(!dtlms_validate_user_answer($question_id, $question_type, $user_answer)) {
		
		echo dtlms_show_answers_with_explanation_content($question_id, -1, false, 0);
		
	} else {
		
		echo 'passed';
		
	}

	die();

}

function dtlms_validate_user_answer($question_id, $question_type, $user_answer) {

	if($question_type == 'multiple-choice') {
		
		$correct_answer = get_post_meta ( $question_id, 'multichoice-correct-answer', TRUE );
		
	} else if($question_type == 'multiple-choice-image') {
		
		$correct_answer = get_post_meta ( $question_id, 'multichoice-image-correct-answer', TRUE );
		
	} else if($question_type == 'multiple-correct') {
		
		$correct_answer = get_post_meta ( $question_id, 'multicorrect-correct-answer', TRUE );
		
	} else if($question_type == 'boolean') {
		
		$correct_answer = get_post_meta ( $question_id, 'boolean-answer', TRUE );
		
	} else if($question_type == 'gap-fill') {
	
		$correct_answer = get_post_meta ( $question_id, 'gap', TRUE );
	
	} else if($question_type == 'single-line') {
					
		$correct_answer = get_post_meta ( $question_id, 'singleline-answer', TRUE );
	
	} else if($question_type == 'multi-line') {
					
		$correct_answer = get_post_meta ( $question_id, 'multiline-answer', TRUE );
		$correct_answer = str_replace(array("\r", "\n", "\r\n", "<br>", "<br />", " ", "'", "\\"), "", $correct_answer);
		
		$user_answer = str_replace(array("\r", "\n", "\r\n", "<br>", "<br />", " ", "'", "\\"), "", $user_answer);
		
	}
	
	$user_answer = str_replace(array("\\"), "", $user_answer);
	
	if($question_type != 'multiple-correct') {
		$correct_answer = strtolower(trim($correct_answer));
		$user_answer = strtolower(trim($user_answer));
	}
	
	if($correct_answer == $user_answer) {
		return true;
	} else {
		return false;
	}

}

function dtlms_show_answers_with_explanation_content($question_id, $grade_post_id, $show_title, $serial_no) {
	
	global $post;
	$post = get_post($question_id);

	$question_type = get_post_meta($question_id, 'question-type', true);

	if($show_title) {
		$user_answer = get_post_meta($grade_post_id, 'dtlms-question-'.$question_id, true);
	} else {
		$user_answer = isset($_POST['dtlms-question-'.$question_id]) ? $_POST['dtlms-question-'.$question_id] : '';
	}
	
	if($grade_post_id > 0) {
		$question_grade = get_post_meta($grade_post_id, 'question-id-'.$question_id.'-grade', true);
		if($question_grade == true) { 
			$grade_cls = 'dtlms-correct'; 
		} else { 
			$grade_cls = 'dtlms-wrong'; 
		}
	} else {
		$grade_cls = ($grade_post_id == -1) ? 'dtlms-wrong' : '';
	}

	$quiz_questions_onebyone = get_post_meta($quiz_id, 'quiz-questions-onebyone', true);

	$add_class = '';
	if($quiz_questions_onebyone == 'true') {
		$add_class = 'dtlms-questions-oneatatime';
	}


	if( defined( 'DOING_AJAX' ) && DOING_AJAX && class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes') ) {
		WPBMap::addAllMappedShortcodes();
	}
	
	$out .= '<div class="dtlms-question '.esc_attr($grade_cls).' '.esc_attr($add_class).'">';

		if($show_title) {
			$quiz_title = apply_filters('the_content', get_post_field('post_content', $question_id));
			$out .= '<div class="dtlms-question-title-container">';
				$out .= '<div class="dtlms-question-title"><span class="dtlms-question-title-counter">'.esc_html__( 'Question', 'dtlms' ).' '.$serial_no.'.</span>'.do_shortcode($quiz_title).'</div>';
			$out .= '</div>';			
		}

		$out .= '<div class="dtlms-user-answer-container">';

			$out .= '<h5>'.esc_html__('Your Answer', 'dtlms').'</h5>';

			$out .= dtlms_generate_quiz_questions($question_id, $question_type, $user_answer, 'useranswer');

		$out .= '</div>';

		$out .= '<div class="dtlms-correct-answer-container">';

			$out .= '<h5>'.esc_html__('Correct Answer', 'dtlms').'</h5>';

			$out .= dtlms_generate_quiz_questions($question_id, $question_type, '', 'correctanswer');

		$out .= '</div>';

		$answer_explanation = get_post_meta($question_id, 'answer-explanation', true);
		
		if($answer_explanation != '') {

			$out .= '<div class="dtlms-answer-explanation-container">';

				$out .= '<h5>'.esc_html__('Answer Explanation', 'dtlms').'</h5>';

				$out .= '<div class="dtlms-answer-explantion-holder">'.do_shortcode(nl2br($answer_explanation)).'</div>';

			$out .= '</div>';

		}	
		
	$out .= '</div>';

	wp_reset_postdata();
		
	return $out;

}

add_action( 'wp_ajax_dtlms_ajax_validate_quiz', 'dtlms_ajax_validate_quiz' );
add_action( 'wp_ajax_nopriv_dtlms_ajax_validate_quiz', 'dtlms_ajax_validate_quiz' );
function dtlms_ajax_validate_quiz() {
	
	$course_id = $_REQUEST['course_id'];
	$user_id = $_REQUEST['user_id'];
	$lesson_id = $_REQUEST['lesson_id'];
	$quiz_id = $_REQUEST['quiz_id'];
	$assignment_id = $_REQUEST['assignment_id'];
	$author_id = $_REQUEST['author_id'];
	$parent_curriculum_id = $_REQUEST['parent_curriculum_id'];
	$next_curriculum_id = $_REQUEST['next_curriculum_id'];
	$timings = $_REQUEST['timings'];

	$dtlms_question_ids = $_REQUEST['dtlms-question-ids'];
	$dtlms_question_grades = $_REQUEST['dtlms-question-grades'];
	$dtlms_question_negative_grades = $_REQUEST['dtlms-question-negative-grades'];
	

	$title = get_the_title($quiz_id);
	$curriculum_details = get_user_meta($user_id, $course_id, true);

	if(isset($curriculum_details['curriculum'][$quiz_id]['temp-grade-post-id']) && $curriculum_details['curriculum'][$quiz_id]['temp-grade-post-id'] > 0) {
		$quiz_grade_id = $curriculum_details['curriculum'][$quiz_id]['temp-grade-post-id'];
		unset($curriculum_details['curriculum'][$quiz_id]['temp-grade-post-id']);
		$curriculum_details['curriculum'][$quiz_id]['grade-post-id'] = $quiz_grade_id;
		delete_post_meta($quiz_grade_id, 'temp-grade-post-id');
	} else {
		if($parent_curriculum_id > 0) {
			$quiz_grade_id = (isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id']) && $curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id'] > 0) ? $curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id'] : -1;
		} else {
			$quiz_grade_id = (isset($curriculum_details['curriculum'][$quiz_id]['grade-post-id']) && $curriculum_details['curriculum'][$quiz_id]['grade-post-id'] > 0) ? $curriculum_details['curriculum'][$quiz_id]['grade-post-id'] : -1;
		}
	}

	$course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;
	

	if($quiz_grade_id > 0) {

		// update user attempt
		$user_attempts = get_post_meta ($quiz_grade_id, 'user-attempts', true);
		$prev_user_attempts = $user_attempts;
		$user_attempts = $user_attempts+1;

		update_post_meta ( $quiz_grade_id, 'user-attempts',  $user_attempts );

		$grade_post_id = $quiz_grade_id;

	} else {	

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

		$grade_post = array(
			'post_title' => $title,
			'post_status' => 'publish',
			'post_type' => 'dtlms_gradings',
			'post_author' => $author_id,
			'post_parent' => $parent_grade_id
		);
		
		$grade_post_id = wp_insert_post( $grade_post );

		update_post_meta ( $grade_post_id, 'dtlms-course-id',  $course_id );
		update_post_meta ( $grade_post_id, 'dtlms-course-grade-id',  $course_grade_id );
		update_post_meta ( $grade_post_id, 'dtlms-user-id',  $user_id );
		update_post_meta ( $grade_post_id, 'dtlms-lesson-id',  $lesson_id );
		update_post_meta ( $grade_post_id, 'dtlms-quiz-id',  $quiz_id );
		update_post_meta ( $grade_post_id, 'dtlms-assignment-id',  $assignment_id );
		update_post_meta ( $grade_post_id, 'dtlms-parent-curriculum-id',  $parent_curriculum_id );	
		update_post_meta ( $grade_post_id, 'grade-type',  'quiz' );

		update_post_meta ( $grade_post_id, 'user-attempts',  1 );

		$prev_user_attempts = 0;

		// Update user meta field
		if($parent_curriculum_id > 0) {
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['grade-post-id'] = $grade_post_id;
		} else {
			$curriculum_details['curriculum'][$quiz_id]['grade-post-id'] = $grade_post_id;
		}		
		update_user_meta($user_id, $course_id, $curriculum_details);			

	}


	// Generate previous gradings
	if($prev_user_attempts > 0) {

		$prev_gradings = get_post_meta ($grade_post_id, 'prev-gradings', true);
		$prev_gradings = (isset($prev_gradings) && !empty($prev_gradings)) ? array_filter($prev_gradings) : array();

		$prev_marks_obtained = get_post_meta ($grade_post_id, 'marks-obtained', true);
		$prev_marks_obtained = (isset($prev_marks_obtained) && $prev_marks_obtained > 0) ? $prev_marks_obtained : 0;
		
		$prev_marks_obtained_percentage = get_post_meta ($grade_post_id, 'marks-obtained-percentage', true); 
		$prev_marks_obtained_percentage = (isset($prev_marks_obtained_percentage) && $prev_marks_obtained_percentage > 0) ? $prev_marks_obtained_percentage : 0;

		$prev_timings = get_post_meta ($grade_post_id, 'timings', true);
		$prev_timings = (isset($prev_timings) && $prev_timings != '') ? $prev_timings : '';	

		$prev_gradings[$prev_user_attempts-1]['attempts'] = $prev_user_attempts;
		$prev_gradings[$prev_user_attempts-1]['mark'] = $prev_marks_obtained;
		$prev_gradings[$prev_user_attempts-1]['percentage'] = $prev_marks_obtained_percentage;
		$prev_gradings[$prev_user_attempts-1]['timings'] = $prev_timings;
		$prev_gradings[$prev_user_attempts-1]['question-ids'] = $dtlms_question_ids;
		$prev_gradings[$prev_user_attempts-1]['question-grades'] = $dtlms_question_grades;
		$prev_gradings[$prev_user_attempts-1]['question-negative-grades'] = $dtlms_question_negative_grades;

		update_post_meta ($grade_post_id, 'prev-gradings',  $prev_gradings);

	}

	// Calculate and update gradings
	$quiz_auto_evaluation = get_post_meta ($quiz_id, 'quiz-auto-evaluation', true);
	$quiz_markasgraded_in_autoevaluation = get_post_meta( $quiz_id, 'quiz-markasgraded-in-autoevaluation', true );
	$quiz_total_grade = get_post_meta ($quiz_id, 'quiz-total-grade', true); 
	$passmark_percentage = get_post_meta ($quiz_id, 'quiz-pass-percentage', true);
	
	$quiz_question = explode(',', $dtlms_question_ids);
	$quiz_question_grade = explode(',', $dtlms_question_grades);
	$quiz_question_negative_grade = explode(',', $dtlms_question_negative_grades);

	$total = $skipped = $correct = $wrong = 0;
	$user_grade = 0;

	foreach($quiz_question as $question_id) {

		$user_answer = isset($_POST['dtlms-question-'.$question_id]) ? $_POST['dtlms-question-'.$question_id] : '';

		if($user_answer == '') {

			delete_post_meta ( $grade_post_id, 'dtlms-question-'.$question_id );
			delete_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade' );

			$skipped++;

		} else {
		
			$question_type = get_post_meta ( $question_id, 'question-type', true );
			if($question_type == 'multi-line') {
				$user_answer = trim(nl2br($user_answer));
			} else if($question_type == 'single-line') {
				$user_answer = trim($user_answer);
			}
			
			update_post_meta ($grade_post_id, 'dtlms-question-'.$question_id, $user_answer);
			
			if(isset($quiz_auto_evaluation) && $quiz_auto_evaluation != '') {
				if($user_answer != '') {
					if(dtlms_validate_user_answer($question_id, $question_type, $user_answer)) {
						update_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade', true );
						$user_grade = $user_grade + $quiz_question_grade[$total];
						$correct++;
					} else {
						delete_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade' );
						$user_grade = $user_grade - $quiz_question_negative_grade[$total];
						$wrong++;
					}
				}
			} else {
				delete_post_meta ( $grade_post_id, 'question-id-'.$question_id.'-grade' );	
			}

		}
		
		$total++;
				
	}

	// Update other details
	update_post_meta ( $grade_post_id, 'timings',  $timings );
	update_post_meta ( $grade_post_id, 'total-questions',  $total );
	update_post_meta ( $grade_post_id, 'skipped-questions',  $skipped );
	update_post_meta ( $grade_post_id, 'correct-questions',  $correct );
	update_post_meta ( $grade_post_id, 'wrong-questions',  $wrong );

	update_post_meta ( $grade_post_id, 'question-ids',  $dtlms_question_ids );
	update_post_meta ( $grade_post_id, 'question-grades',  $dtlms_question_grades );
	update_post_meta ( $grade_post_id, 'question-negative-grades',  $dtlms_question_negative_grades );


	$curriculum_details = get_user_meta($user_id, $course_id, true);	
	
	$user_percentage = 0;
	if($quiz_auto_evaluation == 'true') {

		$user_percentage = round((($user_grade/$quiz_total_grade)*100), 2);
		update_post_meta ( $grade_post_id, 'marks-obtained', stripslashes ( $user_grade ) );
		update_post_meta ( $grade_post_id, 'marks-obtained-percentage', stripslashes ( $user_percentage ) );

		// Update user meta field
		if($parent_curriculum_id > 0) {			
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['marks-obtained'] = $user_grade;
			$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['marks-obtained-percentage'] = $user_percentage;
		} else {
			$curriculum_details['curriculum'][$quiz_id]['marks-obtained'] = $user_grade;
			$curriculum_details['curriculum'][$quiz_id]['marks-obtained-percentage'] = $user_percentage;
		}

		if($quiz_markasgraded_in_autoevaluation == 'true') {

			update_post_meta ( $grade_post_id, 'graded', 'true' );
			if($parent_curriculum_id > 0) {			
				$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]['completed'] = 1;
			} else {
				$curriculum_details['curriculum'][$quiz_id]['completed'] = 1;
			}	
			$completed_count = isset($curriculum_details['completed-count']) ? $curriculum_details['completed-count'] : 0;
			$completed_count = $completed_count + 1;
			$curriculum_details['completed-count'] = $completed_count;		
			
			update_post_meta($course_grade_id, 'completed-count', $completed_count);							
		}

	}

	// Update the next locked curriculum item
	$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
	if($curriculum_completion_lock == 'true') {
		if($next_curriculum_id > 0) {
			$curriculum_details['next-curriculum-id'] = $next_curriculum_id;
			$open_curriculum_on_submission = get_post_meta($course_id, 'open-curriculum-on-submission', true);
			if($open_curriculum_on_submission == 'true' || ($quiz_auto_evaluation == 'true' && $quiz_markasgraded_in_autoevaluation == 'true')) {
				$curriculum_details['active-next-curriculum-id'] = $next_curriculum_id;
			}
		}
	}

	update_user_meta($user_id, $course_id, $curriculum_details);


	// Display post quiz message
	$quiz_postmsg = get_post_meta ($quiz_id, 'quiz-postmsg', true);
	if(isset($quiz_postmsg) && $quiz_postmsg != '') {
		echo '<div class="dtlms-post-quiz-msg">'.$quiz_postmsg.'</div>';
	}

	if(isset($quiz_auto_evaluation) && $quiz_auto_evaluation != '') {
		if($passmark_percentage > 0) {
			if($user_percentage >= $passmark_percentage) {
				$user_result_class = 'dtlms-user-result-pass';				
			} else {
				$user_result_class = 'dtlms-user-result-fail';
			}
			echo '<div class="dtlms-quiz-results-container '.$user_result_class.'">';
				echo '<h2>'.esc_html__('Quiz Result', 'dtlms').'</h2>';
				echo '<div class="dtlms-quiz-results">';
					echo '<h5>'.sprintf( esc_html__('Your grade is %1$s', 'dtlms'), '<span>'.$user_percentage.'%</span>').'</h5>';
				echo '</div>';
				echo dtlms_quiz_statistics_counter_progressbar($grade_post_id, true);
				echo '<h3><strong>'.esc_html__('Note : ', 'dtlms').'</strong>'.sprintf( esc_html__('You require %1$s to pass this quiz.', 'dtlms'), $passmark_percentage.'%').'</h3>';
				echo '<a href="#" class="dtlms-button dtlms-view-quiz-result filled large" data-quizid="'.$quiz_id.'" data-gradeid="'.$grade_post_id.'">'.esc_html__('View Quiz Results', 'dtlms').'</a>';
			echo '</div>';				
		} else  {
			echo '<div class="dtlms-quiz-results-container">';
				echo '<h2>'.esc_html__('Quiz Result', 'dtlms').'</h2>';
				echo '<div class="dtlms-quiz-results">';
					echo '<h5>'.sprintf( esc_html__('Your grade is %1$s', 'dtlms'), '<span>'.$user_percentage.'%</span>').'</h5>';
				echo '</div>';
				echo dtlms_quiz_statistics_counter_progressbar($grade_post_id, true);
				echo '<a href="#" class="dtlms-button dtlms-view-quiz-result filled large" data-quizid="'.$quiz_id.'" data-gradeid="'.$grade_post_id.'">'.esc_html__('View Quiz Results', 'dtlms').'</a>';
			echo '</div>';			
		}
	} else {
		echo '<div class="dtlms-info-box">'.esc_html__('Your quiz have been submitted successfully and it will be graded soon!', 'dtlms').'</div>';
	}

	die();

}

add_action( 'wp_ajax_dtlms_view_quiz_results', 'dtlms_view_quiz_results' );
add_action( 'wp_ajax_nopriv_dtlms_view_quiz_results', 'dtlms_view_quiz_results' );
function dtlms_view_quiz_results() {

	$quiz_id = $_REQUEST['quiz_id'];
	$grade_id = $_REQUEST['grade_id'];
	
	$out = '<div class="dtlms-dashboard-quiz-statistics">';
		$out .= dtlms_get_quiz_statistics($grade_id, $quiz_id);
	$out .= '</div>';
	
	$out .= '<div class="dtlms-hr-invisible"></div>';
	
	$out .= '<div class="dtlms-questions-list-container">';
					
		$out .= '<div class="dtlms-questions-list">';
		
			$dtlms_question_ids = get_post_meta ( $grade_id, 'question-ids',  true );
			$quiz_questions = explode(',', $dtlms_question_ids);
						
			$i = 1;
			foreach($quiz_questions as $question_id) {
				$out .= dtlms_show_answers_with_explanation_content($question_id, $grade_id, true, $i);
				$i++;
			}

		$out .= '</div>';

	$out .= '</div>';

	echo $out;

	die();

}


function dtlms_get_quiz_statistics($grade_id, $quiz_id) {

	/*$output = '<div class="dtlms-column dtlms-one-half first">';

	$output .= '</div>';*/

	$output .= '<div class="dtlms-column dtlms-one-half first">';
		$output .= '<h6>'.esc_html('Your Score', 'dtlms').'</h6>';
		$output .= '<div class="dtlms-quiz-statistics-score-holder">';
			$output .= dtlms_quiz_statistics_progressbar($grade_id);
		$output .= '</div>';	
		$output .= '<h6>'.esc_html('Time Taken', 'dtlms').'</h6>';
		$output .= '<div class="dtlms-quiz-statistics-timetaken-holder">';
			$output .= dtlms_quiz_statistics_timetaken($grade_id, $quiz_id);
		$output .= '</div>';
	$output .= '</div>';	
	$output .= '<div class="dtlms-column dtlms-one-half">';
		$output .= '<h6>'.esc_html('Details', 'dtlms').'</h6>';
		$output .= dtlms_quiz_statistics_counter_progressbar($grade_id, false);
	$output .= '</div>';
	
	return $output;
	
}

function dtlms_quiz_statistics_donutchart($grade_id) {

	$marks_obtained_percentage = get_post_meta($grade_id, 'marks-obtained-percentage', true); 
	$marks_obtained_percentage = (isset($marks_obtained_percentage) && $marks_obtained_percentage > 0) ? $marks_obtained_percentage : 0;

	$output = dtlms_generate_donut_chart($marks_obtained_percentage);

	return $output;

}

function dtlms_quiz_statistics_progressbar($grade_id) {

	$marks_obtained_percentage = get_post_meta($grade_id, 'marks-obtained-percentage', true); 
	$marks_obtained_percentage = (isset($marks_obtained_percentage) && $marks_obtained_percentage > 0) ? $marks_obtained_percentage : 0;
	$marks_obtained_percentage = round($marks_obtained_percentage, 2);

	$output = '<label>'.esc_html__('% Out of 100', 'dtlms').'</label>';
	$output .= dtlms_generate_progressbar($marks_obtained_percentage);
	$output .= '<span class="dtlms-quiz-score">'.$marks_obtained_percentage.'%</span>';

	return $output;

}

function dtlms_quiz_statistics_counter($grade_id) {

    $total_questions = get_post_meta($grade_id, 'total-questions', true);
    $skipped_questions = get_post_meta($grade_id, 'skipped-questions', true);
    $correct_questions = get_post_meta($grade_id, 'correct-questions', true);
    $wrong_questions = get_post_meta($grade_id, 'wrong-questions', true);

	$output = '	<ul class="dtlms-quiz-statistics-counter">';
		$output .= '<li class="dtlms-quiz-total-questions">
						<label>'.esc_html__('Total', 'dtlms').'</label>
						<span class="dtlms-quiz-sepeartor"></span>
						<span class="dtlms-quiz-question-result">'.$total_questions.'</span>
					</li>';
		$output .= '<li class="dtlms-quiz-skipped-questions">
						<label>'.esc_html__('Skipped', 'dtlms').'</label>
						<span class="dtlms-quiz-sepeartor"></span>
						<span class="dtlms-quiz-question-result">'.$skipped_questions.'</span>
					</li>';
		$output .= '<li class="dtlms-quiz-correct-answers">
						<label>'.esc_html__('Correct', 'dtlms').'</label>
						<span class="dtlms-quiz-sepeartor"></span>
						<span class="dtlms-quiz-question-result">'.$correct_questions.'</span>
					</li>';
		$output .= '<li class="dtlms-quiz-wrong-answers">
						<label>'.esc_html__('Wrong', 'dtlms').'</label>
						<span class="dtlms-quiz-sepeartor"></span>
						<span class="dtlms-quiz-question-result">'.$wrong_questions.'</span>
					</li>';
	$output .= '</ul>';

	return $output;

}

function dtlms_quiz_statistics_counter_progressbar($grade_id, $show_grade = false) {

    $total_questions = get_post_meta($grade_id, 'total-questions', true);
    $skipped_questions = get_post_meta($grade_id, 'skipped-questions', true);
    $correct_questions = get_post_meta($grade_id, 'correct-questions', true);
    $wrong_questions = get_post_meta($grade_id, 'wrong-questions', true);

	$total_percentage = 100;
	$skipped_percentage = round(($skipped_questions/$total_questions)*100, 2);
	$correct_percentage = round(($correct_questions/$total_questions)*100, 2);
	$wrong_percentage = round(($wrong_questions/$total_questions)*100, 2);


	$output = '	<ul class="dtlms-quiz-statistics-counter-progressbar">';
		if($show_grade) {

			$marks_obtained_percentage = get_post_meta($grade_id, 'marks-obtained-percentage', true); 
			$marks_obtained_percentage = (isset($marks_obtained_percentage) && $marks_obtained_percentage > 0) ? $marks_obtained_percentage : 0;
			$marks_obtained_percentage = round($marks_obtained_percentage, 2);

			$output .= '<li class="dtlms-quiz-total-grade">
							<label>'.esc_html__('Grade ( % out of 100% )', 'dtlms').'</label>
							'.dtlms_generate_progressbar($marks_obtained_percentage).'
							<span class="dtlms-quiz-question-result">'.$marks_obtained_percentage.'</span>
						</li>';

		} else {

			$output .= '<li class="dtlms-quiz-total-questions">
							<label>'.esc_html__('Total', 'dtlms').'</label>
							'.dtlms_generate_progressbar($total_percentage).'
							<span class="dtlms-quiz-question-result">'.$total_questions.'</span>
						</li>';

		}
		$output .= '<li class="dtlms-quiz-skipped-questions">
						<label>'.esc_html__('Skipped', 'dtlms').'</label>
						'.dtlms_generate_progressbar($skipped_percentage).'
						<span class="dtlms-quiz-question-result">'.$skipped_questions.'</span>
					</li>';
		$output .= '<li class="dtlms-quiz-correct-answers">
						<label>'.esc_html__('Correct', 'dtlms').'</label>
						'.dtlms_generate_progressbar($correct_percentage).'
						<span class="dtlms-quiz-question-result">'.$correct_questions.'</span>
					</li>';
		$output .= '<li class="dtlms-quiz-wrong-answers">
						<label>'.esc_html__('Wrong', 'dtlms').'</label>
						'.dtlms_generate_progressbar($wrong_percentage).'
						<span class="dtlms-quiz-question-result">'.$wrong_questions.'</span>
					</li>';
	$output .= '</ul>';

	return $output;

}

function dtlms_quiz_statistics_timetaken($grade_id, $quiz_id) {

	$timings = get_post_meta($grade_id, 'timings', true);
	
	if($timings != '') {

		$duration = get_post_meta ( $quiz_id, 'duration', true );
		$duration_parameter = get_post_meta ( $quiz_id, 'duration-parameter', true );
		$duration_in_seconds = ($duration * $duration_parameter); 

		$time_taken = ($duration_in_seconds - $timings);
		$time_taken = gmdate('H:i:s', $time_taken);

	} else {

		$time_taken = '-';

	}

	$output = '<div class="dtlms-quiz-statistics-timetaken">
					<i class="far fa-clock"></i>
					<span>'.$time_taken.'</span>
				</div>';

	return $output;

}

function dtlms_generate_donut_chart($percent) {
	
	$fgcolor = '#e85f4f';
	if($percent >= 80) {
		$fgcolor = '#9bbd3c';
	} else if($percent >= 40 && $percent < 80) {
		$fgcolor = '#f5a627';
	}
	
	$out = '<div class="dtlms-donutchart" data-size="130" data-percent="'.$percent.'" data-bgcolor="#808080" data-fgcolor="'.$fgcolor.'"></div>';
	
	return $out;

}

?>