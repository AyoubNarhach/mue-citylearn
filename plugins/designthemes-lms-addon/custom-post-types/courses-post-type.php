<?php

class DTLMSCoursesPostType {
			
	function __construct() {

		add_action ( 'init', array ( $this, 'dtlms_init' ) );		
		add_action ( 'admin_init', array ( $this, 'dtlms_admin_init' ) );
		add_filter ( 'template_include', array ( $this, 'dtlms_template_include'  ) );
		
	}
	
	function dtlms_init() {

		$this->createPostType();
		add_action ( 'save_post', array ( $this, 'dtlms_save_post_meta' ) );
		add_action ( 'transition_post_status', array ( $this, 'dtlms_first_time_post_publish'),10,3);

		add_action ( 'course_category_add_form_fields', array ( $this, 'dtlms_add_category_form_fields' ), 10, 2 );
		add_action ( 'created_course_category', array ( $this, 'dtlms_save_category_form_fields' ), 10, 2 );
		add_action ( 'course_category_edit_form_fields', array ( $this, 'dtlms_update_category_form_fields' ), 10, 2 );
		add_action ( 'edited_course_category', array ( $this, 'dtlms_updated_category_form_fields' ), 10, 2 );		

	}
	
	function createPostType() {

		if(dtlms_option('permalink','course-slug') != '') { $course_slug = trim(dtlms_option('permalink','course-slug')); }
		else { $course_slug = 'courses'; }
		
		if(dtlms_option('permalink','course-category-slug') != '') { $course_cat_slug = trim(dtlms_option('permalink','course-category-slug')); }
		else { $course_cat_slug = 'course-category'; }

		$labels = array (
					'name' => esc_html__( 'Courses', 'dtlms' ),
					'all_items' => esc_html__( 'All Courses', 'dtlms' ),
					'singular_name' => esc_html__( 'Course', 'dtlms' ),
					'add_new' => esc_html__( 'Add New', 'dtlms' ),
					'add_new_item' => esc_html__( 'Add New Course', 'dtlms' ),
					'edit_item' => esc_html__( 'Edit Course', 'dtlms' ),
					'new_item' => esc_html__( 'New Course', 'dtlms' ),
					'view_item' => esc_html__( 'View Course', 'dtlms' ),
					'search_items' => esc_html__( 'Search Courses', 'dtlms' ),
					'not_found' => esc_html__( 'No Courses found', 'dtlms' ),
					'not_found_in_trash' => esc_html__( 'No Courses found in Trash', 'dtlms' ),
					'parent_item_colon' => esc_html__( 'Parent Course:', 'dtlms' ),
					'menu_name' => esc_html__( 'Courses', 'dtlms' ) 
				);
		
		$args = array (
					'labels' => $labels,
					'hierarchical' => false,
					'description' => esc_html__( 'This is custom post type courses', 'dtlms' ),
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
					'rewrite' => array( 'slug' =>  $course_slug, 'hierarchical' => true, 'with_front' => false ),
					'capability_type' => 'post'
				);
		
		register_post_type ( 'dtlms_courses', $args );
		
		register_taxonomy ( 'course_category', array (
					'dtlms_courses' 
			), array (
					'hierarchical' => true,
					'labels' => array(
						'name' 					=>esc_html__( 'Course Categories','dtlms' ),
						'singular_name' 		=>esc_html__( 'Course Category','dtlms' ),
						'search_items'			=>esc_html__( 'Search Course Categories', 'dtlms' ),
						'popular_items'			=>esc_html__( 'Popular Course Categories', 'dtlms' ),
						'all_items'				=>esc_html__( 'All Course Categories', 'dtlms' ),
						'parent_item'			=>esc_html__( 'Parent Course Category', 'dtlms' ),
						'parent_item_colon'		=>esc_html__( 'Parent Course Category', 'dtlms' ),
						'edit_item'				=>esc_html__( 'Edit Course Category', 'dtlms' ),
						'update_item'			=>esc_html__( 'Update Course Category', 'dtlms' ),
						'add_new_item'			=>esc_html__( 'Add New Course Category', 'dtlms' ),
						'new_item_name'			=>esc_html__( 'New Course Category', 'dtlms' ),
						'add_or_remove_items'	=>esc_html__( 'Add or remove', 'dtlms' ),
						'choose_from_most_used'	=>esc_html__( 'Choose from most used', 'dtlms' ),
						'menu_name'				=>esc_html__( 'Course Categories','dtlms' ),
					),
					'show_admin_column' => true,
					'rewrite' => array( 'slug' => $course_cat_slug, 'hierarchical' => true, 'with_front' => false ),
					'query_var' => true 
			) 
		);
					
	}

