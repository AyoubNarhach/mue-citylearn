<?php

// Listing

function dtlms_class_listing_thumb($class_id, $class_title, $class_permalink, $display_type, $column) {

	$output = '';

	if(has_post_thumbnail($class_id)) {

		$image_size = 'full';
		if($column == 3) {
			$image_size = 'dtlms-640x430';
		} else if($column == 2) {
			$image_size = 'dtlms-960x640';
		}

		if($display_type == 'list-item') {

			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id($class_id), $image_size, false);
			$output .= '<a href="'.esc_url($class_permalink).'" title="'.esc_attr($class_title).'">
							<div class="dtlms-classlist-thumb-inner" style="background:url('.esc_url($image_src[0]).');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($class_permalink).'" title="'.esc_attr($class_title).'">'.get_the_post_thumbnail($class_id, $image_size).'</a>';

		}

	} else {

		if($display_type == 'list-item') {

			$output .= '<a href="'.esc_url($class_permalink).'" title="'.esc_attr($class_title).'">
							<div class="dtlms-classlist-thumb-inner" style="background:url('.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($class_permalink).'" title="'.esc_attr($class_title).'"><img src="'.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').'" alt="'.esc_attr($class_title).'" title="'.esc_attr($class_title).'" /></a>';

		}

	}

	return $output;

}

function dtlms_class_listing_single_featured($class_id) {

    $output = '';

	$featured_class = get_post_meta($class_id, 'dtlms-class-featured', true);

	if(isset($featured_class) && $featured_class == 'true') {
		$output .= '<div class="dtlms-class-listing-featured">';
			$output .= '<span class="dtlms-class-listing-featured-text">'.esc_html__('Featured','dtlms').'</span>';
		$output .= '</div>';
	}

	return $output;

}
function dtlms_class_listing_single_class_type($class_id, $class_type) {

	$output = '';

	$class_type_label = '';
	if($class_type == 'online') {
		$class_type_label = esc_html__( 'Online', 'dtlms' );
	} else if($class_type == 'onsite') {
		$class_type_label = esc_html__( 'Onsite', 'dtlms' );	
	}

	if($class_type != '' && $class_type != 'default') {
		$output .= '<span class="dtlms-class-type '.$class_type.'">'.$class_type_label.'</span>';
	}	
					
	return $output;

}

function dtlms_class_listing_total_courses($class_id, $type) {

	$output = '';

	$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);

	if($class_content_options == 'course') {

		$class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
		if(is_array($class_courses) && !empty($class_courses)) {
			$total_curriculum_count = count($class_courses);
		}

		if($total_curriculum_count > 0) {
		    $output .= '<div class="dtlms-classlist-metadata">';
				$output .= '<p>';
					if($type == 'type1') {
					    $output .= '<i class="fas fa-book"></i>';
					}
				    $output .= $total_curriculum_count.' '.esc_html__('Courses', 'dtlms');
				$output .= '</p>';
			$output .= '</div>';
		}

	}
					
	return $output;

}

function dtlms_class_listing_title($class_id, $class_title, $class_permalink) {

	$output = '';

	$output .= '<h5><a href="'.esc_url($class_permalink).'" title="'.esc_attr($class_title).'">'.esc_html($class_title).'</a></h5>';
					
	return $output;

}

function dtlms_class_listing_description($class_id) {

	$output = '';

	$output .= '<div class="dtlms-classlist-description">
		            '.get_the_excerpt($class_id).'
		        </div>';
					
	return $output;

}

function dtlms_class_listing_author($class_id) {

	$author_id = get_the_author_meta( 'ID');

	$user_specialization = get_the_author_meta('user-specialization', $author_id);
	$user_specialization = isset($user_specialization) ? $user_specialization : '';

    $output = '<div class="dtlms-classlist-instructor-item">
			        '.get_avatar($author_id, 150).'
			        <div class="dtlms-instructor-item-meta-data">
			            <p><a href="'.get_author_posts_url($author_id).'" rel="author">'.get_the_author().'</a></p>';
			            if($user_specialization != '') {
				            $output .= '<span>'.esc_html($user_specialization).'</span>';
				        }			            
		$output .= '</div>
			    </div>';

	return $output;

}

function dtlms_class_listing_review($class_id, $type = '') {

	$average_rating = get_post_meta($class_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$comments = get_approved_comments($class_id);
	$total_comments = count($comments);

    $output = '<div class="dtlms-class-detail-ratings-container">';
		$output .= '<div class="dtlms-class-detail-ratings">'.dtlms_comment_rating_display($average_rating).'</div>';
		$output .= '<div class="dtlms-class-detail-average-ratings">'.esc_html($average_rating).'</div>';
		if($type != 'type3') {
			//$output .= '<div class="dtlms-class-detail-total-reviews">( '.sprintf(esc_html__('%1$s Reviews', 'dtlms'), $total_comments).' )</div>';
			$output .= '<div class="dtlms-class-detail-total-reviews">( '.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).' )</div>';
		}
	$output .= '</div>';

	return $output;

}


