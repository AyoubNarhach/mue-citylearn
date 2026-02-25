<?php

class DTPortfolioAddonPostType {
		
	function __construct() {

		add_action ( 'init', array ( $this, 'dtportfolio_init' ) );


		add_action ( 'admin_init', array ( $this, 'dtportfolio_admin_init'  ) );		
		
		add_filter ( 'cs_metabox_options', array ( $this, 'dtportfolio_portfolio_cs_metabox_options' ) );

		add_filter ( 'template_include', array ( $this, 'dtportfolio_template_include' ) );

		add_action( 'widgets_init', array( $this, 'dtportfolio_widget_init' ) );
		require_once plugin_dir_path ( __FILE__ ) . '/widgets/widget-recent-portfolios.php';
	}

	/* Init Functions */

	function dtportfolio_init() {

		$this->createPostType();
		add_filter( 'cs_taxonomy_options', array( $this, 'dtportfolio_category_options') );
		add_filter( 'cs_save_taxonomy', array( $this, 'dtportfolio_category_save_options'), 10, 3);
	}

	function dtportfolio_widget_init() {

		register_widget('DTPortfolio_Widget');
	}

	function createPostType() {

		$portfolio_slug = dtportfolio_cs_get_option( 'single-portfolio-slug', 'portfolios' );
		$taxonomy_slug = dtportfolio_cs_get_option( 'portfolio-category-slug', 'portfolio-categories' );


		$labels = array (
				'name' => esc_html__( 'Portfolios', 'dtportfolio' ),
				'all_items' => esc_html__( 'All Portfolios', 'dtportfolio' ),
				'singular_name' => esc_html__( 'Portfolio', 'dtportfolio' ),
				'add_new' => esc_html__( 'Add New', 'dtportfolio' ),
				'add_new_item' => esc_html__( 'Add New Portfolio', 'dtportfolio' ),
				'edit_item' => esc_html__( 'Edit Portfolio', 'dtportfolio' ),
				'new_item' => esc_html__( 'New Portfolio', 'dtportfolio' ),
				'view_item' => esc_html__( 'View Portfolio', 'dtportfolio' ),
				'search_items' => esc_html__( 'Search Portfolios', 'dtportfolio' ),
				'not_found' => esc_html__( 'No Portfolios found', 'dtportfolio' ),
				'not_found_in_trash' => esc_html__( 'No Portfolios found in Trash', 'dtportfolio' ),
				'parent_item_colon' => esc_html__( 'Parent Portfolio:', 'dtportfolio' ),
				'menu_name' => esc_html__( 'Portfolios', 'dtportfolio' ) ,					
		);
		
		$args = array (
					'labels' => $labels,
					'hierarchical' => false,
					'description' => esc_html__( 'This is custom post type portfolios', 'dtportfolio' ),
					'supports' => array (
							'title',
							'editor',
							'comments',
							'thumbnail',
							'excerpt'
					),
					
					'public' => true,
					'show_ui' => true,
					'show_in_menu' => true,
					'menu_position' => 5,
					'menu_icon' => 'dashicons-format-image',
					
					'show_in_nav_menus' => true,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					'has_archive' => true,
					'query_var' => true,
					'can_export' => true,
					'rewrite' => array ( 'slug' => $portfolio_slug ),
					'capability_type' => 'post'
				);

		register_post_type ( 'dt_portfolios', $args );

		register_taxonomy ( 'portfolio_entries', array (
				'dt_portfolios' 
		), array (
				'hierarchical' => true,
				'label' => esc_html__( 'Categories', 'dtportfolio' ),
				'singular_label' => esc_html__( 'Category', 'dtportfolio' ),
				'show_admin_column' => true,
				'rewrite' => array ( 'slug' => $taxonomy_slug ),
				'query_var' => true 
		) );

	}

	function dtportfolio_category_options( $options ) {

		$options[] = array(
			'id'       => 'dtportfolio_category_settings',
			'taxonomy' => 'portfolio_entries',
			'fields'   => array(

				// Featured Image
				array(
					'id'        => 'category-image-id',
					'type'      => 'image',
					'title'     => esc_html__( 'Featured Image', 'dtportfolio'),
					'add_title' => esc_html__( 'Add Image', 'dtportfolio' ),
				),

				// Enable Album Gallery
				array(
					'id'      => 'category-enable-album',
					'type'    => 'select',
					'title'   => esc_html__('Enable Album Gallery', 'dtportfolio'),
					'default' => 'false',
					'options' => array(
						'true'  => esc_html__('Yes', 'dtportfolio'),
						'false' => esc_html__('No', 'dtportfolio'),
					)
				),
			)
		);

		return $options;
	}

