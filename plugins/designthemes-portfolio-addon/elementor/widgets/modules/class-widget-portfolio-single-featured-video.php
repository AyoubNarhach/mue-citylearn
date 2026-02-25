<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Featured_Video extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-featuredvideo';
	}

	public function get_title() {
		return __( 'Portfolio Single - Featured Video', 'dtportfolio' );
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

			$portfolio_featured_video = get_post_meta($settings['portfolio_id'], '_portfolio_featured_video', TRUE);
			$portfolio_featured_video = is_array($portfolio_featured_video) ? $portfolio_featured_video  : array();

			$featured_video =  isset($portfolio_featured_video['featured_video']) ? $portfolio_featured_video['featured_video'] : '';

			if($featured_video != '') {
				$output .= '<div class="dtportfolio-featured-video-holder '.esc_attr($settings['class']).'">';
					$output .= '<video id="portfolio-featured-video" poster="'.plugins_url('designthemes-portfolio-addon').'/images/video-image.png" class="play">
									<source src="'.esc_attr($featured_video).'" type="video/mp4">
									<source src="'.esc_attr($featured_video).'" type="video/webm">
									<source src="'.esc_attr($featured_video).'" type="video/ogg">
								</video>';	
				$output .= '</div>';

			}

		}

		echo $output;

	}

	protected function _content_template() {



	}

}