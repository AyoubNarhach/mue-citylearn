<?php
/*
 * Plugin Name:	DesignThemes Portfolio Addon 
 * URI: 		http://wedesignthemes.com/plugins/designthemes-portfolio-addon
 * Description: A simple wordpress plugin designed to implements <strong>Portfolio addon features of DesignThemes</strong>
 * Version: 	1.6
 * Author: 		DesignThemes 
 * Text Domain: dtportfolio
 * Author URI:	http://themeforest.net/user/designthemes
 */

if (! class_exists ( 'DTPortfolioAddon' )) {

	class DTPortfolioAddon {

		private $plugin_dir_path;
		private $page_builder;
		private $elementor_preview_mode;

		function __construct() {

			$themeData = wp_get_theme();
			$name      = $themeData->get('Name');
			$template  = $themeData->get('Template');

			if( ($name == 'Elearni') || ($name == 'Elearni Child') ) {
			} else {
				if( $template == 'elearni' ) {
				} else {
					return;
				}
			}			

			if(!defined('THEME_NAME')) {
				define( 'THEME_NAME', 'Elearni');
			}
			
			$this->plugin_dir_path = plugin_dir_path ( __FILE__ );

			add_action ( 'init', array ( $this, 'dtportfolio_init' ) );
			add_action ( 'admin_enqueue_scripts', array ( $this, 'dtportfolio_admin_enqueue_scripts' ) );
			add_action ( 'wp_enqueue_scripts', array ( $this, 'dtportfolio_enqueue_scripts' ) );


			# Code Star Framework Init
			require_once plugin_dir_path ( __FILE__ ) . '/config-codestar.php';
			if(class_exists('DTPortfolioCodeStar')) {
				new DTPortfolioCodeStar();
			}

			// Portfolio Addon Custom Post Types
			require_once plugin_dir_path ( __FILE__ ) . '/custom-post-types/dtportfolio-addon-post-type.php';
			if (class_exists ( 'DTPortfolioAddonPostType' )) {
				new DTPortfolioAddonPostType();
			}

			// Register Shortcodes
			require_once plugin_dir_path ( __FILE__ ) . '/shortcodes/shortcodes.php';
			if(class_exists('DTPortfolioShortcodesDefinition')){
				new DTPortfolioShortcodesDefinition();
			}
			
			// Register Visual Composer

			$this->page_builder = '';

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if (is_plugin_active('js_composer/js_composer.php') || is_plugin_active_for_network('js_composer/js_composer.php')) { // Visual Composer Plugin	

				require_once plugin_dir_path ( __FILE__ ) . '/visual-composer/register-visual-composer.php';
				if(class_exists('DTPortfolioVisualComposer')){
					new DTPortfolioVisualComposer();
				}

				$this->page_builder = 'visual-composer';

			} else if (is_plugin_active('elementor/elementor.php') || is_plugin_active_for_network('elementor/elementor.php')) {  // Elementor Plugin	

				require_once plugin_dir_path ( __FILE__ ) . '/elementor/register-elementor.php';

				$this->page_builder = 'elementor';

			}

			// Functions
			require_once plugin_dir_path ( __FILE__ ) . '/functions-singlepage.php';
			require_once plugin_dir_path ( __FILE__ ) . '/functions-templatepage.php';			
			require_once plugin_dir_path ( __FILE__ ) . '/utils.php';	

			include_once plugin_dir_path ( __FILE__ ) . '/theme-support/class-designthemes.php';
		}		

		function dtportfolio_init() {
			load_plugin_textdomain ( 'dtportfolio', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );
		}

		function dtportfolio_admin_enqueue_scripts() {
			
			wp_enqueue_style ( 'dtportfolio-backend', plugin_dir_url ( __FILE__ ) . 'css/backend.css', array (), false, 'all' );
		}

		function dtportfolio_enqueue_scripts() {

			// enqueueing css files
			wp_enqueue_style ( 'fontawesome-icons', plugin_dir_url ( __FILE__ ) . 'css/fontawesome-all.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-animation', plugin_dir_url ( __FILE__ ) . 'css/animations.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-fullpage', plugin_dir_url ( __FILE__ ) . 'css/jquery.fullPage.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-ilightbox', plugin_dir_url ( __FILE__ ) . 'css/ilightbox.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-multiscroll', plugin_dir_url ( __FILE__ ) . 'css/jquery.multiscroll.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-swiper', plugin_dir_url ( __FILE__ ) . 'css/swiper.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtportfolio-frontend', plugin_dir_url ( __FILE__ ) . 'css/frontend.css', array (), false, 'all' );	
			wp_enqueue_style ( 'dtportfolio-responsive',  	plugin_dir_url ( __FILE__ ) . 'css/responsive.css', array (), false, 'all' );


			// Custom Skin
			require_once plugin_dir_path ( __FILE__ ) . '/css/skin.php';
			$css = dtportfolio_generate_skin_colors();
			wp_add_inline_style ( 'dtportfolio-frontend', $css );	
			

			// enqueueing js files
			wp_enqueue_script ( 'dtportfolio-plugins', plugin_dir_url ( __FILE__ ) . 'js/jquery.plugins.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-isotope', plugin_dir_url ( __FILE__ ) . 'js/isotope.pkgd.min.js', array ('jquery'), false, true);

			wp_enqueue_script ( 'dtportfolio-fullpage', plugin_dir_url ( __FILE__ ) . 'js/jquery.fullPage.min.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-ilightbox', plugin_dir_url ( __FILE__ ) . 'js/ilightbox.min.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-jarallax', plugin_dir_url ( __FILE__ ) . 'js/jarallax.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-multiscroll', plugin_dir_url ( __FILE__ ) . 'js/jquery.multiscroll.min.js', array ('jquery'), false, true );

			wp_enqueue_script ( 'dtportfolio-inview', plugin_dir_url ( __FILE__ ) . 'js/jquery.inview.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-swiper', plugin_dir_url ( __FILE__ ) . 'js/swiper.min.js', array ('jquery'), false, true );
			wp_enqueue_script ( 'dtportfolio-frontend', plugin_dir_url ( __FILE__ ) . 'js/frontend.js', array ('jquery'), false, true );


			$this->elementor_preview_mode = '';

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			if (is_plugin_active('elementor/elementor.php') || is_plugin_active_for_network('elementor/elementor.php')) {  // Elementor Plugin	

				if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
				      $this->elementor_preview_mode = 'true';
				}

			}


			wp_localize_script ( 'dtportfolio-frontend', 'dtportfoliofrontendobject', array (
					'ajaxurl' => admin_url('admin-ajax.php'),
					'page_builder' => $this->page_builder,
					'elementor_preview_mode' => $this->elementor_preview_mode
				));


		}

		public static function DTPortfolioAddonActivate() {
			if( ! function_exists('elearni_cs_get_option') ){
				wp_die( sprintf( esc_html__('Please make sure %s theme is activated.', 'dtportfolio'), THEME_NAME ) );
			}
		}

		public static function DTPortfolioAddonDectivate() {
		}	

	}
}