	function dtportfolio_category_save_options( $request, $request_key, $term_id ) {

		if( $request_key == 'dtportfolio_category_settings' ) {

			if( isset( $request['category-image-id'] ) && !empty( $request['category-image-id'] ) ) {

				update_term_meta( $term_id, 'category-image-id', $request['category-image-id'] );
			} else {
				delete_term_meta( $term_id, 'category-image-id' );
			}

			if( isset( $request['category-enable-album'] ) ) {

				update_term_meta( $term_id, 'category-enable-album', $request['category-enable-album'] );
			} else {

				delete_term_meta( $term_id, 'category-enable-album' );
			}		
		}

		return $request;
	}

	/* Admin Init Functions */

	function dtportfolio_admin_init() {

		add_filter ( 'manage_edit-dt_portfolios_columns', array ( $this, 'dtportfolio_portfolios_edit_columns'  ) );		
		add_action ( 'manage_posts_custom_column', array ( $this, 'dtportfolio_portfolios_columns_display' ), 10, 2 );

	}

	function dtportfolio_portfolios_edit_columns($columns) {

		$newcolumns = array (
			'cb' => '<input type="checkbox" />',
			'dt_portfolio_thumb' => esc_html__('Image', 'dtportfolio'),
			'title' => esc_html__('Title', 'dtportfolio'),
			'author' => esc_html__('Author', 'dtportfolio')
		);
		$columns = array_merge ( $newcolumns, $columns );
		return $columns;

	}

	function dtportfolio_portfolios_columns_display($columns, $id) {

		global $post;

		switch ($columns) {

			case 'dt_portfolio_thumb' :

			    $image = wp_get_attachment_image(get_post_thumbnail_id($id), array (75,75));
				if(!empty($image)):
				  	echo ($image);
			    else:
					$portfolio_settings = get_post_meta ( $post->ID, '_portfolio_settings', TRUE );
					$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();

					if( array_key_exists('portfolio-gallery', $portfolio_settings)) {
						$items = explode(',', $portfolio_settings['portfolio-gallery']);
						echo wp_get_attachment_image( $items[0], array (75, 75) );
					}
				endif;

			break;
		}

	}

	/* Metabox Options */

