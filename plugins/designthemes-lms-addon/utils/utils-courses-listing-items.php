<?php

function dtlms_course_listing_thumb($course_id, $course_title, $course_permalink, $display_type, $column, $type, $class) {

	$output = '';

	if(has_post_thumbnail($course_id)) {

		$classes = explode(' ', $class);
		$classes = array_map('trim', $classes);

		$image_size = 'full';
		if($type == 'type10') {

			if($column == 3 || in_array('dtlms-three', $classes)) {
				$image_size = 'dtlms-640x490';
			} else if($column == 2 || in_array('dtlms-two', $classes)) {
				$image_size = 'dtlms-960x735';
			}

		} else if($type == 'type9') {

			if($column == 3 || in_array('dtlms-three', $classes)) {
				$image_size = 'dtlms-640x387';
			} else if($column == 2 || in_array('dtlms-two', $classes)) {
				$image_size = 'dtlms-960x581';
			}

		} else {

			if($column == 3 || in_array('dtlms-three', $classes)) {
				$image_size = 'dtlms-640x430';
			} else if($column == 2 || in_array('dtlms-two', $classes)) {
				$image_size = 'dtlms-960x640';
			}

		}


		if($display_type == 'list-item') {

			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id($course_id), $image_size, false);
			$output .= '<a href="'.esc_url($course_permalink).'" title="'.esc_attr($course_title).'">
							<div class="dtlms-courselist-thumb-inner" style="background:url('.esc_url($image_src[0]).');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($course_permalink).'" title="'.esc_attr($course_title).'">'.get_the_post_thumbnail($course_id, $image_size).'</a>';

		}

	} else {

		if($display_type == 'list-item') {

			$output .= '<a href="'.esc_url($course_permalink).'" title="'.esc_attr($course_title).'">
							<div class="dtlms-courselist-thumb-inner" style="background:url('.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($course_permalink).'" title="'.esc_attr($course_title).'"><img src="'.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').'" alt="'.esc_attr($course_title).'" title="'.esc_attr($course_title).'" /></a>';

		}

	}

	return $output;

}

function dtlms_course_listing_certificatenbadge($course_id) {

	$output = '';

	$enable_certificate = get_post_meta($course_id, 'enable-certificate', true);
	$enable_badge = get_post_meta($course_id, 'enable-badge', true);
	
	if($enable_certificate || $enable_badge) {

		$output .= '<div class="dtlms-certificate-badge">';
			if($enable_badge) {
				$output .= '<span class="dtlms-badge"></span>';
			}		
			if($enable_certificate) {
				$output .= '<span class="dtlms-certificate"></span>';	
			}
		$output .= '</div>';

	}

	return $output;

}

function dtlms_course_listing_featured($course_id) {

    $output = '';

	$featured_course = get_post_meta($course_id, 'featured-course', true);

	if(isset($featured_course) && $featured_course == 'true') {
		$output .= '<div class="dtlms-course-listing-featured">';
			$output .= '<span class="dtlms-course-listing-featured-text">'.esc_html__('Featured','dtlms').'</span>';
		$output .= '</div>';
	}

	return $output;

}

function dtlms_course_listing_title($course_id, $course_title, $course_permalink) {

	$output = '<h5><a href="'.esc_url($course_permalink).'">'.esc_attr($course_title).'</a></h5>';

	return $output;

}

function dtlms_course_listing_description($course_id) {

	$output = '<div class="dtlms-courselist-description">
                    '.get_the_excerpt($course_id).'
                </div>';

	return $output;

}

function dtlms_excerpt_max_charlength($course_id, $charlength) {

	$output = '<div class="dtlms-courselist-description">';

	$excerpt = get_the_excerpt($course_id);
	$charlength++;

	if ( mb_strlen( $excerpt ) > $charlength ) {
		$subex = mb_substr( $excerpt, 0, $charlength - 5 );
		$exwords = explode( ' ', $subex );
		$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
		if ( $excut < 0 ) {
			$output .= mb_substr( $subex, 0, $excut );
		} else {
			$output .= $subex;
		}
		$output .= '...';
	} else {
		$output .= $excerpt;
	}

	$output .= '</div>';

	return $output;

}