if (class_exists ( 'DTPortfolioAddon' )) {

	register_activation_hook ( __FILE__, array (
			'DTPortfolioAddon',
			'DTPortfolioAddonActivate' 
	) );
	register_deactivation_hook ( __FILE__, array (
			'DTPortfolioAddon',
			'DTPortfolioAddonDectivate' 
	) );

	global $dtportfolio_animationtypes;
	$dtportfolio_animationtypes = array("" => "none", "flash" => "flash", "shake" => "shake", "bounce" => "bounce", "tada" => "tada", "swing" => "swing", "wobble" => "wobble", "pulse" => "pulse", "flip" => "flip", "flipInX" => "flipInX", "flipOutX" => "flipOutX", "flipInY" => "flipInY", "flipOutY" => "flipOutY", "fadeIn" => "fadeIn", "fadeInUp" => "fadeInUp", "fadeInDown" => "fadeInDown", "fadeInLeft" => "fadeInLeft", "fadeInRight" => "fadeInRight", "fadeInUpBig" => "fadeInUpBig", "fadeInDownBig" => "fadeInDownBig", "fadeInLeftBig" => "fadeInLeftBig", "fadeInRightBig" => "fadeInRightBig", "fadeOut" => "fadeOut", "fadeOutUp" => "fadeOutUp","fadeOutDown" => "fadeOutDown", "fadeOutLeft" => "fadeOutLeft", "fadeOutRight" => "fadeOutRight", "fadeOutUpBig" => "fadeOutUpBig", "fadeOutDownBig" => "fadeOutDownBig", "fadeOutLeftBig" => "fadeOutLeftBig","fadeOutRightBig" => "fadeOutRightBig", "bounceIn" => "bounceIn", "bounceInUp" => "bounceInUp", "bounceInDown" => "bounceInDown", "bounceInLeft" => "bounceInLeft", "bounceInRight" => "bounceInRight", "bounceOut" => "bounceOut", "bounceOutUp" => "bounceOutUp", "bounceOutDown" => "bounceOutDown", "bounceOutLeft" => "bounceOutLeft", "bounceOutRight" => "bounceOutRight", "rotateIn" => "rotateIn", "rotateInUpLeft" => "rotateInUpLeft", "rotateInDownLeft" => "rotateInDownLeft", "rotateInUpRight" => "rotateInUpRight", "rotateInDownRight" => "rotateInDownRight", "rotateOut" => "rotateOut", "rotateOutUpLeft" => "rotateOutUpLeft","rotateOutDownLeft" => "rotateOutDownLeft", "rotateOutUpRight" => "rotateOutUpRight", "rotateOutDownRight" => "rotateOutDownRight", "hinge" => "hinge", "rollIn" => "rollIn", "rollOut" => "rollOut", "lightSpeedIn" => "lightSpeedIn", "lightSpeedOut" => "lightSpeedOut", "slideDown" => "slideDown", "slideUp" => "slideUp", "slideLeft" => "slideLeft", "slideRight" => "slideRight", "slideExpandUp" => "slideExpandUp", "expandUp" => "expandUp", "expandOpen" => "expandOpen", "bigEntrance" => "bigEntrance", "hatch" => "hatch", "floating" => "floating", "tossing" => "tossing", "pullUp" => "pullUp", "pullDown" => "pullDown", "stretchLeft" => "stretchLeft", "stretchRight" => "stretchRight", "zoomIn" => "zoomIn");	
	
	$dt_portfolio_addon = new DTPortfolioAddon ();
}
?>