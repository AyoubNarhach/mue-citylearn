<?php

// Course Single - Image
function dtlms_course_single_image($course_id) {

	$output = '';

	if(has_post_thumbnail($course_id)) {

		$output .= '<div class="dtlms-course-detail-image">
						'.get_the_post_thumbnail($course_id, 'full').'
					</div>';

	}

	return $output;

}

// Course Single - Title
function dtlms_course_single_title($course_id, $course_title) {

	$output = '';

	if($course_title != '') {

	    $output .= '<div class="dtlms-main-title-section">
				        <h2>'.$course_title.'</h2>
				    </div>';

	}

	return $output;

}

// Course Single - Author
function dtlms_course_single_author($course_id, $author_id, $type) {

	$output = '';

    $output .= '<div class="dtlms-course-detail-author">';
    	if($type == 'type4') {
	    	$output .= '<span>'.esc_html__('Instructor', 'dtlms').'</span>';
	    }
        $output .= '<div class="dtlms-course-detail-author-image">';
            $output .= get_avatar($author_id, 150);
        $output .= '</div>';

        if($type == 'type1' || $type == 'type3') {
        	$output .= '<div class="dtlms-course-detail-author-meta">';
        }

	        $output .= '<div class="dtlms-course-detail-author-title">';
				$output .= '<h5>';
					$output .= '<a href="'.get_author_posts_url($author_id).'" rel="author">';
						$output .= get_the_author_meta('display_name', $author_id);
					$output .= '</a>';
				$output .= '</h5>';

							if($type == 'type2') {
						   		$user_specialization = get_the_author_meta('user-specialization', $author_id);
						   		if($user_specialization != '') {
						   			$output .= '<span>'.$user_specialization.'</span>';
						   		}
							}

	        $output .= '</div>';

			if(is_user_logged_in() && 'true' ==  dtlms_option('course','contact-instructor-in-coursepage')) {
				$output .= '<ul class="dtlms-author-contact-details">';
					if ( class_exists( 'BuddyPress' ) ) {
						if(function_exists('bp_get_messages_slug')) {
							$link = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $author_id ) );
							$output .= '<li><a href="'.esc_url($link).'"><span class="fa fa-send"></span>'.esc_html__('Send Message', 'dtlms').'</a></li>';							
						}
					}
					$user_info = get_userdata($author_id);
					if(isset($user_info->user_email)) {
						$output .= '<li><a href="mailto:'.esc_url($user_info->user_email).'"><span class="far fa-envelope"></span>'.esc_html__('Send Mail', 'dtlms').'</a></li>';
					}
				$output .= '</ul>';
			}

        if($type == 'type1' || $type == 'type3') {
        	$output .= '</div>';
        }			

    $output .= '</div>';

	return $output;

}

// Course Single - Curriculum Count
function dtlms_course_single_curriculum_count($course_id, $author_id) {

	$curriculums_count = dtlms_course_curriculum_counts($course_id, true);

    $output = '<div class="dtlms-course-detail-curriculum">
	                <i class="fas fa-book"></i>
	                '.sprintf( esc_html__( '%1$s Curriculum', 'dtlms' ), $curriculums_count ).'
	            </div>';

	return $output;

}

// Course Single - Review
function dtlms_course_single_review($course_id, $type) {

	$average_rating = get_post_meta($course_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$comments = get_approved_comments($course_id);
	$total_comments = count($comments);

    $output = '<div class="dtlms-course-detail-ratings-container">';
    	if($type == 'type4') {
	    	$output .= '<span>'.esc_html__('Reviews', 'dtlms').'</span>';
	    }
		$output .= '<div class="dtlms-course-detail-ratings">'.dtlms_comment_rating_display($average_rating).'</div>';
		//$output .= '<div class="dtlms-course-detail-total-reviews">( '.sprintf(esc_html__('%1$s Reviews', 'dtlms'), $total_comments).' )</div>';
		$output .= '<div class="dtlms-course-detail-total-reviews">( '.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).' )</div>';
	$output .= '</div>';

	return $output;

}