// Detail

// Class Single - Image
function dtlms_class_single_image($class_id) {

	$output = '';

	if(has_post_thumbnail($class_id)) {

		$output .= '<div class="dtlms-class-detail-image">
						'.get_the_post_thumbnail($class_id, 'full').'
					</div>';

	}

	return $output;

}

// Class Single - Title
function dtlms_class_single_title($class_id, $class_title) {

	$output = '';

	if($class_title != '') {

	    $output .= '<div class="dtlms-main-title-section">
				        <h2>'.$class_title.'</h2>
				    </div>';

	}

	return $output;

}

// Class Single - Author
function dtlms_class_single_author($class_id, $author_id, $type) {

	$output = '';

    $output .= '<div class="dtlms-class-detail-author">';
    	if($type == 'type4') {
	    	$output .= '<span>'.esc_html__('Instructor', 'dtlms').'</span>';
	    }
        $output .= '<div class="dtlms-class-detail-author-image">';
            $output .= get_avatar($author_id, 150);
        $output .= '</div>';

        if($type == 'type1' || $type == 'type3') {
        	$output .= '<div class="dtlms-class-detail-author-meta">';
        }

	        $output .= '<div class="dtlms-class-detail-author-title">';
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

			if(is_user_logged_in() && 'true' ==  dtlms_option('class','contact-instructor-in-classpage')) {
				$output .= '<ul class="dtlms-author-contact-details">';
					if ( class_exists( 'BuddyPress' ) ) {
						if(function_exists('bp_get_messages_slug')) {
							$link = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $author_id ) );
							$output .= '<li><a href="'.esc_url($link).'"><span class="fa fa-send"></span>'.esc_html__('Send Message', 'dtlms').'</a></li>';
						}
					}
					$user_info = get_userdata($author_id);
					$output .= '<li><a href="mailto:'.esc_url($user_info->user_email).'"><span class="far fa-envelope"></span>'.esc_html__('Send Mail', 'dtlms').'</a></li>';
				$output .= '</ul>';
			}

        if($type == 'type1' || $type == 'type3') {
        	$output .= '</div>';
        }			

    $output .= '</div>';

	return $output;

}

// Class Single - Curriculum Count
function dtlms_class_single_courses_count($class_id, $page_layout) {

	$output = '';

	$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);

	if($class_content_options == 'course') {

		$total_curriculum_count = 0;

		$class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
		if(is_array($class_courses) && !empty($class_courses)) {
			$total_curriculum_count = count($class_courses);
		}

		if($total_curriculum_count > 0) {

			if($page_layout == 'type4') {

			    $output .= '<div class="dtlms-class-detail-curriculum">
			                    <span>'.esc_html__('Courses', 'dtlms').'</span>
			                    '.sprintf(esc_html__('%1$s Items', 'dtlms'), $total_curriculum_count).'
			                </div>';

		    } else if($page_layout == 'type3' || $page_layout == 'type1') {

			    $output .= '<div class="dtlms-class-detail-curriculum">
	                            <i class="fas fa-book"></i>
	                            '.sprintf(esc_html__('%1$s Courses', 'dtlms'), $total_curriculum_count).'
	                        </div>';

		    }

	    }

	}

	return $output;

}

// Class Single - Review
function dtlms_class_single_review($class_id, $type) {

	$average_rating = get_post_meta($class_id, 'average-ratings', true);
	$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

	$comments = get_approved_comments($class_id);
	$total_comments = count($comments);

    $output = '<div class="dtlms-class-detail-ratings-container">';
    	if($type == 'type4') {
	    	$output .= '<span>'.esc_html__('Reviews', 'dtlms').'</span>';
	    }
		$output .= '<div class="dtlms-class-detail-ratings">'.dtlms_comment_rating_display($average_rating).'</div>';
		//$output .= '<div class="dtlms-class-detail-total-reviews">( '.sprintf(esc_html__('%1$s Reviews', 'dtlms'), $total_comments).' )</div>';
		$output .= '<div class="dtlms-class-detail-total-reviews">( '.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).' )</div>';
	$output .= '</div>';

	return $output;

}

// Class Single - Price
function dtlms_class_listing_single_price($purchased_paid_class, $free_class, $woo_price) {

	$output = '';

	if(!$free_class) {

		$output .= '<div class="dtlms-classdetail-price-details">
						<span class="dtlms-price-status dtlms-cost">
							'.$woo_price.'
						</span>
					</div>';

	}

	return $output;

}