	function dtlms_save_post_meta($post_id) {
		
		if( key_exists ( '_inline_edit', $_POST )) :
			if ( wp_verify_nonce($_POST['_inline_edit'], 'inlineeditnonce')) return;
		endif;
		
		if( key_exists( 'dtlms_courses_meta_nonce',$_POST ) ) :
			if ( ! wp_verify_nonce( $_POST['dtlms_courses_meta_nonce'], 'dtlms_courses_nonce' ) ) return;
		endif;
	 
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

		if (!current_user_can('edit_post', $post_id)) :
			return;
		endif;

		if ( (key_exists('post_type', $_POST)) && ('dtlms_courses' == $_POST['post_type']) ) :

			if(isset($_POST['page-layout']) && $_POST['page-layout'] != '') {
				update_post_meta($post_id, 'page-layout', stripslashes($_POST['page-layout']));
			} else {
				delete_post_meta($post_id, 'page-layout' );
			}
			
			if( isset( $_POST ['course-curriculum'] ) && !empty($_POST ['course-curriculum'])) {
				update_post_meta ( $post_id, 'course-curriculum', array_unique( $_POST ['course-curriculum'] ) );
			} else {
				delete_post_meta ( $post_id, 'course-curriculum' );
			}		
			
			if( isset( $_POST ['coinstructors'] ) && $_POST ['coinstructors'] != '') {
				update_post_meta ( $post_id, 'coinstructors', array_filter ( $_POST ['coinstructors'] ) );
			} else {
				delete_post_meta ( $post_id, 'coinstructors' );
			}

			if( isset( $_POST ['featured-course'] ) && $_POST ['featured-course'] != '') {
				update_post_meta ( $post_id, 'featured-course', stripslashes ( $_POST ['featured-course'] ) );
			} else {
				delete_post_meta ( $post_id, 'featured-course' );
			}
			
			if( isset( $_POST ['socialshare-items'] ) && !empty($_POST ['socialshare-items'])) {
				update_post_meta ( $post_id, 'socialshare-items', array_filter ( $_POST ['socialshare-items'] ) );
			} else {
				delete_post_meta ( $post_id, 'socialshare-items' );
			}

			if( isset( $_POST ['show-related-course'] ) && $_POST ['show-related-course'] != '') {
				update_post_meta ( $post_id, 'show-related-course', stripslashes ( $_POST ['show-related-course'] ) );
			} else {
				delete_post_meta ( $post_id, 'show-related-course' );
			}

			if( isset( $_POST ['reference-url'] ) && $_POST ['reference-url'] != '') {
				update_post_meta ( $post_id, 'reference-url', $_POST ['reference-url'] );
			} else {
				delete_post_meta ( $post_id, 'reference-url' );
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
			
			if( isset( $_POST ['media-attachment-urls'] ) && !empty($_POST ['media-attachment-urls'])) {
				update_post_meta ( $post_id, 'media-attachment-urls', $_POST ['media-attachment-urls'] );
			} else {
				delete_post_meta ( $post_id, 'media-attachment-urls' );
			}

			if( isset( $_POST ['media-attachment-ids'] ) && !empty($_POST ['media-attachment-ids'])) {
				update_post_meta ( $post_id, 'media-attachment-ids', $_POST ['media-attachment-ids'] );
			} else {
				delete_post_meta ( $post_id, 'media-attachment-ids' );
			}	

			if( isset( $_POST ['media-attachment-titles'] ) && !empty($_POST ['media-attachment-titles'])) {
				update_post_meta ( $post_id, 'media-attachment-titles', $_POST ['media-attachment-titles'] );
			} else {
				delete_post_meta ( $post_id, 'media-attachment-titles' );
			}	

			if( isset( $_POST ['media-attachment-icons'] ) && !empty($_POST ['media-attachment-icons'])) {
				update_post_meta ( $post_id, 'media-attachment-icons', $_POST ['media-attachment-icons'] );
			} else {
				delete_post_meta ( $post_id, 'media-attachment-icons' );
			}


			if( isset( $_POST ['course-start-date'] ) && $_POST ['course-start-date'] != '') {
				update_post_meta ( $post_id, 'course-start-date', stripslashes ( $_POST ['course-start-date'] ) );
				$coursestartdate_compare_format = date('Ymd', strtotime($_POST ['course-start-date']));
				update_post_meta ( $post_id, 'course-start-date-compare-format', $coursestartdate_compare_format );
			} else {
				delete_post_meta ( $post_id, 'course-start-date' );
				delete_post_meta ( $post_id, 'course-start-date-compare-format' );
			}

			if( isset( $_POST ['allowpurchases-before-course-startdate'] ) && $_POST ['allowpurchases-before-course-startdate'] != '') {
				update_post_meta ( $post_id, 'allowpurchases-before-course-startdate', stripslashes ( $_POST ['allowpurchases-before-course-startdate'] ) );
			} else {
				delete_post_meta ( $post_id, 'allowpurchases-before-course-startdate' );
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

			if( isset( $_POST ['capacity'] ) && $_POST ['capacity'] != '') {
				update_post_meta ( $post_id, 'capacity', stripslashes ( $_POST ['capacity'] ) );
			} else {
				delete_post_meta ( $post_id, 'capacity' );
			}

			if( isset( $_POST ['disable-purchases-over-capacity'] ) && $_POST ['disable-purchases-over-capacity'] != '') {
				update_post_meta ( $post_id, 'disable-purchases-over-capacity', stripslashes ( $_POST ['disable-purchases-over-capacity'] ) );
			} else {
				delete_post_meta ( $post_id, 'disable-purchases-over-capacity' );
			}


			if( isset( $_POST ['course-prerequisite'] ) && $_POST ['course-prerequisite'] != '') {
				update_post_meta ( $post_id, 'course-prerequisite', stripslashes ( $_POST ['course-prerequisite'] ) );
			} else {
				delete_post_meta ( $post_id, 'course-prerequisite' );
			}
					
			if( isset( $_POST ['allowpurchases-before-course-prerequisite'] ) && $_POST ['allowpurchases-before-course-prerequisite'] != '') {
				update_post_meta ( $post_id, 'allowpurchases-before-course-prerequisite', stripslashes ( $_POST ['allowpurchases-before-course-prerequisite'] ) );
			} else {
				delete_post_meta ( $post_id, 'allowpurchases-before-course-prerequisite' );
			}
				

			if( isset( $_POST ['drip-completionlock-switch'] ) && $_POST ['drip-completionlock-switch'] != '') {

				update_post_meta ( $post_id, 'drip-completionlock-switch',  $_POST ['drip-completionlock-switch'] );

				if( isset( $_POST ['drip-completionlock-switch'] ) && $_POST ['drip-completionlock-switch'] == 'completionlock') {

					if( isset( $_POST ['curriculum-completion-lock'] ) && $_POST ['curriculum-completion-lock'] != '') {
						update_post_meta ( $post_id, 'curriculum-completion-lock', stripslashes ( $_POST ['curriculum-completion-lock'] ) );
					} else {
						delete_post_meta ( $post_id, 'curriculum-completion-lock' );
					}

					if( isset( $_POST ['open-curriculum-on-submission'] ) && $_POST ['open-curriculum-on-submission'] != '') {
						update_post_meta ( $post_id, 'open-curriculum-on-submission', stripslashes ( $_POST ['open-curriculum-on-submission'] ) );
					} else {
						delete_post_meta ( $post_id, 'open-curriculum-on-submission' );
					}

					delete_post_meta ( $post_id, 'drip-feed' );
					delete_post_meta ( $post_id, 'drip-content-type' );
					delete_post_meta ( $post_id, 'drip-duration-type' );
					delete_post_meta ( $post_id, 'drip-duration' );
					delete_post_meta ( $post_id, 'drip-duration-parameter' );

				} else if( isset( $_POST ['drip-completionlock-switch'] ) && $_POST ['drip-completionlock-switch'] == 'dripfeed') {

					if( isset( $_POST ['drip-feed'] ) && $_POST ['drip-feed'] != '') {
						update_post_meta ( $post_id, 'drip-feed', stripslashes ( $_POST ['drip-feed'] ) );
					} else {
						delete_post_meta ( $post_id, 'drip-feed' );
					}
						
					if( isset( $_POST ['drip-content-type'] ) && $_POST ['drip-content-type'] != '') {
						update_post_meta ( $post_id, 'drip-content-type', stripslashes ( $_POST ['drip-content-type'] ) );
					} else {
						delete_post_meta ( $post_id, 'drip-content-type' );
					}

					if( isset( $_POST ['drip-duration-type'] ) && $_POST ['drip-duration-type'] != '') {
						update_post_meta ( $post_id, 'drip-duration-type', stripslashes ( $_POST ['drip-duration-type'] ) );
					} else {
						delete_post_meta ( $post_id, 'drip-duration-type' );
					}			

					if( isset( $_POST ['drip-duration'] ) && $_POST ['drip-duration'] != '') {
						update_post_meta ( $post_id, 'drip-duration', stripslashes ( $_POST ['drip-duration'] ) );
					} else {
						delete_post_meta ( $post_id, 'drip-duration' );
					}

					if( isset( $_POST ['drip-duration-parameter'] ) && $_POST ['drip-duration-parameter'] != '') {
						update_post_meta ( $post_id, 'drip-duration-parameter', stripslashes ( $_POST ['drip-duration-parameter'] ) );
					} else {
						delete_post_meta ( $post_id, 'drip-duration-parameter' );
					}

					delete_post_meta ( $post_id, 'curriculum-completion-lock' );
					delete_post_meta ( $post_id, 'open-curriculum-on-submission' );

				}

			} else {
				delete_post_meta ( $post_id, 'dtlms-course-event-catid' );
			}


			// from side metobox			
			if( isset( $_POST ['dtlms-course-event-catid'] ) && !empty($_POST ['dtlms-course-event-catid']) ) {
				update_post_meta ( $post_id, 'dtlms-course-event-catid',  $_POST ['dtlms-course-event-catid'] );
			} else {
				delete_post_meta ( $post_id, 'dtlms-course-event-catid' );
			}

			if( isset( $_POST ['dtlms-course-group-id'] ) && $_POST ['dtlms-course-group-id'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-course-group-id',  $_POST ['dtlms-course-group-id'] );

				if ( class_exists( 'BuddyPress' ) ) {

					$author_id = get_post_field( 'post_author', $post_id );
					groups_join_group( $_POST ['dtlms-course-group-id'], $author_id );
					$member = new BP_Groups_Member( $author_id, $_POST ['dtlms-course-group-id'] );
					$member->promote( 'admin' );

				}

			} else {
				delete_post_meta ( $post_id, 'dtlms-course-group-id' );
			}	

			if( isset( $_POST ['course-video'] ) && $_POST ['course-video'] != '') {
				update_post_meta ( $post_id, 'course-video', stripslashes ( $_POST ['course-video'] ) );
			} else {
				delete_post_meta ( $post_id, 'course-video' );
			}

			if( isset( $_POST ['course-news'] ) && !empty($_POST ['course-news']) ) {
				update_post_meta ( $post_id, 'course-news',  $_POST ['course-news'] );
			} else {
				delete_post_meta ( $post_id, 'course-news' );
			}

			if( isset( $_POST ['dtlms-course-forum'] ) && $_POST ['dtlms-course-forum'] != '' ) {
				update_post_meta ( $post_id, 'dtlms-course-forum-id',  $_POST ['dtlms-course-forum'] );
			} else {
				delete_post_meta ( $post_id, 'dtlms-course-forum-id' );
			}

			if(!get_post_meta($post_id, 'purchased_users', true)) {
				update_post_meta($post_id, 'purchased_users', array());
			}							

		endif;
		
	}

	function dtlms_first_time_post_publish($new, $old, $post) {

		if ($new == 'publish' && $old != 'publish' && isset($post->post_type) && $post->post_type == 'dtlms_courses') {
			// Notification & Mail
			do_action('dtlms_poc_course_added', $post->ID, $post->post_author);
		}

	}	


	function dtlms_add_category_form_fields ( $taxonomy ) { 

	   	echo '<div class="form-field term-group">
			    <label for="category-image">'.esc_html__('Image', 'dtlms').'</label>
			    <div class="dtlms-upload-media-items-container">
	                <input name="dtlms-category-image-url" type="hidden" class="uploadfieldurl" readonly value=""/>
	                <input name="dtlms-category-image-id" type="hidden" class="uploadfieldid" readonly value=""/>
	                <input type="button" value="'.esc_html__('Add Image', 'dtlms').'" class="dtlms-upload-media-item-button show-preview with-image-holder" />
	                <input type="button" value="'.esc_html__('Remove','dtlms').'" class="dtlms-upload-media-item-reset" />
	                '.dtlms_adminpanel_image_preview('').'
	            </div>
			    <p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
			</div>';

	   	echo '<div class="form-field term-group">
			    <label for="category-iconimage">'.esc_html__('Icon Image', 'dtlms').'</label>
			    <div class="dtlms-upload-media-items-container">
	                <input name="dtlms-category-iconimage-url" type="hidden" class="uploadfieldurl" readonly value=""/>
	                <input name="dtlms-category-iconimage-id" type="hidden" class="uploadfieldid" readonly value=""/>
	                <input type="button" value="'.esc_html__('Add Icon Image', 'dtlms').'" class="dtlms-upload-media-item-button show-preview with-image-holder" />
	                <input type="button" value="'.esc_html__('Remove','dtlms').'" class="dtlms-upload-media-item-reset" />
	                '.dtlms_adminpanel_image_preview('').'
	            </div>
			    <p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
			</div>';
			
	   	echo '<div class="form-field term-group">
			    <label for="category-icon">'.esc_html__('Icon', 'dtlms').'</label>
			    <input type="text" name="dtlms-category-icon" value="">
				<p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
			</div>';

		echo '<div class="form-field term-group">
				<label for="category-icon-color">'.esc_html__( 'Icon Color', 'dtlms' ).'</label>
				<input name="dtlms-category-icon-color" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="" />
				<p>'.esc_html__('Choose icon color here.', 'dtlms').'</p>
			</div>';

		echo '<div class="form-field term-group">
				<label for="background-color">'.esc_html__( 'Background Color', 'dtlms' ).'</label>
				<input name="dtlms-background-color" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="" />
				<p>'.esc_html__('Choose background color here.', 'dtlms').'</p>
			</div>';						

	}

	function dtlms_save_category_form_fields ( $term_id, $tt_id ) {

		if( isset( $_POST['dtlms-category-image-url'] ) ){
			$image_url = $_POST['dtlms-category-image-url'];
			add_term_meta( $term_id, 'dtlms-category-image-url', $image_url, true );
		}

		if( isset( $_POST['dtlms-category-image-id'] ) ){
			$image_id = $_POST['dtlms-category-image-id'];
			add_term_meta( $term_id, 'dtlms-category-image-id', $image_id, true );
		}	

		if( isset( $_POST['dtlms-category-iconimage-url'] ) ){
			$iconimage_url = $_POST['dtlms-category-iconimage-url'];
			add_term_meta( $term_id, 'dtlms-category-iconimage-url', $iconimage_url, true );
		}

		if( isset( $_POST['dtlms-category-iconimage-id'] ) ){
			$iconimage_id = $_POST['dtlms-category-iconimage-id'];
			add_term_meta( $term_id, 'dtlms-category-iconimage-id', $iconimage_id, true );
		}		

		if( isset( $_POST['dtlms-category-icon'] ) ){
			$category_icon = $_POST['dtlms-category-icon'];
			add_term_meta( $term_id, 'dtlms-category-icon', $category_icon, true );
		}

		if( isset( $_POST['dtlms-category-icon-color'] ) ){
			$category_icon_color = $_POST['dtlms-category-icon-color'];
			add_term_meta( $term_id, 'dtlms-category-icon-color', $category_icon_color, true );
		}

		if( isset( $_POST['dtlms-background-color'] ) ){
			$background_color = $_POST['dtlms-background-color'];
			add_term_meta( $term_id, 'dtlms-background-color', $background_color, true );
		}				

	}	

	function dtlms_update_category_form_fields ( $term, $taxonomy ) {

		echo '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="category-image">'.esc_html__('Image', 'dtlms').'</label>
				</th>
				<td>';
					$image_url = get_term_meta( $term->term_id, 'dtlms-category-image-url', true );
					$image_id = get_term_meta( $term->term_id, 'dtlms-category-image-id', true );
				echo '<div class="dtlms-upload-media-items-container">
		                <input name="dtlms-category-image-url" type="hidden" class="uploadfieldurl" readonly value="'.$image_url.'"/>
		                <input name="dtlms-category-image-id" type="hidden" class="uploadfieldid" readonly value="'.$image_id.'"/>
		                <input type="button" value="'.esc_html__( 'Add Image', 'dtlms' ).'" class="dtlms-upload-media-item-button show-preview with-image-holder" />
		                <input type="button" value="'.esc_html__('Remove','dtlms').'" class="dtlms-upload-media-item-reset" />
		                '.dtlms_adminpanel_image_preview($image_url).'
		            </div>
					<p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
				</td>
			</tr>';

		echo '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="category-iconimage">'.esc_html__('Icon Image', 'dtlms').'</label>
				</th>
				<td>';
					$iconimage_url = get_term_meta( $term->term_id, 'dtlms-category-iconimage-url', true );
					$iconimage_id = get_term_meta( $term->term_id, 'dtlms-category-iconimage-id', true );
				echo '<div class="dtlms-upload-media-items-container">
		                <input name="dtlms-category-iconimage-url" type="hidden" class="uploadfieldurl" readonly value="'.$iconimage_url.'"/>
		                <input name="dtlms-category-iconimage-id" type="hidden" class="uploadfieldid" readonly value="'.$iconimage_id.'"/>
		                <input type="button" value="'.esc_html__( 'Add Image', 'dtlms' ).'" class="dtlms-upload-media-item-button show-preview with-image-holder" />
		                <input type="button" value="'.esc_html__('Remove','dtlms').'" class="dtlms-upload-media-item-reset" />
		                '.dtlms_adminpanel_image_preview($iconimage_url).'
		            </div>
					<p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
				</td>
			</tr>';

		echo '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="category-icon">'.esc_html__('Icon', 'dtlms').'</label>
				</th>
				<td>';
					$icon = get_term_meta ( $term->term_id, 'dtlms-category-icon', true );
					echo '<input type="text" name="dtlms-category-icon" value="'.$icon.'">
					<p>'.esc_html__('This option will be used for "Course Categories" shortcode.', 'dtlms').'</p>
				</td>
			</tr>';	

		echo '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="category-icon-color">'.esc_html__('Icon Color', 'dtlms').'</label>
				</th>
				<td>';
					$icon_color = get_term_meta ( $term->term_id, 'dtlms-category-icon-color', true );
					echo '<input name="dtlms-category-icon-color" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$icon_color.'" />
					<p>'.esc_html__('Choose icon color here.', 'dtlms').'</p>
				</td>
			</tr>';	

		echo '<tr class="form-field term-group-wrap">
				<th scope="row">
					<label for="background-color">'.esc_html__('Background Color', 'dtlms').'</label>
				</th>
				<td>';
					$background_color = get_term_meta ( $term->term_id, 'dtlms-background-color', true );
					echo '<input name="dtlms-background-color" class="dtlms-color-field color-picker" data-alpha="true" type="text" value="'.$background_color.'" />
					<p>'.esc_html__('Choose background color here.', 'dtlms').'</p>
				</td>
			</tr>';						

	}			

	function dtlms_updated_category_form_fields ( $term_id, $tt_id ) {

		if( isset( $_POST['dtlms-category-image-url'] ) && '' !== $_POST['dtlms-category-image-url'] ){
			$image_url = $_POST['dtlms-category-image-url'];
			update_term_meta ( $term_id, 'dtlms-category-image-url', $image_url );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-image-url', '' );
		}

		if( isset( $_POST['dtlms-category-image-id'] ) && '' !== $_POST['dtlms-category-image-id'] ){
			$image_id = $_POST['dtlms-category-image-id'];
			update_term_meta ( $term_id, 'dtlms-category-image-id', $image_id );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-image-id', '' );
		}

		if( isset( $_POST['dtlms-category-iconimage-url'] ) && '' !== $_POST['dtlms-category-iconimage-url'] ){
			$iconimage_url = $_POST['dtlms-category-iconimage-url'];
			update_term_meta ( $term_id, 'dtlms-category-iconimage-url', $iconimage_url );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-iconimage-url', '' );
		}

		if( isset( $_POST['dtlms-category-iconimage-id'] ) && '' !== $_POST['dtlms-category-iconimage-id'] ){
			$iconimage_id = $_POST['dtlms-category-iconimage-id'];
			update_term_meta ( $term_id, 'dtlms-category-iconimage-id', $iconimage_id );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-iconimage-id', '' );
		}		

		if( isset( $_POST['dtlms-category-icon'] ) && '' !== $_POST['dtlms-category-icon'] ){
			$icon = $_POST['dtlms-category-icon'];
			update_term_meta ( $term_id, 'dtlms-category-icon', $icon );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-icon', '' );
		}

		if( isset( $_POST['dtlms-category-icon-color'] ) && '' !== $_POST['dtlms-category-icon-color'] ){
			$icon_color = $_POST['dtlms-category-icon-color'];
			update_term_meta ( $term_id, 'dtlms-category-icon-color', $icon_color );
		} else {
			update_term_meta ( $term_id, 'dtlms-category-icon-color', '' );
		}

		if( isset( $_POST['dtlms-background-color'] ) && '' !== $_POST['dtlms-background-color'] ){
			$background_color = $_POST['dtlms-background-color'];
			update_term_meta ( $term_id, 'dtlms-background-color', $background_color );
		} else {
			update_term_meta ( $term_id, 'dtlms-background-color', '' );
		}		

	}	


	function dtlms_admin_init() {
		
		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_course_default_metabox' ) );

		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_course_featured_video_metabox' ) );
		
		if(class_exists('Tribe__Events__Main')) {
			add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_events_calendar_metabox' ) );
		}

		if(class_exists('BuddyPress') && class_exists('BP_Groups_Group')) {
			add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_buddypress_group_metabox'  ) );
		}

		add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_news_metabox'  ) );

		if(class_exists('bbPress')) {
			add_action ( 'add_meta_boxes', array ( $this, 'dtlms_add_bbpress_forum_metabox'  ) );
		}
		
		add_filter ( 'manage_dtlms_courses_posts_columns', array ( $this, 'set_custom_edit_dtlms_courses_columns' ) );
		add_action ( 'manage_dtlms_courses_posts_custom_column', array ( $this, 'custom_dtlms_courses_column' ), 10, 2 );			

	}

	function dtlms_add_course_default_metabox() {
		add_meta_box ( 'dtlms-course-default-metabox', esc_html__( 'Courses Options', 'dtlms' ), array ( $this, 'dtlms_course_default_metabox' ), 'dtlms_courses', 'normal', 'default' );
	}

	function dtlms_add_course_featured_video_metabox() {
		add_meta_box ( 'dtlms-course-featured-video-metabox', esc_html__( 'Featured Video', 'dtlms' ), array ( $this, 'dtlms_course_featured_video_metabox' ), 'dtlms_courses', 'side', 'low' );
	}

	function dtlms_add_events_calendar_metabox() {
		add_meta_box ( 'dtlms-events-calendar-metabox', esc_html__( 'Course Events', 'dtlms' ), array ( $this, 'dtlms_events_calendar_metabox' ), 'dtlms_courses', 'side', 'low' );
	}

	function dtlms_add_buddypress_group_metabox() {
		add_meta_box ( 'dtlms-buddypress-group-metabox', esc_html__( 'Course Group', 'dtlms' ), array ( $this, 'dtlms_buddypress_group_metabox' ), 'dtlms_courses', 'side', 'low' );
	}

	function dtlms_add_news_metabox() {
		add_meta_box ( 'dtlms-news-metabox', esc_html__( 'Course News', 'dtlms' ), array ( $this, 'dtlms_news_metabox' ), 'dtlms_courses', 'side', 'low' );
	}

	function dtlms_add_bbpress_forum_metabox() {
		add_meta_box ( 'dtlms-bbpress-forum-metabox', esc_html__( 'Course Forum', 'dtlms' ), array ( $this, 'dtlms_bbpress_forum_metabox' ), 'dtlms_courses', 'side', 'low' );
	}		

	
	function dtlms_course_default_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-default-metabox.php';
	}
	
	function dtlms_course_featured_video_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-featured-video-metabox.php';
	}

	function dtlms_events_calendar_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-events-calendar-metabox.php';
	}

