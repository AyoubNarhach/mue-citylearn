<?php
use DTPortfolioElementor\Widgets\DTPortfolioElementorWidgetBase;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Utils;

class Elementor_Portfolio_Listing_Widget extends DTPortfolioElementorWidgetBase {
	public function get_name() {
		return 'dt-portfolio-listingwidget';
	}

	public function get_title() {
		return __( 'Portfolio Listing - Widget', 'dtportfolio' );
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

				# Title
					$this->add_control( 'title', array(
						'type'    => Controls_Manager::TEXT,
						'label'   => __('Title', 'dtportfolio'),
						'default' => '',
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

					$this->add_control( '_post_categories', array(
						'type'    => Controls_Manager::SELECT2,
						'label'   => __('Categories', 'dtportfolio'),
						'default' => '',
						'multiple' => true,
						'options' => $categories_array
					) );

				# Number Of Posts
					$this->add_control( '_post_count', array(
						'type'    => Controls_Manager::NUMBER,
						'label'   => __('Number Of Posts', 'dtportfolio'),
						'default' => 6,
						'min'     => 1,
						'max'     => 30,
						'step'    => 1,						
					) );

			$this->end_controls_section();	

	}

	protected function render() {

		$settings = $this->get_settings_for_display();

		$output = '';

        ob_start();
        echo '<div class="dt-widget-portfolio-wrapper">';

        	global $wp_widget_factory;
        	$args = array();
        	$type = 'DTPortfolio_Widget';

        	if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
        		the_widget( $type,  $settings, $args );
            }

        echo '</div>';
        $output .= ob_get_clean();

		echo $output;

	}

	protected function _content_template() {



	}

}