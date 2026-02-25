<?php 
add_action( 'vc_before_init', 'dtportfolio_portfolios_addon_vc_map' );
function dtportfolio_portfolios_addon_vc_map() {

	global $dtportfolio_animationtypes;

	vc_map( array(
		"name" => esc_html__( "Portfolios Listing", 'dtportfolio' ),
		"base" => "dtportfolio_portfolios",
		"icon" => "dtportfolio_portfolios",
		"category" => DTPORTFOLIO_ADDON_TITLE,
		"params" => array(

			// Portfolio ID
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Portfolio ID', 'dtportfolio' ),
				'param_name' => 'portfolioid',
				'description' => esc_html__( 'Enter portfolio ids separated by commas to display portfolio items.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Posts Per Page
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Posts Per Page', 'dtportfolio' ),
				'param_name' => 'portfolio-post-per-page',
				'description' => esc_html__( 'Number of posts to show.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Post Style
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Post Style','dtportfolio'),
				'param_name' => 'portfolio-post-style',
				'value' => array( 
							esc_html__('Default','dtportfolio') => 'default', 
							esc_html__('Full Page','dtportfolio') => 'dtportfolio-fullpage', 
							esc_html__('Parallax','dtportfolio') => 'dtportfolio-parallax', 
							esc_html__('Striped','dtportfolio') => 'dtportfolio-striped', 
							esc_html__('Fixed','dtportfolio') => 'dtportfolio-fixed', 
							esc_html__('Framed','dtportfolio') => 'dtportfolio-framed', 
							esc_html__('Multi Scroll','dtportfolio') => 'dtportfolio-multiscroll', 
						),
				'description' => esc_html__( '"Full Page", "Multi Scroll" will work correctly when compatible header and footer is used.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Details Below Image
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Show Details Below Image','dtportfolio'),
				'param_name' => 'portfolio-details-below-image',
				'value' => array( 
								esc_html__('None','dtportfolio') => '', 
								esc_html__('Details Below Image','dtportfolio') => 'details-below-image', 
                            ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Enable Arrows
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrows','dtportfolio'),
				'param_name' => 'portfolio-multiscroll-enable-arrows',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-multiscroll',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),				

			// Fullpage Navigation Direction
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Fullpage Navigation Direction','dtportfolio'),
				'param_name' => 'portfolio-fullpage-navigation',
				'value' => array( 
							esc_html__('None','dtportfolio') => '', 
							esc_html__('Left','dtportfolio') => 'left', 
							esc_html__('Right','dtportfolio') => 'right', 
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-fullpage',
				),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Fullpage type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Fullpage Type','dtportfolio'),
				'param_name' => 'portfolio-fullpage-type',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '', 
							esc_html__('Splitted Section','dtportfolio') => 'splitted-section', 
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-fullpage',
				),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Disable Mouse Scrolling
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Disable Mouse Scrolling','dtportfolio'),
				'param_name' => 'portfolio-disable-auto-scrolling',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-fullpage',
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Fullpage splitted sections
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Fullpage Splitted Sections','dtportfolio'),
				'param_name' => 'portfolio-fullpage-splittedsections',
				'value' => array( 
							esc_html__('Leftside Image','dtportfolio') => 'leftside-image', 
							esc_html__('Rightside Image','dtportfolio') => 'rightside-image', 
							esc_html__('Alternate Content','dtportfolio') => 'alternate-content', 
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-fullpage',
				),
				'std' => 'leftside-image',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Parallax Effects
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Parallax Effects','dtportfolio'),
				'param_name' => 'portfolio-parallax-effects',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '', 
							esc_html__('Different Parallax Speed','dtportfolio') => 'different-parallax-speed', 
							esc_html__('Random Parallax Effect','dtportfolio') => 'random-parallax-effect', 
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => 'dtportfolio-parallax',
				),
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Post Layout
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Post Layout','dtportfolio'),
				'param_name' => 'portfolio-post-layout',
				'value' => array( 
							esc_html__('I Column','dtportfolio') => 'dtportfolio-one-column' ,
							esc_html__('II Columns','dtportfolio') => 'dtportfolio-one-half-column' ,
							esc_html__('III Columns','dtportfolio') => 'dtportfolio-one-third-column',
							esc_html__('IV Columns','dtportfolio') => 'dtportfolio-one-fourth-column',
							esc_html__('V Columns','dtportfolio') => 'dtportfolio-one-fifth-column',
							esc_html__('VI Columns','dtportfolio') => 'dtportfolio-one-sixth-column',
							esc_html__('VII Columns','dtportfolio') => 'dtportfolio-one-seventh-column',
							esc_html__('VIII Columns','dtportfolio') => 'dtportfolio-one-eight-column',
							esc_html__('IX Columns','dtportfolio') => 'dtportfolio-one-nineth-column',
							esc_html__('X Columns','dtportfolio') => 'dtportfolio-one-tenth-column',
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' =>  array('default', 'dtportfolio-parallax', 'dtportfolio-striped', 'dtportfolio-fixed', 'dtportfolio-framed'),
				),
				'std' => 'dtportfolio-one-fourth-column',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Grid Space
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Grid Space','dtportfolio'),
				'param_name' => 'portfolio-grid-space',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' => array('default', 'dtportfolio-parallax', 'dtportfolio-framed'),
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Hover Style
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hover Style','dtportfolio'),
				'param_name' => 'portfolio-hover-style',
				'value' => array( 
								esc_html__('Modern Title','dtportfolio') => 'modern-title', 
								esc_html__('Title Overlay','dtportfolio') => 'title-icons-overlay', 
								esc_html__('Title Overlay 2','dtportfolio') => 'title-overlay',
	                            esc_html__('Icons Only','dtportfolio') => 'icons-only', 
	                            esc_html__('Classic','dtportfolio') => 'classic', 
	                            esc_html__('Minimal Icons','dtportfolio') => 'minimal-icons',
	                            esc_html__('Presentation','dtportfolio') => 'presentation', 
	                            esc_html__('Girly','dtportfolio') => 'girly', 
	                            esc_html__('Art','dtportfolio') => 'art', 
	                            esc_html__('Extended','dtportfolio') => 'extended', 
	                            esc_html__('Boxed','dtportfolio') => 'boxed', 
	                            esc_html__('Centered Box','dtportfolio') => 'centered-box',
	                            esc_html__('With Gallery Thumb','dtportfolio') => 'with-gallery-thumb', 
	                            esc_html__('With Gallery List','dtportfolio') => 'with-gallery-list', 
	                            esc_html__('Grayscale','dtportfolio') => 'grayscale', 
	                            esc_html__('Highlighter','dtportfolio') => 'highlighter', 
	                            esc_html__('With Details','dtportfolio') => 'with-details', 
	                            esc_html__('Bottom Border','dtportfolio') => 'bottom-border',
	                            esc_html__('With Intro','dtportfolio') => 'with-intro'
                            ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'std' => 'modern-title',
			),

			// Cursor Hover Style
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Cursor Hover Style','dtportfolio'),
				'param_name' => 'portfolio-cursor-hover-style',
				'value' => array( 
								esc_html__('Default','dtportfolio') => '', 
								esc_html__('Style 1','dtportfolio') => 'cursor-hover-style1', 
								esc_html__('Style 2','dtportfolio') => 'cursor-hover-style2',
								esc_html__('Style 3','dtportfolio') => 'cursor-hover-style3',
								esc_html__('Style 4','dtportfolio') => 'cursor-hover-style4',
								esc_html__('Style 5','dtportfolio') => 'cursor-hover-style5',
								esc_html__('Style 6','dtportfolio') => 'cursor-hover-style6', 
                            ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Filter
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Filter','dtportfolio'),
				'param_name' => 'filter',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' =>  array('default', 'dtportfolio-framed'),
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Filter Design Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Filter Design Type','dtportfolio'),
				'param_name' => 'portfolio-filterdesign-type',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '', 
							esc_html__('Type 1','dtportfolio') => 'type1', 
							esc_html__('Type 2','dtportfolio') => 'type2', 
							esc_html__('Type 3','dtportfolio') => 'type3', 
						),
				'dependency' => array(
					'element' => 'portfolio-post-style',
					'value' =>  array('default', 'dtportfolio-framed'),
				),	
				'edit_field_class' => 'vc_column vc_col-sm-6',			
			),

			// Categories
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Categories', 'dtportfolio' ),
				'param_name' => 'portfolio-categories',
				'description' => esc_html__( 'Item categories to display. Separate category id with comma.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Pagination Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Pagination Type','dtportfolio'),
				'param_name' => 'portfolio-pagination-type',
				'value' => array( 
							esc_html__('None','dtportfolio') => '',
							esc_html__('Numbered Pagination','dtportfolio') => 'numbered-pagination',
							esc_html__('Load More','dtportfolio') => 'load-more',
							esc_html__('Lazy Load','dtportfolio') => 'lazy-load',
						),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Enable Fullwidth
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Fullwidth','dtportfolio'),
				'param_name' => 'enable-fullwidth',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
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

			// Paged
			array(
				'type' => 'hidden',
				'heading' => esc_html__( 'Paged', 'dtportfolio' ),
				'param_name' => 'paged',
				'value' => 1,
				'dependency' => array(
					'element' => 'portfolio-pagination-type',
					'value' => array('load-more', 'lazy-load'),
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Ajax Call
			array(
				'type' => 'hidden',
				'heading' => esc_html__( 'Ajax Call', 'dtportfolio' ),
				'param_name' => 'ajax-call',
				'value' => 0,
				'dependency' => array(
					'element' => 'portfolio-pagination-type',
					'value' => array('load-more', 'lazy-load'),
				),
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Disable Portfolio Item Options
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Disable Portfolio Item Options','dtportfolio'),
				'param_name' => 'portfolio-disable-item-options',
				'value' => array( 
							esc_html__('False','dtportfolio') => '',
							esc_html__('True','dtportfolio') => 'true',
						),
				'description' => esc_html__( 'YES will override individual portfolio item options with below options.', 'dtportfolio' ),
				'group' => 'Miscellaneous',
			),	

			// Animation Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__( 'Animation Effect', 'dtportfolio' ),
				'param_name' => 'animationeffect',
				'value' => $dtportfolio_animationtypes,
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),				
				),

			// Animation Delay
			array(
				'type' => 'textfield',
				'heading' => esc_html__( 'Animation Delay', 'dtportfolio' ),
				'param_name' => 'animationdelay',
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),				
				),

			// Repeat Animation
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Repeat Animation','dtportfolio'),
				'param_name' => 'repeat-animation',
				'value' => array( 
							esc_html__('False','dtportfolio') => '',
							esc_html__('True','dtportfolio') => 'true',
						),
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),				
			),
			
			// Hover Background Color
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Hover Background Color', 'dtportfolio' ),
				'param_name' => 'hover-background-color',
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),				
			),

			// Hover Content Color
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hover Content Color','dtportfolio'),
				'param_name' => 'hover-content-color',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '',
							esc_html__('Hover Content Color Dark','dtportfolio') => 'hover-content-color-dark',
							esc_html__('Hover Content Color Light','dtportfolio') => 'hover-content-color-light',
						),
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
								'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),
			),

			// Hover Gradient Color
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Hover Gradient Color', 'dtportfolio' ),
				'param_name' => 'hover-gradient-color',
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
								'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),
			),

			// Hover Gradient Direction
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hover Gradient Direction','dtportfolio'),
				'param_name' => 'hover-gradient-direction',
				'value' => array( 
							esc_html__('Left to Right','dtportfolio') => 'lefttoright',
							esc_html__('Top to Bottom','dtportfolio') => 'toptobottom',
							esc_html__('Diagonal','dtportfolio') => 'diagonal',
						),
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),
			),

			// Hover State
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Hover State','dtportfolio'),
				'param_name' => 'hover-state',
				'value' => array( 
							esc_html__('False','dtportfolio') => '',
							esc_html__('True','dtportfolio') => 'true',
						),
				'group' => 'Miscellaneous',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-disable-item-options',
					'value' => 'true',
				),
			),	


			// Display Style
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Display Style','dtportfolio'),
				'param_name' => 'portfolio-displaystyle',
				'value' => array( 
							esc_html__('List','dtportfolio') => '', 
							esc_html__('Carousel','dtportfolio') => 'carousel', 
						),
				'description' => esc_html__( 'Select display style for your portfolio.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
			),

			// Effect
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Effect','dtportfolio'),
				'param_name' => 'portfolio-carousel-effect',
				'value' => array( 
							esc_html__('Default','dtportfolio') => '', 
							esc_html__('Cube','dtportfolio') => 'cube', 
							esc_html__('Fade','dtportfolio') => 'fade', 
							esc_html__('Flip','dtportfolio') => 'flip', 
							esc_html__('Multi Rows','dtportfolio') => 'multirows', 
						),
				'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),				
			),	

			// Number of Rows
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Number of Rows','dtportfolio'),
				'param_name' => 'portfolio-carousel-numberofrows',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
							4 => 4, 
							5 => 5, 
						),
				'description' => esc_html__( 'Number rows to show, it will work along with multi row effect only.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),	
			),	

			// Auto Play
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Auto Play','dtportfolio'),
				'param_name' => 'portfolio-carousel-autoplay',
				'description' => esc_html__( 'Delay between transitions ( in ms ). Leave empty if you don\'t want to auto play.', 'dtportfolio' ),			
				'group' => 'Carousel',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),	
			),			

			// Slides Per View
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Slides Per View','dtportfolio'),
				'param_name' => 'portfolio-carousel-slidesperview',
				'value' => array( 
							1 => 1, 
							2 => 2, 
							3 => 3, 
							4 => 4, 
							5 => 5, 
							6 => 6, 
							7 => 7, 
							8 => 8, 
							9 => 9, 
							10 => 10, 							
						),
				'description' => esc_html__( 'Number slides of to show in view port.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),	
			),

			// Enable loop mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Loop Mode','dtportfolio'),
				'param_name' => 'portfolio-carousel-loopmode',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel. Thumbnail pagination wont work along with "Loop Mode".', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',	
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),		
			),	

			// Enable mousewheel control
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Mousewheel Control','dtportfolio'),
				'param_name' => 'portfolio-carousel-mousewheelcontrol',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',	
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),	
			),	
			
			// Enable centered mode
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Centered Mode','dtportfolio'),
				'param_name' => 'portfolio-carousel-centermode',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'description' => esc_html__( 'If you wish you can enable centered mode for your carousel.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',	
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),		
			),

			// Enable vertical direction
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Vertical Direction','dtportfolio'),
				'param_name' => 'portfolio-carousel-verticaldirection',
				'value' => array(
					esc_html__('False','dtportfolio') => 'false',
					esc_html__('True','dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To make your slides to navigate vertically.', 'dtportfolio' ),
				'group' => 'Carousel',
				'std' => '',
				'edit_field_class' => 'vc_column vc_col-sm-6',		
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),	
			),

			// Pagination Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Pagination Type', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-paginationtype',
				'value' => array(
					esc_html__('None', 'dtportfolio') => '',
					esc_html__('Bullets', 'dtportfolio') => 'bullets',
					esc_html__('Fraction', 'dtportfolio') => 'fraction',
					esc_html__('Progress Bar', 'dtportfolio') => 'progressbar',
				),
				'description' => esc_html__( 'Choose pagination type you like to use.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => ''	,	
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),			
			),

			// Enable Thumbnail Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Thumbnail Pagination', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-thumbnailpagination',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable thumbnail pagination.  Thumbnail Pagination won\'t work along with Play / Pause button & Carousel Pagination Type.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),				
			),	

			// Enable Arrow Pagination
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow Pagination', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-arrowpagination',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable arrow pagination.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),							
			),	
			
			// Arrow Type
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Arrow Type', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-arrowpagination_type',
				'value' => array( 
							esc_html__('Default', 'dtportfolio') => '', 
							esc_html__('Type 2', 'dtportfolio') => 'type2', 
						),
				'description' => esc_html__( 'Choose arrow pagination type for your carousel. Arrow Type - Type 2 won\'t work along with Play / Pause button & Carousel Pagination Type.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),
			),	


			// Enable Scrollbar
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Scrollbar', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-scrollbar',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable scrollbar for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),
			),							

			// Enable Arrow For Mouse Pointer
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Arrow For Mouse Pointer', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-arrowformousepointer',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable arrow for mouse pointer for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),				
			),

			// Pagination Color Scheme
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Pagination Color Scheme', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-paginationcolorscheme',
				'value' => array( 
							esc_html__('Light','dtportfolio') => '', 
							esc_html__('Dark','dtportfolio') => 'dark', 
						),
				'description' => esc_html__( 'Choose pagination color scheme for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),
			),				

			// Enable Play/Pause Button
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Enable Play/Pause Button', 'dtportfolio'),
				'param_name' => 'portfolio-carousel-playpausebutton',
				'value' => array(
					esc_html__('False', 'dtportfolio') => 'false',
					esc_html__('True', 'dtportfolio') => 'true',
				),
				'description' => esc_html__( 'To enable play pause button for your carousel.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				)								
			),																											

			// Space Between Sliders
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Space Between Sliders','dtportfolio'),
				'param_name' => 'portfolio-carousel-spacebetween',
				'description' => esc_html__( 'Space between sliders can be given here.', 'dtportfolio' ),
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				),
			),	

			// Overall Pagination Design Types
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Overall Pagination Design Types','dtportfolio'),
				'param_name' => 'portfolio-carousel-pagination_designtype',
				'value' => array( 
							esc_html__('Default', 'dtportfolio') => '', 
							esc_html__('Type 2', 'dtportfolio') => 'type2', 
							esc_html__('Type 3', 'dtportfolio') => 'type3', 
						),
				'description' => esc_html__( 'Choose overall pagination design type for your carousel.', 'dtportfolio' ),	
				'edit_field_class' => 'vc_column vc_col-sm-6',
				'group' => 'Carousel',		
				'std' => '',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				)
			),	

			// Content Over Slider
			array(
				'type' => 'textarea_html',
				'heading' => esc_html__( 'Content Over Slider', 'dtportfolio' ),
				'description' => esc_html__( 'If you like to show any content over slider you can add it here.', 'dtportfolio' ),
				'param_name' => 'content',
				'group' => 'Carousel',
				'dependency' => array(
					'element' => 'portfolio-displaystyle',
					'value' => 'carousel',
				)				
			),

		)
	) );
}
?>