function dtlms_course_listing_rating($course_id, $type = '') {

	$output = '';

	$average_rating = get_post_meta($course_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$comments = get_approved_comments($course_id);
	$total_comments = count($comments);

	if($type == 'type5') {

	    $output .= '<div class="dtlms-courselist-ratings-container">';
			$output .= '<p class="dtlms-courselist-ratings">'.dtlms_comment_rating_display($average_rating).'</p>';
		$output .= '</div>';

	} else if($type == 'type4') {

	    $output .= '<div class="dtlms-courselist-ratings-container">';
			$output .= '<p class="dtlms-courselist-ratings">'.dtlms_comment_rating_display($average_rating).'</p>';
			//$output .= '<p class="dtlms-courselist-total-reviews">'.sprintf(esc_html__('%1$s Reviews', 'dtlms'), $total_comments).'</p>';
			$output .= '<p class="dtlms-courselist-total-reviews">'.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).'</p>';
		$output .= '</div>';

	} else if($type == 'type3') {

	    $output .= '<div class="dtlms-courselist-ratings-container">';
			$output .= '<p class="dtlms-courselist-ratings">'.dtlms_comment_rating_display($average_rating).'</p>';
			$output .= '<p class="dtlms-courselist-overall-ratings">';
				$output .= esc_attr($average_rating);
				//$output .= '<span class="dtlms-courselist-total-reviews">'.sprintf(esc_html__('%1$s Reviews', 'dtlms'), $total_comments).'</span>';
				$output .= '<span class="dtlms-courselist-total-reviews">'.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).'</span>';
			$output .= '</p>';
		$output .= '</div>';

	} else if($type == 'type2') {

	    $output .= '<div class="dtlms-courselist-ratings-container">';
			$output .= '<p class="dtlms-courselist-ratings">'.dtlms_comment_rating_display($average_rating).'</p>';
			$output .= '<p class="dtlms-courselist-overall-ratings">';
				$output .= esc_attr($average_rating);
			$output .= '</p>';
		$output .= '</div>';

	} else {

		$output .= '<div class="dtlms-courselist-ratings-container">
            			<p class="dtlms-courselist-overall-ratings">
            				<span class="icon-moon icon-moon-star-full"></span>
            				'.sprintf(esc_html__('%1$s Stars', 'dtlms'), $average_rating).'
            			</p>
            		</div>';

	}

	return $output;

}

function dtlms_course_listing_duration($course_id, $design_type = '', $style = '') {

	$duration = dtlms_get_course_duration($course_id, $style, 'course');

	if($design_type == 'type2') {

		if($duration != '') {

			$output = '<div class="dtlms-courselist-duration">
			                <i class="far fa-clock"></i>
			                <span>'.esc_html($duration).'</span>
			            </div>';

		}

    } else {

    	if($duration != '') {

			$output = '<div class="dtlms-courselist-duration">
			                <i class="fas fa-clock"></i>
			                <span>'.esc_html($duration).'</span>
			            </div>';

		}

    }

	return $output;

}

function dtlms_course_listing_author($course_id, $type = '') {

	$author_id = get_the_author_meta( 'ID');

	if($type == 'type6') {

		$user_specialization = get_the_author_meta('user-specialization', $author_id);
		$user_specialization = isset($user_specialization) ? $user_specialization : '';

		$output = '<div class="dtlms-courselist-author-image">
						<a href="'.get_author_posts_url($author_id).'" rel="author">'.get_avatar($author_id, 150).'</a>
	                </div>
	                <div class="dtlms-courselist-author-description">
						<p>
							<a href="'.get_author_posts_url($author_id).'" rel="author">
								'.get_the_author().'
							</a>';
							if($user_specialization != '') {
								$output .= '<span>'.esc_html($user_specialization).'</span>';
							}
			$output .= '</p>
	                </div>';

	} else if($type == 'type5') {

		$instructor_singular = apply_filters( 'instructor_label', 'singular' ); 

		$output = '<h5><a href="'.get_author_posts_url($author_id).'" rel="author">'.get_the_author().'</a><span>'.esc_html($instructor_singular).'</span></h5>';

	} else if($type == 'type2') {

		$user_specialization = get_the_author_meta('user-specialization', $author_id);
		$user_specialization = isset($user_specialization) ? $user_specialization : '';

		$output = '<h5><a href="'.get_author_posts_url($author_id).'" rel="author">'.get_the_author().'</a>';
			if($user_specialization != '') {
				$output .= '<span>'.esc_html($user_specialization).'</span>';
			}		
		$output .= '</h5>';

	} else {

		$user_specialization = get_the_author_meta('user-specialization', $author_id);
		$user_specialization = isset($user_specialization) ? $user_specialization : '';

		$output = '<div class="dtlms-courselist-author-image">
						<a href="'.get_author_posts_url($author_id).'" rel="author">'.get_avatar($author_id, 150).'</a>
	                </div>
	                <div class="dtlms-courselist-author-description">
						<h5>
							<a href="'.get_author_posts_url($author_id).'" rel="author">
								'.get_the_author().'
							</a>';
							if($user_specialization != '') {
								$output .= '<span>'.esc_html($user_specialization).'</span>';
							}
			$output .= '</h5>
	                </div>';

    }

	return $output;

}

