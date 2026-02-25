<?php add_action( 'vc_before_init', 'dtportfolio_slider_vc_map' );
function dtportfolio_slider_vc_map() {
	vc_map( array(
		"name" => esc_html__( "Single Portfolio - Sliders", 'dtportfolio' ),
		"base" => "dtportfolio_slider",
		"icon" => "dtportfolio_slider",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Portfolio ID
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Portfolio ID', 'dtportfolio' ),
				'param_name' => 'portfolio_id',
				'description' => esc_html__( 'Enter portfolio id here. If you are going to use this shortcode in portfolio single page no need to give portfolio id.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'admin_label' => true
			),

			// Gallery ID(s)
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Gallery ID', 'dtportfolio' ),
				'param_name' => 'gallery_ids',
				'description' => esc_html__( 'Comma separated gallery item ids to display particular gallery items only.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'admin_label' => true
			),	

			// Include Feature Image
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Featured Image', 'dtportfolio'),
				'description' => esc_html__('Choose "True" if you like to include featured image in this gallery.', 'dtportfolio'),
				'param_name' => 'include_featured_image',
				'value' => array(
					esc_html__( 'False', 'dtportfolio' ) => 'false',
					esc_html__( 'True', 'dtportfolio' ) => 'true',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Include Feature Video
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Include Featured Video', 'dtportfolio'),
				'description' => esc_html__('Choose "True" if you like to include featured video in this gallery.', 'dtportfolio'),
				'param_name' => 'include_featured_video',
				'value' => array(
					esc_html__( 'False', 'dtportfolio' ) => 'false',
					esc_html__( 'True', 'dtportfolio' ) => 'true',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Class
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Class', 'dtportfolio' ),
				'param_name' => 'class',
				'description' => esc_html__( 'If you wish you can add additional class name here.', 'dtportfolio' ),	
				'edit_field_class' => 'vc_column vc_col-sm-6',			
			),


			/* Carousel Tab */

			// Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Effect','dtportfolio'),
				'param_name' => 'carousel_effect',
				'value' => array( 
							esc_html__('Default', 'dtportfolio') => '', 
							esc_html__('Cube', 'dtportfolio') => 'cube', 
							esc_html__('Fade', 'dtportfolio') => 'fade', 
							esc_html__('Flip', 'dtportfolio') => 'flip', 
						),
				'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''
			),

			// Auto Play
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Auto Play', 'dtportfolio'),
				'param_name' => 'carousel_autoplay',
				'description' => esc_html__( 'Delay between transitions ( in ms ). Leave empty if you don\'t want to auto play.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
			),	

			// Slides Per View
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Slides Per View', 'dtportfolio'),
				'param_name' => 'carousel_slidesperview',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
							4 => 4, 						
						),
				'description' => esc_html__( 'Number slides of to show in view port.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''
			),

			// Enable loop mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Loop Mode', 'dtportfolio'),
				'param_name' => 'carousel_loopmode',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel. Thumbnail pagination wont work along with "Loop Mode".', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),	

			// Enable mousewheel control
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Mousewheel Control', 'dtportfolio'),
				'param_name' => 'carousel_mousewheelcontrol',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),	
			
			// Enable vertical direction
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Vertical Direction', 'dtportfolio'),
				'param_name' => 'carousel_verticaldirection',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To make your slides to navigate vertically.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),		

			// Pagination Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Pagination Type', 'dtportfolio'),
				'param_name' => 'carousel_paginationtype',
				'value' => array(
					esc_html__('None', 'dtportfolio') => '',
					esc_html__('Bullets', 'dtportfolio') => 'bullets',
					esc_html__('Fraction', 'dtportfolio') => 'fraction',
					esc_html__('Progress Bar', 'dtportfolio') => 'progressbar',
				),
				'description' => esc_html__( 'Choose pagination type you like to use.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),

			// Enable Thumbnail Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Thumbnail Pagination', 'dtportfolio'),
				'param_name' => 'carousel_thumbnailpagination',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable thumbnail pagination.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),			

			// Enable Arrow Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow Pagination', 'dtportfolio'),
				'param_name' => 'carousel_arrowpagination',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable arrow pagination.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),	
			
			// Arrow Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Arrow Type', 'dtportfolio'),
				'param_name' => 'carousel_arrowpagination_type',
				'value' => array( 
							esc_html__('Default', 'dtportfolio') => '', 
							esc_html__('Type 2', 'dtportfolio') => 'type2', 
						),
				'description' => esc_html__( 'Choose arrow pagination type for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''
			),	

			// Enable Scrollbar
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Scrollbar', 'dtportfolio'),
				'param_name' => 'carousel_scrollbar',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable scrollbar for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),								

			// Enable Arrow For Mouse Pointer
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow For Mouse Pointer', 'dtportfolio'),
				'param_name' => 'carousel_arrowformousepointer',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable arrow for mouse pointer, along with click event.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),	

			// Pagination Color Scheme
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Pagination Color Scheme', 'dtportfolio'),
				'param_name' => 'carousel_paginationcolorscheme',
				'value' => array( 
							esc_html__('Light','dtportfolio') => '', 
							esc_html__('Dark','dtportfolio') => 'dark', 
						),
				'description' => esc_html__( 'Choose pagination color scheme for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''
			),	

			// Enable Play/Pause Button
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Play/Pause Button', 'dtportfolio'),
				'param_name' => 'carousel_playpausebutton',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable play pause button for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''				
			),																											

			// Space Between Sliders
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Space Between Sliders','dtportfolio'),
				'param_name' => 'carousel_spacebetween',
				'description' => esc_html__( 'Space between sliders can be given here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
			),	

			// Overall Pagination Design Types
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Overall Pagination Design Types','dtportfolio'),
				'param_name' => 'carousel_pagination_designtype',
				'value' => array( 
							esc_html__('Default', 'dtportfolio') => '', 
							esc_html__('Type 2', 'dtportfolio') => 'type2', 
							esc_html__('Type 3', 'dtportfolio') => 'type3', 
						),
				'description' => esc_html__( 'Choose overall pagination design type for your carousel.', 'dtportfolio' ),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',		
				'std' => ''
			),	

		)
	) );
}?>