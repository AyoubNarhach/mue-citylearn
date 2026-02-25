<?php
/**
 * The main class that initiates and runs the plugin.
 */
final class DTPortfolioElementor {

	/**
	 * Plugin Version
	 */
	const DT_ELEMENTOR_VERSION = '1.0';

	/**
	 * Minimum Elementor version
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.2.7';

	/**
	 * Minimum PHP Version
	 */
	const MINIMUM_PHP_VERSION = '7.0';

	/**
	 * Instance variable
	 */
	private static $_instance = null;

	/**
	 * Base Plugin URL
	 */
	private $plugin_url = null;	

	/**
	 * Base Plugin Path
	 */
	private $plugin_path = null;

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

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initialize the plugin
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', array( $this, 'missing_elementor_plugin' ) );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( $this, 'minimum_elementor_version' ) );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'minimum_php_version' ) );
			return;
		}

		add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

		require $this->plugin_path( 'widgets/class-register-widgets.php' );

	}

	/**
	 * Admin notice
	 * Warning when the site doesn't have Elementor installed or activated.
	 */
	public function missing_elementor_plugin() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'dtportfolio' ),
			'<strong>' . esc_html__( 'DesignThemes Elementor Addon', 'dtportfolio' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'dtportfolio' ) . '</strong>'			
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Admin notice
	 * Warning when the site doesn't have a minimum required Elementor version.
	 */
	public function minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'dtportfolio' ),
			'<strong>' . esc_html__( 'DesignThemes Elementor Addon', 'dtportfolio' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'dtportfolio' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );		
	}

	/**
	 * Admin notice
	 * Warning when the site doesn't have a minimum required PHP version.
	 */
	public function minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'dtportfolio' ),
			'<strong>' . esc_html__( 'DesignThemes Elementor Addon', 'dtportfolio' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'dtportfolio' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

	/**
	 * Returns path to file or dir inside plugin folder
	 */
	public function plugin_path( $path = null ) {

		if ( ! $this->plugin_path ) {
			$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
		}

		return $this->plugin_path . $path;
	}

	/**
	 * Returns url to file or dir inside plugin folder
	 */
	public function plugin_url( $path = null ) {

		if ( ! $this->plugin_url ) {
			$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
		}

		return $this->plugin_url . $path;
	}

	/**
	 * Register category
	 * Add DesignThemes category in elementor 
	 */
	public function register_category( $elements_manager ) {

		$elements_manager->add_category(
			'dt-portfolio-widgets',array(
				'title' => esc_html__( 'DesignThemes Portfolio Addon', 'dtportfolio' ),
				'icon'  => 'font'
			)
		);
	}
}

if( !function_exists('dt_portfolio_elementor') ) {

	function dt_portfolio_elementor() {
		return DTPortfolioElementor::instance();
	}
}

dt_portfolio_elementor();