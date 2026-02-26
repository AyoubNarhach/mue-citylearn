<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_One_Page_Navigation extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-onepagenavigation';
	}

	public function get_title() {
		return __( 'Portfolio Single - Onepage Navigation Title Holder', 'dtportfolio' );
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

				# Navigation IDs
					$this->add_control( 'navigation_ids', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Navigation IDs', 'dtportfolio'),
						'default' => '',
						'description' => __('Enter navigation ids separated by commas.', 'dtportfolio'),
					) );

				# Navigation Titles
					$this->add_control( 'navigation_titles', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Navigation Titles', 'dtportfolio'),
						'default' => '',
						'description' => __('Enter navigation titles separated by commas.', 'dtportfolio'),
					) );

				# Type
					$this->add_control( 'type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Type', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''        => esc_html__('Default', 'dtportfolio'), 
							'boxed'   => esc_html__('Boxed', 'dtportfolio'), 
							'rounded' => esc_html__('Rounded', 'dtportfolio')
						),
					) );

				# Position
					$this->add_control( 'position', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Position', 'dtportfolio'),
						'default' => 'left',
						'options' => array(
							'left'        => esc_html__('Left', 'dtportfolio'), 
							'right'   => esc_html__('Right', 'dtportfolio'), 
							'bottom-left' => esc_html__('Bottom Left', 'dtportfolio'),
							'bottom-right' => esc_html__('Bottom Right', 'dtportfolio')
						),
					) );

			$this->end_controls_section();	

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$output = '';

		$navigation_ids = explode(',', $settings['navigation_ids']);
		$navigation_titles = explode(',', $settings['navigation_titles']);

		$output .= '<ul class="dtportfolio-onepage-navigation-title-holder '.$settings['type'].' '.$settings['position'].'">';
					if(is_array($navigation_ids) && !empty($navigation_ids)) {
						$i = 1;
						foreach($navigation_ids as $navigation_id) {
							$class = '';
							if($i == 1) {
								$class = 'class="active"';
							}
							$output .= '<li>
											<a href="#'.$navigation_id.'" onclick="return false" '.$class.'>
												<span>'.$i.'</span>
												<span>'.$navigation_titles[$i-1].'</span>
											</a>
										</li>';
							$i++;
						}
					}
		$output .= '</ul>';

		echo $output;
	}

	protected function _content_template() {



	}

}