// Course Single - Info
function dtlms_course_single_info($course_id, $show_title = true, $type = 'type1') {

	$output = '';

	$output .= '<div class="dtlms-courses-detail-holder">';

		if($show_title) {
			$output .= '<div class="dtlms-title">'.esc_html__('Course Info', 'dtlms').'</div>';
		}

		$output .= '<ul class="dtlms-course-detail-info">';

			$class_singular_label = apply_filters( 'class_label', 'singular' );

			if(dtlms_get_course_classes_details($course_id, 'existornot')) {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-class"></span>';
					} else {
						$output .= '<i class="fa fa-institution"></i>';	
					}
					$output .= '<label>'.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_singular_label ).' : </label>';
					$output .= dtlms_get_course_classes_details($course_id, 'links');
				$output .= '</li>';
			}

			if($type == 'type2') {
				$output .=  get_the_term_list( $course_id, 'course_category', '<li><span class="info-category"></span><label>'.esc_html__('Categories', 'dtlms').' : </label>', ', ', '</li>' );
			} else {
				$output .=  get_the_term_list( $course_id, 'course_category', '<li><i class="fa fa-bookmark"></i><label>'.esc_html__('Categories', 'dtlms').' : </label>', ', ', '</li>' );
			}		

			$curriculums_count = dtlms_course_curriculum_counts($course_id, false);
			$curriculums_count = explode('|', $curriculums_count);
			if(isset($curriculums_count) && !empty($curriculums_count)) {
				if(isset($curriculums_count[0]) && !empty($curriculums_count[0])) {
					$output .= '<li>';
						if($type == 'type2') {
							$output .= '<span class="info-lessons"></span>';
						} else {
							$output .= '<i class="fa fa-book"></i>';	
						}					
						$output .= '<label>'.esc_html__('Lessons', 'dtlms').' : </label>';
						$output .= $curriculums_count[0];
					$output .= '</li>';
				}
				if(isset($curriculums_count[1]) && !empty($curriculums_count[1])) {
					$output .= '<li>';
						if($type == 'type2') {
							$output .= '<span class="info-quizzes"></span>';
						} else {
							$output .= '<i class="fa fa-pencil-square"></i>';	
						}					
						$output .= '<label>'.esc_html__('Quizzes', 'dtlms').' : </label>';
						$output .= $curriculums_count[1];
					$output .= '</li>';
				}
				if(isset($curriculums_count[2]) && !empty($curriculums_count[2])) {
					$output .= '<li>';
						if($type == 'type2') {
							$output .= '<span class="info-assignments"></span>';
						} else {
							$output .= '<i class="fa fa-file"></i>';	
						}					
						$output .= '<label>'.esc_html__('Assignments', 'dtlms').' : </label>';
						$output .= $curriculums_count[2];
					$output .= '</li>';
				}
			}

			$duration = dtlms_get_course_duration($course_id, '', 'course');
			if($duration != '') {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-duration"></span>';
					} else {
						$output .= '<i class="fa fa-clock-o"></i>';	
					}				
					$output .= '<label>'.esc_html__('Duration', 'dtlms').' : </label>';
					$output .= $duration;
				$output .= '</li>';
			}

			$reference_url = get_post_meta($course_id, 'reference-url', true);
			if($reference_url != '') {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-reference"></span>';
					} else {
						$output .= '<i class="fa fa-chain-broken"></i>';	
					}				
					$output .= '<label>'.esc_html__('Reference URL', 'dtlms').' : </label>';
					$output .= '<a href="'.esc_url($reference_url).'" target="_new">'.$reference_url.'</a>';
				$output .= '</li>';
			}

			$packages_list = dtlms_get_course_packages($course_id);
			if(is_array($packages_list) && !empty($packages_list)) {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-packages"></span>';
					} else {
						$output .= '<i class="fa fa-ticket"></i>';	
					}				
					$output .= '<label>'.esc_html__('Packages', 'dtlms').' : </label>';
					$package_items_string = '';
					foreach($packages_list as $package) {
						$package_items_string .= '<a href="'.get_permalink($package).'">'.get_the_title($package).'</a>, ';
					}
					$package_items_string = rtrim($package_items_string, ', ');
					$output .= $package_items_string;
				$output .= '</li>';
			}

			$capacity = get_post_meta($course_id, 'capacity', true);
			if($capacity != '' && $capacity > 0) {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-capacity"></span>';
					} else {
						$output .= '<i class="fa fa-group"></i>';	
					}				
					$output .= '<label>'.esc_html__('Capacity', 'dtlms').' </label>: ';
					$output .= dtlms_calculate_course_available_seats($course_id, $capacity);
				$output .= '</li>';	
			}

			$drip_feed = get_post_meta($course_id, 'drip-feed', true);
			if($drip_feed == 'true') {
				$output .= '<li>';
					if($type == 'type2') {
						$output .= '<span class="info-dripfeed"></span>';
					} else {
						$output .= '<i class="fa fa-hourglass-half"></i>';	
					}					
					$output .= '<label>'.esc_html__('Drip Feed', 'dtlms').' </label>: ';
					$output .= esc_html__('Yes', 'dtlms');
				$output .= '</li>';	
			}					

		$output .= '</ul>';
	$output .= '</div>';

	return $output;

}

