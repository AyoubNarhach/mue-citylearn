<?php 
if( !class_exists('DTPortfolioVisualComposer') ) {

	class DTPortfolioVisualComposer {

		function __construct() {

			add_action( 'admin_enqueue_scripts', array ( $this, 'dtportfolio_admin_scripts') );
			add_action( 'after_setup_theme', array ( $this, 'dtportfolio_map_portfolio_shortcodes' ) , 1000 );
			
			define( 'DTPORTFOLIO_ADDON_TITLE', sprintf( esc_html__('%s - Portfolio', 'dtportfolio'), THEME_NAME ) );

		}

		function dtportfolio_admin_scripts( $hook ) {

			if($hook == "post.php" || $hook == "post-new.php") {
				wp_enqueue_style( 'dtportfolio-vc-admin', plugins_url('designthemes-portfolio-addon') .'/visual-composer/admin.css', array(), false, 'all' );
			}

		}

		function dtportfolio_map_portfolio_shortcodes() {

			global $pagenow;

			$path = plugin_dir_path ( __FILE__ ).'modules/';

			$modules = array(
				'dtportfolio_portfolios'                      => $path.'portfolios.php',
				'dtportfolio_onepage_navigation_title_holder' => $path.'onepage-navigation-title-holder.php',
				'dtportfolio_comment_form'                    => $path.'portfolio-comment-form.php',
				'dtportfolio_comment_list'                    => $path.'portfolio-comment-list.php',
				'dtportfolio_custom_details'                  => $path.'portfolio-customdetails.php',
				'dtportfolio_feature_image'                   => $path.'portfolio-featuredimage.php',
				'dtportfolio_feature_video'                   => $path.'portfolio-featuredvideo.php',
				'dtportfolio_gallery_listing'                 => $path.'portfolio-gallerylisting.php',
				'dtportfolio_navigation_links'                => $path.'portfolio-navigationlinks.php',
				'dtportfolio_related_portfolios'              => $path.'portfolio-related.php',
				'dtportfolio_slider'                          => $path.'portfolio-slider.php',
				'dtportfolio_terms'                           => $path.'portfolio-terms.php',
				'dtportfolio_title'                           => $path.'portfolio-title.php',
				'dtportfolio_widget'                          => $path.'portfolio-widget.php',
			);

			// Apply filters so you can easily modify the modules 100%
			$modules = apply_filters( 'vcex_builder_modules', $modules );

			if( !empty( $modules ) ){
				foreach ( $modules as $key => $val ) {
					require_once( $val );
				}
			}

		}

	}

}
?>