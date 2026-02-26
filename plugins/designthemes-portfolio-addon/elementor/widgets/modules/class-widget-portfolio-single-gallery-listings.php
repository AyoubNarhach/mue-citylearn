<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Gallery_Listings extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-gallerylisting';
	}

	public function get_title() {
		return __( 'Portfolio Single - Gallery Listings', 'dtportfolio' );
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
					$this->add_control( 'portfolio_id', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Portfolio ID', 'dtportfolio'),
						'default' => '',
						'description' => __('Enter portfolio id here. If you are going to use this shortcode in portfolio single page no need to give portfolio id.', 'dtportfolio'),
					) );

				# Gallery ID(s)
					$this->add_control( 'gallery_ids', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Gallery ID(s)', 'dtportfolio'),
						'default' => '',
						'description' => __('Comma separated gallery item ids to display particular gallery items only.', 'dtportfolio'),
					) );

				# Post Layout
					$this->add_control( 'portfolio-post-layout', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Post Layout', 'dtportfolio'),
						'default' => 'dtportfolio-one-half-column',
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
						)							
					) );

				# Grid Space
					$this->add_control( 'portfolio-grid-space', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Grid Space', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
							'true'  => esc_html__('True', 'dtportfolio') 
						)							
					) );

				# Hover Design
					$this->add_control( 'portfolio-hover-design', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Hover Design', 'dtportfolio'),
						'default' => 'false',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
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

				# Class
					$this->add_control( 'class', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Class', 'dtportfolio'),
						'default' => '',
						'description' => __('If you wish you can add additional class name here.', 'dtportfolio'),
					) );

			$this->end_controls_section();	

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$output = '';				

		if($settings['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$settings['portfolio_id'] = $post->ID;
		}

		if($settings['portfolio_id'] != '') {

			$gallery_settings = is_array($settings) ? array_filter($settings) : array();

			$output .= '<div class="dtportfolio-gallery-listing-holder '.esc_attr($settings['class']).'">';
				$output .= dtportfolio_get_portfolio_gallery_listing($gallery_settings);
			$output .= '</div>';

		}

		echo $output;

	}

	protected function _content_template() {



	}

}