<?php

// Listing

function dtlms_packages_listing_thumb($package_id, $package_title, $package_permalink, $display_type) {

	$output = '';

	if(has_post_thumbnail($package_id)) {

		if($display_type == 'list-item') {

			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id($package_id), 'full', false);
			$output .= '<a href="'.esc_url($package_permalink).'" title="'.esc_attr($package_title).'">
							<div class="dtlms-packagelist-thumb-inner" style="background:url('.esc_url($image_src[0]).');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($package_permalink).'" title="'.esc_attr($package_title).'">'.get_the_post_thumbnail($package_id, 'full').'</a>';

		}

	} else {

		if($display_type == 'list-item') {

			$output .= '<a href="'.esc_url($package_permalink).'" title="'.esc_attr($package_title).'">
							<div class="dtlms-packagelist-thumb-inner" style="background:url('.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').');"></div>
						</a>';

		} else {

			$output .= '<a href="'.esc_url($package_permalink).'" title="'.esc_attr($package_title).'"><img src="'.esc_url(plugins_url ('designthemes-lms-addon').'/images/no-image-1920x800.jpg').'" alt="'.esc_attr($package_title).'" title="'.esc_attr($package_title).'" /></a>';

		}

	}

	return $output;

}

function dtlms_packages_listing_title($package_id, $package_title, $package_permalink) {

	$output = '<h5><a href="'.esc_url($package_permalink).'" title="'.esc_attr($package_title).'">'.esc_html($package_title).'</a></h5>';
					
	return $output;

}

function dtlms_packages_listing_subtitle($package_id) {

	$subtitle = get_post_meta($package_id, 'subtitle', true);

	$output = '<h6>'.esc_html($subtitle).'</h6>';
					
	return $output;

}

function dtlms_packages_listing_inclusion($package_id) {

	$courses_included = get_post_meta($package_id, 'courses-included', true);
	$classes_included = get_post_meta($package_id, 'classes-included', true);

	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$output = '';

	$output .= '<div class="dtlms-packagelist-inclusion">';
		$output .= '<p>'.count($courses_included).' '.esc_html__('Courses', 'dtlms').'</p>';
		$output .= '<p>'.count($classes_included).' '.sprintf( esc_html__( '%1$s', 'dtlms' ), $class_plural_label ).'</p>';
	$output .= '</div>';
				
	return $output;

}

function dtlms_packages_listing_description($package_id) {

	$output = '<div class="dtlms-packagelist-description">
                    '.get_the_excerpt($package_id).'
                </div>';

	return $output;

}

function dtlms_packages_listing_single_price($woo_price, $package_id) {

	$period = get_post_meta($package_id, 'period', true);
	$term = get_post_meta($package_id, 'term', true);
	$terms_list = array('D' => 'Day(s)', 'W' => 'Week(s)', 'M' => 'Month(s)', 'Y' => 'Year(s)', 'L' => 'Lifetime');

	$output = '<div class="dtlms-packagelist-price-details">
					<span class="dtlms-price-status dtlms-cost">
						'.$woo_price.' / '.$period.' '.$terms_list[$term].'
					</span>
				</div>';

	return $output;

}

function dtlms_packages_listing_single_addtocart($purchased_package, $package_id, $user_id, $product, $woo_price) {

	$output = '';

	if(class_exists('WooCommerce')) {

		if(!$purchased_package) {

			if(dtlms_check_item_is_in_cart($package_id)) {

				$output .= '<div class="dtlms-packagedetail-cart-details">';
					$output .= '<a href="'.wc_get_cart_url().'" target="_self" class="dtlms-packagedetail-cart-link dtlms-button small filled"><i class="fa fa-cart-plus"></i>'.esc_html__('View Cart','dtlms').'</a>';
				$output .= '</div>';

			} else {

				$purchased_packages = get_user_meta($user_id, 'purchased_packages', true);
				$purchased_packages = (is_array($purchased_packages) && !empty($purchased_packages)) ? $purchased_packages : array ();
				$purchased_packages_keys = array_keys($purchased_packages);

				if(in_array($package_id, $purchased_packages_keys)) {
					$output .= '<span class="dtlms-expired">
									<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Expired','dtlms').
								'</span>';						
				}

				if($woo_price != '') {

					$output .= '<div class="dtlms-packagedetail-cart-details">';
						$output .= '<a href="'. apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) ) .'" rel="nofollow" data-product_id="'.esc_attr($product->get_id()).'" class="dtlms-button small filled add_to_cart_button ajax_add_to_cart product_type_'.esc_attr($product->get_type()).'"><i class="fa fa-shopping-cart"></i>'.esc_html__('Add to Cart', 'dtlms').'</a>';
					$output .= '</div>';	

				}							

			}

		}

	}

	return $output;

}

function dtlms_packages_listing_purchase_status($purchased_package) {

	$output = '';

	if($purchased_package) {

		$output .= '<div class="dtlms-courselist-purchase-status-details">';
			$output .= '<span class="dtlms-purchase-status dtlms-purchased">
							<span class="fa fa-cart-arrow-down"></span> '.esc_html__('Purchased','dtlms').
						'</span>';
		$output .= '</div>';

	}

	return $output;

}

?>