	function dtportfolio_portfolio_cs_metabox_options( $options ) {

		$fields = cs_get_option( 'portfolio-custom-fields');
		$bothfields = $fielddef = $x = array ();
		$before = '';

		if(!empty($fields)) :
			$i = 1;
			foreach($fields as $field):

				$x['id'] = 'portfolio_opt_flds_title_'.$i;
				$x['type'] = 'text';
				$x['title'] = 'Title';
				$x['attributes'] = array ( 'style' => 'background-color: #f0eff9;' );
				$bothfields[] = $x;
				unset($x);
		
				$x['id'] = 'portfolio_opt_flds_value_'.$i;
				$x['type'] = 'text';
				$x['title'] = 'Value';
				$bothfields[] = $x;

				$fielddef['portfolio_opt_flds_title_'.$i] = $field['portfolio-custom-fields-text'];

				$i++;

			endforeach;	
		else:
			$before = '<span>'.esc_html__('Go to settings add few custom fields, then return back here.', 'dtportfolio').'</span>';
		endif;

		
		$options[]    = array (
		  'id'        => '_portfolio_settings',
		  'title'     => esc_html__('Custom Portfolio Options', 'dtportfolio'),
		  'post_type' => 'dt_portfolios',
		  'context'   => 'normal',
		  'priority'  => 'default',
		  'sections'  => array (
								
							// General Section	
							array ( 

								'name'  => 'general_section',
								'title' => esc_html__('General Options', 'dtportfolio'),
								'icon'  => 'fa fa-cogs',
								'fields' => array (

												// Portfolio Layout
												array (
													'id'  		 => 'portfolio-layout',
													'type'  	 => 'select',
													'title' 	 => esc_html__('Portfolio Layout', 'dtportfolio'),
													'class'		 => 'chosen',
													'options'    => array (
														'custom-layout'=>'Custom Layout',
														'media-on-top'=>'Media On Top',
														'fixed-gallery'=>'Fixed Gallery',
														'fixed-feature-image'=>'Fixed Feature Image',
														'gallery-list'=>'Gallery List',
													),	
													'desc'  => esc_html__('Except "Custom Layout", rest of the options won\'t work with all themes.', 'dtportfolio'),
												),

												// Masonry size
												array (
													'id'           => 'masonry-size',
													'type'         => 'select',
													'title'        => esc_html__('Masonry Size', 'dtportfolio'),
													'options'      => array (
														''     			   => esc_html__('Default', 'dtportfolio'),
														'dtportfolio-grid-sizer-1'     => esc_html__('Grid Size 1', 'dtportfolio'),
														'dtportfolio-grid-sizer-2'     => esc_html__('Grid Size 2', 'dtportfolio'),
														'dtportfolio-grid-sizer-3'     => esc_html__('Grid Size 3', 'dtportfolio'),
														'dtportfolio-grid-sizer-4'     => esc_html__('Grid Size 4', 'dtportfolio'),
														'dtportfolio-grid-sizer-5'     => esc_html__('Grid Size 5', 'dtportfolio'),
														'dtportfolio-grid-sizer-6'     => esc_html__('Grid Size 6', 'dtportfolio'),
														'dtportfolio-grid-sizer-7'     => esc_html__('Grid Size 7', 'dtportfolio'),
														'dtportfolio-grid-sizer-8'     => esc_html__('Grid Size 8', 'dtportfolio'),
														'dtportfolio-grid-sizer-9'     => esc_html__('Grid Size 9', 'dtportfolio')
													),
													'class'        => 'chosen',
													'default'      => '',
													'desc'         => esc_html__('It works with portfolio infinite shortcode only.', 'dtportfolio')
												),


												// Gallery list options
												array (
													'id'           => 'gallery-position',
													'type'         => 'select',
													'title'        => esc_html__('Gallery Position', 'dtportfolio'),
													'dependency'   => array ( 
														'portfolio-layout', 
														'any', 
														'fixed-gallery,fixed-feature-image,gallery-list' 
													),
													'options' => array (
														'' => 'Left', 
														'right' => 'Right'
													),
													'class'        => 'chosen',
													'default'      => '',
													'info'         => esc_html__('Choose position for your gallery items.', 'dtportfolio')
												),

											)												

							),

							// Gallery Section	
							array (

								'name'  => 'gallery_section',
								'title' => esc_html__('Gallery Options', 'dtportfolio'),
								'icon'  => 'fa fa-picture-o',
								'fields' => array (

												array (
													'id'          => 'portfolio-gallery',
													'type'        => 'gallery',
													'title'       => esc_html__('Gallery Images', 'dtportfolio'),
													'desc'        => esc_html__('Simply add images to gallery items.', 'dtportfolio'),
													'add_title'   => esc_html__('Add Images', 'dtportfolio'),
													'edit_title'  => esc_html__('Edit Images', 'dtportfolio'),
													'clear_title' => esc_html__('Remove Images', 'dtportfolio'),
													'show_id' => true
												),

											),

							),

							// Optional Section	
							array (
								'name'  => 'optional_section',
								'title' => esc_html__('Optional Fields', 'dtportfolio'),
								'icon'  => 'fa fa-plug',
								'fields' => array (

												array (
													'id'        => 'portfolio_opt_flds',
													'type'      => 'fieldset',
													'title'     => esc_html__('Optional Fields', 'dtportfolio'),
													'fields'    => $bothfields,
													'default'   => $fielddef,
													'before' 	=> $before
												),

											),
							),

							// Portfolio Addon Section
							array (
							  'name'  => 'portfolio_addon_section',
							  'title' => esc_html__('DesignTheme Addon Options', 'dtportfolio'),
							  'icon'  => 'fa fa-cogs',
							  'fields' => array (
											
												// Hover style
												array (
													'id'    => 'hover-styles',
													'type'  => 'select',
													'title' => esc_html__('Hover Styles', 'dtportfolio'),
													'desc'  => esc_html__('Choose hover style for your portfolio item.', 'dtportfolio'),
													'options'=> array ( 
															'' => esc_html__('Default','dtportfolio'), 
															'modern-title' => esc_html__('Modern Title','dtportfolio'), 
															'title-icons-overlay' => esc_html__('Title & Icons Overlay','dtportfolio'), 
															'title-overlay' => esc_html__('Title Overlay','dtportfolio'),
															'icons-only' => esc_html__('Icons Only','dtportfolio'), 
															'classic' => esc_html__('Classic','dtportfolio'), 
															'minimal-icons' => esc_html__('Minimal Icons','dtportfolio'),
			                                    			'presentation' => esc_html__('Presentation','dtportfolio'), 
			                                    			'girly' => esc_html__('Girly','dtportfolio'), 
			                                    			'art' => esc_html__('Art','dtportfolio'), 
			                                    			'extended' => esc_html__('Extended','dtportfolio'), 
			                                    			'boxed' => esc_html__('Boxed','dtportfolio'), 
			                                    			'centered-box' => esc_html__('Centered Box','dtportfolio'), 
			                                    			'with-gallery-thumb' => esc_html__('With Gallery Thumb','dtportfolio'), 
			                                    			'with-gallery-list' => esc_html__('With Gallery List','dtportfolio'), 
			                                    			'grayscale' => esc_html__('Grayscale','dtportfolio'), 
			                                    			'highlighter' => esc_html__('Highlighter','dtportfolio'), 
			                                    			'with-details' => esc_html__('With Details','dtportfolio'), 
			                                    			'bottom-border' => esc_html__('Bottom Border','dtportfolio'), 
			                                    			'with-intro' => esc_html__('With Intro','dtportfolio')
		                                    			)										  
												),

												// Different Parallax Speed Values
												array (
													'id'    => 'different-parallax-speed-values',
													'type'  => 'text',
													'title' => esc_html__('Different Parallax Speed Values', 'dtportfolio'),
												),

												// Random Parallax Speed Values
												array (
													'id'    => 'random-parallax-speed-values',
													'type'  => 'text',
													'title' => esc_html__('Random Parallax Speed Values', 'dtportfolio'),
												),	

												// Animation Effect
												array (
													'id'    => 'animationeffect',
													'type'  => 'select',
													'title' => esc_html__('Animation Effect', 'dtportfolio'),
													'options'=> $GLOBALS['dtportfolio_animationtypes']
												),	

												// Animation Delay
												array (
													'id'    => 'animationdelay',
													'type'  => 'number',
													'title' => esc_html__('Animation Delay', 'dtportfolio'),
												),															

												// Hover Background Color
												array (
													'id'    => 'hover-background-color',
													'type'  => 'color_picker',
													'title' => esc_html__('Hover Background Color', 'dtportfolio'),
												),

												// Hover Content Color
												array (
													'id'    => 'hover-content-color',
													'type'  => 'select',
													'title' => esc_html__('Hover Content Color', 'dtportfolio'),
													'options'=> array ( 
															'' => 'Default', 
															'hover-content-color-dark' => 'Hover Content Color Dark', 
															'hover-content-color-light' => 'Hover Content Color Light'
		                                    			)										  
												),													

												// Gradient Second Color
												array (
													'id'    => 'hover-gradient-color',
													'type'  => 'color_picker',
													'title' => esc_html__('Gradient Second Color', 'dtportfolio'),
												),	

												// Gradient Direction
												array (
													'id'    => 'hover-gradient-direction',
													'type'  => 'select',
													'title' => esc_html__('Gradient Direction', 'dtportfolio'),
													'options'=> array (
																	'lefttoright' => 'Left to Right', 
																	'toptobottom' => 'Top to Bottom', 
																	'diagonal' => 'Diagonal'
																)							  
												),	

												// Hover State
												array (
													'id'  		=> 'hover-state',
													'type'  	=> 'switcher',
													'title' 	=> esc_html__('Hover State', 'dtportfolio'),
												),						

											)
							)

		  				),
		);

		$options[] = array (
							'id'        => '_portfolio_featured_video',
							'title'     => esc_html__('Featured Video', 'dtportfolio'),
							'post_type' => 'dt_portfolios',
							'context'   => 'side',
							'priority'  => 'low',
							'sections'  => array (
												array (
													'name'  => 'featured_video_section',
													'fields' => array (
																	array (
																		'id'    => 'featured_video',
																		'type'  => 'text',
																		'title' => esc_html__('Featured Video', 'dtportfolio'),
																		'desc'  => esc_html__('Please enter the video url in following format mp4, webm,... If video URL field is empty featured image will be used. It will be used to display in template page or shorcodes.', 'dtportfolio')
																	),
																)
												)
											)
						);
		

		// Filter to add additional options for themes
		$options = apply_filters( 'dtportfolio_template_metabox_options', $options );

		return $options;
		
	}			


	/* Include Template Files */

	function dtportfolio_template_include($template) {

		if (is_singular( 'dt_portfolios' )) {
			if (! file_exists ( get_template_directory () . '/single-dt_portfolios.php' )) {
				$template = plugin_dir_path ( __FILE__ ) . 'templates/single-dt_portfolios.php';
			}			
		} elseif (is_tax ( 'portfolio_entries' )) {
			if (! file_exists ( get_template_directory () . '/taxonomy-portfolio_entries.php' )) {
				$template = plugin_dir_path ( __FILE__ ) . 'templates/taxonomy-portfolio_entries.php';
			}
		} elseif (is_post_type_archive ( 'dt_portfolios' )) {
			if (! file_exists ( get_template_directory () . '/archive-dt_portfolios.php' )) {
				$template = plugin_dir_path ( __FILE__ ) . 'templates/archive-dt_portfolios.php';
			}					
		}
		return $template;
	}

}

?>