	function dtlms_buddypress_group_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-buddypress-group-metabox.php';
	}
	
	function dtlms_bbpress_forum_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-bbpress-forum-metabox.php';
	}

	function dtlms_news_metabox() {
		include_once plugin_dir_path ( __FILE__ ) . 'metaboxes/course-news-metabox.php';
	}

	function set_custom_edit_dtlms_courses_columns($columns) {

		$newcolumns = array (
			'cb' => '<input type="checkbox" />',
			'dtlms_course_thumb' => 'Image',
			'title' => 'Title',
			'taxonomy-course_category' => 'Course Category',
			'date' => 'Date'
		);
		
		$columns = array_merge ( $newcolumns, $columns );
		return $columns;

	}

	function custom_dtlms_courses_column($columns, $id) {

		global $post;
		
		switch ($columns) {
			
			case 'dtlms_course_thumb':
			    $image = wp_get_attachment_image(get_post_thumbnail_id($id), array(75,75));
				if( ! empty( $image ) ) {
				  	echo $image;
				} else {
					echo '<img src="http'.dtlms_ssl().'://placehold.it/75x75" alt="'.$id.'" />';
				}
			break;
			
		}
	}
	
	function dtlms_template_include($template) {

		if (is_singular( 'dtlms_courses' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dtlms_courses.php';
		} elseif (is_tax ( 'course_category' )) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/taxonomy-course_category.php';
		} elseif ( is_post_type_archive('dtlms_courses') ) {
			$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dtlms_courses.php';
		}

		return $template;

	}

}

?>