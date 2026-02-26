<?php

class DTLMSClassesPostType {

	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );		
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );
		add_filter ( 'template_include', array ( $this, 'dtlms_template_include'  ) );
		
	}

	function dtlms_init() {

		$this->createPostType();
		add_action ( 'save_post', array ( $this, 'dtlms_save_post_meta' ) );
		add_action ( 'transition_post_status', array ( $this, 'dtlms_first_time_post_publish'),10,3);

	}

	function createPostType() {
		
		if(dtlms_option('permalink','class-slug') != '') { $class_slug = trim(dtlms_option('permalink','class-slug')); }
		else { $class_slug = 'classes'; }

		$class_title_singular = apply_filters( 'class_label', 'singular' );
		$class_title_plural = apply_filters( 'class_label', 'plural' );

		$labels = array (
				'name' => sprintf( esc_html__('%1$s', 'dtlms'), $class_title_plural ),
				'all_items' => sprintf( esc_html__('All %1$s', 'dtlms'), $class_title_plural ),
				'singular_name' => sprintf( esc_html__('%1$s', 'dtlms'), $class_title_singular ),
				'add_new' => esc_html__( 'Add New', 'dtlms' ),
				'add_new_item' => sprintf( esc_html__('Add New %1$s', 'dtlms'), $class_title_singular ),
				'edit_item' => sprintf( esc_html__('Edit %1$s', 'dtlms'), $class_title_singular ),
				'new_item' => sprintf( esc_html__('New %1$s', 'dtlms'), $class_title_singular ),
				'view_item' => sprintf( esc_html__('View %1$s', 'dtlms'), $class_title_singular ),
				'search_items' => sprintf( esc_html__('Search %1$s', 'dtlms'), $class_title_plural ),
				'not_found' => sprintf( esc_html__('No %1$s found', 'dtlms'), $class_title_plural ),
				'not_found_in_trash' => sprintf( esc_html__('No %1$s found in Trash', 'dtlms'), $class_title_plural ),
				'parent_item_colon' => sprintf( esc_html__('Parent %1$s:', 'dtlms'), $class_title_singular ),
				'menu_name' => sprintf( esc_html__('%1$s', 'dtlms'), $class_title_plural ),
		);
		
		$args = array (
				'labels' => $labels,
				'hierarchical' => false,
				'description' => sprintf( esc_html__('This is custom post type %1$s', 'dtlms'), strtolower($class_title_plural) ),
				'supports' => array (
						'title',
						'editor',
						'excerpt',
						'author',
						'comments',
						'page-attributes',
						'thumbnail'
				),
				
				'public' => true,
				'show_ui' => true,
				'show_in_menu' => 'dtlms',
				
				'show_in_nav_menus' => true,
				'publicly_queryable' => true,
				'exclude_from_search' => false,
				'has_archive' => true,
				'query_var' => true,
				'can_export' => true,
				'rewrite' => array( 'slug' => $class_slug, 'hierarchical' => true, 'with_front' => false ),
				'capability_type' => 'post' 
		);
		
		register_post_type ( 'dtlms_classes', $args );
		
	}

	function dtlms_save_post_meta($post_id) {
		
		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_classes_meta_nonce',$_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_classes_meta_nonce'], 'dtlms_classes_nonce' ) ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_classes' == $_POST['post_type']) ) :
			
			if(isset($_POST['page-layout']) && $_POST['page-layout'] != '') {
				update_post_meta($post_id, 'page-layout', stripslashes($_POST['page-layout']));
			} else {
				delete_post_meta($post_id, 'page-layout' );
			}
			
			update_post_meta ( $post_id, 'dtlms-class-type', stripslashes ( $_POST ['dtlms-class-type'] ) );
					
			if( isset( $_POST ['dtlms-class-featured'] ) && $_POST ['dtlms-class-featured'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-featured', stripslashes ( $_POST ['dtlms-class-featured'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-featured' );
			}
			
			if( isset( $_POST ['dtlms-class-maintabtitle'] ) && $_POST ['dtlms-class-maintabtitle'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-maintabtitle', stripslashes ( $_POST ['dtlms-class-maintabtitle'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-maintabtitle' );
			}
			
			if( isset( $_POST ['dtlms-class-content-options'] ) && $_POST ['dtlms-class-content-options'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-class-content-options', stripslashes ( $_POST ['dtlms-class-content-options'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-content-options' );
			}
			
			if( isset( $_POST ['dtlms-class-content-title'] ) && $_POST ['dtlms-class-content-title'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-class-content-title', stripslashes ( $_POST ['dtlms-class-content-title'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-content-title' );
			}
			
			if( isset( $_POST ['dtlms-class-courses'] ) && $_POST ['dtlms-class-courses'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-class-courses', array_unique ( $_POST ['dtlms-class-courses'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-courses' );
			}

			if( isset( $_POST ['enable-certificate'] ) && $_POST ['enable-certificate'] != '') {
				update_post_meta ( $post_id, 'enable-certificate', stripslashes ( $_POST ['enable-certificate'] ) );
			} else {
				delete_post_meta ( $post_id, 'enable-certificate' );
			}

			if( isset( $_POST ['enable-certificate'] ) && $_POST ['enable-certificate'] == 'true') {
				if( isset( $_POST ['certificate-percentage'] ) && $_POST ['certificate-percentage'] != '') {
					update_post_meta ( $post_id, 'certificate-percentage', stripslashes ( $_POST ['certificate-percentage'] ) );
				} else {
					update_post_meta ( $post_id, 'certificate-percentage', 100 );
				}
			} else {
				delete_post_meta ( $post_id, 'certificate-percentage' );
			}
			
			if( isset( $_POST ['certificate-template'] ) && $_POST ['certificate-template'] != '') {
				update_post_meta ( $post_id, 'certificate-template', stripslashes ( $_POST ['certificate-template'] ) );
			} else {
				delete_post_meta ( $post_id, 'certificate-template' );
			}

			if( isset( $_POST ['enable-badge'] ) && $_POST ['enable-badge'] != '') {
				update_post_meta ( $post_id, 'enable-badge', stripslashes ( $_POST ['enable-badge'] ) );
			} else {
				delete_post_meta ( $post_id, 'enable-badge' );
			}

			if( isset( $_POST ['enable-badge'] ) && $_POST ['enable-badge'] == 'true') {
				if( isset( $_POST ['badge-percentage'] ) && $_POST ['badge-percentage'] != '') {
					update_post_meta ( $post_id, 'badge-percentage', stripslashes ( $_POST ['badge-percentage'] ) );
				} else {
					update_post_meta ( $post_id, 'badge-percentage', 100 );
				}
			} else {
				delete_post_meta ( $post_id, 'badge-percentage' );
			}			
					
			if( isset( $_POST ['badge-image-url'] ) && $_POST ['badge-image-url'] != '') {
				update_post_meta ( $post_id, 'badge-image-url', stripslashes ( $_POST ['badge-image-url'] ) );
			} else {
				delete_post_meta ( $post_id, 'badge-image-url' );
			}

			if( isset( $_POST ['badge-image-id'] ) && $_POST ['badge-image-id'] != '') {
				update_post_meta ( $post_id, 'badge-image-id', stripslashes ( $_POST ['badge-image-id'] ) );
			} else {
				delete_post_meta ( $post_id, 'badge-image-id' );
			}				
			
			if( isset( $_POST ['dtlms-class-shortcode'] ) && $_POST ['dtlms-class-shortcode'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-class-shortcode', stripslashes ( $_POST ['dtlms-class-shortcode'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-shortcode' );
			}


			if( isset( $_POST ['dtlms-class-type'] ) && $_POST ['dtlms-class-type'] == 'onsite'){

				if( isset( $_POST ['dtlms-class-start-date'] ) && $_POST ['dtlms-class-start-date'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-start-date', stripslashes ( $_POST ['dtlms-class-start-date'] ) );
					$classstartdate_compare_format = date('Ymd', strtotime($_POST ['dtlms-class-start-date']));
					update_post_meta ( $post_id, 'class-start-date-compare-format', $classstartdate_compare_format );					
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-start-date' );
					delete_post_meta ( $post_id, 'class-start-date-compare-format' );
				}
				
				if( isset( $_POST ['dtlms-class-capacity'] ) && $_POST ['dtlms-class-capacity'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-capacity', stripslashes ( $_POST ['dtlms-class-capacity'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-capacity' );
				}
				
				if( isset( $_POST ['dtlms-class-disable-purchases-regsitration'] ) && $_POST ['dtlms-class-disable-purchases-regsitration'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-disable-purchases-regsitration', stripslashes ( $_POST ['dtlms-class-disable-purchases-regsitration'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-disable-purchases-regsitration' );
				}
				
				if( isset( $_POST ['dtlms-class-enable-purchases'] ) && $_POST ['dtlms-class-enable-purchases'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-enable-purchases', stripslashes ( $_POST ['dtlms-class-enable-purchases'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-enable-purchases' );
				}
				
				if( isset( $_POST ['dtlms-class-enable-registration'] ) && $_POST ['dtlms-class-enable-registration'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-enable-registration', stripslashes ( $_POST ['dtlms-class-enable-registration'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-enable-registration' );
				}
				
				if( isset( $_POST ['dtlms-class-shyllabus-preview'] ) && $_POST ['dtlms-class-shyllabus-preview'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-shyllabus-preview', stripslashes ( $_POST ['dtlms-class-shyllabus-preview'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-shyllabus-preview' );
				}
				
				if( isset( $_POST ['dtlms-class-address'] ) && $_POST ['dtlms-class-address'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-address', stripslashes ( $_POST ['dtlms-class-address'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-address' );
				}
				
				if( isset( $_POST ['dtlms-class-gps'] ) && $_POST ['dtlms-class-gps'] != ''){
					update_post_meta ( $post_id, 'dtlms-class-gps', array_filter ( $_POST ['dtlms-class-gps'] ) );
				} else {
					delete_post_meta ( $post_id, 'dtlms-class-gps' );
				}

			} else {

				delete_post_meta ( $post_id, 'dtlms-class-start-date' );
				delete_post_meta ( $post_id, 'dtlms-class-capacity' );
				delete_post_meta ( $post_id, 'dtlms-class-disable-purchases-regsitration' );
				delete_post_meta ( $post_id, 'dtlms-class-enable-purchases' );
				delete_post_meta ( $post_id, 'dtlms-class-enable-registration' );
				delete_post_meta ( $post_id, 'dtlms-class-shyllabus-preview' );
				delete_post_meta ( $post_id, 'dtlms-class-address' );
				delete_post_meta ( $post_id, 'dtlms-class-gps' );

			}
			

			if( isset( $_POST ['dtlms-class-accessories-tabtitle'] ) && $_POST ['dtlms-class-accessories-tabtitle'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-accessories-tabtitle', stripslashes ( $_POST ['dtlms-class-accessories-tabtitle'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-accessories-tabtitle' );
			}

			if( isset( $_POST ['dtlms-class-accessories-icon'] ) && $_POST ['dtlms-class-accessories-icon'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-accessories-icon', array_filter ( $_POST ['dtlms-class-accessories-icon'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-accessories-icon' );
			}
			
			if( isset( $_POST ['dtlms-class-accessories-label'] ) && $_POST ['dtlms-class-accessories-label'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-accessories-label', array_filter ( $_POST ['dtlms-class-accessories-label'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-accessories-label' );
			}
			
			if( isset( $_POST ['dtlms-class-accessories-value'] ) && $_POST ['dtlms-class-accessories-value'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-accessories-value', array_filter ( $_POST ['dtlms-class-accessories-value'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-accessories-value' );
			}

			if( isset( $_POST ['dtlms-class-accessories-description'] ) && $_POST ['dtlms-class-accessories-description'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-accessories-description', array_filter ( $_POST ['dtlms-class-accessories-description'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-accessories-description' );
			}
			
			if( isset( $_POST ['dtlms-class-tabs-title'] ) && $_POST ['dtlms-class-tabs-title'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-tabs-title', array_filter ( $_POST ['dtlms-class-tabs-title'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-tabs-title' );
			}
			
			if( isset( $_POST ['dtlms-class-tabs-content'] ) && $_POST ['dtlms-class-tabs-content'] != ''){
				update_post_meta ( $post_id, 'dtlms-class-tabs-content', array_filter ( $_POST ['dtlms-class-tabs-content'] ) );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-tabs-content' );
			}

			if( isset( $_POST ['dtlms-class-event-catid'] ) && $_POST ['dtlms-class-event-catid'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-class-event-catid',  $_POST ['dtlms-class-event-catid'] );
			} else {
				delete_post_meta ( $post_id, 'dtlms-class-event-catid' );
			}
			
			if( isset( $_POST ['enable-sidebar'] ) && $_POST ['enable-sidebar'] != '') {
				update_post_meta ( $post_id, 'enable-sidebar', stripslashes ( $_POST ['enable-sidebar'] ) );
			} else {
				delete_post_meta ( $post_id, 'enable-sidebar' );
			}

			if( isset( $_POST ['sidebar-content'] ) && $_POST ['sidebar-content'] != '') {
				update_post_meta ( $post_id, 'sidebar-content', stripslashes ( $_POST ['sidebar-content'] ) );
			} else {
				delete_post_meta ( $post_id, 'sidebar-content' );
			}
			
		endif;

	}

	function dtlms_first_time_post_publish($new, $old, $post) {
		if ($new == 'publish' && $old != 'publish' && isset($post->post_type) && $post->post_type == 'dtlms_classes') {
			// Notification & Mail
			do_action('dtlms_poc_class_added', $post->ID, $post->post_author);
		}
	}

	function dtlms_admin_init() {
		
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_class_default_metabox' ) );
		if (class_exists('Tribe__Events__Main')) {
			
			add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_events_calendar_metabox' ) );
			
		}

		add_filter ( 'manage_dtlms_classes_posts_columns', array ( $this, 'set_custom_edit_dtlms_classes_columns' ) );
		add_action ( 'manage_dtlms_classes_posts_custom_column', array ( $this, 'custom_dtlms_classes_column' ), 10, 2 );			

	}

	function dtlms_add_class_default_metabox() {
		$class_singular_label = apply_filters( 'class_label', 'singular' );
		add_meta_box ( 'dtlms-class-default-metabox', sprintf( esc_html__( '%1$s Options', 'dtlms' ), $class_singular_label ), array ( $this, 'dtlms_class_default_metabox' ), 'dtlms_classes', 'normal', 'default' );
	}

	function dtlms_add_events_calendar_metabox() {
		$class_singular_label = apply_filters( 'class_label', 'singular' );
		add_meta_box ( 'dtlms-events-calendar-metabox', sprintf( esc_html__( '%1$s Events', 'dtlms' ), $class_singular_label ), array ( $this, 'dtlms_events_calendar_metabox' ), 'dtlms_classes', 'side', 'low' );
	}

	function dtlms_class_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/class-default-metabox.php';
	}

	function dtlms_events_calendar_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/class-events-calendar-metabox.php';
	}
		

	function set_custom_edit_dtlms_classes_columns($columns) {

		$newcolumns = array (
			'cb' => '<input type="checkbox" />',
			'dtlms_class_thumb' => 'Image',
			'title' => 'Title',
			'date' => 'Date'
		);
		
		$columns = array_merge ( $newcolumns, $columns );
		return $columns;
	}

	function custom_dtlms_classes_column($columns, $id) {

		global $post;
		
		switch ($columns) {
			
			case 'dtlms_class_thumb':
			    $image = wp_get_attachment_image(get_post_thumbnail_id($id), array(75,75));
				if(!empty($image)) {
				  	echo $image;
				} else {
					echo '<img src="http'.dtlms_ssl().'://placehold.it/75x75" alt="'.$id.'" />';
				}
			break;
			
		}

	}
		
	function dtlms_template_include($template) {

		if (is_singular( 'dtlms_classes' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_classes.php';
		} elseif ( is_post_type_archive('dtlms_classes') ) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dtlms_classes.php';
		}

		return $template;

	}
	
}

?>