function dtlms_course_listing_tags($course_id, $with_icon = false) {

	$icon_html = '';
	if($with_icon) {
		$icon_html = '<i class="fas fa-tag"></i>';
	}

	$output = get_the_term_list($course_id, 'course_category', '<p class="dtlms-courselist-tags">'.$icon_html, ', ', '</p>');

	return $output;

}

function dtlms_course_listing_curriculum_count($course_id) {

	$total_curriculum_count = dtlms_course_curriculum_counts($course_id, true);

	$output = '<p class="dtlms-courselist-curriculum">
					<i class="fas fa-book"></i>'.sprintf(esc_html__('%1$s Curriculum', 'dtlms'), $total_curriculum_count).'
				</p>';

	return $output;

}

function dtlms_course_listing_metadata($course_id) {

	$duration = dtlms_get_course_duration($course_id, 'style3', 'course');

	$started_users = get_post_meta($course_id, 'started_users', true);
	$student_enrolled = count($started_users);

	$output = '<div class="dtlms-courselist-meta">
		        	<ul>
		            	<li>
		                	<label>'.esc_html__('Instructor', 'dtlms').'</label>
		                    <span>'.get_the_author().'</span>
		                </li>
		                <li>
		                	<label>'.esc_html__('Duration', 'dtlms').'</label>
		                    <span>'.esc_attr($duration).'</span>
		                </li>
		                <li>
		                	<label>'.esc_html__('Categories', 'dtlms').'</label>
		                    <span>'.get_the_term_list($course_id, 'course_category', '', ', ', '').'</span>
		                </li>
		                <li>
		                	<label>'.esc_html__('Student Enrolled', 'dtlms').'</label>
		                    <span>'.esc_html($student_enrolled).'</span>
		                </li>
		            </ul>
		        </div>';

	return $output;

}

function dtlms_course_listing_students_enrolled($course_id) {

	$started_users = get_post_meta($course_id, 'started_users', true);
	$student_enrolled = count($started_users);

	$output = '<div class="dtlms-courselist-students-enrolled">
        			<i class="fas fa-users"></i>
        			<span>'.esc_html($student_enrolled).'</span>
        		</div>';

    return $output;

}

function dtlms_course_listing_single_price($purchased_paid_course, $free_course, $woo_price) {

	$output = '';

	if($free_course) {

		$output = '<div class="dtlms-coursedetail-price-details">
						<span class="dtlms-price-status dtlms-free">
							<span class="fa fa-check"></span> '.esc_html__('Free', 'dtlms').'
						</span>
					</div>';

	} else {

		$output = '<div class="dtlms-coursedetail-price-details">
						<span class="dtlms-price-status dtlms-cost">
							'.$woo_price.'
						</span>
					</div>';

	}

	return $output;

}

