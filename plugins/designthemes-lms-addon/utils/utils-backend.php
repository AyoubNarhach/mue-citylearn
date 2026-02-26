<?php

add_action( 'wp_ajax_dtlms_revoke_user_submission', 'dtlms_revoke_user_submission' );
add_action( 'wp_ajax_nopriv_dtlms_revoke_user_submission', 'dtlms_revoke_user_submission' );
function dtlms_revoke_user_submission() {

	$item_type = $_POST['item_type'];

	if($item_type == 'course') {

		$course_id = $_POST['course_id'];
		$user_id = $_POST['user_id'];

		$submitted_users = get_post_meta($course_id, 'submitted_users', true);
		$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
		if (($key = array_search($user_id, $submitted_users)) !== false) {
		    unset($submitted_users[$key]);
		}	
		update_post_meta($course_id, 'submitted_users', array_unique($submitted_users));		

		$submitted_courses = get_user_meta($user_id, 'submitted_courses', true);
		$submitted_courses = (is_array($submitted_courses) && !empty($submitted_courses)) ? $submitted_courses : array();
		if (($key = array_search($course_id, $submitted_courses)) !== false) {
		    unset($submitted_courses[$key]);
		}	
		update_user_meta($user_id, 'submitted_courses', array_unique($submitted_courses));


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


		$curriculum_details = get_user_meta($user_id, $course_id, true);
		$course_grade_id = isset($curriculum_details['grade-post-id']) ? $curriculum_details['grade-post-id'] : -1;

		if($course_grade_id > 0) {

			$completed_items_count = dtlms_parse_array_and_count_particular_key($curriculum_details['curriculum'], 'completed', 0);

			update_post_meta($course_grade_id, 'completed-count', $completed_items_count);
			delete_post_meta($course_grade_id, 'submitted');
			delete_post_meta($course_grade_id, 'completed');

			$curriculum_details['completed-count'] = $completed_items_count;
			unset($curriculum_details['submitted']);
			unset($curriculum_details['completed']);

			update_user_meta($user_id, $course_id, $curriculum_details);

		}

		// Notification & Mail
		do_action('dtlms_poc_course_submission_revoke', $course_id, $user_id); 

	}

	if($item_type == 'class') {

		$class_id = $_POST['class_id'];
		$user_id = $_POST['user_id'];

		$submitted_users = get_post_meta($class_id, 'submitted_users', true);
		$submitted_users = (is_array($submitted_users) && !empty($submitted_users)) ? $submitted_users : array();
		if (($key = array_search($user_id, $submitted_users)) !== false) {
		    unset($submitted_users[$key]);
		}	
		update_post_meta($class_id, 'submitted_users', array_unique($submitted_users));		

		$submitted_classes = get_user_meta($user_id, 'submitted_classes', true);
		$submitted_classes = (is_array($submitted_classes) && !empty($submitted_classes)) ? $submitted_classes : array();
		if (($key = array_search($class_id, $submitted_classes)) !== false) {
		    unset($submitted_classes[$key]);
		}	
		update_user_meta($user_id, 'submitted_classes', array_unique($submitted_classes));


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


		$class_curriculum_details = get_user_meta($user_id, $class_id, true);
		$class_grade_id = isset($class_curriculum_details['grade-post-id']) ? $class_curriculum_details['grade-post-id'] : -1;

		if($class_grade_id > 0) {

			delete_post_meta($class_grade_id, 'submitted');
			delete_post_meta($class_grade_id, 'completed');

			unset($class_curriculum_details['submitted']);
			unset($class_curriculum_details['completed']);

			update_user_meta($user_id, $class_id, $class_curriculum_details);

		}

		// Notification & Mail
		do_action('dtlms_poc_class_submission_revoke', $class_id, $user_id); 

	}	

	die();

}

?>