// Class Single - Add to cart
function dtlms_class_listing_single_addtocart($purchased_paid_class, $class_id, $free_class, $user_id) {

	$output = '';

	if(!$purchased_paid_class) {

		$class_start_date = get_post_meta ( $class_id, 'dtlms-class-start-date', true );
		$class_startdate_timestamp = strtotime($class_start_date);
		$current_timestamp = current_time( 'timestamp', 1 );

		if($current_timestamp < $class_startdate_timestamp) {

		} else {

			$product = dtlms_get_product_object($class_id);

			$class_type = get_post_meta($class_id, 'dtlms-class-type', true);

			if($class_type == 'online') {

				if(class_exists('WooCommerce')) {

					if(!$free_class) {

						if(dtlms_check_item_is_in_cart($class_id)) {

							$output .= '<div class="dtlms-classdetail-cart-details">';
								$output .= '<a href="'.wc_get_cart_url().'" target="_self" class="dtlms-classdetail-cart-link dtlms-button small filled"><i class="fa fa-cart-plus"></i>'.esc_html__('View Cart','dtlms').'</a>';
							$output .= '</div>';

						} else {

							$output .= '<div class="dtlms-classdetail-cart-details">';
								$output .= '<a href="'. apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) ) .'" rel="nofollow" data-product_id="'.esc_attr($product->get_id()).'" class="dtlms-button small filled add_to_cart_button ajax_add_to_cart product_type_'.esc_attr($product->get_type()).'"><i class="fa fa-shopping-cart"></i>'.esc_html__('Add to Cart', 'dtlms').'</a>';
							$output .= '</div>';								

						}

					}

				}

			} else if($class_type == 'onsite') {

				$class_disable_purchases_registration = get_post_meta($class_id, 'dtlms-class-disable-purchases-regsitration', true);
				$class_enable_purchases = get_post_meta($class_id, 'dtlms-class-enable-purchases', true);
				$class_enable_registration = get_post_meta($class_id, 'dtlms-class-enable-registration', true);

				$class_capacity = get_post_meta($class_id, 'dtlms-class-capacity', true);
				$class_capacity = ($class_capacity != '') ? $class_capacity : 'NA';


		        if($class_enable_purchases == 'true') {

					if(class_exists('WooCommerce')) {

						if(!$free_class) {

							$purchased_users = get_post_meta($class_id, 'purchased_users', true);
							$seats_alloted = (is_array($purchased_users) && !empty($purchased_users)) ? count($purchased_users) : 0;

							if($class_capacity == 'NA') {
								$available_seats = 1;
							} else {
								if($seats_alloted > 0) {
									$available_seats = $class_capacity - $seats_alloted;
								} else {
									$available_seats = $class_capacity;
								}
							}

							if(dtlms_check_item_is_in_cart($class_id)) {

								$output .= '<div class="dtlms-classdetail-cart-details">';
									$output .= '<a href="'.wc_get_cart_url().'" target="_self" class="dtlms-classdetail-cart-link dtlms-button small filled"><i class="fa fa-cart-plus"></i>'.esc_html__('View Cart','dtlms').'</a>';
								$output .= '</div>';

							} else {

								if($available_seats > 0 || ($available_seats <= 0 && $class_disable_purchases_registration != 'true')) {

									$output .= '<div class="dtlms-classdetail-cart-details">';
										$output .= '<a href="'. apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) ) .'" rel="nofollow" data-product_id="'.esc_attr($product->get_id()).'" class="dtlms-button small filled add_to_cart_button ajax_add_to_cart product_type_'.esc_attr($product->get_type()).'"><i class="fa fa-shopping-cart"></i>'.esc_html__('Add to Cart', 'dtlms').'</a>';
									$output .= '</div>';

								}								

							}

						}

					}

				} else if($class_enable_registration == 'true') {

					if($class_capacity == 'NA') {

						$available_seats = 1;
						$registered_users = array ();

					} else {

						$registered_users = get_post_meta($class_id, 'registered_users', true);
						$registered_users = (is_array($registered_users) && !empty($registered_users)) ? $registered_users : array ();
						$seats_alloted = count($registered_users);

						$seats_alloted_anonymous = 0;
						if(dtlms_option('class','class-registration-without-login') == 'true') {
							$registered_users_anonymous = get_post_meta($class_id, 'registered_users_anonymous', true);
							$seats_alloted_anonymous = (is_array($registered_users_anonymous) && !empty($registered_users_anonymous)) ? count($registered_users_anonymous) : 0;			
						}

						$seats_alloted = $seats_alloted + $seats_alloted_anonymous;
						$available_seats = $class_capacity - $seats_alloted;

					}

					if(is_user_logged_in()) {

						$registered_users = array_keys($registered_users);
						if(in_array($user_id, $registered_users)) {

							$output .= '<div class="dtlms-item-status-details"><span class="dtlms-applied">
											<span class="fa fa-check"></span>'.esc_html__('Applied', 'dtlms').
										'</span></div>';

						} else {

							if($available_seats > 0 || ($available_seats <= 0 && $class_disable_purchases_registration != 'true')) {
								$output .= '<div class="dtlms-item-status-details"><div class="dtlms-proceed-button">
												<a href="#" class="dtlms-button dtlms-apply-onsite-class large" data-classid="'.$class_id.'" data-userid="'.$user_id.'">'.esc_html__('Apply', 'dtlms').'</a>
											</div></div>';
							}

						}

					} else {

						if(dtlms_option('class','class-registration-without-login') == 'true') {

							if($available_seats > 0 || ($available_seats <= 0 && $class_disable_purchases_registration != 'true')) {
								$output .= '<div class="dtlms-item-status-details"><div class="dtlms-proceed-button"><a href="#" class="dtlms-button dtlms-register-onsite-class small" data-classid="'.$class_id.'">'.esc_html__('Register', 'dtlms').'</a></div></div>';
							} else {
								$output .= '<div class="dtlms-item-status-details"><div class="dtlms-proceed-button"><a href="#" class="dtlms-button dtlms-registration-closed small">'.esc_html__('Regsitration Closed', 'dtlms').'</a></div></div>';
							}

						} else {
							$output .= '<div class="dtlms-item-status-details"><div class="dtlms-proceed-button"><input type="button" class="dtlms-button dtlms-login-link" value="'.esc_html__('Login To Apply','dtlms').'" /></div></div>';
						}

					}

				}

			}

		}

	}

	return $output;

}

