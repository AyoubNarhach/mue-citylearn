<?php

class DTLMSQuestionsPostType {
	
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
				'name' => esc_html__('Questions', 'dtlms'),
				'all_items' => esc_html__('All Questions', 'dtlms'),
				'singular_name' => esc_html__('Question', 'dtlms'),
				'add_new' => esc_html__('Add New', 'dtlms'),
				'add_new_item' => esc_html__('Add New Question', 'dtlms'),
				'edit_item' => esc_html__('Edit Question', 'dtlms'),
				'new_item' => esc_html__('New Question', 'dtlms'),
				'view_item' => esc_html__('View Question', 'dtlms'),
				'search_items' => esc_html__('Search Questions', 'dtlms'),
				'not_found' => esc_html__('No Questions found', 'dtlms'),
				'not_found_in_trash' => esc_html__('No Questions found in Trash', 'dtlms'),
				'parent_item_colon' => esc_html__('Parent Question:', 'dtlms'),
				'menu_name' => esc_html__('Questions', 'dtlms' ) 
		);
		
		$args = array (
				'labels' => $labels,
				'hierarchical' => true,
				'description' => 'This is custom post type questions',
				'supports' => array (
						'title',
						'editor',
						'author',
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
		
		register_post_type ( 'dtlms_questions', $args );

		register_taxonomy ( 'question_category', array (
					'dtlms_questions' 
			), array (
					'hierarchical' => true,
					'labels' => array(
						'name' 					=>esc_html__( 'Question Categories','dtlms' ),
						'singular_name' 		=>esc_html__( 'Question Category','dtlms' ),
						'search_items'			=>esc_html__( 'Search Question Categories', 'dtlms' ),
						'popular_items'			=>esc_html__( 'Popular Question Categories', 'dtlms' ),
						'all_items'				=>esc_html__( 'All Question Categories', 'dtlms' ),
						'parent_item'			=>esc_html__( 'Parent Question Category', 'dtlms' ),
						'parent_item_colon'		=>esc_html__( 'Parent Question Category', 'dtlms' ),
						'edit_item'				=>esc_html__( 'Edit Question Category', 'dtlms' ),
						'update_item'			=>esc_html__( 'Update Question Category', 'dtlms' ),
						'add_new_item'			=>esc_html__( 'Add New Question Category', 'dtlms' ),
						'new_item_name'			=>esc_html__( 'New Question Category', 'dtlms' ),
						'add_or_remove_items'	=>esc_html__( 'Add or remove', 'dtlms' ),
						'choose_from_most_used'	=>esc_html__( 'Choose from most used', 'dtlms' ),
						'menu_name'				=>esc_html__( 'Question Categories','dtlms' ),
					),
					'show_admin_column' => true,
					'query_var' => true 
			) 
		);
			
	}

	// Defining a callback function
	function myFilter($var){
	    return ($var !== NULL && $var !== FALSE && $var !== "");
	}

	function dtlms_save_post_meta($post_id) {

		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_questions_meta_nonce', $_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_questions_meta_nonce'], 'dtlms_questions_nonce') ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_questions' == $_POST['post_type']) ) :	
		
			if( isset( $_POST ['dt_question_type'] ) && $_POST ['dt_question_type'] != '' ) update_post_meta ( $post_id, 'question-type', stripslashes ( $_POST ['dt_question_type'] ) );
			else delete_post_meta ( $post_id, 'question-type' );

			if($_POST ['dt_question_type'] == 'multiple-choice') {

				$array = $_POST['dt_multichoice_answers'];
				// Filtering the array
				$_POST['dt_multichoice_answers'] = array_filter($array, array($this, 'myFilter'));

				if( isset( $_POST ['dt_multichoice_answers'] ) && !empty($_POST ['dt_multichoice_answers']) ) update_post_meta ( $post_id, 'multichoice-answers', $_POST ['dt_multichoice_answers'] );
				else delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				
				$dt_multichoice_answers = $_POST ['dt_multichoice_answers'];
				$dt_multichoice_correct_answers = $dt_multichoice_answers[$_POST ['dtlms-multichoice-correct-answer']];
				
				if( $dt_multichoice_correct_answers != '' ) update_post_meta ( $post_id, 'multichoice-correct-answer', $dt_multichoice_correct_answers );
				else delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				delete_post_meta ( $post_id, 'multiline-answer' );
			
			} else if($_POST ['dt_question_type'] == 'multiple-choice-image') {
				
				if( isset( $_POST ['dt_multichoice_image_answers'] ) && !empty($_POST ['dt_multichoice_image_answers']) ) update_post_meta ( $post_id, 'multichoice-image-answers', array_filter($_POST ['dt_multichoice_image_answers']) );
				else delete_post_meta ( $post_id, 'multichoice-image-answers' );
				
				$dt_multichoice_image_answers = $_POST ['dt_multichoice_image_answers'];
				$dt_multichoice_image_correct_answers = $dt_multichoice_image_answers[$_POST ['dtlms-multichoice-image-correct-answer']];
									
				if( isset( $dt_multichoice_image_correct_answers ) && !empty($dt_multichoice_image_correct_answers) ) update_post_meta ( $post_id, 'multichoice-image-correct-answer', $dt_multichoice_image_correct_answers );
				else delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				delete_post_meta ( $post_id, 'multiline-answer' );

			} else if($_POST ['dt_question_type'] == 'multiple-correct') {
				
				$array = $_POST['dt_multicorrect_answers'];
				// Filtering the array
				$_POST['dt_multicorrect_answers'] = array_filter($array, array($this, 'myFilter'));

				if( isset( $_POST ['dt_multicorrect_answers'] ) && !empty($_POST ['dt_multicorrect_answers']) ) update_post_meta ( $post_id, 'multicorrect-answers', $_POST ['dt_multicorrect_answers'] );
				
				$multicorrect_answer = $_POST ['dtlms-multicorrect-correct-answer'];
				$dt_multicorrect_correct_answers = array();
				$dt_multicorrect_answers = $_POST ['dt_multicorrect_answers'];
				
				if(isset($dt_multicorrect_answers) && !empty($dt_multicorrect_answers)) {
					foreach($dt_multicorrect_answers as $key => $answer)
					{
						if(in_array($key, $multicorrect_answer)) {
							$dt_multicorrect_correct_answers[] = $answer;
						}
					}
				}
				
				if( $dt_multicorrect_correct_answers != '' ) update_post_meta ( $post_id, 'multicorrect-correct-answer', $dt_multicorrect_correct_answers);
				else delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				delete_post_meta ( $post_id, 'multiline-answer' );
			
			} else if($_POST ['dt_question_type'] == 'boolean') {
							
				if( isset( $_POST ['dtlms-boolean-answer'] ) && !empty($_POST ['dtlms-boolean-answer']) ) update_post_meta ( $post_id, 'boolean-answer', $_POST ['dtlms-boolean-answer'] );
				else delete_post_meta ( $post_id, 'boolean-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				delete_post_meta ( $post_id, 'multiline-answer' );
				
			} else if($_POST ['dt_question_type'] == 'gap-fill') {
							
				if( $_POST ['dt_text_before_gap'] != '' ) update_post_meta ( $post_id, 'text-before-gap', stripslashes($_POST ['dt_text_before_gap']) );
				else delete_post_meta ( $post_id, 'text-before-gap' );
				
				if( $_POST ['dt_gap'] != '' ) update_post_meta ( $post_id, 'gap', stripslashes($_POST ['dt_gap']) );
				else delete_post_meta ( $post_id, 'gap' );

				if( $_POST ['dt_text_after_gap'] != '' ) update_post_meta ( $post_id, 'text-after-gap', stripslashes($_POST ['dt_text_after_gap']) );
				else delete_post_meta ( $post_id, 'text-after-gap' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				delete_post_meta ( $post_id, 'multiline-answer' );
			
			} else if($_POST ['dt_question_type'] == 'single-line') {
							
				if( $_POST ['dt_singleline_answer'] != '' ) update_post_meta ( $post_id, 'singleline-answer', stripslashes($_POST ['dt_singleline_answer']) );
				else delete_post_meta ( $post_id, 'singleline-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'multiline-answer' );

			} else if($_POST ['dt_question_type'] == 'multi-line') {
							
				if( $_POST ['dt_multiline_answer'] != '' ) update_post_meta ( $post_id, 'multiline-answer', nl2br(stripslashes($_POST ['dt_multiline_answer'])) );
				else delete_post_meta ( $post_id, 'multiline-answer' );
				
				delete_post_meta ( $post_id, 'multichoice-image-answers' );
				delete_post_meta ( $post_id, 'multichoice-image-correct-answer' );
				delete_post_meta ( $post_id, 'multicorrect-answers' );
				delete_post_meta ( $post_id, 'multicorrect-correct-answer' );
				delete_post_meta ( $post_id, 'multichoice-answers' );
				delete_post_meta ( $post_id, 'multichoice-correct-answer' );
				delete_post_meta ( $post_id, 'boolean-answer' );
				delete_post_meta ( $post_id, 'text-before-gap' );
				delete_post_meta ( $post_id, 'gap' );
				delete_post_meta ( $post_id, 'text-after-gap' );
				delete_post_meta ( $post_id, 'singleline-answer' );
				
			}

			if( isset( $_POST ['dt_answer_hint'] ) && !empty($_POST ['dt_answer_hint']) ) update_post_meta ( $post_id, 'answer-hint', stripslashes($_POST ['dt_answer_hint']) );
			else delete_post_meta ( $post_id, 'answer-hint' );			
			
			if( isset( $_POST ['dt_answer_explanation'] ) && !empty($_POST ['dt_answer_explanation']) ) update_post_meta ( $post_id, 'answer-explanation', stripslashes($_POST ['dt_answer_explanation']) );
			else delete_post_meta ( $post_id, 'answer-explanation' );
			
		endif;
					
	}

	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_question_default_metabox' ) );
	}
	
	function dtlms_add_question_default_metabox() {
		add_meta_box ( 'dtlms-question-default-metabox', esc_html__('Question Options', 'dtlms'), array ( $this, 'dtlms_question_default_metabox' ), 'dtlms_questions', 'normal', 'default' );
	}

	function dtlms_question_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/question-default-metabox.php';
	}
	
	function dtlms_template_include($template) {
		
		if (is_singular( 'dtlms_questions' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_questions.php';
		}

		return $template;
		
	}

}

?>