// Course Single - Featured
function dtlms_course_single_featured($course_id) {

    $output = '';

	$featured_course = get_post_meta($course_id, 'featured-course', true);

	if(isset($featured_course) && $featured_course == 'true') {
		$output .= '<div class="dtlms-course-detail-featured">';
			$output .= '<span class="dtlms-course-detail-featured-text">'.esc_html__('Featured','dtlms').'</span>';
		$output .= '</div>';
	}

	return $output;

}

// Course Single - Certificate & Badge
function dtlms_course_single_certificatenbadge($course_id) {

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

// Course Single - Review Box
function dtlms_course_single_review_box($average_rating, $total_comments, $page_layout) {

    $output = '';

	$output .= '<div class="dtlms-course-detail-review-box">';

		if($page_layout == 'type3') {
			$output .= '<h6>'.esc_html__('Average Rating', 'dtlms').'</h6>';
		}

		$output .= '<div class="dtlms-course-detail-average-value">'.esc_html($average_rating).'</div>';
		$output .= '<div class="dtlms-course-detail-star-review">';
			$output .= dtlms_comment_rating_display($average_rating);
		$output .= '</div>';
		/*$output .= '<div class="dtlms-course-detail-total-reviews">';
			$output .= esc_html($total_comments).' '.esc_html__('Reviews', 'dtlms');
		$output .= '</div>';*/
		$output .= '<div class="dtlms-course-detail-total-reviews">'.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).'</div>';

	$output .= '</div>';

	return $output;

}

// Course Single - Review Rating Splitup
function dtlms_course_single_review_rating_splitup($comments, $total_comments, $page_layout) {

    $output = '';

	$one_star = $two_stars = $three_stars = $four_stars = $five_stars = 0;
	$one_star_percent = $two_stars_percent = $three_stars_percent = $four_stars_percent = $five_stars_percent = 0;

	foreach($comments as $comment) {
		$commentrating = get_comment_meta( $comment->comment_ID, 'lms_rating', true );
		if($commentrating == 1) {
			$one_star++;
		} else if($commentrating == 2) {
			$two_stars++;
		} else if($commentrating == 3) {
			$three_stars++;
		} else if($commentrating == 4) {
			$four_stars++;
		} else if($commentrating == 5) {
			$five_stars++;
		}
	}

	if($total_comments > 0) {
		$one_star_percent = floor(($one_star/$total_comments)*100);
		$two_stars_percent = floor(($two_stars/$total_comments)*100);
		$three_stars_percent = floor(($three_stars/$total_comments)*100);
		$four_stars_percent = floor(($four_stars/$total_comments)*100);
		$five_stars_percent = floor(($five_stars/$total_comments)*100);
	}

	if($page_layout == 'type3') {
		$output .= '<h6>'.esc_html__('Details', 'dtlms').'</h6>';
	}
	
	$output .= '<ul class="dtlms-course-detail-ratings-breakup">
					<li>
						<span class="dtlms-course-detail-ratings-label">'.esc_html__('1 Star', 'dtlms').'</span>
						<div class="dtlms-course-detail-ratings-percentage">
							<span style="width:'.esc_attr($one_star_percent).'%"></span>
						</div>
						<span>'.esc_html($one_star).'</span>
					</li>
					<li>
						<span class="dtlms-course-detail-ratings-label">'.esc_html__('2 Stars', 'dtlms').'</span>
						<div class="dtlms-course-detail-ratings-percentage">
							<span style="width:'.esc_attr($two_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($two_stars).'</span>
					</li>
					<li>
						<span class="dtlms-course-detail-ratings-label">'.esc_html__('3 Stars', 'dtlms').'</span>
						<div class="dtlms-course-detail-ratings-percentage">
							<span style="width:'.esc_attr($three_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($three_stars).'</span>
					</li>
					<li>
						<span class="dtlms-course-detail-ratings-label">'.esc_html__('4 Stars', 'dtlms').'</span>
						<div class="dtlms-course-detail-ratings-percentage">
							<span style="width:'.esc_attr($four_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($four_stars).'</span>
					</li>
					<li>
						<span class="dtlms-course-detail-ratings-label">'.esc_html__('5 Stars', 'dtlms').'</span>
						<div class="dtlms-course-detail-ratings-percentage">
							<span style="width:'.esc_attr($five_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($five_stars).'</span>
					</li>
				</ul>';

	return $output;

}

