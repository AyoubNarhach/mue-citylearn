<?php

class DTLMSPackagesPostType {
	
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
					'name' => esc_html__('Packages', 'dtlms'),
					'all_items' => esc_html__('All Packages', 'dtlms'),
					'singular_name' => esc_html__('Package', 'dtlms'),
					'add_new' => esc_html__('Add New', 'dtlms'),
					'add_new_item' => esc_html__('Add New Package', 'dtlms'),
					'edit_item' => esc_html__('Edit Package', 'dtlms'),
					'new_item' => esc_html__('New Package', 'dtlms'),
					'view_item' => esc_html__('View Package', 'dtlms'),
					'search_items' => esc_html__('Search Packages', 'dtlms'),
					'not_found' => esc_html__('No Packages found', 'dtlms'),
					'not_found_in_trash' => esc_html__('No Packages found in Trash', 'dtlms'),
					'parent_item_colon' => esc_html__('Parent Package:', 'dtlms'),
					'menu_name' => esc_html__('Packages', 'dtlms' ) 
				);
		
		$args = array (
					'labels' => $labels,
					'hierarchical' => true,
					'description' => 'This is custom post type packages',
					'supports' => array (
						'title',
						'editor',
						'excerpt',
						'author',
						'page-attributes',
						'thumbnail'
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => 'dtlms',
					'show_in_nav_menus' => false,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'capability_type' => 'post' 
				);
		
		register_post_type ( 'dtlms_packages', $args );
					
	}	

	function dtlms_save_post_meta($post_id) {

		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_packages_meta_nonce', $_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_packages_meta_nonce'], 'dtlms_packages_nonce') ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_packages' == $_POST['post_type']) ) :	
				
			if(isset( $_POST ['subtitle'] ) && !empty($_POST ['subtitle'])) {
				update_post_meta ( $post_id, 'subtitle', stripslashes ( $_POST ['subtitle'] ) );
			} else {
				delete_post_meta ( $post_id, 'subtitle' );
			}

			if(isset( $_POST ['courses-included'] ) && $_POST ['courses-included'] != '' ) {
				update_post_meta ( $post_id, 'courses-included', array_filter ( $_POST ['courses-included'] ) );
			} else {
				delete_post_meta ( $post_id, 'courses-included' );
			}

			if(isset( $_POST ['classes-included'] ) && $_POST ['classes-included'] != '' ) {
				update_post_meta ( $post_id, 'classes-included', array_filter ( $_POST ['classes-included'] ) );
			} else {
				delete_post_meta ( $post_id, 'classes-included' );
			}			

			if(isset( $_POST ['period'] ) && !empty($_POST ['period'])) {
				update_post_meta ( $post_id, 'period', stripslashes ( $_POST ['period'] ) );
			} else {
				delete_post_meta ( $post_id, 'period' );
			}

			if(isset( $_POST ['term'] ) && !empty($_POST ['term'])) {
				update_post_meta ( $post_id, 'term', stripslashes ( $_POST ['term'] ) );
			} else {
				delete_post_meta ( $post_id, 'term' );
			}			
				
		endif;

	}	
	

	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_package_default_metabox' ) );
	}

	function dtlms_add_package_default_metabox() {
		add_meta_box ( 'dtlms-package-default-metabox', esc_html__('Package Options', 'dtlms'), array ( $this, 'dtlms_package_default_metabox' ), 'dtlms_packages', 'normal', 'default' );
	}

	function dtlms_package_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/package-default-metabox.php';
	}


	function dtlms_template_include($template) {

		if (is_singular( 'dtlms_packages' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_packages.php';
		} elseif ( is_post_type_archive('dtlms_packages') ) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dtlms_packages.php';			
		}

		return $template;

	}
	
}

?>