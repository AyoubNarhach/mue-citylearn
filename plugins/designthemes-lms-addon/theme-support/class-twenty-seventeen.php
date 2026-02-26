<?php

if( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'DTLmsTwentySeventeen' ) ) {

	class DTLmsTwentySeventeen {

		function __construct() {

			add_filter( 'body_class', array( $this, 'dtlms_ts_body_class' ), 20 );

			add_action( 'wp_enqueue_scripts', array( $this, 'dtlms_ts_enqueue_styles' ), 104 );

			add_action( 'dtlms_before_main_content', array( $this, 'dtlms_ts_before_main_content' ), 10 );
			add_action( 'dtlms_after_main_content', array( $this, 'dtlms_ts_after_main_content' ), 10 );

			add_action( 'dtlms_before_content', array( $this, 'dtlms_ts_before_content' ), 10 );
			add_action( 'dtlms_after_content', array( $this, 'dtlms_ts_after_content' ), 10 );

		}
	
		function dtlms_ts_body_class( $classes ) {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_packages' )) {
				$classes[] = 'dtlms-remove-sitetitle';
			}

			return $classes;

		}		

		function dtlms_ts_enqueue_styles() {

			wp_enqueue_style ( 'dtlms-twentyseventeen', plugins_url ('designthemes-lms-addon') . '/css/themes/twenty-seventeen.css' );

		}

		function dtlms_ts_before_main_content() {	

			echo '<div class="wrap">';
				echo '<div id="primary" class="content-area twentyseventeen">';
					echo '<main id="main" class="site-main" role="main">';

		}

		function dtlms_ts_after_main_content() {

					echo '</main>';
				echo '</div>';
			echo '</div>';

		}

		function dtlms_ts_before_content() {
			?>
			<header class="entry-header">
				<?php
				if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {

					the_title( '<h1 class="entry-title">', '</h1>' );
					twentyseventeen_edit_link( get_the_ID() );	

				} else if(is_post_type_archive('dtlms_classes') || is_post_type_archive('dtlms_courses') || is_post_type_archive('dtlms_lessons') || is_post_type_archive('dtlms_quizzes') || is_post_type_archive('dtlms_questions') || is_post_type_archive('dtlms_assignments') || is_post_type_archive('dtlms_certificates') || is_post_type_archive('dtlms_packages') || is_tax ( 'course_category' ) || is_tax ( 'question_category' ) || is_author()) {

					the_archive_title( '<h1 class="page-title">', '</h1>' );
					
				}
				?>
			</header>
			<?php

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {
			} else {
				global $post;
				echo '<article id="post-'.$post->ID.'" class="'.get_post_class().'">';
			}
						
		}

		function dtlms_ts_after_content() {

			if (is_singular( 'dtlms_classes' ) || is_singular( 'dtlms_courses' ) || is_singular( 'dtlms_lessons' ) || is_singular( 'dtlms_quizzes' ) || is_singular( 'dtlms_questions' ) || is_singular( 'dtlms_assignments' ) || is_singular( 'dtlms_certificates' ) || is_singular( 'dtlms_packages' )) {
			} else {
				echo '</article>';
			}

		}

	}

	new DTLmsTwentySeventeen();

}

?>