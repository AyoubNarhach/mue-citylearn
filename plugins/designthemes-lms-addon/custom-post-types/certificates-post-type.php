<?php

class DTLMSCertificatesPostType {
	
	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );
		add_filter ( 'template_include', array ( $this, 'dtlms_template_include' ) );

	}

	function dtlms_init() {

		$this->createPostType();

	}

	function createPostType() {

		$labels = array (
				'name' => esc_html__('Certificates', 'dtlms'),
				'all_items' => esc_html__('All Certificates', 'dtlms'),
				'singular_name' => esc_html__('Certificate', 'dtlms'),
				'add_new' => esc_html__('Add New', 'dtlms'),
				'add_new_item' => esc_html__('Add New Certificate', 'dtlms'),
				'edit_item' => esc_html__('Edit Certificate', 'dtlms'),
				'new_item' => esc_html__('New Certificate', 'dtlms'),
				'view_item' => esc_html__('View Certificate', 'dtlms'),
				'search_items' => esc_html__('Search Certificates', 'dtlms'),
				'not_found' => esc_html__('No Certificates found', 'dtlms'),
				'not_found_in_trash' => esc_html__('No Certificates found in Trash', 'dtlms'),
				'parent_item_colon' => esc_html__('Parent Certificate:', 'dtlms'),
				'menu_name' => esc_html__('Certificates', 'dtlms' ) 
		);
		
		$args = array (
				'labels' => $labels,
				'hierarchical' => true,
				'description' => 'This is custom post type certificates',
				'supports' => array (
						'title',
						'editor',
						'author',
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
		
		register_post_type ( 'dtlms_certificates', $args );
			
	}
	
	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_certificate_default_metabox' ) );
	}

	function dtlms_add_certificate_default_metabox() {
		add_meta_box ( 'dtlms-certificate-default-metabox', esc_html__('Certificate Options', 'dtlms'), array ( $this, 'dtlms_certificate_default_metabox' ), 'dtlms_certificates', 'normal', 'default' );
	}	

	function dtlms_certificate_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/certificate-default-metabox.php';
	}

	function dtlms_template_include($template) {
		if (is_singular( 'dtlms_certificates' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_certificates.php';
		}
		return $template;
	}

}

?>