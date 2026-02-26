<?php

class DTLMSCommissionPaymentsPostType {
	

	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );

	}

	function dtlms_init() {
		$this->createPostType();
	}	
	
	function createPostType() {

		$labels = array (
				'name' => esc_html__('Commission Payments', 'dtlms'),
				'all_items' => esc_html__('Commission Payments', 'dtlms'),
				'singular_name' => esc_html__('Commission Payment', 'dtlms'),
				'add_new' => esc_html__('Add New', 'dtlms'),
				'add_new_item' => esc_html__('Add New Commission Payment', 'dtlms'),
				'edit_item' => esc_html__('Edit Commission Payment', 'dtlms'),
				'new_item' => esc_html__('New Commission Payment', 'dtlms'),
				'view_item' => esc_html__('View Commission Payment', 'dtlms'),
				'search_items' => esc_html__('Search Commission Payments', 'dtlms'),
				'not_found' => esc_html__('No Commission Payments found', 'dtlms'),
				'not_found_in_trash' => esc_html__('No Commission Payments found in Trash', 'dtlms'),
				'parent_item_colon' => esc_html__('Parent Commission Payment:', 'dtlms'),
				'menu_name' => esc_html__('Commission Payments', 'dtlms' ) 
		);
		
		$args = array (
				'labels' => $labels,
				'hierarchical' => true,
				'description' => 'This is custom post type payments',
				'supports' => array ('title', 'author'),				
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
		
		register_post_type ( 'dtlms_payments', $args );
			
	}

	function dtlms_admin_init() {
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_payment_default_metabox' ) );
		add_filter ( 'manage_dtlms_payments_posts_columns', array ( $this, 'set_custom_edit_dtlms_payments_columns' ) );
		add_action ( 'manage_dtlms_payments_posts_custom_column', array ( $this, 'custom_dtlms_payments_column' ), 10, 2 );	
	}

	function dtlms_add_payment_default_metabox() {
		add_meta_box ( 'dtlms-payment-default-metabox', esc_html__('Commission Payments', 'dtlms'), array ( $this, 'dtlms_payment_default_metabox' ), 'dtlms_payments', 'normal', 'default' );
	}

	function dtlms_payment_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/commissionpayment-default-metabox.php';
	}

	function set_custom_edit_dtlms_payments_columns($columns) {

		$instructor_label = apply_filters( 'instructor_label', 'singular' );

		$newcolumns = array (
			'cb' => '<input type="checkbox" />',
			'title' => 'Title',
			'instructor' => $instructor_label,
			'date' => 'Date'
		);
		
		$columns = array_merge ( $newcolumns, $columns );

	    return $columns;		

	}

	function custom_dtlms_payments_column($column, $post_id) {

		global $post;

		switch ($column) {
			
			case 'instructor':

			    $instructor_id = get_post_meta($post_id, 'instructor-id', true);
				$instructor_info = get_userdata($instructor_id);
				$instructor_name = $instructor_info->display_name;
				echo $instructor_name;

			break;
			
		}

	}

}

?>