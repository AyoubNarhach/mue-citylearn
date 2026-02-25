<?php 
if( !class_exists('DTLMSVisualComposer') ) {

	class DTLMSVisualComposer {

		function __construct() {

			add_action( 'admin_enqueue_scripts', array ( $this, 'dtlms_vc_admin_scripts') );
			add_action( 'after_setup_theme', array ( $this, 'dtlms_vc_map_shortcodes' ) , 1000 );
			add_action( 'vc_load_default_templates_action', array ( $this, 'dtlms_vc_certificate_template' ) );

			define( 'DTLMSADDON_TITLE',  esc_html__('DT LMS Addon','dtlms') );
			define( 'DTLMSADDON_DASHBOARD_TITLE',  esc_html__('DT LMS Addon - Dashboard','dtlms') );

		}

		function dtlms_vc_admin_scripts( $hook ) {

			if($hook == "post.php" || $hook == "post-new.php") {
				wp_enqueue_style( 'dtlms-vc-admin', plugins_url('designthemes-lms-addon') .'/visual-composer/admin.css', array(), false, 'all' );
			}

		}

		function dtlms_vc_map_shortcodes() {

			global $pagenow;

			$path = plugin_dir_path ( __FILE__ ).'modules/';

			$modules = array(
				'dtlms_login_logout_links' => $path.'login-logout-links.php',
				'dtlms_certificate_details' => $path.'certificate-details.php',
				'dtlms_certificate' => $path.'certificate.php',
				'dtlms_courses_listing' => $path.'courses-listing.php',
				'dtlms_classes_listing' => $path.'classes-listing.php',
				'dtlms_packages_listing' => $path.'packages-listing.php',
				'dtlms_course_categories' => $path.'course-categories.php',
				'dtlms_instructor_list' => $path.'instructor-list.php',

				'dtlms_total_items' => $path.'dashboard/total-items.php',
				'dtlms_total_items_chart' => $path.'dashboard/total-items-chart.php',
				'dtlms_purchases_overview_chart' => $path.'dashboard/purchases-overview-chart.php',
				'dtlms_instructor_commission_earnings' => $path.'dashboard/instructor-commission-earnings.php',

				'dtlms_instructor_courses' => $path.'dashboard/instructor-courses.php',
				'dtlms_instructor_added_courses' => $path.'dashboard/instructor-added-courses.php',
				'dtlms_instructor_commissions' => $path.'dashboard/instructor-commissions.php',
				'dtlms_student_courses' => $path.'dashboard/student-courses.php',
				'dtlms_package_details' => $path.'dashboard/package-details.php',
				'dtlms_class_details' => $path.'dashboard/class-details.php',

				'dtlms_student_purchased_items' => $path.'dashboard/student-purchased-items.php',
				'dtlms_student_assigned_items' => $path.'dashboard/student-assigned-items.php',
				'dtlms_student_undergoing_items' => $path.'dashboard/student-undergoing-items.php',
				'dtlms_student_underevaluation_items' => $path.'dashboard/student-underevaluation-items.php',
				'dtlms_student_completed_items' => $path.'dashboard/student-completed-items.php',

				'dtlms_student_badges' => $path.'dashboard/student-badges.php',
				'dtlms_student_certificates' => $path.'dashboard/student-certificates.php',				

				'dtlms_student_purchased_items_list' => $path.'dashboard/student-purchased-items-list.php',
				'dtlms_student_assigned_items_list' => $path.'dashboard/student-assigned-items-list.php',
				'dtlms_student_undergoing_items_list' => $path.'dashboard/student-undergoing-items-list.php',
				'dtlms_student_underevaluation_items_list' => $path.'dashboard/student-underevaluation-items-list.php',
				'dtlms_student_completed_items_list' => $path.'dashboard/student-completed-items-list.php',

				'dtlms_student_course_curriculum_details' => $path.'dashboard/student-course-curriculum-details.php',
				'dtlms_student_course_events' => $path.'dashboard/student-course-events.php',

				'dtlms_student_class_curriculum_details' => $path.'dashboard/student-class-curriculum-details.php',
			);

			// Apply filters so you can easily modify the modules 100%
			$modules = apply_filters( 'vcex_builder_modules', $modules );

			if( !empty( $modules ) ){
				foreach ( $modules as $key => $val ) {
					require_once( $val );
				}
			}

		}

		function dtlms_vc_certificate_template() {

		    $data               = array ();
		    $data['name']       = esc_html__( 'DT LMS - Certificate Template', 'dtlms' );
			$data['content']    = <<<CONTENT
		        [vc_row][vc_column][dtlms_certificate logo1="13845" logo2="13463" heading="Certificate of Achievement" subheading="This certification is proudly presented for honorable achievement to" footer_logo="14310" signature="13759"]Aenean et volutpat dui, a vulputate orci. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.[/dtlms_certificate][/vc_column][/vc_row]
CONTENT;
  
    		vc_add_default_templates( $data );

		}

	}

}
?>