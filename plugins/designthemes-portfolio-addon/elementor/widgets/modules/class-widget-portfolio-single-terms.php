<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Terms extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-terms';
	}

	public function get_title() {
		return __( 'Portfolio Single - Terms', 'dtportfolio' );
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

				# With Icon
					$this->add_control( 'with_icon', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('With Icon', 'dtportfolio'),
						'default' => '',
						'options' => array(
							'false' => esc_html__('False', 'dtportfolio'), 
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

			$output .= '<div class="dtportfolio-terms-holder '.esc_attr($settings['class']).'">';

				if($settings['with_icon']) {
					$output .= get_the_term_list($settings['portfolio_id'], 'portfolio_entries', '<p class="dtportfolio-categories"><i class="fas fa-tags"></i>', ', ', '</p> ');
				} else {
					$output .= get_the_term_list($settings['portfolio_id'], 'portfolio_entries', '<p class="dtportfolio-categories">', ', ', '</p>');
				}

			$output .= '</div>';

		}

		echo $output;

	}

	protected function _content_template() {



	}

}