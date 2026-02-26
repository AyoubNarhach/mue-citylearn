<?php

class DTLMSQuizzesPostType {
	
	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );	
		add_filter ( 'template_include', array ( $this, 'dtlms_template_include' ) );

	}
	
	function dtlms_init() {

		$this->createPostType();
		add_action ( 'save_post', array ( $this, 'dtlms_save_post_meta' ) );

	}

	function createPostType() {
			
		$labels = array (
					'name' => esc_html__('Quizzes', 'dtlms'),
					'all_items' => esc_html__('All Quizzes', 'dtlms'),
					'singular_name' => esc_html__('Quiz', 'dtlms'),
					'add_new' => esc_html__('Add New', 'dtlms'),
					'add_new_item' => esc_html__('Add New Quiz', 'dtlms'),
					'edit_item' => esc_html__('Edit Quiz', 'dtlms'),
					'new_item' => esc_html__('New Quiz', 'dtlms'),
					'view_item' => esc_html__('View Quiz', 'dtlms'),
					'search_items' => esc_html__('Search Quizzes', 'dtlms'),
					'not_found' => esc_html__('No Quizzes found', 'dtlms'),
					'not_found_in_trash' => esc_html__('No Quizzes found in Trash', 'dtlms'),
					'parent_item_colon' => esc_html__('Parent Quiz:', 'dtlms'),
					'menu_name' => esc_html__('Quizzes', 'dtlms' ) 
				);
		
		$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type quizzes',
					'supports' => array (
							'title',
							'editor',
							'author',
							'thumbnail',
					),				
					'public' => false,
					'show_ui' => true,
					'show_in_menu' => 'dtlms',
					'show_in_nav_menus' => false,
					'publicly_queryable' => false,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'capability_type' => 'post' 
				);
		
		register_post_type ( 'dtlms_quizzes', $args );
			
	}

	function dtlms_save_post_meta($post_id) {

		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dttheme_quizzes_meta_nonce', $_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dttheme_quizzes_meta_nonce'], 'dttheme_quizzes_nonce') ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_quizzes' == $_POST['post_type']) ) :
					
			if( isset( $_POST ['free-quiz'] ) && $_POST ['free-quiz'] != '' ){
				update_post_meta ( $post_id, 'free-quiz', stripslashes ( $_POST ['free-quiz'] ) );
			} else {
				delete_post_meta ( $post_id, 'free-quiz' );
			}					
			
			if( isset( $_POST ['quiz-subtitle'] ) && $_POST ['quiz-subtitle'] != '' ) {
				update_post_meta ( $post_id, 'quiz-subtitle', stripslashes ( $_POST ['quiz-subtitle'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-subtitle' );
			}

			if( isset( $_POST ['duration'] ) && $_POST ['duration'] != '') {
				update_post_meta ( $post_id, 'duration', stripslashes ( $_POST ['duration'] ) );
			} else {
				delete_post_meta ( $post_id, 'duration' );
			}

			if( isset( $_POST ['duration-parameter'] ) && $_POST ['duration-parameter'] != '') {
				update_post_meta ( $post_id, 'duration-parameter', stripslashes ( $_POST ['duration-parameter'] ) );
			} else {
				delete_post_meta ( $post_id, 'duration-parameter' );
			}

			
			if( isset( $_POST ['quiz-retakes'] ) && $_POST ['quiz-retakes'] != '' ) {
				update_post_meta ( $post_id, 'quiz-retakes', stripslashes ( $_POST ['quiz-retakes'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-retakes' );
			}

			if( isset( $_POST ['quiz-postmsg'] ) && $_POST ['quiz-postmsg'] != '' ) {
				update_post_meta ( $post_id, 'quiz-postmsg', stripslashes ( $_POST ['quiz-postmsg'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-postmsg' );
			}

			if( isset( $_POST ['quiz-randomize-questions'] ) && $_POST ['quiz-randomize-questions'] != '' ) {update_post_meta ( $post_id, 'quiz-randomize-questions', stripslashes ( $_POST ['quiz-randomize-questions'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-randomize-questions' );
			}

			if( isset( $_POST ['quiz-auto-evaluation'] ) && $_POST ['quiz-auto-evaluation'] != '' ) {
				update_post_meta ( $post_id, 'quiz-auto-evaluation', stripslashes ( $_POST ['quiz-auto-evaluation'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-auto-evaluation' );
			}

			if( isset( $_POST ['quiz-markasgraded-in-autoevaluation'] ) && $_POST ['quiz-markasgraded-in-autoevaluation'] != '' ) {
				update_post_meta ( $post_id, 'quiz-markasgraded-in-autoevaluation', stripslashes ( $_POST ['quiz-markasgraded-in-autoevaluation'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-markasgraded-in-autoevaluation' );
			}

			if( isset( $_POST ['dtlms-quiz-question-type'] ) && $_POST ['dtlms-quiz-question-type'] != '' ) {
				update_post_meta ( $post_id, 'quiz-question-type', stripslashes ( $_POST ['dtlms-quiz-question-type'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-question-type' );
			}			

			if( isset( $_POST ['dtlms-quiz-question'] ) && $_POST ['dtlms-quiz-question'] != '' ) {
				update_post_meta ( $post_id, 'quiz-question', array_filter ( $_POST ['dtlms-quiz-question'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-question' );
			}

			if( isset( $_POST ['dtlms-quiz-question-grade'] ) && $_POST ['dtlms-quiz-question-grade'] != '' ) {
				update_post_meta ( $post_id, 'quiz-question-grade', array_filter ( $_POST ['dtlms-quiz-question-grade'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-question-grade' );
			}

			if( isset( $_POST ['dtlms-quiz-question-negative-grade'] ) && $_POST ['dtlms-quiz-question-negative-grade'] != '' ) {
				update_post_meta ( $post_id, 'quiz-question-negative-grade', array_filter ( $_POST ['dtlms-quiz-question-negative-grade'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-question-negative-grade' );
			}			


			if( isset( $_POST ['dtlms-quiz-categories'] ) && $_POST ['dtlms-quiz-categories'] != '' ) {
				update_post_meta ( $post_id, 'quiz-categories', array_filter ( $_POST ['dtlms-quiz-categories'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-categories' );
			}

			if( isset( $_POST ['dtlms-quiz-categories-questions'] ) && $_POST ['dtlms-quiz-categories-questions'] != '' ) {
				update_post_meta ( $post_id, 'quiz-categories-questions', array_filter ( $_POST ['dtlms-quiz-categories-questions'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-categories-questions' );
			}

			if( isset( $_POST ['dtlms-quiz-categories-grade'] ) && $_POST ['dtlms-quiz-categories-grade'] != '' ) {
				update_post_meta ( $post_id, 'quiz-categories-grade', array_filter ( $_POST ['dtlms-quiz-categories-grade'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-categories-grade' );
			}

			if( isset( $_POST ['dtlms-quiz-categories-negative-grade'] ) && $_POST ['dtlms-quiz-categories-negative-grade'] != '' ) {
				update_post_meta ( $post_id, 'quiz-categories-negative-grade', array_filter ( $_POST ['dtlms-quiz-categories-negative-grade'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-categories-negative-grade' );
			}


			if( isset( $_POST ['dtlms-quiz-question-type'] ) && $_POST ['dtlms-quiz-question-type'] == 'add-categories' ) {
				$categories_grade = $_POST ['dtlms-quiz-categories-grade'];
				$categories_questions = $_POST ['dtlms-quiz-categories-questions'];
				$i = 0; $total_grade = 0;
				foreach($categories_grade as $category_grade) {
					$total_grade = $total_grade + ($category_grade * $categories_questions[$i]);
					$i++;
				}
				if($total_grade != '' && $total_grade > 0) {
					update_post_meta ( $post_id, 'quiz-total-grade', $total_grade );
				} else {
					delete_post_meta ( $post_id, 'quiz-total-grade' );
				}
			} else {
				$total_grade = array_sum($_POST ['dtlms-quiz-question-grade']);
				if($total_grade != '' && $total_grade > 0) {
					update_post_meta ( $post_id, 'quiz-total-grade', $total_grade );
				} else {
					delete_post_meta ( $post_id, 'quiz-total-grade' );
				}				
			}
			

			if( isset( $_POST ['quiz-pass-percentage'] ) && $_POST ['quiz-pass-percentage'] != '' ) {
				update_post_meta ( $post_id, 'quiz-pass-percentage', stripslashes ( $_POST ['quiz-pass-percentage'] ) );
			} else { 
				delete_post_meta ( $post_id, 'quiz-pass-percentage' );
			}
			
			if( isset( $_POST ['quiz-questions-onebyone'] ) && $_POST ['quiz-questions-onebyone'] != '' ) {
				update_post_meta ( $post_id, 'quiz-questions-onebyone', stripslashes ( $_POST ['quiz-questions-onebyone'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-questions-onebyone' );
			}	
					
			if( isset( $_POST ['quiz-correctanswer-and-answerexplanation'] ) && $_POST ['quiz-correctanswer-and-answerexplanation'] != '' ) {
				update_post_meta ( $post_id, 'quiz-correctanswer-and-answerexplanation', stripslashes ( $_POST ['quiz-correctanswer-and-answerexplanation'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-correctanswer-and-answerexplanation' );
			}	
			
			if( isset( $_POST ['quiz-questions-counter'] ) && $_POST ['quiz-questions-counter'] != '' ) {
				update_post_meta ( $post_id, 'quiz-questions-counter', stripslashes ( $_POST ['quiz-questions-counter'] ) );
			} else {
				delete_post_meta ( $post_id, 'quiz-questions-counter' );
			}									
				
		endif;

	}

	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_quiz_default_metabox'  ) );
	}

	function dtlms_add_quiz_default_metabox() {
		add_meta_box ( 'dtlms-quiz-default-metabox', esc_html__('Quiz Options', 'dtlms'), array ( $this, 'dtlms_quiz_default_metabox' ), 'dtlms_quizzes', 'normal', 'default' );
	}

	function dtlms_quiz_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/quiz-default-metabox.php';
	}

	function dtlms_template_include($template) {

		if (is_singular( 'dtlms_quizzes' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_quizzes.php';
		}

		return $template;

	}

}

?>