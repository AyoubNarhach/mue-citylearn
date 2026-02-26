<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Navigation_Links extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-navigationlinks';
	}

	public function get_title() {
		return __( 'Portfolio Single - Navigation Links', 'dtportfolio' );
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

				# Type
					$this->add_control( 'type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''                 => esc_html__('Default', 'dtportfolio'), 
							'type2'    => esc_html__('Type 2', 'dtportfolio'), 
							'type3'    => esc_html__('Type 3', 'dtportfolio'),
							'type4'    => esc_html__('Type 4', 'dtportfolio')
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

		if(is_singular('dt_portfolios')) {

			$output .= '<div class="dtportfolio-navigation-links-holder '.esc_attr($settings['class']).'">';

				if($settings['type'] == 'type4') {

					$output .= '<div class="post-nav-container '.esc_attr($settings['type']).'">';
						$output .= '<div class="post-prev-link">';
							$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
						$output .= '</div>';		
						$output .= '<div class="post-next-link">';
							$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				} else if($settings['type'] == 'type3') {

					$prev_post = get_previous_post();
					$next_post = get_next_post();

					$output .= '<div class="post-nav-container '.esc_attr($settings['type']).'">';
						$output .= '<div class="post-prev-link">';
							if(isset($prev_post) && !empty($prev_post)) {
								$output .= '<i class="fas fa-chevron-left"> </i>';
								$output .= get_previous_post_link('%link', esc_html__('Prev Entry','dtportfolio') );
								$output .= '<a href="'.get_permalink($prev_post->ID).'">'.$prev_post->post_title.'</a>';
							}
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';	
						$output .= '<div class="post-next-link">';
							if(isset($next_post) && !empty($next_post)) {
								$output .= '<i class="fas fa-chevron-right"> </i>';
								$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio'));
								$output .= '<a href="'.get_permalink($next_post->ID).'">'.$next_post->post_title.'</a>';
							}
						$output .= '</div>';
					$output .= '</div>';

				} else if($settings['type'] == 'type2') {

					$output .= '<div class="post-nav-container '.esc_attr($settings['type']).'">';
						$output .= '<div class="post-prev-link">';
								$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>' );
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';
						$output .= '<div class="post-next-link">';
								$output .= get_next_post_link('%link', '<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				} else {

					$output .= '<div class="post-nav-container type1">';
						$output .= '<div class="post-prev-link">';
								$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';			
						$output .= '<div class="post-next-link">';
								$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				}

			$output .= '</div>';

		}

		echo $output;

	}

	protected function _content_template() {



	}

}