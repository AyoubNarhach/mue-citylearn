<?php

function dtlms_statistics_overview_content($instructor_id) {
	
	$class_singular_label = apply_filters( 'class_label', 'singular' );
	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$output = '';

	$output .= '<div class="dtlms-statistics-container">';
	
		$output .= '<div class="dtlms-column dtlms-one-fifth first">';

			$output .= do_shortcode('[dtlms_total_items item-type="classes" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="courses" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="lessons" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="quizzes" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="questions" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="assignments" /]');
			$output .= do_shortcode('[dtlms_total_items item-type="packages" /]');

			$output .= '<table border="0" cellpadding="0" cellspacing="0" class="dtlms-custom-table">';

				$purchased_users_cnt = 0;
				$purchased_users_complete = array ();

				$courses_args = array (
									'post_type'        => 'dtlms_courses',
									'author'	   	   => $instructor_id,
									'post_status'      => 'publish'
								);

				$courses = get_posts( $courses_args );
				if(is_array($courses) && !empty($courses)) {			
					foreach ( $courses as $course ) {
						setup_postdata( $course ); 
						$course_id = $course->ID;

						$purchased_users = get_post_meta($course_id, 'purchased_users', true);
						$purchased_users_complete = array_merge_recursive($purchased_users_complete, $purchased_users);

						if(is_array($purchased_users) && !empty($purchased_users)) {
							$purchased_users_cnt = $purchased_users_cnt + count($purchased_users);
						}

					}
				}
				wp_reset_postdata();

				$purchased_users_complete_cnt = count(array_unique($purchased_users_complete));


				$output .= '<tr>
								<td><strong>'.esc_html__('Total Purchases', 'dtlms').'</strong></td>
								<td>'.$purchased_users_cnt.'</td>
							</tr>';

				$output .= '<tr>
								<td><strong>'.esc_html__('Total Students In My Courses', 'dtlms').'</strong></td>
								<td>'.$purchased_users_complete_cnt.'</td>
							</tr>';							

				$badges_args = array (
								'meta_key'         => 'badge-achieved',
								'meta_value'       => 'true',
								'post_type'        => 'dtlms_gradings',
								'author'	   	   => $instructor_id,
								'post_status'      => 'publish',
							);
				$badges = new WP_Query( $badges_args );
				$badges_count = $badges->found_posts;
				wp_reset_postdata();

				$output .= '<tr>
								<td><strong>'.esc_html__('Total Badges Given', 'dtlms').'</strong></td>
								<td>'.$badges_count.'</td>
							</tr>';


				$certificates_args = array (
										'meta_key'         => 'certificate-achieved',
										'meta_value'       => 'true',
										'post_type'        => 'dtlms_gradings',
										'author'	   	   => $instructor_id,
										'post_status'      => 'publish',
									);
				$certificates = new WP_Query( $certificates_args );
				$certificates_count = $certificates->found_posts; 
				wp_reset_postdata();				

				$output .= '<tr>
								<td><strong>'.esc_html__('Total Certificates Given', 'dtlms').'</strong></td>
								<td>'.$certificates_count.'</td>
							</tr>';

				$gradings_graded_args = array ( 
									'author' => $instructor_id, 
									'post_type' => 'dtlms_gradings',
									'meta_query'=>array(), 
								);	
				$gradings_graded_args['meta_query'][] = array (
											'key'     => 'grade-type',
											'value'   => 'course',
											'compare' => '=='
										);																
				$gradings_graded_args['meta_query'][] = array (
											'key'     => 'graded',
											'value'   => 'true',
											'compare' => '=='
										);
				$gradings_graded = new WP_Query( $gradings_graded_args );
				$gradings_graded_count = $gradings_graded->found_posts; 
				wp_reset_postdata();

				$output .= '<tr>
								<td><strong>'.esc_html__('Total Courses Evaluated', 'dtlms').'</strong></td>
								<td>'.$gradings_graded_count.'</td>
							</tr>';


				$under_gradings_args = array ( 
									'author' => $instructor_id, 
									'post_type' => 'dtlms_gradings',
									'meta_query'=>array(), 
								);	
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'grade-type',
											'value'   => 'course',
											'compare' => '=='
										);																	
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'graded',
											'compare' => 'NOT EXISTS'
										);
				$under_gradings_args['meta_query'][] = array (
											'key'     => 'submitted',
											'value'   => '1',
											'compare' => '=='
										);					
				$under_gradings = new WP_Query( $under_gradings_args );
				$under_gradings_count = $under_gradings->found_posts; 
				wp_reset_postdata();

				$output .= '<tr>
								<td><strong>'.esc_html__('Total Courses Under Evaluation', 'dtlms').'</strong></td>
								<td>'.$under_gradings_count.'</td>
							</tr>';

			$output .= '</table>';

		$output .= '</div>';

		$output .= '<div class="dtlms-column dtlms-four-fifth">';

			$output .= '<div class="dtlms-column dtlms-one-half first">';
				$output .= '<h2>'.esc_html__('Total Items', 'dtlms').'</h2>';
				$output .= do_shortcode('[dtlms_total_items_chart /]');
			$output .= '</div>';
			$output .= '<div class="dtlms-column dtlms-one-half">';
				$output .= '<h2>'.esc_html__('Total Items', 'dtlms').'</h2>';
				$output .= do_shortcode('[dtlms_total_items_chart chart-type="bar" /]');
			$output .= '</div>';

			$output .= '<div class="dtlms-hr-invisible"></div>';
			
			$output .= '<div class="dtlms-column dtlms-one-column first">';
				$output .= '<h2>'.esc_html__('Overall Purchases', 'dtlms').'</h2>';
				$output .= do_shortcode('[dtlms_purchases_overview_chart include-class-purchases="true" include-course-purchases="true" include-package-purchases="true" include-data="true"]');
			$output .= '</div>';

			$output .= '<div class="dtlms-hr-invisible"></div>';
			$output .= '<div class="dtlms-hr-invisible"></div>';

			$output .= '<div class="dtlms-column dtlms-one-half first">';
				$output .= '<h2>'.sprintf( esc_html__( '%1$s Purchases', 'dtlms' ), $class_singular_label ).'</h2>';
				$output .= do_shortcode('[dtlms_purchases_overview_chart include-class-purchases="true"]');
			$output .= '</div>';
			$output .= '<div class="dtlms-column dtlms-one-half">';
				$output .= '<h2>'.esc_html__('Course Purchases', 'dtlms').'</h2>';
				$output .= do_shortcode('[dtlms_purchases_overview_chart include-course-purchases="true"]');
			$output .= '</div>';

			$output .= '<div class="dtlms-hr-invisible"></div>';
			$output .= '<div class="dtlms-hr-invisible"></div>';
			
			$output .= '<div class="dtlms-column dtlms-one-column first">';		
				$output .= '<div class="dtlms-column dtlms-one-half first">';
					$output .= '<h2>'.sprintf( esc_html__( 'Instructor Earnings - Over %1$s', 'dtlms' ), $class_plural_label ).'</h2>';
					$output .= do_shortcode('[dtlms_instructor_commission_earnings enable-instructor-filter="false" instructor-earnings="over-item" timeline-filter="alltime" include-course-commission="false" include-class-commission="true" include-other-commission="false" include-total-commission="false"]');		
				$output .= '</div>';			
				$output .= '<div class="dtlms-column dtlms-one-half">';
					$output .= '<h2>'.esc_html__('Instructor Earnings - Over Courses', 'dtlms').'</h2>';
					$output .= do_shortcode('[dtlms_instructor_commission_earnings enable-instructor-filter="false" instructor-earnings="over-item" chart-type="pie" timeline-filter="alltime" include-class-commission="false" include-other-commission="false" include-total-commission="false"]');		
				$output .= '</div>';	
				$output .= '<div class="dtlms-column dtlms-one-column no-space">';
					$output .= '<h2>'.esc_html__('Instructor Earnings - Over Period', 'dtlms').'</h2>';
					$output .= do_shortcode('[dtlms_instructor_commission_earnings enable-instructor-filter="false" include-class-commission="true" include-other-commission="false" include-total-commission="true"]');
				$output .= '</div>';
			$output .= '</div>';

		$output .= '</div>';			

	$output .= '</div>';

	echo $output;

}

function dtlms_statistics_mycourses_content($instructor_id) {
	
	$output = '';

	$output .= do_shortcode('[dtlms_instructor_courses enable-instructor-filter="false" /]');

	echo $output;	

}

function dtlms_statistics_myclasses_content($instructor_id) {
	
	$output = '';

	$output .= do_shortcode('[dtlms_class_details enable-instructor-filter="false" /]');

	echo $output;

}

function dtlms_statistics_commissions_content($instructor_id) {
	
	$output = '';

	$output .= do_shortcode('[dtlms_instructor_commissions enable-instructor-filter="false" /]');

	echo $output;

}

?>