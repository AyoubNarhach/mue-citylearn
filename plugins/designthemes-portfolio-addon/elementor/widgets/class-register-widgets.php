<?php

class DTPortfolioElementorWidgets {

	/**
	 * A Reference to an instance of this class
	 */
	private static $_instance = null;

	/**
	 * Instance
	 * 
	 * Ensures only one instance of the class is loaded or can be loaded.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets' ) );

	}

	/**
	 * Register designthemes widgets
	 */
	public function register_widgets( $widgets_manager ) {

		require dt_portfolio_elementor()->plugin_path( 'widgets/class-common-widget-base.php' );

		# Portfolio - Listing
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-listing.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Listing() );

		# Portfolio - Listing Widget
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-listing-widget.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Listing_Widget() );			

		# Portfolio Single - Onepage Navigation Title Holder
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-onepage-navigation-title-holder.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_One_Page_Navigation() );

		# Portfolio Single - Comment Form
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-comment-form.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Comment_Form() );

		# Portfolio Single - Comment List
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-comment-list.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Comment_List() );

		# Portfolio Single - Custom Details
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-custom-details.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Custom_Details() );	

		# Portfolio Single - Featured Image
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-featured-image.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Featured_Image() );	

		# Portfolio Single - Featured Video
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-featured-video.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Featured_Video() );	

		# Portfolio Single - Gallery Listings
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-gallery-listings.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Gallery_Listings() );	

		# Portfolio Single - Navigation Links
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-navigation-links.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Navigation_Links() );

		# Portfolio Single - Related Portfolios
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-related-portfolios.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Related_Portfolios() );

		# Portfolio Single - Sliders
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-sliders.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Sliders() );

		# Portfolio Single - Terms
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-terms.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Terms() );

		# Portfolio Single - Title
			require dt_portfolio_elementor()->plugin_path( 'widgets/modules/class-widget-portfolio-single-title.php' );
			$widgets_manager->register_widget_type( new \Elementor_Portfolio_Single_Title() );

	}

}

DTPortfolioElementorWidgets::instance();?>