// Class Single - Seats Available
function dtlms_class_single_seats_available($class_id, $class_type, $page_layout) {

	$output = '';

	if($class_type == 'onsite') {
		$seats_available = dtlms_calculate_class_available_seats($class_id);
		if($seats_available < 0) {
			$seats_available = 0;
		}
		if($page_layout == 'type4') {
			$output .= '<div class="dtlms-classdetail-seats-available">
							<span>'.esc_html__('Seats', 'dtlms').' </span>'.esc_html__('Available : ', 'dtlms').$seats_available.'
						</div>';
		} else {
			$output .= '<div class="dtlms-classdetail-seats-available">
							<i class="fa fa-institution"></i>
							<label>'.esc_html__('Seats Available', 'dtlms').' : </label><span>'.$seats_available.'</span>
						</div>';					
		}
	}

	return $output;

}

// Class Single - Review Box
function dtlms_class_single_review_box($average_rating, $total_comments, $page_layout) {

    $output = '';

	$output .= '<div class="dtlms-class-detail-review-box">';

		if($page_layout == 'type3') {
			$output .= '<h6>'.esc_html__('Average Rating', 'dtlms').'</h6>';
		}

		$output .= '<div class="dtlms-class-detail-average-value">'.esc_html($average_rating).'</div>';
		$output .= '<div class="dtlms-class-detail-star-review">';
			$output .= dtlms_comment_rating_display($average_rating);
		$output .= '</div>';
/*		$output .= '<div class="dtlms-class-detail-total-reviews">';
			$output .= esc_html($total_comments).' '.esc_html__('Reviews', 'dtlms');
		$output .= '</div>';*/
		$output .= '<div class="dtlms-class-detail-total-reviews">'.sprintf( _n( '%d Review', '%d Reviews', $total_comments, 'dtlms' ), number_format_i18n($total_comments) ).'</div>';

	$output .= '</div>';

	return $output;

}

// Class Single - Review Rating Splitup
function dtlms_class_single_review_rating_splitup($comments, $total_comments, $page_layout) {

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
	
	$output .= '<ul class="dtlms-class-detail-ratings-breakup">
					<li>
						<span class="dtlms-class-detail-ratings-label">'.esc_html__('1 Star', 'dtlms').'</span>
						<div class="dtlms-class-detail-ratings-percentage">
							<span style="width:'.esc_attr($one_star_percent).'%"></span>
						</div>
						<span>'.esc_html($one_star).'</span>
					</li>
					<li>
						<span class="dtlms-class-detail-ratings-label">'.esc_html__('2 Stars', 'dtlms').'</span>
						<div class="dtlms-class-detail-ratings-percentage">
							<span style="width:'.esc_attr($two_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($two_stars).'</span>
					</li>
					<li>
						<span class="dtlms-class-detail-ratings-label">'.esc_html__('3 Stars', 'dtlms').'</span>
						<div class="dtlms-class-detail-ratings-percentage">
							<span style="width:'.esc_attr($three_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($three_stars).'</span>
					</li>
					<li>
						<span class="dtlms-class-detail-ratings-label">'.esc_html__('4 Stars', 'dtlms').'</span>
						<div class="dtlms-class-detail-ratings-percentage">
							<span style="width:'.esc_attr($four_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($four_stars).'</span>
					</li>
					<li>
						<span class="dtlms-class-detail-ratings-label">'.esc_html__('5 Stars', 'dtlms').'</span>
						<div class="dtlms-class-detail-ratings-percentage">
							<span style="width:'.esc_attr($five_stars_percent).'%"></span>
						</div>
						<span>'.esc_html($five_stars).'</span>
					</li>
				</ul>';

	return $output;

}



