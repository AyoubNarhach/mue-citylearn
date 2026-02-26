<?php
/*
 * Plugin Name:	DesignThemes LMS Addon 
 * URI: 		http://wedesignthemes.com/plugins/designthemes-lms-addon
 * Description: A simple wordpress plugin designed to implements <strong>LMS addon features of DesignThemes</strong>
 * Version: 	2.0
 * Author: 		DesignThemes 
 * Text Domain: dtlms
 * Author URI:	http://themeforest.net/user/designthemes
 */


if (! class_exists ( 'DTLMSAddon' )) {

	class DTLMSAddon {

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
			
			add_action ( 'init', array ( $this, 'dtlms_init' ) );
			add_action ( 'admin_enqueue_scripts', array ( $this, 'dtlms_admin_enqueue_scripts' ) );
			add_action ( 'wp_enqueue_scripts', array ( $this, 'dtlms_enqueue_scripts' ) );
			add_action ( 'wp_enqueue_scripts', array ( $this, 'dtlms_load_visual_composer_css' ) );


			// Configure LMS addon mene items
			add_action ( 'admin_menu', array ( $this, 'dtlms_configure_admin_menu' ) );
			add_action ( 'parent_file', array ( $this, 'dtlms_change_active_menu' ) );


			// Code Star Framework Init
			require_once plugin_dir_path ( __FILE__ ) . '/config-codestar.php';
			if(class_exists('DTLMSCodeStar')) {
				new DTLMSCodeStar();
			}

			// Register Custom Post Types
			require_once plugin_dir_path ( __FILE__ ) . '/custom-post-types/register-post-types.php';
			if (class_exists ( 'DTLMSCustomPostTypes' )) {
				new DTLMSCustomPostTypes ();
			}

			// Register Shortcodes
			require_once plugin_dir_path ( __FILE__ ) . '/shortcodes/shortcodes.php';
			if(class_exists('DTLMSShortcodes')){
				new DTLMSShortcodes();
			}

			// Register Visual Composer
			require_once plugin_dir_path ( __FILE__ ) . '/visual-composer/register-visual-composer.php';
			if(class_exists('DTLMSVisualComposer')){
				new DTLMSVisualComposer();
			}

			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-admin.php';	

			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils.php';	
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-comment.php';	
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-core.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-classes.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-classes-items.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-courses.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-courses-listing-items.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-courses-single-items.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-lesson.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-quiz.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-assignment.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-packages.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-packages-items.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-register.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-social-login.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-woocommerce.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-common.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-backend.php';
			require_once plugin_dir_path ( __FILE__ ) . '/utils/utils-menu.php'; // Instructor & Student Menu

			// Settings
			require_once plugin_dir_path ( __FILE__ ) . '/settings/settings.php';

			// Statistics
			require_once plugin_dir_path ( __FILE__ ) . '/statistics/statistics.php';

			// Class Registration
			require_once plugin_dir_path ( __FILE__ ) . '/custom-post-types/class-registrations.php';

			// Dashboard Functionality
			require_once plugin_dir_path ( __FILE__ ) . '/dashboard/dashboard-utils.php';
			add_action ( 'bp_include', array ( $this, 'dtlms_activate_buddypress_dashboard' ) ); // BuddyPress
			add_action ( 'plugins_loaded',  array ( $this, 'dtlms_activate_woocommerce_dashboard' ) ); // WooCommerce

			// Theme Support
			$this->dtlms_theme_support_includes();

		}		


		function dtlms_init() {

			load_plugin_textdomain ( 'dtlms', false, dirname ( plugin_basename ( __FILE__ ) ) . '/languages/' );

			/* WooCommerce Payment Functionality */
			if ( class_exists( 'WooCommerce' ) ) {
				
				require_once plugin_dir_path ( __FILE__ ) . '/custom-post-types/woocommerce.php';	
				if (class_exists ( 'DTLMSWooCommerce' )) {
					new DTLMSWooCommerce ();
				}

			}

		}

		function dtlms_admin_enqueue_scripts() {

			$current_screen = get_current_screen();

			//Enqueue CSS files
			wp_enqueue_style ( 'wp-color-picker' );
			wp_enqueue_style ( 'fontawesome-icons', plugin_dir_url ( __FILE__ ) . 'css/fontawesome-all.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'icon-moon', plugin_dir_url ( __FILE__ ) . 'css/icon-moon.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-backend', plugin_dir_url ( __FILE__ ) . 'css/backend.css', array (), false, 'all' );
			wp_enqueue_style ( 'chosen', plugin_dir_url ( __FILE__ ) . 'css/chosen.css', array (), false, 'all' );
			wp_enqueue_style ( 'jquery-ui', plugin_dir_url ( __FILE__ ) . 'css/jquery-ui.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-common', plugin_dir_url ( __FILE__ ) . 'css/common.css', array (), false, 'all' );

			wp_enqueue_style ( 'dtlms-misc', plugin_dir_url ( __FILE__ ) . 'css/misc.css', array (), false, 'all' );


			//Enqueue JS files
			wp_enqueue_media();
			wp_enqueue_script ( 'jquery-ui-datepicker' );
			wp_enqueue_script ( 'jquery-ui-sortable' );

			if($current_screen->id == 'edit-course_category' || $current_screen->id == 'lms_page_dtlms-settings-options' || $current_screen->id == 'page' || $current_screen->id == 'post' || $current_screen->id == 'dt_headers' || $current_screen->id == 'dt_footers' || $current_screen->id == 'product') {
                wp_enqueue_script ( 'wp-color-picker' );
                wp_enqueue_script ( 'wp-color-picker-alpha', plugin_dir_url ( __FILE__ ) . 'js/wp-color-picker-alpha.min.js', array (), false, true );
            }

			wp_enqueue_script ( 'chosen', plugin_dir_url ( __FILE__ ) . 'js/chosen.jquery.min.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-timepicker', plugin_dir_url ( __FILE__ ) . 'js/jquery-ui-timepicker-addon.js', array (), false, true );
			if($current_screen->id != 'sitepress-multilingual-cms/menu/languages' && $current_screen->id != 'sitepress-multilingual-cms/menu/theme-localization' && $current_screen->id != 'sitepress-multilingual-cms/menu/menu-sync/menus-sync' && $current_screen->id != 'wpml_page_sitepress-multilingual-cms/menu/taxonomy-translation' && $current_screen->id != 'sitepress-multilingual-cms/menu/translation-options' && $current_screen->id != 'page' && $current_screen->id != 'post' && $current_screen->id != 'dt_headers' && $current_screen->id != 'dt_footers' && $current_screen->id != 'product') {
				wp_enqueue_script ( 'dtlms-chart', plugin_dir_url ( __FILE__ ) . 'js/chart.min.js', array (), false, false );
			}
			wp_enqueue_script ( 'dtlms-nicescroll', plugin_dir_url ( __FILE__ ) . 'js/jquery.nicescroll.min.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-print', plugin_dir_url ( __FILE__ ) . 'js/jquery.print.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-common', plugin_dir_url ( __FILE__ ) . 'js/common.js', array (), false, true );
			wp_localize_script ( 'dtlms-common', 'lmscommonobject', array (
					'ajaxurl' => admin_url('admin-ajax.php'),
					'noResult' => esc_html__('No Results Found!', 'dtlms'),
				));

			wp_enqueue_script ( 'dtlms-backend', plugin_dir_url ( __FILE__ ) . 'js/backend.js', array (), false, true );
			wp_localize_script ( 'dtlms-backend', 'lmsbackendobject', array (
					'ajaxurl' => admin_url('admin-ajax.php'),
					'revokeUserSubmission' => esc_html__('User item submission have been revoked successfully.', 'dtlms'),
					'revokeUserSubmissionWarning' => esc_html__('You can\'t revoke the item once it is graded.', 'dtlms'),
					'gradingWarningTrash' => esc_html__('If this item has child item(s), all item(s) will be moved to trash. If course is under "Curriculum Completion Lock", workflow will be breaked.', 'dtlms'),
					'gradingWarningDelete' => esc_html__('If this item has child item(s), all item(s) will be deleted permanently. If course is under "Curriculum Completion Lock", workflow will be breaked.', 'dtlms'),
					'selectInstructor' => esc_html__('Please select any instructor!', 'dtlms'),
					'quizTimeout' => esc_html__('Timeout!', 'dtlms'),
					'noResult' => esc_html__('No Results Found!', 'dtlms'),
					'noGraph' => esc_html__('No enough data to generate graph!', 'dtlms'),
					'onRefresh' => esc_html__('Refreshing this quiz page will mark this session as completed.', 'dtlms'),
					'onRefreshCurriculum' => esc_html__('Would you like to abort this quiz session, which will mark this session as completed ?.', 'dtlms'),
					'registrationSuccess' => esc_html__('You have successfully registered with our class!', 'dtlms'),
					'locationAlert1' => esc_html__('To get GPS location please fill address.', 'dtlms'),
					'locationAlert2' => esc_html__('Please add latitude and longitude', 'dtlms'),
					'resetGrade' => esc_html__('Gradings have been resetted successfully', 'dtlms'),
					'invalidFile' => esc_html__('No file chosen!', 'dtlms'),
					'importUploadTitle' => esc_html__('Upload / Select CSV Files', 'dtlms'),
					'importInsertFile' => esc_html__('Insert File', 'dtlms'),
					'attachmentTitle' => esc_html__('Attachment Title', 'dtlms'),
					'attachmentIcon' => esc_html__('Attachment Icon', 'dtlms')
				));

			$googlemap_api_key = (dtlms_option('general', 'googlemap-api-key') != '') ? dtlms_option('general', 'googlemap-api-key') : '';
			wp_enqueue_script ( 'dtlms-google-map', 'https://maps.googleapis.com/maps/api/js?key='.$googlemap_api_key, array('jquery'), false, true );			

		}

		function dtlms_enqueue_scripts() {

			$skin_settings = get_option('dtlms-skin-settings');
			$primary_color = ( isset($skin_settings['primary-color']) && '' !=  $skin_settings['primary-color'] ) ? $skin_settings['primary-color'] : '#ffcc21';
			$quiztimer_foreground_color = ( isset($skin_settings['quiztimer-foreground-color']) && '' !=  $skin_settings['quiztimer-foreground-color'] ) ? $skin_settings['quiztimer-foreground-color'] : '#40c4ff';
			$quiztimer_background_color = ( isset($skin_settings['quiztimer-background-color']) && '' !=  $skin_settings['quiztimer-background-color'] ) ? $skin_settings['quiztimer-background-color'] : '#ffcc21';


			//Enqueue CSS files
			wp_enqueue_style ( 'fontawesome-icons', plugin_dir_url ( __FILE__ ) . 'css/fontawesome-all.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'icon-moon', plugin_dir_url ( __FILE__ ) . 'css/icon-moon.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-swiper', plugin_dir_url ( __FILE__ ) . 'css/swiper.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'jquery-ui', plugin_dir_url ( __FILE__ ) . 'css/jquery-ui.min.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-frontend', plugin_dir_url ( __FILE__ ) . 'css/frontend.css', array (), false, 'all' );
			wp_enqueue_style ( 'chosen', plugin_dir_url ( __FILE__ ) . 'css/chosen.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-common', plugin_dir_url ( __FILE__ ) . 'css/common.css', array (), false, 'all' );
			wp_enqueue_style ( 'scrolltabs', plugin_dir_url ( __FILE__ ) . 'css/scrolltabs.css', array (), false, 'all' );

			wp_enqueue_style ( 'dtlms-gridlist', plugin_dir_url ( __FILE__ ) . 'css/gridlist-items.css', array (), false, 'all' );
			wp_enqueue_style ( 'dtlms-single', plugin_dir_url ( __FILE__ ) . 'css/single-items.css', array (), false, 'all' );


			// google fonts -----------------------------------------------------------------
			wp_enqueue_style ( 'dtlms-google-fonts', dtlms_load_fonts_url(), array(), false, 'all' );


			if(is_rtl() || (isset($_REQUEST['rtl']) && $_REQUEST['rtl'] == 'yes')) {
				wp_enqueue_style('dtlms-rtl', 	plugin_dir_url ( __FILE__ ) . 'css/rtl.css', array (), false, 'all' );
			}
			
			// Custom Skin
			require_once plugin_dir_path ( __FILE__ ) . '/css/skin.php';
			$css = dtlms_generate_skin_colors();
			wp_add_inline_style ( 'dtlms-single', $css );
				

			//Enqueue JS files
			wp_enqueue_script ( 'jquery-ui-sortable' );
			wp_enqueue_script ( 'jquery-ui-datepicker' );
			wp_enqueue_script ( 'donutchart', plugin_dir_url ( __FILE__ ) . 'js/jquery.donutchart.js', array (), false, true );
			wp_enqueue_script ( 'chosen', plugin_dir_url ( __FILE__ ) . 'js/chosen.jquery.min.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-knob', plugin_dir_url ( __FILE__ ) . 'js/jquery.knob.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-knob-custom', plugin_dir_url ( __FILE__ ) . 'js/jquery.knob.custom.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-print', plugin_dir_url ( __FILE__ ) . 'js/jquery.print.js', array (), false, true );
			wp_enqueue_script ( 'nicescroll', plugin_dir_url ( __FILE__ ) . 'js/jquery.nicescroll.min.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-tabs', plugin_dir_url ( __FILE__ ) . 'js/jquery.tabs.min.js', array (), false, true );
			wp_enqueue_script ( 'inview', plugin_dir_url ( __FILE__ ) . 'js/jquery.inview.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-swiper-js', plugin_dir_url ( __FILE__ ) . 'js/swiper.min.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-chart', plugin_dir_url ( __FILE__ ) . 'js/chart.min.js', array (), false, false );
			wp_enqueue_script ( 'sticky', plugin_dir_url ( __FILE__ ) . 'js/jquery.sticky.js', array (), false, true );
			wp_enqueue_script ( 'downcount', plugin_dir_url ( __FILE__ ) . 'js/jquery.downCount.js', array (), false, true );
			wp_enqueue_script ( 'isotope-3.0.5', plugin_dir_url ( __FILE__ ) . 'js/isotope.pkgd.min.js', array(), false, true);
			wp_enqueue_script ( 'scrolltab', plugin_dir_url ( __FILE__ ) . 'js/jquery.scrolltabs.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-common', plugin_dir_url ( __FILE__ ) . 'js/common.js', array (), false, true );
			wp_localize_script ( 'dtlms-common', 'lmscommonobject', array (
				'ajaxurl' => admin_url('admin-ajax.php'),
				'noResult' => esc_html__('No Results Found!', 'dtlms'),
			));

			wp_enqueue_script ( 'dtlms-toggle-click', plugin_dir_url ( __FILE__ ) . 'js/jquery.toggle.click.js', array (), false, true );
			wp_enqueue_script ( 'dtlms-frontend', plugin_dir_url ( __FILE__ ) . 'js/frontend.js', array (), false, true );
			wp_localize_script ( 'dtlms-frontend', 'lmsfrontendobject', array (
					'pluginPath' => plugin_dir_url ( __FILE__ ),
					'ajaxurl' => admin_url('admin-ajax.php'),
					'quizTimeout' => esc_html__('Timeout!', 'dtlms'),
					'noGraph' => esc_html__('No enough data to generate graph!', 'dtlms'),
					'onRefresh' => esc_html__('Refreshing this quiz page will mark this session as completed.', 'dtlms'),
					'onRefreshCurriculum' => esc_html__('Would you like to abort this quiz session, which will mark this session as completed ?.', 'dtlms'),
					'registrationSuccess' => esc_html__('You have successfully registered with our class!', 'dtlms'),
					'locationAlert1' => esc_html__('To get GPS location please fill address.', 'dtlms'),
					'locationAlert2' => esc_html__('Please add latitude and longitude', 'dtlms'),
					'submitCourse' => esc_html__('You can submit course only when you have completed all items in course.', 'dtlms'),
					'submitClass' => esc_html__('You can submit class only when you have submitted all courses.', 'dtlms'),
					'confirmRegistration' => esc_html__('Please confirm your registration to this class!', 'dtlms'),
					'closedRegistration' => esc_html__('Regsitration Closed', 'dtlms'),
					'primarColor' => $primary_color,
					'quizTimerForegroundColor' => $quiztimer_foreground_color,
					'quizTimerBackgroundColor' => $quiztimer_background_color,
					'assignmentNotification' => esc_html__('Please make sure required fields are filled.', 'dtlms'),
					'printerTitle' => esc_html__('Certificate Printer', 'dtlms'),
				));

			if( is_singular('dtlms_classes') ) {

				$googlemap_api_key = (dtlms_option('general', 'googlemap-api-key') != '') ? dtlms_option('general', 'googlemap-api-key') : '';
				//wp_enqueue_script ( 'dtlms-google-map', 'https://maps.googleapis.com/maps/api/js?key='.$googlemap_api_key.'&callback=ClassLocationMap', array('jquery'), false, true );
				wp_enqueue_script ( 'dtlms-google-map', 'https://maps.googleapis.com/maps/api/js?key='.$googlemap_api_key, array('jquery'), false, true );

			}

		}	

		function dtlms_load_visual_composer_css() {
			if(is_singular('dtlms_classes') || is_singular('dtlms_courses')) {
			    wp_enqueue_script( 'wpb_composer_front_js' );
			    wp_enqueue_style( 'js_composer_front' );
			    wp_enqueue_style( 'js_composer_custom_css' );
			}
		}

		function dtlms_configure_admin_menu() {

			$class_singular_label = apply_filters( 'class_label', 'singular' );

			add_menu_page( esc_html__('Learning Management System','dtlms'), esc_html__('LMS','dtlms'), 'edit_posts', 'dtlms', 'dtlms_dashboard', 'dashicons-book', 6 );
			add_submenu_page( 'dtlms', 'Course Category', 'Course Category', 'edit_posts', 'edit-tags.php?taxonomy=course_category&post_type=dtlms_courses' );
			add_submenu_page( 'dtlms', 'Question Category', 'Question Category', 'edit_posts', 'edit-tags.php?taxonomy=question_category&post_type=dtlms_questions' );

			add_submenu_page( 'dtlms', sprintf( esc_html__( '%1$s Registrations', 'dtlms' ), $class_singular_label ), sprintf( esc_html__( '%1$s Registrations', 'dtlms' ), $class_singular_label ), 'manage_options', 'dtlms-classregistrations-options', 'dtlms_classregistrations_options' );

			add_submenu_page( 'dtlms', 'Statistics', 'Statistics', 'edit_posts', 'dtlms-statistics-options', 'dtlms_statistics_options' ); 
			add_submenu_page( 'dtlms', 'Settings', 'Settings', 'edit_posts', 'dtlms-settings-options', 'dtlms_settings_options' ); 

		}

		function dtlms_change_active_menu($parent_file) {

			global $submenu_file, $current_screen;
			$taxonomy = $current_screen->taxonomy;
			if ($taxonomy == 'course_category') {
				$submenu_file = 'edit-tags.php?taxonomy=course_category&post_type=dtlms_courses';
				$parent_file = 'dtlms';
			}
			if ($taxonomy == 'question_category') {
				$submenu_file = 'edit-tags.php?taxonomy=question_category&post_type=dtlms_questions';
				$parent_file = 'dtlms';
			}			
			return $parent_file;

		}

		/* Dashboard Functionality */
		function dtlms_activate_woocommerce_dashboard() {
			if ( class_exists( 'WooCommerce' ) ) {
				require_once plugin_dir_path ( __FILE__ ) . '/dashboard/woocommerce.php';
			}
		}

		function dtlms_activate_buddypress_dashboard() {
			if ( class_exists( 'BuddyPress' ) ) {
				require_once plugin_dir_path ( __FILE__ ) . '/dashboard/buddypress.php';	
			}
		}	

		/* Theme Support */
		function dtlms_theme_support_includes() {
			switch ( get_template() ) {
				case 'twentyseventeen':
					include_once plugin_dir_path ( __FILE__ ) . '/theme-support/class-twenty-seventeen.php';
				break;
				case 'elearni':
					include_once plugin_dir_path ( __FILE__ ) . '/theme-support/class-designthemes.php';
				break;				
				default:
					include_once plugin_dir_path ( __FILE__ ) . '/theme-support/class-default.php';
				break;				
			}
		}		

		public static function DTLMSAddonActivate() {

			$dtlms_settings = get_option('dtlms-settings');

			if(!isset($dtlms_settings['plugin-status']) && $dtlms_settings['plugin-status'] != 'activated-already') {

				$default_settings = dtlms_plugins_default_settings();
				update_option('dtlms-settings', $default_settings);

				$skin_settings = dtlms_plugins_skin_settings();
				update_option('dtlms-skin-settings', $skin_settings);

			}

		}

		public static function DTLMSAddonDectivate() {
		}

	}
}



if ( class_exists ( 'DTLMSAddon' ) ) {

	register_activation_hook ( __FILE__, array (
			'DTLMSAddon',
			'DTLMSAddonActivate' 
	) );
	register_deactivation_hook ( __FILE__, array (
			'DTLMSAddon',
			'DTLMSAddonDectivate' 
	) );
	
	$dtlmsaddon = new DTLMSAddon ();
	
}

?>