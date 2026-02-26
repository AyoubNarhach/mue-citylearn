<?php

class DTLMSLessonsPostType {
	
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
					'name' => esc_html__('Lessons', 'dtlms'),
					'all_items' => esc_html__('All Lessons', 'dtlms'),
					'singular_name' => esc_html__('Lesson', 'dtlms'),
					'add_new' => esc_html__('Add New', 'dtlms'),
					'add_new_item' => esc_html__('Add New Lesson', 'dtlms'),
					'edit_item' => esc_html__('Edit Lesson', 'dtlms'),
					'new_item' => esc_html__('New Lesson', 'dtlms'),
					'view_item' => esc_html__('View Lesson', 'dtlms'),
					'search_items' => esc_html__('Search Lessons', 'dtlms'),
					'not_found' => esc_html__('No Lessons found', 'dtlms'),
					'not_found_in_trash' => esc_html__('No Lessons found in Trash', 'dtlms'),
					'parent_item_colon' => esc_html__('Parent Lesson:', 'dtlms'),
					'menu_name' => esc_html__('Lessons', 'dtlms' ) 
				);
		
		$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type lessons',
					'supports' => array (
							'title',
							'editor',
							'excerpt',
							'author',
							'page-attributes',
							'thumbnail'
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
		
		register_post_type ( 'dtlms_lessons', $args );
					
	}	

	function dtlms_save_post_meta($post_id) {

		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_lessons_meta_nonce', $_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_lessons_meta_nonce'], 'dtlms_lessons_nonce') ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_lessons' == $_POST['post_type']) ) :	
				
			if( isset( $_POST ['free-lesson'] ) && $_POST ['free-lesson'] != '' ) {
				update_post_meta ( $post_id, 'free-lesson', stripslashes ( $_POST ['free-lesson'] ) );
			} else {
				delete_post_meta ( $post_id, 'free-lesson' );
			}

			if( isset( $_POST ['lesson-curriculum'] ) && !empty($_POST ['lesson-curriculum'])) {
				update_post_meta ( $post_id, 'lesson-curriculum', array_unique ( $_POST ['lesson-curriculum'] ) );
			} else {
				delete_post_meta ( $post_id, 'lesson-curriculum' );
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
						

			if( isset( $_POST ['lesson-maximum-mark'] ) && $_POST ['lesson-maximum-mark'] != '' ) {
				update_post_meta ( $post_id, 'lesson-maximum-mark', stripslashes ( $_POST ['lesson-maximum-mark'] ) );
			} else {
				delete_post_meta ( $post_id, 'lesson-maximum-mark' );
			}

			if( isset( $_POST ['lesson-pass-percentage'] ) && $_POST ['lesson-pass-percentage'] != '' ) {
				update_post_meta ( $post_id, 'lesson-pass-percentage', stripslashes ( $_POST ['lesson-pass-percentage'] ) );
			} else {
				delete_post_meta ( $post_id, 'lesson-pass-percentage' );
			}		

			if( isset( $_POST ['lesson-video'] ) && $_POST ['lesson-video'] != '') {
				update_post_meta ( $post_id, 'lesson-video', stripslashes ( $_POST ['lesson-video'] ) );
			} else {
				delete_post_meta ( $post_id, 'lesson-video' );
			}			
				
		endif;

	}	
	

	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_lesson_default_metabox' ) );
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_lesson_featured_video_metabox' ) );					
	}

	function dtlms_add_lesson_default_metabox() {
		add_meta_box ( 'dtlms-lesson-default-metabox', esc_html__('Lesson Options', 'dtlms'), array ( $this, 'dtlms_lesson_default_metabox' ), 'dtlms_lessons', 'normal', 'default' );
	}

	function dtlms_add_lesson_featured_video_metabox() {
		add_meta_box ( 'dtlms-lesson-featured-video-metabox', esc_html__('Featured Video', 'dtlms'), array ( $this, 'dtlms_lesson_featured_video_metabox' ), 'dtlms_lessons', 'side', 'low' );
	}	
	
	function dtlms_lesson_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/lesson-default-metabox.php';
	}

	function dtlms_lesson_featured_video_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/lesson-featured-video-metabox.php';
	}

	function dtlms_template_include($template) {

		if (is_singular( 'dtlms_lessons' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_lessons.php';	
		}

		return $template;

	}
	
}

?>