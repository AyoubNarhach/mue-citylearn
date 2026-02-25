<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Single_Custom_Details extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-single-customdetails';
	}

	public function get_title() {
		return __( 'Portfolio Single - Custom Details', 'dtportfolio' );
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

				# Types
					$this->add_control( 'type', array(
						'type'    => Controls_Manager::SELECT,
						'label'   => __('Types', 'dtportfolio'),
						'default' => '',
						'options' => array(
							''                 => esc_html__('Default', 'dtportfolio'), 
							'type2'    => esc_html__('Type 2', 'dtportfolio'), 
							'type3'    => esc_html__('Type 3', 'dtportfolio')
						),
						'description' => __('Select the type of custom details.', 'dtportfolio')
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

			$portfolio_settings = get_post_meta ( $settings['portfolio_id'], '_portfolio_settings', TRUE );
			$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();	

		    if(array_key_exists('portfolio_opt_flds', $portfolio_settings)) {

				$output .= '<ul class="dtportfolio-project-details '.esc_attr($settings['type']).' '.esc_attr($settings['class']).'">';

					for( $i = 1; $i <= sizeof($portfolio_settings['portfolio_opt_flds']) / 2; $i++ ):

						$label = $portfolio_settings['portfolio_opt_flds']["portfolio_opt_flds_title_{$i}"];
        				$value = $portfolio_settings['portfolio_opt_flds']["portfolio_opt_flds_value_{$i}"];

        				if(filter_var($value ,FILTER_VALIDATE_URL)) {
        					$value = '<a href="'.esc_url($value).'">'.esc_html($value).'</a>';
        				} elseif(is_email($value)) {
        					$email = sanitize_email($value);
        					$value = '<a href="mailto:'.antispambot($email,1).'">'.antispambot($value).'</a>';
        				}

	    				if($value != '') {
	            			$output .= '<li><span>'.esc_html($label).' : </span> '.$value.' </li>';
	            		}

        			endfor;

		        $output .= '</ul>';

		    }		

		}


		echo $output;

	}

	protected function _content_template() {



	}

}