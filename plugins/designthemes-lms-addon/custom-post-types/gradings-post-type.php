<?php

class DTLMSGradingsPostType {
	
	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );

	}

	function dtlms_init() {

		$this->createPostType();
		add_action ( 'save_post', array ( $this, 'dtlms_save_post_meta' ) );
		//add_action ( 'trash_dtlms_gradings', array ( $this, 'dtlms_thrash_post' ) );
		add_action ( 'before_delete_post', array ( $this, 'dtlms_delete_post_curriculum_metadata'), 10 );

	}	

	function createPostType() {

		$labels = array (
				'name' => esc_html__('Gradings', 'dtlms'),
				'all_items' => esc_html__('All Gradings', 'dtlms'),
				'singular_name' => esc_html__('Grading', 'dtlms'),
				'add_new' => esc_html__('Add New', 'dtlms'),
				'add_new_item' => esc_html__('Add New Grading', 'dtlms'),
				'edit_item' => esc_html__('Edit Grading', 'dtlms'),
				'new_item' => esc_html__('New Grading', 'dtlms'),
				'view_item' => esc_html__('View Grading', 'dtlms'),
				'search_items' => esc_html__('Search Gradings', 'dtlms'),
				'not_found' => esc_html__('No Gradings found', 'dtlms'),
				'not_found_in_trash' => esc_html__('No Gradings found in Trash', 'dtlms'),
				'parent_item_colon' => esc_html__('Parent Grading:', 'dtlms'),
				'menu_name' => esc_html__('Gradings', 'dtlms' ) 
		);
		
		$args = array (
				'labels' => $labels,
				'hierarchical' => true,
				'description' => 'This is custom post type gradings',
				'supports' => array ('title', 'author', 'page-attributes'),
				
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => 'dtlms',
				
				'show_in_nav_menus' => false,
				'publicly_queryable' => false,
				'exclude_from_search' => true,
				'has_archive' => false,
				'query_var' => true,
				'can_export' => true,
				'capability_type' => 'post',
				'capabilities' => array(
					'create_posts' => 'do_not_allow',
				),
				'map_meta_cap' => true,
		);
		
		register_post_type ( 'dtlms_gradings', $args );

	}

	function dtlms_save_post_meta($post_id) {

		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_gradings_meta_nonce', $_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_gradings_meta_nonce'], 'dtlms_gradings_nonce') ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_gradings' == $_POST['post_type']) ) :	

			$class_id = get_post_meta ( $post_id, 'dtlms-class-id', true );
			$course_id = get_post_meta ( $post_id, 'dtlms-course-id', true );
			$course_grade_id = get_post_meta ( $post_id, 'dtlms-course-grade-id', true );
			$user_id = get_post_meta ( $post_id, 'dtlms-user-id', true );			
			$lesson_id = get_post_meta ( $post_id, 'dtlms-lesson-id', true );
			$quiz_id = get_post_meta ( $post_id, 'dtlms-quiz-id', true );
			$assignment_id = get_post_meta ( $post_id, 'dtlms-assignment-id', true );		
			$parent_curriculum_id = get_post_meta ( $post_id, 'dtlms-parent-curriculum-id', true );
			$grade_type = get_post_meta ( $post_id, 'grade-type', true );		


			if( isset( $_POST ['review-or-feedback'] ) && $_POST ['review-or-feedback'] != '' ) {
				update_post_meta ( $post_id, 'review-or-feedback', stripslashes ( $_POST ['review-or-feedback'] ) );
			} else {
				delete_post_meta ( $post_id, 'review-or-feedback' );
			}

			if( isset( $_POST ['graded'] ) && $_POST ['graded'] != '' ) {				
				update_post_meta ( $post_id, 'graded', stripslashes ( $_POST ['graded'] ) );
				$graded = true;			
			} else {
				delete_post_meta ( $post_id, 'graded' );
				$graded = false;
			}

			// Updating user array
			$item_id = $lock_item_id = -1;
			$free_item = false;
			if($grade_type == 'lesson') {
				$item_id = $lesson_id;
				$free_item = get_post_meta ( $lesson_id, 'free-lesson', true );
			} else if($grade_type == 'quiz') {
				$item_id = $quiz_id;
				$free_item = get_post_meta ( $quiz_id, 'free-quiz', true );
			} else if($grade_type == 'assignment') {
				$item_id = $assignment_id;
				$free_item = get_post_meta ( $assignment_id, 'free-assignment', true );
			}

			if($quiz_id > 0) {

				$question_ids = $_POST['question-ids'];
				$quiz_question = explode(',', $question_ids);

				foreach($quiz_question as $question_id) {
				
					if(isset($_POST['dtlms-question-id-'.$question_id.'-grade']) && $_POST ['dtlms-question-id-'.$question_id.'-grade'] == true) {
						update_post_meta ($post_id, 'question-id-'.$question_id.'-grade', stripslashes ($_POST['dtlms-question-id-'.$question_id.'-grade']));
					} else {
						delete_post_meta ( $post_id, 'question-id-'.$question_id.'-grade' );
					}
				
				}

				if(isset($_POST['total-questions']) && $_POST ['total-questions'] != '') {
					update_post_meta ( $post_id, 'total-questions',  $_POST ['total-questions'] );
				} else {
					delete_post_meta ( $post_id, 'total-questions' );
				}

				if(isset($_POST['skipped-questions']) && $_POST ['skipped-questions'] != '') {
					update_post_meta ( $post_id, 'skipped-questions',  $_POST ['skipped-questions'] );
				} else {
					delete_post_meta ( $post_id, 'skipped-questions' );
				}

				if(isset($_POST['correct-questions']) && $_POST ['correct-questions'] != '') {
					update_post_meta ( $post_id, 'correct-questions',  $_POST ['correct-questions'] );
				} else {
					delete_post_meta ( $post_id, 'correct-questions' );
				}

				if(isset($_POST['wrong-questions']) && $_POST ['wrong-questions'] != '') {
					update_post_meta ( $post_id, 'wrong-questions',  $_POST ['wrong-questions'] );
				} else {
					delete_post_meta ( $post_id, 'wrong-questions' );
				}								

			}			

			$curriculum_details = get_user_meta($user_id, $course_id, true);
			$class_curriculum_details = get_user_meta($user_id, $class_id, true);	

			if($item_id > 0) {

				$marks_obtained = isset($_POST ['dtlms-marks-obtained']) ? $_POST ['dtlms-marks-obtained'] : '';
				$marks_obtained_percentage = isset($_POST ['dtlms-marks-obtained-percentage']) ? $_POST ['dtlms-marks-obtained-percentage'] : '';

				if( $marks_obtained != '' && $marks_obtained > 0 ) {
					update_post_meta ( $post_id, 'marks-obtained', stripslashes ( $marks_obtained ) );
				} else {
					delete_post_meta ( $post_id, 'marks-obtained' );
				}

				if( $marks_obtained_percentage != '' && $marks_obtained_percentage > 0 ) {
					update_post_meta ( $post_id, 'marks-obtained-percentage', stripslashes ( $marks_obtained_percentage ) );
				} else {
					delete_post_meta ( $post_id, 'marks-obtained-percentage' );
				}


				if($graded) {

					$completed_count = isset($curriculum_details['completed-count']) ? $curriculum_details['completed-count'] : 0;
					$completed_count = $completed_count + 1;
					$curriculum_details['completed-count'] = $completed_count;				

					if($parent_curriculum_id > 0) {
						$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['completed'] = 1;
					} else {
						$curriculum_details['curriculum'][$item_id]['completed'] = 1;
					}		

				} else {

					$completed_count = isset($curriculum_details['completed-count']) ? $curriculum_details['completed-count'] : 0;
					$completed_count = $completed_count - 1;
					$curriculum_details['completed-count'] = $completed_count;				

					if($parent_curriculum_id > 0) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['completed']);
					} else {
						unset($curriculum_details['curriculum'][$item_id]['completed']);
					}						

				}

				if( $marks_obtained != '' && $marks_obtained > 0 ) {
					if($parent_curriculum_id > 0) {
						$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['marks-obtained'] = $marks_obtained;
					} else {
						$curriculum_details['curriculum'][$item_id]['marks-obtained'] = $marks_obtained;
					}
				} else {
					if($parent_curriculum_id > 0) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['marks-obtained']);
					} else {
						unset($curriculum_details['curriculum'][$item_id]['marks-obtained']);
					}	
				}

				if( $marks_obtained_percentage != '' && $marks_obtained_percentage > 0 ) {
					if($parent_curriculum_id > 0) {
						$curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['marks-obtained-percentage'] = $marks_obtained_percentage;
					} else {
						$curriculum_details['curriculum'][$item_id]['marks-obtained-percentage'] = $marks_obtained_percentage;
					}
				} else {
					if($parent_curriculum_id > 0) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$item_id]['marks-obtained-percentage']);
					} else {
						unset($curriculum_details['curriculum'][$item_id]['marks-obtained-percentage']);
					}	
				}

				update_post_meta($course_grade_id, 'completed-count', $completed_count);

			}

			if($grade_type == 'class') {
				
				if($graded) {

					$class_curriculum_details['completed'] = 1;

					$completed_users = get_post_meta($class_id, 'completed_users', true);
					$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
					array_push($completed_users, $user_id);
					update_post_meta($class_id, 'completed_users', array_unique($completed_users));		

					$completed_classes = get_user_meta($user_id, 'completed_classes', true);
					$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();
					array_push($completed_classes, $class_id);
					update_user_meta($user_id, 'completed_classes', array_unique($completed_classes));

					// Notification & Mail
					do_action('dtlms_poc_class_evaluated', $class_id, $user_id); 

				} else {

					unset($class_curriculum_details['completed']);

					$completed_users = get_post_meta($class_id, 'completed_users', true);
					$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
					if (($key = array_search($user_id, $completed_users)) !== false) {
					    unset($completed_users[$key]);
					}
					update_post_meta($class_id, 'completed_users', array_unique($completed_users));		

					$completed_classes = get_user_meta($user_id, 'completed_classes', true);
					$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();
					if (($key = array_search($class_id, $completed_classes)) !== false) {
					    unset($completed_classes[$key]);
					}					
					update_user_meta($user_id, 'completed_classes', array_unique($completed_classes));

				}
					

			}

			if($grade_type == 'course') {
				
				if($graded) {

					$curriculum_details['completed'] = 1;

					$completed_users = get_post_meta($course_id, 'completed_users', true);
					$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
					array_push($completed_users, $user_id);
					update_post_meta($course_id, 'completed_users', array_unique($completed_users));		

					$completed_courses = get_user_meta($user_id, 'completed_courses', true);
					$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();
					array_push($completed_courses, $course_id);
					update_user_meta($user_id, 'completed_courses', array_unique($completed_courses));

					// Notification & Mail
					do_action('dtlms_poc_course_evaluated', $course_id, $user_id); 					

				} else {

					unset($curriculum_details['completed']);

					$completed_users = get_post_meta($course_id, 'completed_users', true);
					$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
					if (($key = array_search($user_id, $completed_users)) !== false) {
					    unset($completed_users[$key]);
					}
					update_post_meta($course_id, 'completed_users', array_unique($completed_users));		

					$completed_courses = get_user_meta($user_id, 'completed_courses', true);
					$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();
					if (($key = array_search($course_id, $completed_courses)) !== false) {
					    unset($completed_courses[$key]);
					}					
					update_user_meta($user_id, 'completed_courses', array_unique($completed_courses));

				}

			}

			if($grade_type == 'class' || $grade_type == 'course') {	

				if( isset( $_POST ['user-percentage'] ) && $_POST ['user-percentage'] != '' ) {
					update_post_meta ( $post_id, 'user-percentage', stripslashes ( $_POST ['user-percentage'] ) );
				} else {
					delete_post_meta ( $post_id, 'user-percentage' );
				}


				update_post_meta ( $post_id, 'certificate-achieved', stripslashes ( $_POST ['certificate-achieved'] ) );

				if( isset( $_POST ['certificate-achieved'] ) && $_POST ['certificate-achieved'] != '' ) {
					
					// Notification & Mail
					if($grade_type == 'class') {
						do_action('dtlms_poc_class_certificate_achieved', $class_id, $user_id); 
					} else if($grade_type == 'course') {
						do_action('dtlms_poc_course_certificate_achieved', $course_id, $user_id); 
					}					
				/*} else {
					delete_post_meta ( $post_id, 'certificate-achieved' );*/
				}

				update_post_meta ( $post_id, 'badge-achieved', stripslashes ( $_POST ['badge-achieved'] ) );

				if( isset( $_POST ['badge-achieved'] ) && $_POST ['badge-achieved'] != '' ) {
					
					// Notification & Mail
					if($grade_type == 'class') {
						do_action('dtlms_poc_class_badge_achieved', $class_id, $user_id);
					} else if($grade_type == 'course') {
						do_action('dtlms_poc_course_badge_achieved', $course_id, $user_id);
					}								
				/*} else {
					delete_post_meta ( $post_id, 'badge-achieved' );*/
				}

				if( isset( $_POST ['date-on-certificate'] ) && $_POST ['date-on-certificate'] != '' ) {
					update_post_meta ( $post_id, 'date-on-certificate', stripslashes ( $_POST ['date-on-certificate'] ) );
				} else {
					delete_post_meta ( $post_id, 'date-on-certificate' );
				}									

			}	

			// Open the next locked curriculum item
			if($graded) {
				
				if(!$free_item) {
					$curriculum_completion_lock = get_post_meta($course_id, 'curriculum-completion-lock', true);
					if($curriculum_completion_lock == 'true') {
						$next_curriculum_id = $curriculum_details['next-curriculum-id'];
						$curriculum_details['active-next-curriculum-id'] = $next_curriculum_id;
					}
				}

			}

			update_user_meta($user_id, $course_id, $curriculum_details);
			update_user_meta($user_id, $class_id, $class_curriculum_details);

		endif;
			
	}

	function dtlms_thrash_post($post_id) {

		//echo 'A'.$post_id;
		//exit;

	   /* $childs = get_posts ( array(
	        'post_parent' => $post_id,
	        'post_type' => 'dtlms_gradings' 
	    ) );  

	    if(!empty($childs)) {
		    foreach($childs as $post){
		        wp_trash_post($post->ID);
		    }
	    }*/

	}

	function dtlms_delete_post_curriculum_metadata($post_id) {

		if (!current_user_can('delete_posts'))
	    	return;

	    global $post_type;   
	    if ( $post_type != 'dtlms_gradings' ) 
	    	return;

		$user_id = get_post_meta($post_id, 'dtlms-user-id', true);
		$course_id = get_post_meta($post_id, 'dtlms-course-id', true);
		$class_id = get_post_meta($post_id, 'dtlms-class-id', true);
		$lesson_id = get_post_meta($post_id, 'dtlms-lesson-id', true);
		$quiz_id = get_post_meta($post_id, 'dtlms-quiz-id', true);
		$assignment_id = get_post_meta($post_id, 'dtlms-assignment-id', true);
		$parent_curriculum_id = get_post_meta ( $post_id, 'dtlms-parent-curriculum-id', true );	
		$grade_type = get_post_meta ( $post_id, 'grade-type', true );		

		if($class_id > 0) {
			$curriculum_details = get_user_meta($user_id, $class_id, true);
		} else {
			$curriculum_details = get_user_meta($user_id, $course_id, true);
		}
		$grade_post_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;
		

		/*echo $post_id."<br>";
		echo $course_id."<br>";
		echo $class_id."<br>";
		echo $lesson_id."<br>";
		echo $assignment_id."<br>";
		echo $parent_curriculum_id."<br>";
		echo $grade_type."<br>";
		exit;*/		

	    if($post_id == $grade_post_id) {

	    	if($course_id > 0) {

		    	delete_user_meta($user_id, $course_id);

				$started_users = get_post_meta($course_id, 'started_users', true);
				$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array();
				if(in_array($user_id, $started_users)) {
				    unset($started_users[array_search($user_id, $started_users)]);
				}
				update_post_meta($course_id, 'started_users', $started_users);

				$started_courses = get_user_meta($user_id, 'started_courses', true);
				$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array();
				if(in_array($course_id, $started_courses)) {
				    unset($started_courses[array_search($course_id, $started_courses)]);
				}				
				update_user_meta($user_id, 'started_courses', $started_courses);	

				$submitted_users = get_post_meta($course_id, 'submitted_users', true);
				$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
				if(in_array($user_id, $submitted_users)) {
				    unset($submitted_users[array_search($user_id, $submitted_users)]);
				}
				update_post_meta($course_id, 'submitted_users', $submitted_users);

				$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
				$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();
				if(in_array($course_id, $submitted_courses)) {
				    unset($submitted_courses[array_search($course_id, $submitted_courses)]);
				}				
				update_user_meta($user_id, 'submitted_courses', $submitted_courses);	

				$completed_users = get_post_meta($course_id, 'completed_users', true);
				$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
				if(in_array($user_id, $completed_users)) {
				    unset($completed_users[array_search($user_id, $completed_users)]);
				}
				update_post_meta($course_id, 'completed_users', $completed_users);

				$completed_courses = get_user_meta($user_id, 'completed_courses', true);
				$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();
				if(in_array($course_id, $completed_courses)) {
				    unset($completed_courses[array_search($course_id, $completed_courses)]);
				}				
				update_user_meta($user_id, 'completed_courses', $completed_courses);

			}

	    	if($class_id > 0) {

		    	delete_user_meta($user_id, $class_id);

				$started_users = get_post_meta($class_id, 'started_users', true);
				$started_users = (is_array($started_users) && !empty($started_users)) ? $started_users : array();
				if(in_array($user_id, $started_users)) {
				    unset($started_users[array_search($user_id, $started_users)]);
				}
				update_post_meta($class_id, 'started_users', $started_users);

				$started_classes = get_user_meta($user_id, 'started_classes', true);
				$started_classes = (is_array($started_classes) && !empty($started_classes)) ? $started_classes : array();
				if(in_array($class_id, $started_classes)) {
				    unset($started_classes[array_search($class_id, $started_classes)]);
				}				
				update_user_meta($user_id, 'started_classes', $started_classes);	

				$submitted_users = get_post_meta($class_id, 'submitted_users', true);
				$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
				if(in_array($user_id, $submitted_users)) {
				    unset($submitted_users[array_search($user_id, $submitted_users)]);
				}
				update_post_meta($class_id, 'submitted_users', $submitted_users);

				$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
				$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();
				if(in_array($class_id, $submitted_classes)) {
				    unset($submitted_classes[array_search($class_id, $submitted_classes)]);
				}				
				update_user_meta($user_id, 'submitted_classes', $submitted_classes);	

				$completed_users = get_post_meta($class_id, 'completed_users', true);
				$completed_users = (is_array($completed_users) && !empty($completed_users)) ? $completed_users : array();
				if(in_array($user_id, $completed_users)) {
				    unset($completed_users[array_search($user_id, $completed_users)]);
				}
				update_post_meta($class_id, 'completed_users', $completed_users);

				$completed_classes = get_user_meta($user_id, 'completed_classes', true);
				$completed_classes = (is_array($completed_classes) && !empty($completed_classes)) ? $completed_classes : array();
				if(in_array($class_id, $completed_classes)) {
				    unset($completed_classes[array_search($class_id, $completed_classes)]);
				}				
				update_user_meta($user_id, 'completed_classes', $completed_classes);

			}			

	    } else {

			if($lesson_id > 0) {
				if($parent_curriculum_id > 0) {
					if(isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id])) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$lesson_id]);
					}
				} else {
					if(isset($curriculum_details['curriculum'][$lesson_id])) {
						unset($curriculum_details['curriculum'][$lesson_id]);
					}
				}
			}
			if($quiz_id > 0) {
				if($parent_curriculum_id > 0) {
					if(isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id])) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$quiz_id]);
					}
				} else {
					if(isset($curriculum_details['curriculum'][$quiz_id])) {
						unset($curriculum_details['curriculum'][$quiz_id]);
					}
				}				
			}
			if($assignment_id > 0) {
				if($parent_curriculum_id > 0) {
					if(isset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$assignment_id])) {
						unset($curriculum_details['curriculum'][$parent_curriculum_id]['curriculum'][$assignment_id]);
					}
				} else {
					if(isset($curriculum_details['curriculum'][$assignment_id])) {
						unset($curriculum_details['curriculum'][$assignment_id]);
					}
				}				
			}	

			$completed_count = isset($curriculum_details['completed-count']) ? $curriculum_details['completed-count'] : 0;
			if($completed_count > 0) {
				$completed_count = $completed_count - 1;
				$curriculum_details['completed-count'] = $completed_count;		
				if($grade_type == 'course') {
					update_post_meta($post_id, 'completed-count', $completed_count);
				}
			}

			update_user_meta($user_id, $course_id, $curriculum_details);

		}

		$attachment_id = get_post_meta ( $post_id, 'attachment-id', true);
		wp_delete_attachment( $attachment_id, true );


	    $childs = get_posts ( array(
	        'post_parent' => $post_id,
	        'post_type' => 'dtlms_gradings',
	        'post_status' => 'any'
	    ) );  

	    if(is_array($childs) && !empty($childs)) {
		    foreach($childs as $post) {
		        wp_delete_post($post->ID, false);
		    }
	    }					

	}	

	function dtlms_admin_init() {
	
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_grading_default_metabox' ) );		
		add_filter ( 'manage_dtlms_gradings_posts_columns', array ( $this, 'set_custom_edit_dtlms_gradings_columns' ) );
		add_action ( 'manage_dtlms_gradings_posts_custom_column', array ( $this, 'custom_dtlms_gradings_column' ), 10, 2 );			
		
	}

	function dtlms_add_grading_default_metabox() {
		add_meta_box ( 'dtlms-grading-default-metabox', esc_html__('Grading Options', 'dtlms'), array ( $this, 'dtlms_grading_default_metabox' ), 'dtlms_gradings', 'normal', 'default' );
	}
		
	function dtlms_grading_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/grading-default-metabox.php';
	}	

	function set_custom_edit_dtlms_gradings_columns($columns) {

		$newcolumns = array (
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'type' => 'Type',
			'learner' => 'Learner',
			'grade' => 'Grade',
			'status' => 'Status',
			'date' => 'Date'
		);
		$columns = array_merge ( $newcolumns, $columns );

		return $columns;

	}
	
	function custom_dtlms_gradings_column($columns, $id) {

		global $post;

		$grade_type = get_post_meta ($id, 'grade-type', true );

		$class_singular_label = apply_filters( 'class_label', 'singular' );
		
		switch ($columns) {
		
			case 'type':
				$labels = array(
						'class' => sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ),
						'course' => esc_html__( 'Course', 'dtlms' ),
						'lesson' => esc_html__( 'Lesson', 'dtlms' ),
						'quiz' => esc_html__( 'Quiz', 'dtlms' ),
						'assignment' => esc_html__( 'Assignment', 'dtlms' ),
					);
				$grade_type = get_post_meta ($id, 'grade-type', true);
				echo isset($labels[$grade_type]) ? $labels[$grade_type] : '';
			break;

			case 'learner':
					$user_id = get_post_meta ($id, 'dtlms-user-id', true);
					if(isset($user_id) && $user_id > 0) {
						$user_info = get_userdata($user_id);
						echo $user_info->display_name;
					}
			break;			
		
			case 'grade':
					if($grade_type == 'course' || $grade_type == 'class') {

						$user_percentage = get_post_meta ($id, 'user-percentage', true);
						if($user_percentage != '' && $user_percentage >= 0) {
							echo $user_percentage.'%'; 
						}

					} else {

						$dt_marks_obtained = get_post_meta ($id, 'marks-obtained', true); 
						$dt_marks_obtained = (isset($dt_marks_obtained) && $dt_marks_obtained > 0) ? $dt_marks_obtained : 0;
						$dt_marks_obtained_percent = get_post_meta( $id, 'marks-obtained-percentage', true); 
						$dt_marks_obtained_percent = (isset($dt_marks_obtained_percent) && $dt_marks_obtained_percent > 0) ? $dt_marks_obtained_percent : 0;
						if($dt_marks_obtained != '' && $dt_marks_obtained >= 0) {
							echo $dt_marks_obtained.' ('.$dt_marks_obtained_percent.'%)';
						}

					}
			break;
			
			case 'status':
					if($grade_type == 'course' || $grade_type == 'class') {
						$graded = get_post_meta ($id, 'graded', true);
						if($graded == 'true') {
							echo esc_html__('Graded', 'dtlms');
						} else {
							$submitted = get_post_meta ($id, 'submitted', true);
							if($submitted == '1') {
								echo esc_html__('Submitted for evaluation.', 'dtlms');
							} else {
								echo esc_html__('Not submitted yet', 'dtlms');
							}
						}
					} else {
						$temp_garde_post_id = get_post_meta ($id, 'temp-grade-post-id', true);
						if(isset($temp_garde_post_id) && $temp_garde_post_id > 0) {
							echo esc_html__('Not submitted yet', 'dtlms');
						} else {
							$graded = get_post_meta ($id, 'graded', true);
							if($graded == 'true') {
								echo esc_html__('Graded', 'dtlms');
							} else {
								echo esc_html__('Ungraded', 'dtlms');
							}
						}
					}
			break;
			
		}

	}
	
}

?>