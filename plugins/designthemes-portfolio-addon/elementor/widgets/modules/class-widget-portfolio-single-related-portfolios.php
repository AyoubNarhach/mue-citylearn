<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Related_Portfolios extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-relatedportfolios';
	}

	public function get_title() {
		return __( 'Portfolio Single - Related Portfolios', 'dtportfolio' );
	}

	protected function _register_controls() {
		$this->content_tab();
	}

	# CONTENT TAB
	protected function content_tab() {

		# General
			$this->start_controls_section( 'dt_section_general', array(
				'label' => __( 'General', 'dtportfolio'),
			) );

				# Portfolio ID
					$this->add_control( 'related-portfolio-id', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Portfolio ID', 'dtportfolio'),
						'default' => '',
						'description' => __('Enter portfolio id to display the related portfolio items.', 'dtportfolio'),
					) );

				# Posts Per Page
					$this->add_control( 'portfolio-post-per-page', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Posts Per Page', 'dtportfolio'),
						'default' => '',
						'description' => __('Number of posts to show.', 'dtportfolio'),
					) );

				# Post Style
					$this->add_control( 'portfolio_post_style', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Post Style', 'dtportfolio'),
						'default' => 'default',
						'options' => array(
							'default'                 => esc_html__('Default', 'dtportfolio'), 
							'dtportfolio-striped'     => esc_html__('Striped', 'dtportfolio'), 
							'dtportfolio-fixed'       => esc_html__('Fixed', 'dtportfolio'), 
							'dtportfolio-framed'      => esc_html__('Framed', 'dtportfolio')
						)
					) );

				# Show Details Below Image
					$this->add_control( 'portfolio-details-below-image', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Show Details Below Image', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''                    => esc_html__('None', 'dtportfolio'), 
							'details-below-image' => esc_html__('Details Below Image', 'dtportfolio') 
						),
					) );	

				# Post Layout
					$this->add_control( 'portfolio-post-layout', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Post Layout', 'dtportfolio'),
						'default' => 'dtportfolio-one-fourth-column',
						'options' => array(
							'dtportfolio-one-column' => esc_html__('I Column', 'dtportfolio'),
							'dtportfolio-one-half-column' => esc_html__('II Columns', 'dtportfolio'),
							'dtportfolio-one-third-column' => esc_html__('III Columns', 'dtportfolio'),
							'dtportfolio-one-fourth-column' => esc_html__('IV Columns', 'dtportfolio'),
							'dtportfolio-one-fifth-column' => esc_html__('V Columns', 'dtportfolio'),
							'dtportfolio-one-sixth-column' => esc_html__('VI Columns', 'dtportfolio'),
							'dtportfolio-one-seventh-column' => esc_html__('VII Columns', 'dtportfolio'),
							'dtportfolio-one-eight-column' => esc_html__('VIII Columns', 'dtportfolio'),
							'dtportfolio-one-nineth-column' => esc_html__('IX Columns', 'dtportfolio'),
							'dtportfolio-one-tenth-column' => esc_html__('X Columns', 'dtportfolio')
						),
						'condition' => [
							'portfolio_post_style' => array('default', 'dtportfolio-parallax', 'dtportfolio-striped', 'dtportfolio-fixed', 'dtportfolio-framed'),
						]							
					) );

				# Grid Space
					$this->add_control( 'portfolio-grid-space', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Grid Space', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						),
						'condition' => [
							'portfolio_post_style' => array('default', 'dtportfolio-parallax', 'dtportfolio-framed'),
						]							
					) );

				# Hover Style
					$this->add_control( 'portfolio-hover-style', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Hover Style', 'dtportfolio'),
						'default' => 'modern-title',
						'options' => array(
							'modern-title' => esc_html__('Modern Title', 'dtportfolio'), 
							'title-icons-overlay' => esc_html__('Title & Icons Overlay', 'dtportfolio'), 
							'title-overlay' => esc_html__('Title Overlay', 'dtportfolio'),
                            'icons-only' => esc_html__('Icons Only', 'dtportfolio'), 
                            'classic' => esc_html__('Classic', 'dtportfolio'), 
                            'minimal-icons' => esc_html__('Minimal Icons', 'dtportfolio'),
                            'presentation' => esc_html__('Presentation', 'dtportfolio'), 
                            'girly' => esc_html__('Girly', 'dtportfolio'), 
                            'art' => esc_html__('Art', 'dtportfolio'), 
                            'extended' => esc_html__('Extended', 'dtportfolio'), 
                            'boxed' => esc_html__('Boxed', 'dtportfolio'), 
                            'centered-box' => esc_html__('Centered Box', 'dtportfolio'),
                            'with-gallery-thumb' => esc_html__('With Gallery Thumb', 'dtportfolio'), 
                            'with-gallery-list' => esc_html__('With Gallery List', 'dtportfolio'), 
                            'grayscale' => esc_html__('Grayscale', 'dtportfolio'), 
                            'highlighter' => esc_html__('Highlighter', 'dtportfolio'), 
                            'with-details' => esc_html__('With Details', 'dtportfolio'), 
                            'bottom-border' => esc_html__('Bottom Border', 'dtportfolio'),
                            'with-intro' => esc_html__('With Intro', 'dtportfolio')
						)							
					) );

				# Cursor Hover Style
					$this->add_control( 'portfolio-cursor-hover-style', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Cursor Hover Style', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''                    => esc_html__('Default', 'dtportfolio'),
							'cursor-hover-style1' => esc_html__('Style 1', 'dtportfolio'),
							'cursor-hover-style2' => esc_html__('Style 2', 'dtportfolio'),
							'cursor-hover-style3' => esc_html__('Style 3', 'dtportfolio'),
							'cursor-hover-style4' => esc_html__('Style 4', 'dtportfolio'),
							'cursor-hover-style5' => esc_html__('Style 5', 'dtportfolio'),
							'cursor-hover-style6' => esc_html__('Style 6', 'dtportfolio')
						)							
					) );

				# Filter
					$this->add_control( 'filter', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Filter', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						),
						'condition' => [
							'portfolio_post_style' => array('default', 'dtportfolio-framed'),
						]							
					) );

				# Filter Design Type
					$this->add_control( 'portfolio-filterdesign-type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Filter Design Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'' => esc_html__('Default', 'dtportfolio'),
							'type1' => esc_html__('Type 1', 'dtportfolio'),
							'type2' => esc_html__('Type 2', 'dtportfolio'),
							'type3' => esc_html__('Type 3', 'dtportfolio')
						),
						'condition' => [
							'portfolio_post_style' => array('default', 'dtportfolio-framed'),
						]							
					) );

				# Categories

					// Generate Categories List

					$categories = get_categories(
						array(
							'hide_empty' =>  0,
							'taxonomy'   =>  'portfolio_entries'
						)
					);

					$categories_array = array ();
					foreach( $categories as $category ){
						$categories_array[$category->term_id] = $category->name;
					}

					$this->add_control( 'portfolio-categories', array(
						'type'    => Controls_Manager::SELECT2,
						'label'   => __('Categories', 'dtportfolio'),
						'default' => '',
						'multiple' => true,
						'options' => $categories_array
					) );

				# Pagination Type
					$this->add_control( 'portfolio_pagination_type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Pagination Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''                    => esc_html__('None', 'dtportfolio'),
							'numbered-pagination' => esc_html__('Numbered Pagination', 'dtportfolio'),
							'load-more'           => esc_html__('Load More', 'dtportfolio'),
							'lazy-load'           => esc_html__('Lazy Load', 'dtportfolio')
						)						
					) );

				# Enable Fullwidth
					$this->add_control( 'enable-fullwidth', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Fullwidth', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)						
					) );

				# Class
					$this->add_control( 'class', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Class', 'dtportfolio'),
						'default' => ''		
					) );

				# Paged
					$this->add_control( 'paged', array(
						'type'    => Controls_Manager::HIDDEN,
						'label'   => __('Paged', 'dtportfolio'),
						'default' => 1,
						'condition' => [
							'portfolio_pagination_type' => array('load-more', 'lazy-load'),
						]						
					) );

				# Ajax Call
					$this->add_control( 'ajax-call', array(
						'type'    => Controls_Manager::HIDDEN,
						'label'   => __('Ajax Call', 'dtportfolio'),
						'default' => 0,
						'condition' => [
							'portfolio_pagination_type' => array('load-more', 'lazy-load'),
						]							
					) );

			$this->end_controls_section();	


		# Miscellaneous

			$this->start_controls_section( 'dt_section_miscellaneous', array(
				'label' => __( 'Miscellaneous', 'dtportfolio'),
			) );

				# Disable Portfolio Item Options
					$this->add_control( 'portfolio-disable-item-options', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Disable Portfolio Item Options', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)						
					) );

				# Animation Effect
					$dtportfolio_animationtypes = array('' => 'none', 'flash' => 'flash', 'shake' => 'shake', 'bounce' => 'bounce', 'tada' => 'tada', 'swing' => 'swing', 'wobble' => 'wobble', 'pulse' => 'pulse', 'flip' => 'flip', 'flipInX' => 'flipInX', 'flipOutX' => 'flipOutX', 'flipInY' => 'flipInY', 'flipOutY' => 'flipOutY', 'fadeIn' => 'fadeIn', 'fadeInUp' => 'fadeInUp', 'fadeInDown' => 'fadeInDown', 'fadeInLeft' => 'fadeInLeft', 'fadeInRight' => 'fadeInRight', 'fadeInUpBig' => 'fadeInUpBig', 'fadeInDownBig' => 'fadeInDownBig', 'fadeInLeftBig' => 'fadeInLeftBig', 'fadeInRightBig' => 'fadeInRightBig', 'fadeOut' => 'fadeOut', 'fadeOutUp' => 'fadeOutUp','fadeOutDown' => 'fadeOutDown', 'fadeOutLeft' => 'fadeOutLeft', 'fadeOutRight' => 'fadeOutRight', 'fadeOutUpBig' => 'fadeOutUpBig', 'fadeOutDownBig' => 'fadeOutDownBig', 'fadeOutLeftBig' => 'fadeOutLeftBig','fadeOutRightBig' => 'fadeOutRightBig', 'bounceIn' => 'bounceIn', 'bounceInUp' => 'bounceInUp', 'bounceInDown' => 'bounceInDown', 'bounceInLeft' => 'bounceInLeft', 'bounceInRight' => 'bounceInRight', 'bounceOut' => 'bounceOut', 'bounceOutUp' => 'bounceOutUp', 'bounceOutDown' => 'bounceOutDown', 'bounceOutLeft' => 'bounceOutLeft', 'bounceOutRight' => 'bounceOutRight', 'rotateIn' => 'rotateIn', 'rotateInUpLeft' => 'rotateInUpLeft', 'rotateInDownLeft' => 'rotateInDownLeft', 'rotateInUpRight' => 'rotateInUpRight', 'rotateInDownRight' => 'rotateInDownRight', 'rotateOut' => 'rotateOut', 'rotateOutUpLeft' => 'rotateOutUpLeft','rotateOutDownLeft' => 'rotateOutDownLeft', 'rotateOutUpRight' => 'rotateOutUpRight', 'rotateOutDownRight' => 'rotateOutDownRight', 'hinge' => 'hinge', 'rollIn' => 'rollIn', 'rollOut' => 'rollOut', 'lightSpeedIn' => 'lightSpeedIn', 'lightSpeedOut' => 'lightSpeedOut', 'slideDown' => 'slideDown', 'slideUp' => 'slideUp', 'slideLeft' => 'slideLeft', 'slideRight' => 'slideRight', 'slideExpandUp' => 'slideExpandUp', 'expandUp' => 'expandUp', 'expandOpen' => 'expandOpen', 'bigEntrance' => 'bigEntrance', 'hatch' => 'hatch', 'floating' => 'floating', 'tossing' => 'tossing', 'pullUp' => 'pullUp', 'pullDown' => 'pullDown', 'stretchLeft' => 'stretchLeft', 'stretchRight' => 'stretchRight', 'zoomIn' => 'zoomIn');


					$this->add_control( 'animationeffect', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Animation Effect', 'dtportfolio'),
						'default' => '',
						'options' => $dtportfolio_animationtypes			
					) );

				# Animation Delay
					$this->add_control( 'animationdelay', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Animation Delay', 'dtportfolio'),
						'default' => '',
						'min'     => 1,
						'max'     => 50000,
						'step'    => 1,
					) );

				# Repeat Animation
					$this->add_control( 'repeat-animation', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Repeat Animation', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)						
					) );

				# Hover Background Color
					$this->add_control( 'hover-background-color', array(
						'type'    => Controls_Manager::COLOR,
						'label'   => __('Hover Background Color', 'dtportfolio'),
						'default' => '',
					) );

				# Hover Content Color
					$this->add_control( 'hover-content-color', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Hover Content Color', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'' => esc_html__('Default', 'dtportfolio'), 
							'hover-content-color-dark'  => esc_html__('Hover Content Color Dark', 'dtportfolio'),
							'hover-content-color-light'  => esc_html__('Hover Content Color Light', 'dtportfolio') 
						)						
					) );

				# Hover Gradient Color
					$this->add_control( 'hover-gradient-color', array(
						'type'    => Controls_Manager::COLOR,
						'label'   => __('Hover Gradient Color', 'dtportfolio'),
						'default' => '',
					) );

				# Hover Gradient Direction
					$this->add_control( 'hover-gradient-direction', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Hover Gradient Direction', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'lefttoright' => esc_html__('Left to Right', 'dtportfolio'),
							'toptobottom' => esc_html__('Top to Bottom', 'dtportfolio'),
							'diagonal' => esc_html__('Diagonal', 'dtportfolio')
						)						
					) );

				# Hover State
					$this->add_control( 'hover-state', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Hover State', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''     => esc_html__('False', 'dtportfolio'),
							'true' => esc_html__('True', 'dtportfolio')
						)						
					) );

			$this->end_controls_section();


		# Carousel

			$this->start_controls_section( 'dt_section_carousel', array(
				'label' => __( 'Carousel', 'dtportfolio'),
			) );

				# Display Style
					$this->add_control( 'portfolio-displaystyle', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Display Style', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''     => esc_html__('List', 'dtportfolio'),
							'carousel' => esc_html__('Carousel', 'dtportfolio')
						),
						'description' => __('Select display style for your portfolio.', 'dtportfolio'),
					) );

				# Effect
					$this->add_control( 'portfolio-carousel-effect', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Effect', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'cube'      => esc_html__('Cube', 'dtportfolio'),
							'fade'      => esc_html__('Fade', 'dtportfolio'),
							'flip'      => esc_html__('Flip', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose effect for your carousel. Slides Per View has to be 1 for Cube and Fade effect.', 'dtportfolio' )
					) );

				# Auto Play
					$this->add_control( 'portfolio-carousel-autoplay', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Auto Play', 'dtportfolio'),
						'default' => '',
						'min'     => 1,
						'max'     => 50000,
						'step'    => 1,
					) );

				# Slides Per View
					$this->add_control( 'portfolio-carousel-slidesperview', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Slides Per View', 'dtportfolio'),
						'default' => 1,
						'options' => array(
							1 => 1, 
							2 => 2, 
							3 => 3, 
							4 => 4, 
							5 => 5, 
							6 => 6, 
							7 => 7, 
							8 => 8, 
							9 => 9, 
							10 => 10
						),
						'description' => esc_html__( 'Number slides of to show in view port.', 'dtportfolio' )
					) );

				# Enable Loop Mode
					$this->add_control( 'portfolio-carousel-loopmode', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Loop Mode', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'If you wish you can enable continous loop mode for your carousel. Thumbnail pagination wont work along with "Loop Mode".', 'dtportfolio' )
					) );

				# Enable Mousewheel Control
					$this->add_control( 'portfolio-carousel-mousewheelcontrol', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Mousewheel Control', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'If you wish you can enable mouse wheel control for your carousel.', 'dtportfolio' )
					) );

				# Enable Centered Mode
					$this->add_control( 'portfolio-carousel-centermode', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Centered Mode', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'If you wish you can enable centered mode for your carousel.', 'dtportfolio' )
					) );

				# Enable Vertical Direction
					$this->add_control( 'portfolio-carousel-verticaldirection', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Vertical Direction', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To make your slides to navigate vertically.', 'dtportfolio' ),
					) );

				# Pagination Type
					$this->add_control( 'portfolio-carousel-paginationtype', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Pagination Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''            => esc_html__('None', 'dtportfolio'),
							'bullets'     => esc_html__('Bullets', 'dtportfolio'),
							'fraction'    => esc_html__('Fraction', 'dtportfolio'),
							'progressbar' => esc_html__('Progress Bar', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose pagination type you like to use.', 'dtportfolio' ),
					) );

				# Enable Thumbnail Pagination
					$this->add_control( 'portfolio-carousel-thumbnailpagination', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Thumbnail Pagination', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable thumbnail pagination.', 'dtportfolio' ),
					) );

				# Enable Arrow Pagination
					$this->add_control( 'portfolio-carousel-arrowpagination', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Arrow Pagination', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable arrow pagination.', 'dtportfolio' ),
					) );

				# Arrow Type
					$this->add_control( 'portfolio-carousel-arrowpagination_type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Arrow Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'type2'      => esc_html__('Type 2', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose arrow pagination type for your carousel.', 'dtportfolio' )
					) );

				# Enable Scrollbar
					$this->add_control( 'portfolio-carousel-scrollbar', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Scrollbar', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false'          => esc_html__('False', 'dtportfolio'),
							'true'      => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable scrollbar for your carousel.', 'dtportfolio' ),
					) );

				# Enable Arrow For Mouse Pointer
					$this->add_control( 'portfolio-carousel-arrowformousepointer', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Arrow For Mouse Pointer', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable arrow for mouse pointer for your carousel.', 'dtportfolio' ),
					) );

				# Pagination Color Scheme
					$this->add_control( 'portfolio-carousel-paginationcolorscheme', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Pagination Color Scheme', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Light', 'dtportfolio'),
							'dark'      => esc_html__('Dark', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose pagination color scheme for your carousel.', 'dtportfolio' ),
					) );

				# Enable Play/Pause Button
					$this->add_control( 'portfolio-carousel-playpausebutton', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Enable Play/Pause Button', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'),
							'true'  => esc_html__('True', 'dtportfolio')
						),
						'description' => esc_html__( 'To enable play pause button for your carousel.', 'dtportfolio' ),
					) );

				# Space Between Sliders
					$this->add_control( 'portfolio-carousel-spacebetween', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Space Between Sliders', 'dtportfolio'),
						'description' => esc_html__( 'Space between sliders can be given here.', 'dtportfolio' ),
						'default' => '',
						'min'     => 1,
						'max'     => 100,
						'step'    => 1,
					) );

				# Overall Pagination Design Types
					$this->add_control( 'portfolio-carousel-pagination_designtype', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Overall Pagination Design Types', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''          => esc_html__('Default', 'dtportfolio'),
							'type2'      => esc_html__('Type 2', 'dtportfolio'),
							'type3'      => esc_html__('Type 3', 'dtportfolio')
						),
						'description' => esc_html__( 'Choose overall pagination design type for your carousel.', 'dtportfolio' )
					) );

			$this->end_controls_section();


	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$output = '&nbsp;';

		$settings = (is_array($settings) && !empty($settings)) ?array_filter($settings) : array();

		$tpl_default_settings = array_filter($settings);
		$tpl_default_settings['from'] = 'related-portfolio';

		// Change key name with hypen
		$tpl_default_settings['portfolio-post-style'] = $tpl_default_settings['portfolio_post_style'];
		$tpl_default_settings['portfolio-pagination-type'] = isset($tpl_default_settings['portfolio_pagination_type']) ? $tpl_default_settings['portfolio_pagination_type'] : '';
		unset($tpl_default_settings['portfolio_post_style']);
		unset($tpl_default_settings['portfolio_pagination_type']);

		global $post;
		$portfolio_shortcode_pageid = $post->ID;

		$show_sidebar = dtportfolio_shortcode_page_details($portfolio_shortcode_pageid);

		wp_enqueue_style ( 'dtportfolioaddon-css-custom', plugins_url('designthemes-portfolio-addon') . '/css/custom.css', array (), false, 'all' );

		$output .= dtportfolio_portfolio_lists($portfolio_shortcode_pageid, $tpl_default_settings, $show_sidebar);


		echo $output;

	}

	protected function _content_template() {



	}

}