// Course Single - Tab Content
function dtlms_course_single_tab_content($course_id, $user_id, $author_id, $page_layout) {

	$dtlms_course_curriculum = dtlms_generate_course_curriculum($user_id, $course_id, '', false, -1, $page_layout);

	$started_courses = get_user_meta($user_id, 'started_courses', true);
	$started_courses = (is_array($started_courses) && !empty($started_courses)) ? $started_courses : array ();

	$course_video = get_post_meta($course_id, 'course-video', true);					
	?>

	<div class="dtlms-tabs-horizontal-container">
		<ul class="dtlms-tabs-horizontal">
			<?php
			if(dtlms_check_course_items_visibility('curriculum', $course_id, $user_id)) {
				if($dtlms_course_curriculum != '') {
					?>
					<li>
						<a href="javascript:void(0);"><span class="fa fa-book"></span><?php echo esc_html__('Curriculum', 'dtlms'); ?></a>
					</li>
					<?php
				}
			}

			if($page_layout == 'type5') {
				?>
				<li>
					<a href="javascript:void(0);" class="current"><span class="fab fa-docker"></span><?php echo esc_html__('Course Info', 'dtlms'); ?></a>
				</li>
				<?php
			}
			?>

			<li>
				<a href="javascript:void(0);" class="current"><span class="fab fa-docker"></span><?php echo esc_html__('About', 'dtlms'); ?></a>
			</li>

			<?php
			if(isset($course_video) && $course_video != '') { 
				?>
				<li>
					<a href="javascript:void(0);" class="current"><span class="fa fa-video-camera"></span><?php echo esc_html__('Video', 'dtlms'); ?></a>
				</li>
				<?php
			}

			if(dtlms_check_course_items_visibility('members', $course_id, $user_id)) {
				?>
				<li>
					<a href="javascript:void(0);"><span class="fa fa-user-circle"></span><?php echo esc_html__('Members', 'dtlms'); ?></a>
				</li>
				<?php
			}
			?>
			<li>
				<a href="javascript:void(0);"><span class="fa fa-id-card"></span>
					<?php 
					$label = apply_filters( 'instructor_label', 'plural' ); 
					echo $label;
					?>
				</a>
			</li>
			<?php
			if(class_exists('BuddyPress')) {
				if(dtlms_check_course_items_visibility('buddypressgroup', $course_id, $user_id)) {
					?>
					<li>
						<a href="javascript:void(0);"><span class="fa fa-users"></span><?php echo esc_html__('Group', 'dtlms'); ?></a>
					</li>
					<?php
				}
			}
			if(class_exists('Tribe__Events__Pro__Main')) {
				if(dtlms_check_course_items_visibility('events', $course_id, $user_id)) {
					?>
					<li>
						<a href="javascript:void(0);"><span class="fa fa-calendar"></span><?php echo esc_html__('Events', 'dtlms'); ?></a>
					</li>
					<?php
				}
			}
			if(dtlms_check_course_items_visibility('news', $course_id, $user_id)) {
				?>
				<li>
					<a href="javascript:void(0);"><span class="far fa-newspaper"></span><?php echo esc_html__('News', 'dtlms'); ?></a>
				</li>
				<?php
			}
			$media_attachments_urls = get_post_meta($course_id, 'media-attachment-urls', true);
			if(isset($media_attachments_urls) && !empty($media_attachments_urls)) {
				?>
				<li>
					<a href="javascript:void(0);"><span class="fa fa-object-group"></span><?php echo esc_html__('Media Attachments', 'dtlms'); ?></a>
				</li>
				<?php
				}
			?>									
			<li>
				<a href="javascript:void(0);"><span class="fa fa-star"></span><?php echo esc_html__('Reviews', 'dtlms'); ?></a>
			</li>						
		</ul>

		<div class="dtlms-tabs-horizontal-content" style="display: none;">
			
			<?php 
			if($page_layout == 'type5') {
				echo dtlms_generate_course_startnprogress($course_id, $user_id);
				echo '<div class="dtlms-tabs-horizontal-content-holder">';
			}
			?>

			<div class="dtlms-title"><?php echo esc_html__( 'Curriculum', 'dtlms' ); ?></div>
			<?php
			if(dtlms_check_course_items_visibility('curriculum', $course_id, $user_id)) {
				if($dtlms_course_curriculum != '') {
					echo $dtlms_course_curriculum;
					echo dtlms_generate_loader_html(false);
				}
			}
			?>
			
			<?php 
			if($page_layout == 'type5') {
				echo '</div>';
			}
			?>

		</div>

		<?php
		if($page_layout == 'type5') {
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: block;">
				<?php 
				echo dtlms_course_single_info($course_id, true, 'type2');
				?>				
			</div>
			<?php
		}
		?>

		<div class="dtlms-tabs-horizontal-content" style="display: block;">
			<?php 
			$content = apply_filters('the_content', get_post_field('post_content', $course_id));
			echo ($content);
			?>
		</div>

		<?php
		if(isset($course_video) && $course_video != '') { 
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: none;">
				<div class="dtlms-title"><?php echo esc_html__( 'Course Intro Video', 'dtlms' ); ?></div>
				<div class="dtlms-course-detail-video">
					<?php 
					if(wp_oembed_get( $course_video ) != '') { 
						echo wp_oembed_get( $course_video ); 
					} else { 
						echo wp_video_shortcode( array('src' => $course_video) ); 
					}
					?>
				</div>
			</div>
			<?php 
		}
		?>
		

		<?php
		if(dtlms_check_course_items_visibility('members', $course_id, $user_id)) {
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: none;">
				<div class="dtlms-title"><?php echo esc_html__( 'Members', 'dtlms' ); ?></div>
				<?php
				$started_users = get_post_meta($course_id, 'started_users', true);
				if(!empty($started_users)) {
					echo '<h4 class="dtlms-course-detail-total-students">'.esc_html__('Total number of Students in this course : ', 'dtlms').' <span>'.count($started_users).'</span></h4>';

					echo '<ul class="dtlms-course-detail-students-enrolled-list">';
						foreach($started_users as $student_id) {
							if($student_id > 0) {
								$student_info = get_userdata($student_id);
								if(isset($student_info) && !empty($student_info)) {
									echo '<li>
											'.get_avatar($student_id).'<h5>'.$student_info->display_name.'</h5>
										</li>';
								}
							}
						}
				  	echo '</ul>';
				 } else {
				 	echo '<p>'.esc_html__('No students have enrolled for this course.', 'dtlms').'</span></p>';
				 }
				?>
			</div>
			<?php
		}
		?>

		<div class="dtlms-tabs-horizontal-content" style="display: none;">

			<?php $instructor_plural = apply_filters( 'instructor_label', 'plural' ); ?>

			<div class="dtlms-title"><?php echo esc_html($instructor_plural); ?></div>

			<?php
			$instructors_list = $author_id;

			$coinstructors = get_post_meta($course_id, 'coinstructors', TRUE);
			if(is_array($coinstructors) && !empty($coinstructors)) {
				$instructors_list = $instructors_list.','.implode(',', $coinstructors);							
			}

			if($page_layout == 'type4') {
				$instructor_type = 'type10';
				$columns = 2;
			} else if($page_layout == 'type3') {
				$instructor_type = 'type7';
				$columns = 2;
			} else if($page_layout == 'type2') {
				$instructor_type = 'type8';
				$columns = 3;
			} else {
				$instructor_type = 'type2';
				$columns = 2;
			}

			echo do_shortcode('[dtlms_instructor_list include="'.esc_attr($instructors_list).'" columns="'.esc_attr($columns).'" type="'.esc_attr($instructor_type).'" /]');
			?>

		</div>

		<?php
		if(class_exists('BuddyPress')) {
			if(dtlms_check_course_items_visibility('buddypressgroup', $course_id, $user_id)) {
				?>
				<div class="dtlms-tabs-horizontal-content" style="display: none;">

					<div class="dtlms-title"><?php echo esc_html__( 'Group', 'dtlms' ); ?></div>

					<?php

					$course_group = get_post_meta( $course_id, 'dtlms-course-group-id', true );
					
					if($course_group != '' && $course_group > 0) {
					
						echo '<div class="dtlms-course-detail-group-section">';
					
							if ( function_exists('bp_has_groups') && bp_has_groups( array('include' => $course_group) ) ) : 
								?>
								
								<ul id="groups-list" class="item-list">
								
									<?php while ( bp_groups() ) : bp_the_group(); ?>
									
										<li <?php bp_group_class(); ?>>
										
											<div class="item-avatar">
												<a href="<?php bp_group_permalink(); ?>"><?php bp_group_avatar( 'type=thumb&width=50&height=50' ); ?></a>
											</div>
											
											<div class="item">
												<div class="item-title"><h3><a href="<?php bp_group_permalink(); ?>"><?php bp_group_name(); ?></a></h3></div>
												<div class="item-meta"><span class="activity"><?php printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() ); ?></span></div>
												
												<div class="item-desc"><?php bp_group_description_excerpt(); ?></div>
												
												<?php do_action( 'bp_directory_groups_item' ); ?>
											</div>
												
											<div class="action">
												<?php do_action( 'bp_directory_groups_actions' ); ?>
											
												<div class="meta">
													<?php bp_group_type(); ?> / <?php bp_group_member_count(); ?>
												</div>
											</div>
											
											<div class="clear"></div>
											
										</li>
									
									<?php endwhile; ?>
								
								</ul>
								
								<?php
							endif;
						
					  	echo '</div>';
				
					} else {
						echo esc_html__('No group assigned for this course!', 'dtlms');
					}
					?>

				</div>
				<?php
			}
		}

		if(class_exists( 'Tribe__Events__Pro__Main' )) {
			if(dtlms_check_course_items_visibility('events', $course_id, $user_id)) {
				?>
				<div class="dtlms-tabs-horizontal-content" style="display: none;">

					<?php
						
					$course_event_catids =  get_post_meta( $course_id, 'dtlms-course-event-catid', true );
					
					if(is_array($course_event_catids) && !empty($course_event_catids)) {
						
						echo '<div class="dtlms-title">'.esc_html__( 'Course Event(s)', 'dtlms' ).'</div>';

						$filter_str = '';
						foreach($course_event_catids as $course_event_catid) {
							$filter_str .= '{"tribe_events_cat":["'.$course_event_catid.'"]},';
						}
						$filter_str = rtrim($filter_str, ',');
						
						$instance = array();
						$instance['title'] = '';
						$instance['count'] = 10;
						$instance['filters'] = $filter_str;
						$instance['operand'] = 'OR';

						ob_start();
						the_widget('Tribe__Events__Pro__Mini_Calendar_Widget', $instance);
						$output = ob_get_contents();
						ob_end_clean();

						wp_enqueue_script( 'tribe-mini-calendar', tribe_events_pro_resource_url( 'widget-calendar.js' ), array( 'jquery' ), apply_filters( 'tribe_events_pro_js_version', Tribe__Events__Pro__Main::VERSION ) );

						echo $output;
					
					} else {

						echo '<div class="dtlms-title">'.esc_html__( 'Course Event(s)', 'dtlms' ).'</div>';
						echo esc_html__('No event assigned for this course!', 'dtlms');	
												
					}
					
					?>

				</div>
				<?php
			}
		}

		if(dtlms_check_course_items_visibility('news', $course_id, $user_id)) {
			?>		

			<div class="dtlms-tabs-horizontal-content" style="display: none;">

				<div class="dtlms-title"><?php echo esc_html__( 'News', 'dtlms' ); ?></div>

				<?php
				$course_news_id = get_post_meta( $course_id, 'course-news', true );
				$course_news_id = (is_array($course_news_id) && !empty($course_news_id)) ? $course_news_id : array ();

				if(!empty($course_news_id)):

					$args = array ( 
								'posts_per_page' => -1, 
								'post_type' => 'post',
								'post__in' => $course_news_id
							);

					$posts_query = new WP_Query( $args );

					if ( $posts_query->have_posts() ) :

						$i = 1;
						while ( $posts_query->have_posts() ) : 
							$posts_query->the_post();

							$news_id = get_the_ID();
							$news_title = get_the_title();
							$news_permalink = get_permalink();

							echo '<div class="dtlms-course-detail-news-item">';
									if(has_post_thumbnail($news_id)) {
										echo '<div class="dtlms-course-detail-news-thumb">
												<a href="'.$news_permalink.'" title="'.$news_title.'">';
												echo get_the_post_thumbnail($news_id, 'full');
											echo '</a>';
										echo '</div>';
									}
									echo '<div class="dtlms-course-detail-news-details">';
										echo '<h5><a href="'.$news_permalink.'" title="'.$news_title.'">'.$news_title.'</a></h5>';
										echo '<div class="dtlms-course-detail-news-date">'.get_the_date ( get_option('date_format') ).'</div>';
			                        	echo '<div class="dtlms-course-detail-news-description">'.get_the_excerpt($news_id).'</div>';
									echo '</div>';
							echo '</div>';

						endwhile;
						wp_reset_postdata();

					else :

						echo esc_html__('No news found!', 'dtlms');

					endif;

				else:

					echo esc_html__('No news found!', 'dtlms');

				endif;
				?>

			</div>

			<?php
		}
	
		$media_attachments_urls = get_post_meta($course_id, 'media-attachment-urls', true);
		if(isset($media_attachments_urls) && !empty($media_attachments_urls)) {
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: none;">

				<div class="dtlms-title"><?php echo esc_html__( 'Media Attachments', 'dtlms' ); ?></div>

				<table border="0" cellpadding="0" cellspacing="0" class="dtlms-course-detail-media-attachment">
					<thead>
						<tr>
							<th scope="col"><?php echo esc_html__('#', 'dtlms'); ?></th>
							<th scope="col"><?php echo esc_html__('Title', 'dtlms'); ?></th>
							<?php
							if(in_array($course_id, $started_courses)) {
								?>
								<th scope="col"><?php echo esc_html__('Option', 'dtlms'); ?></th>
								<?php
							}
							?>
						</tr>
					</thead>										
					<tbody>

						<?php
	                    $media_attachments_ids = get_post_meta($course_id, 'media-attachment-ids', true);
	                    $media_attachments_titles = get_post_meta($course_id, 'media-attachment-titles', true);
	                    $media_attachments_icons = get_post_meta($course_id, 'media-attachment-icons', true);
	                    
                        $i = 0;
                        foreach($media_attachments_urls as $media_attachments_url) {
                            if($media_attachments_url != '') {
                            	$attachment_icon = '';
                            	if($media_attachments_icons[$i] != '') {
                            		$attachment_icon = '<span class="'.$media_attachments_icons[$i].'"></span>';
                            	}
                            	echo '<tr>
										<td>'.($i+1).'</td>
										<td>'.$attachment_icon.' '.$media_attachments_titles[$i].'</td>';
										if(in_array($course_id, $started_courses)) {
											echo '<td>'.'<a href="'.$media_attachments_url.'" target="_blank">'.esc_html__('Download', 'dtlms').'</a></td>';
										}
								echo '</tr>';
                            	$i++;
                            }
                        }	
                        ?>														

					</tbody>
				</table>

			</div>

			<?php
		}
		?>							

		<div class="dtlms-tabs-horizontal-content" style="display: none;">

			<div class="dtlms-title"><?php echo esc_html__( 'Reviews', 'dtlms' ); ?></div>

			<?php
			$average_rating = get_post_meta($course_id, 'average-ratings', true);
			$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

			$comments = get_approved_comments($course_id);
			$total_comments = count($comments);

			if($page_layout == 'type2') {
				echo '<div class="dtlms-column dtlms-one-fifth first"></div>';
				echo '<div class="dtlms-column dtlms-three-fifth">';
					echo dtlms_course_single_review_box($average_rating, $total_comments, $page_layout);
					echo dtlms_course_single_review_rating_splitup($comments, $total_comments, $page_layout);
				echo '</div>';
				echo '<div class="dtlms-column dtlms-one-fifth"></div>';
			} else {
				echo '<div class="dtlms-column dtlms-one-third first">';
					echo dtlms_course_single_review_box($average_rating, $total_comments, $page_layout);
				echo '</div>';
				echo '<div class="dtlms-column dtlms-two-third">';
					echo dtlms_course_single_review_rating_splitup($comments, $total_comments, $page_layout);
				echo '</div>';								
			}
			?>

			<?php comments_template( '', true); ?>

		</div>

	</div>

	<?php
}
?>