function dtlms_course_listing_single_addtocart($purchased_paid_course, $free_course, $course_id, $product, $submitted_courses, $completed_courses) {

	$output = '';

	if(!$purchased_paid_course && !$free_course) {

		if(dtlms_check_item_is_in_cart($course_id)) {

			$output .= '<div class="dtlms-coursedetail-cart-details">';
				$output .= '<a href="'.wc_get_cart_url().'" target="_self" class="dtlms-coursedetail-cart-link dtlms-button small filled"><i class="fa fa-cart-plus"></i>'.esc_html__('View Cart','dtlms').'</a>';
			$output .= '</div>';

		} else {

			$allow_purchase = true;

			$course_start_date = get_post_meta ( $course_id, 'course-start-date', true );
			$course_startdate_timestamp = strtotime($course_start_date);
			$current_timestamp = current_time( 'timestamp', 1 );

			$allowpurchases_before_course_startdate = get_post_meta($course_id, 'allowpurchases-before-course-startdate', true);

			if(($current_timestamp >= $course_startdate_timestamp) || (($current_timestamp < $course_startdate_timestamp) && $allowpurchases_before_course_startdate =='true')) {
				$allow_purchase = true;
			} else {
				$allow_purchase = false;
			}

			$course_prerequisite = get_post_meta ( $course_id, 'course-prerequisite', true );
			if($course_prerequisite > 0) {

				$allow_purchase = false;

				$allowpurchases_before_course_prerequisite = get_post_meta($course_id, 'allowpurchases-before-course-prerequisite', true);

				if('true' ==  dtlms_option('course','course-prerequisite-on-complete')) {
					if(in_array($course_prerequisite, $completed_courses) || $allowpurchases_before_course_prerequisite == 'true') {
						$allow_purchase = true;
					}
				} else {
					if(in_array($course_prerequisite, $submitted_courses) || $allowpurchases_before_course_prerequisite == 'true') {
						$allow_purchase = true;
					}
				}
				
			}

			$capacity = get_post_meta ( $course_id, 'capacity', true );
			if($capacity != '' && $capacity > 0) {
				$disable_purchases_over_capacity = get_post_meta($course_id, 'disable-purchases-over-capacity', true);
				$actual_capacity = dtlms_calculate_course_available_seats($course_id, $capacity);
				if($actual_capacity <= 0 && $disable_purchases_over_capacity == 'true') {
					$allow_purchase = false;
				}
			}								

			if($allow_purchase) {
				$output .= '<div class="dtlms-coursedetail-cart-details">';
					$output .= '<a href="'. apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) ) .'" rel="nofollow" data-product_id="'.esc_attr($product->get_id()).'" class="dtlms-button small filled add_to_cart_button ajax_add_to_cart product_type_'.esc_attr($product->get_type()).'"><i class="fa fa-shopping-cart"></i>'.esc_html__('Add to Cart', 'dtlms').'</a>';
				$output .= '</div>';								
			}

		}

	}

	return $output;

}

function dtlms_course_listing_purchase_status($purchased_paid_course, $course_id, $active_package_courses, $purchased_class_courses, $assigned_courses, $purchased_courses) {

	$output = '';

	if($purchased_paid_course) {

		$output .= '<div class="dtlms-courselist-purchase-status-details">';

			if(in_array($course_id, $active_package_courses)) {

				$output .= '<span class="dtlms-purchase-status dtlms-purchased-package">
								'.esc_html__('Purchased Package','dtlms').
							'</span>';

			} else if(in_array($course_id, $purchased_class_courses)) {

				$class_singular_label = apply_filters( 'class_label', 'singular' );

				$output .= '<span class="dtlms-purchase-status dtlms-purchased-class">
								'.sprintf( esc_html__( 'Purchased %1$s', 'dtlms' ), $class_singular_label ).
							'</span>';

			} else if(in_array($course_id, $assigned_courses)) {

				$output .= '<span class="dtlms-purchase-status dtlms-assigned">
								'.esc_html__('Assigned','dtlms').
							'</span>';

			} else if(in_array($course_id, $purchased_courses)) {

				$output .= '<span class="dtlms-purchase-status dtlms-purchased">
								'.esc_html__('Purchased','dtlms').
							'</span>';

			}

		$output .= '</div>';

	}

	return $output;

}

function dtlms_course_listing_progress_details($purchased_paid_course, $free_course, $course_id, $started_courses, $submitted_courses, $completed_courses, $origin) {

	$courses_undergoing = array_diff($started_courses, $submitted_courses);
	$courses_underevaluation = array_diff($submitted_courses, $completed_courses);

	$output = '';

	if($purchased_paid_course || $free_course) {

		$label_class = '';
		if($origin == 'single') {
			$label_class = '<label>'.esc_html__('Status : ', 'dtlms').'</label>';
		}

		if(in_array($course_id, $courses_undergoing)) {

			$output .= '<div class="dtlms-courselist-progress-details">';
				$output .= '<span class="dtlms-undergoing">
								'.$label_class.'
								'.esc_html__('Undergoing', 'dtlms').
							'</span>';
			$output .= '</div>';

		}

		if(in_array($course_id, $courses_underevaluation)) {

			$output .= '<div class="dtlms-courselist-progress-details">';
				$output .= '<span class="dtlms-underevaluation">
								'.$label_class.'
								'.esc_html__('Under Evaluation', 'dtlms').
							'</span>';
			$output .= '</div>';

		}

		if(in_array($course_id, $completed_courses)) {

			$output .= '<div class="dtlms-courselist-progress-details">';
				$output .= '<span class="dtlms-completed">
								'.$label_class.'
								'.esc_html__('Completed', 'dtlms').
							'</span>';
			$output .= '</div>';

		}	

	}

	return $output;

}

?>