// Class List / Single 

// Class List / Single - Certificate & Badge
function dtlms_class_listing_single_certificatenbadge($class_id) {

    $output = '';

	$enable_certificate = get_post_meta($class_id, 'enable-certificate', true);
	$enable_badge = get_post_meta($class_id, 'enable-badge', true);

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

// Class List / Single - Progress Details
function dtlms_class_listing_single_progress_details($purchased_paid_class, $free_class, $class_id, $started_classes, $submitted_classes, $completed_classes, $origin) {

	$classes_undergoing = array_diff($started_classes, $submitted_classes);
	$classes_underevaluation = array_diff($submitted_classes, $completed_classes);

	$output = '';

	if($purchased_paid_class || $free_class) {

		$label_class = '';
		if($origin == 'single') {
			$label_class = '<label>'.esc_html__('Status : ', 'dtlms').'</label>';
		}

		if(in_array($class_id, $classes_undergoing)) {

			$output .= '<div class="dtlms-class-progress-details">';

				$output .= '<span class="dtlms-undergoing">
								'.$label_class.'
								'.esc_html__('Undergoing', 'dtlms').
							'</span>';

			$output .= '</div>';

		}

		if(in_array($class_id, $classes_underevaluation)) {

			$output .= '<div class="dtlms-class-progress-details">';

				$output .= '<span class="dtlms-underevaluation">
								'.$label_class.'
								'.esc_html__('Under Evaluation', 'dtlms').
							'</span>';

			$output .= '</div>';

		}

		if(in_array($class_id, $completed_classes)) {

			$output .= '<div class="dtlms-class-progress-details">';

				$output .= '<span class="dtlms-completed">
								'.$label_class.'
								'.esc_html__('Completed', 'dtlms').
							'</span>';

			$output .= '</div>';

		}	

	}

	return $output;

}

// Class List / Single - Purchase Status
function dtlms_class_listing_single_purchase_status($purchased_paid_class, $class_id, $active_package_classes, $assigned_classes, $purchased_classes) {

	$output = '';

	if($purchased_paid_class) {

		$output .= '<div class="dtlms-classlist-purchase-status-details">';

			if(in_array($class_id, $active_package_classes)) {

				$output .= '<span class="dtlms-purchase-status dtlms-purchased-package">
								'.esc_html__('Purchased Package','dtlms').
							'</span>';

			} else if(in_array($class_id, $assigned_classes)) {

				$output .= '<span class="dtlms-purchase-status dtlms-assigned">
								'.esc_html__('Assigned','dtlms').
							'</span>';

			} else if(in_array($class_id, $purchased_classes)) {

				$output .= '<span class="dtlms-purchase-status dtlms-purchased">
								'.esc_html__('Purchased','dtlms').
							'</span>';

			}

		$output .= '</div>';

	}

	return $output;

}


// Class Single - Tab Content
function dtlms_class_single_tab_content($class_id, $user_id, $author_id, $page_layout) {

	$class_title = get_the_title();

	$class_maintabtitle = get_post_meta($class_id, 'dtlms-class-maintabtitle', true);
	if($class_maintabtitle == '') {
		$class_maintabtitle = esc_html__('About', 'dtlms');
	}
	
	$class_accessories_tabtitle = get_post_meta($class_id, 'dtlms-class-accessories-tabtitle', true);
	if($class_accessories_tabtitle == '') {
		$class_accessories_tabtitle = esc_html__('Accessories', 'dtlms');
	}
	
	$class_content_title = get_post_meta($class_id, 'dtlms-class-content-title', true);
	
	$class_content_options = get_post_meta($class_id, 'dtlms-class-content-options', true);

	if($class_content_options != '') {
		
		if($class_content_title != '') {
			
			$content_label = $class_content_title;
			
		} else {
			
			if($class_content_options == 'shortcode') {
				$content_label = esc_html__('Schedule', 'dtlms');
			} else {
				$content_label = esc_html__('Courses', 'dtlms');
			}
		
		}
	
	}

	$class_type = get_post_meta($class_id, 'dtlms-class-type', true);
	
	$class_event_catids =  get_post_meta( $class_id, 'dtlms-class-event-catid', true );

	$class_accessories_value = get_post_meta($class_id, 'dtlms-class-accessories-value', true);
	
	$class_shyllabus_preview = '';
	if($class_type == 'onsite') {
		$class_shyllabus_preview = get_post_meta($class_id, 'dtlms-class-shyllabus-preview', true);
	}							
	?>

	<div class="dtlms-tabs-horizontal-container">
		<ul class="dtlms-tabs-horizontal">
			<?php
			if($class_content_options != '') {
				?>
				<li>
					<a href="javascript:void(0);"><?php echo esc_html($content_label); ?></a>
				</li>							
                <?php
			}
			?>
			<li>
				<a href="javascript:void(0);" class="current"><?php echo esc_html($class_maintabtitle); ?></a>
			</li>
			<?php
			if(is_array($class_accessories_value) && !empty($class_accessories_value)) {
				?>
				<li>
					<a href="javascript:void(0);"><?php echo esc_html($class_accessories_tabtitle); ?></a>
				</li>
                <?php
            }
			if(class_exists( 'Tribe__Events__Pro__Main' )) {
				echo '<li>
						<a href="javascript:void(0);">'.esc_html__('Events', 'dtlms').'</a>
					</li>';
			}

            $class_tabs_title = get_post_meta($class_id, 'dtlms-class-tabs-title', true);                    
            if(isset($class_tabs_title) && is_array($class_tabs_title)) {
                foreach($class_tabs_title as $class_tab_title) {
					$class_tab_title_id = str_replace(' ', '', $class_tab_title);
					$class_tab_title_id = strtolower(trim($class_tab_title_id));
					echo '<li>
							<a href="javascript:void(0);">'.esc_html($class_tab_title).'</a>
						</li>';								
                }
            }
			
			if($class_type == 'onsite') {
				$class_gps = get_post_meta($class_id, 'dtlms-class-gps', true);
            	if((isset($class_gps['latitude']) && $class_gps['latitude'] != '') && (isset($class_gps['longitude']) && $class_gps['longitude'] != '')) {						
					echo '<li>
							<a href="javascript:void(0);">'.esc_html__('Location', 'dtlms').'</a>
						</li>';								
				}
			}											
			?>
			<li>
				<a href="javascript:void(0);"><?php echo esc_html__('Reviews', 'dtlms'); ?></a>
			</li>
		</ul>

		<?php
		if($class_content_options != '') {
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: none;">
                <h2 class="dtlms-title"><?php echo esc_html($content_label); ?></h2>
                <div class="column dtlms-one-column first">
                    <?php
                    
                    if($class_content_options == 'shortcode') {
                        
                        $class_shortcode = get_post_meta($class_id, 'dtlms-class-shortcode', true);
                        
                        echo do_shortcode($class_shortcode);
                    
                    } else if($class_content_options == 'course') {
                    
                        $class_courses = get_post_meta($class_id, 'dtlms-class-courses', true);
                        
                        if(is_array($class_courses) && !empty($class_courses)) {
                            foreach($class_courses as $course_id) {
                                
                                $course_status_html = '';
								$completed_courses = get_user_meta($user_id, 'completed_courses', true);
								$completed_courses = (is_array($completed_courses) && !empty($completed_courses)) ? $completed_courses : array();
								if(in_array($course_id, $completed_courses)) {
									$course_status_html .= '<span class="dtlms-completed">
																<span class="fa fa-check"></span>'.
															'</span>';
								}


								$curriculums_count = dtlms_course_curriculum_counts($course_id, false);
								$curriculums_count = explode('|', $curriculums_count);
								$courses_overview = '<ul class="dtlms-course-curriculum-overview">';
								if(isset($curriculums_count) && !empty($curriculums_count)) {
									if(isset($curriculums_count[0]) && !empty($curriculums_count[0])) {
										$courses_overview .= '<li><span class="fa fa-book"></span>'.esc_html__('Lessons', 'dtlms').' : '.$curriculums_count[0].'</li>';
									}
									if(isset($curriculums_count[1]) && !empty($curriculums_count[1])) {
										$courses_overview .= '<li><span class="fa fa-pencil-square"></span>'.esc_html__('Quizzes', 'dtlms').' : '.$curriculums_count[1].'</li>';
									}
									if(isset($curriculums_count[2]) && !empty($curriculums_count[2])) {
										$courses_overview .= '<li><span class="fa fa-file"></span>'.esc_html__('Assignments', 'dtlms').' : '.$curriculums_count[2].'</li>';
									}
								}
								$courses_overview .= '</ul>';

                                
                                echo '<div class="dtlms-toggle-group-set">
                                        <h5 class="dtlms-toggle active">';
                                            if($class_shyllabus_preview == 'true') {
                                                $course_link = '#';
                                                $preview_curriculum = true;
                                            } else {
                                                $course_link = get_permalink($course_id);
                                                $preview_curriculum = false;
                                            }
                                            echo '<a href="'.$course_link.'">'.get_the_title($course_id).$course_status_html.'</a>
                                        </h5>
                                        <div class="dtlms-toggle-content" style="display: block;">
                                            <div class="block">
                                            	<div class="dtlms-column dtlms-one-half first">
                                                	'.$courses_overview.'
                                                </div>
                                                <div class="dtlms-column dtlms-one-half"> 
                                                	'.dtlms_generate_course_startnprogress($course_id, $user_id).'
                                                </div>
                                                <div class="dtlms-class-course-curriculum-holder">
                                                	'.dtlms_generate_course_curriculum($user_id, $course_id, '', $preview_curriculum, -1, '').'
                                                	'.dtlms_generate_loader_html(false).'
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                                
                            }
                        } else {
                        	echo esc_html__('No courses added!', 'dtlms');
                        }
                    
                    }
                    
                    ?>
                </div>                     
            </div>
            <?php 
		}
		?>

        <div class="dtlms-tabs-horizontal-content" style="display: block;">
            <div class="column dtlms-one-column first">
                <?php the_content(); ?>
            </div>                 
        </div>

        <?php
        if(is_array($class_accessories_value) && !empty($class_accessories_value)) {
        	?>
            <div class="dtlms-tabs-horizontal-content" style="display: none;">
                <h2 class="dtlms-title"><?php echo esc_html($class_accessories_tabtitle); ?></h2>
                <div class="column dtlms-one-column first">
					<?php 
                    $class_accessories_icon = get_post_meta($class_id, 'dtlms-class-accessories-icon', true);
                    $class_accessories_label = get_post_meta($class_id, 'dtlms-class-accessories-label', true);
					$class_accessories_description = get_post_meta($class_id, 'dtlms-class-accessories-description', true);

                    $j = 0;
					echo '<ul class="dtlms-acessories-list">';
                        foreach($class_accessories_value as $classaccessoryvalue) {

	                        $classaccessoriesicon = $classaccessorieslabel = $classaccessoriesdescription = '';
	                        if(isset($class_accessories_icon[$j])) {
	                            $classaccessoriesicon = $class_accessories_icon[$j];
	                        }
	                        if(isset($class_accessories_label[$j])) {
	                            $classaccessorieslabel = $class_accessories_label[$j];
	                        }
	                        if(isset($class_accessories_description[$j])) {
	                            $classaccessoriesdescription = $class_accessories_description[$j];
	                        } 

							if($page_layout == 'type1' || $page_layout == 'type4') {
								echo '<li><span class="'.$classaccessoriesicon.'"></span><label>'.$classaccessorieslabel.' : </label>'.$classaccessoryvalue.' <p>'.$classaccessoriesdescription.'</p></li>';
							} else if($page_layout == 'type2' || $page_layout == 'type3') {
								echo '<li>
										<div class="dtlms-acessories-list-meta">
											<span class="'.$classaccessoriesicon.'"></span>
											<p><label>'.$classaccessorieslabel.' : </label>'
											.$classaccessoryvalue.
											'</p>'.
										'</div>'.
										' <p>'.$classaccessoriesdescription.'</p>
									</li>';
							}

							$j++;

                        }
					echo '</ul>';


                    ?>
                </div>                  
            </div>
            <?php
        }

		if(class_exists( 'Tribe__Events__Pro__Main' )) {
			?>
			<div class="dtlms-tabs-horizontal-content" style="display: none;">
				<?php
				if(is_array($class_event_catids) && !empty($class_event_catids)) {
					?>
					<h2 class="dtlms-title"><?php echo esc_html__('Events', 'dtlms'); ?></h2>
					<div class="column dtlms-one-column first">
						<?php
						$filter_str = '';
						foreach($class_event_catids as $class_event_catid) {
							$filter_str .= '{"tribe_events_cat":["'.$class_event_catid.'"]},';
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

						echo $output;
						?>
					</div>
				
					<?php
				} else {

					echo '<h2 class="dtlms-title">'.esc_html__( 'Course Event(s)', 'dtlms' ).'</h2>';
					echo esc_html__('No event assigned for this class!', 'dtlms');	

				}
				?>
			</div>
			<?php
		}		

		
        $class_tabs_title = get_post_meta ( $class_id, "dtlms-class-tabs-title", true);
        $class_tabs_content = get_post_meta ( $class_id, "dtlms-class-tabs-content", true);
        
        $j = 0;
        if(isset($class_tabs_content) && is_array($class_tabs_content)) {
            foreach($class_tabs_content as $class_tab_content) {
				$class_tab_title_id = str_replace(' ', '', $class_tabs_title[$j]);
				$class_tab_title_id = strtolower(trim($class_tab_title_id));
            	?>
            	<div class="dtlms-tabs-horizontal-content" style="display: none;">
                    <h2 class="dtlms-title"><?php echo esc_html($class_tabs_title[$j]); ?></h2>
                    <div class="column dtlms-one-column first">
                        <?php echo do_shortcode($class_tab_content); ?>
                    </div>
                </div>
				<?php
                $j++;
            }
        }
		
		if($class_type == 'onsite') {
			$class_gps = get_post_meta($class_id, 'dtlms-class-gps', true);
			$class_address = get_post_meta($class_id, 'dtlms-class-address', true);

        	if((isset($class_gps['latitude']) && $class_gps['latitude'] != '') && (isset($class_gps['longitude']) && $class_gps['longitude'] != '')) {
				?>
				<div class="dtlms-tabs-horizontal-content" style="display: block;">
                    <h2 class="dtlms-title"><?php echo esc_html__('Location', 'dtlms'); ?></h2>
                    <div class="column dtlms-one-column first">
                    	<?php echo esc_html($class_address);?>
                        <div class="dtlms-hr-invisible"></div>
                        <div class="dtlms-clear"></div>
                        <div class="dtlms-onsite-map-container">
                            <div id="dtlms-class-onsite-map-<?php echo $class_id; ?>" class="dtlms-class-onsite-map"></div>
                            <?php

                            $class_map_location = array (
                            						'title' => $class_title,
                            						'latitude' => $class_gps['latitude'], 
                            						'longitude' => $class_gps['longitude'],
                            						'address' => $class_address
                            					);

                            $class_map_location['classInfo'] = '<div class="dtlms-class-map-location-holder"><h3>'.$class_title.'</h3><span class="dtlms-class-map-location-address">'.$class_address.'</span></div>';

							$map_latitude = $class_gps['latitude'];
							$map_longitude = $class_gps['longitude'];

							echo '<script>
							
									function initClassLocationMap() {

										var map;
										var bounds = new google.maps.LatLngBounds();
										var mapOptions = {
											mapTypeId: "roadmap",
											center: new google.maps.LatLng('.$map_latitude.', '.$map_longitude.'),
											zoom: 8,
										};
										
										map = new google.maps.Map(document.getElementById("dtlms-class-onsite-map-'.$class_id.'"), mapOptions);
										map.setTilt(45);
										
										var classMapLocation = '.json_encode($class_map_location).';	
										
										var infoWindow = new google.maps.InfoWindow();
										var marker, i;
										
										var position = new google.maps.LatLng(classMapLocation.latitude, classMapLocation.longitude);
										bounds.extend(position);
										marker = new google.maps.Marker({
											position: position,
											map: map,
											title: classMapLocation.title
										});
										
										var classInfo = classMapLocation.classInfo;
							
										google.maps.event.addListener(marker, "click", (function (marker, i) {
											return function () {
												infoWindow.setContent(classInfo);
												infoWindow.open(map, marker);
											}
										})(marker, i));
							
										map.fitBounds(bounds);
										
										var boundsListener = google.maps.event.addListener((map), "bounds_changed", function (event) {
											this.setZoom(8);
											google.maps.event.removeListener(boundsListener);
										});
										
									}

								</script>';	                                
                            ?>
                        </div>
                    </div>
                </div>
                <?php
            }
		}

		$comments = get_comments( array('post_id' => $class_id) );
		$total_comments = count($comments);					
        ?>	

        <div class="dtlms-tabs-horizontal-content" style="display: none;">
            <h2 class="dtlms-title"><?php echo esc_html('Reviews', 'dtlms'); ?></h2>

			<?php
			$average_rating = get_post_meta($class_id, 'average-ratings', true);
			$average_rating = (isset($average_rating) && !empty($average_rating)) ? round($average_rating, 1) : 0;

			$comments = get_approved_comments($class_id);
			$total_comments = count($comments);

			if($page_layout == 'type2') {
				echo '<div class="dtlms-column dtlms-one-fifth first"></div>';
				echo '<div class="dtlms-column dtlms-three-fifth">';
					echo dtlms_class_single_review_box($average_rating, $total_comments, $page_layout);
					echo dtlms_class_single_review_rating_splitup($comments, $total_comments, $page_layout);
				echo '</div>';
				echo '<div class="dtlms-column dtlms-one-fifth"></div>';
			} else {
				echo '<div class="dtlms-column dtlms-one-third first">';
					echo dtlms_class_single_review_box($average_rating, $total_comments, $page_layout);
				echo '</div>';
				echo '<div class="dtlms-column dtlms-two-third">';
					echo dtlms_class_single_review_rating_splitup($comments, $total_comments, $page_layout);
				echo '</div>';								
			}
			?>

            <?php comments_template('', true); ?>
            
        </div>

	</div>

	<?php

}

?>