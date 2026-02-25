<?php

if (!class_exists ( 'DTLMSCustomPostTypes' )) {

	class DTLMSCustomPostTypes {

		function __construct() {
				 
			/* Classes Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/classes-post-type.php';
			if (class_exists ( 'DTLMSClassesPostType' )) {				
				new DTLMSClassesPostType ();
			}

			/* Courses Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/courses-post-type.php';
			if (class_exists ( 'DTLMSCoursesPostType' )) {				
				new DTLMSCoursesPostType ();
			}
			
			/* Lesson Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/lessons-post-type.php';
			if (class_exists ( 'DTLMSLessonsPostType' )) {				
				new DTLMSLessonsPostType ();
			}
			
			/* Quizes Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/quizzes-post-type.php';
			if (class_exists ( 'DTLMSQuizzesPostType' )) {				
				new DTLMSQuizzesPostType ();
			}
			
			/* Questions Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/questions-post-type.php';
			if (class_exists ( 'DTLMSQuestionsPostType' )) {				
				new DTLMSQuestionsPostType ();
			}
			
			/* Assignemnts Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/assignments-post-type.php';
			if (class_exists ( 'DTLMSAssignmentsPostType' )) {				
				new DTLMSAssignmentsPostType ();
			}
									
			/* All Certificates Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/certificates-post-type.php';
			if (class_exists ( 'DTLMSCertificatesPostType' )) {				
				new DTLMSCertificatesPostType ();
			}

			/* Grading Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/gradings-post-type.php';
			if (class_exists ( 'DTLMSGradingsPostType' )) {				
				new DTLMSGradingsPostType ();
			}

			/* Packages Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/packages-post-type.php';
			if (class_exists ( 'DTLMSPackagesPostType' )) {				
				new DTLMSPackagesPostType ();
			}					

			/* Commission payments Custom Post Type */
			require_once plugin_dir_path ( __FILE__ ) . '/commissionpayments-post-type.php';
			if (class_exists ( 'DTLMSCommissionPaymentsPostType' )) {				
				new DTLMSCommissionPaymentsPostType ();
			}

			/* Author Single Page */
			add_filter ( 'template_include', array ( $this, 'dtlms_template_include'  ) );

		}

		function dtlms_template_include($template) {

			if(is_author()) {
				$template = plugin_dir_path ( __FILE__ ) . 'templates/single-author.php';
			}

			return $template;

		}		
			
	}

}

?>