<?php

// Get product object
if(!function_exists('dtlms_get_product_object')) {
	function dtlms_get_product_object ( $wc_product_id = 0 ) {

		if ( class_exists( 'WooCommerce' ) ) {

			$wc_product_object = wc_get_product( $wc_product_id );
			return $wc_product_object;

		}

		return false;

	}
}


// Check item is in cart
if(!function_exists('dtlms_check_item_is_in_cart')) {
	function dtlms_check_item_is_in_cart( $product_id ){

		if ( $product_id > 0 ) {

			foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
				$cart_product = $values['data'];
				if( $product_id == $cart_product->get_id() ) {
					return true;
				}
			}
			
		}

		return false;

	}
}

if(!function_exists('dtlms_get_item_price_html')) {
	function dtlms_get_item_price_html($product) {

		$woo_price = '';

		if(!empty($product)) {

			$woo_regular_price = $product->get_regular_price();
			$woo_sale_price = $product->get_sale_price();
			
			if($woo_regular_price != '' && $woo_sale_price != '') {

				$woo_price .= '<del><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$woo_regular_price.'</span></del>';

				$woo_price .= '<ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$woo_sale_price.'</span></ins>';

			} else if($woo_regular_price != '') {

				$woo_price .= '<ins><span class="woocommerce-Price-amount amount"><span class="woocommerce-Price-currencySymbol">'.get_woocommerce_currency_symbol().'</span>'.$woo_regular_price.'</span></ins>';
							
			}

		}

		return $woo_price;

	}
}

if(!function_exists('dtlms_check_item_has_price')) {
	function dtlms_check_item_has_price($product) {

		if(!empty($product)) {

			$woo_regular_price = $product->get_regular_price();
			$woo_sale_price = $product->get_sale_price();
			
			if($woo_regular_price > 0 || $woo_sale_price > 0) {
				return true;
			}

		}

		return false;

	}
}

if(!function_exists('dtlms_on_order_status_completion')) {
	function dtlms_on_order_status_completion($order_id) {

		$order = new WC_Order( $order_id );
		$user_id = get_post_meta($order_id, '_customer_user', true);

		$items = $order->get_items();
		foreach ( $items as $item_id => $item ) {

			$dtlms_item_id = wc_get_order_item_meta($item_id, 'dtlms_item_id');	
			$post_type = get_post_type($dtlms_item_id);

			if(in_array($post_type, array('dtlms_classes'))) {

				$class_id = $dtlms_item_id;
				$class_data = get_post($class_id);
				$author_id = $class_data->post_author;

				$purchased_users = get_post_meta($class_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				array_push($purchased_users, $user_id);
				update_post_meta($class_id, 'purchased_users', $purchased_users);

				$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
				$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();
				array_push($purchased_classes, $class_id);
				update_user_meta($user_id, 'purchased_classes', $purchased_classes);


				$current_timestamp = strtotime(current_time(get_option('date_format')));

				$purchased_users_timestamp = get_post_meta($class_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				$purchased_users_timestamp[$current_timestamp][] = $user_id;
				update_post_meta($class_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_classes_timestamp = get_user_meta($user_id, 'purchased_classes_timestamp', true);
				$purchased_classes_timestamp = (is_array($purchased_classes_timestamp) && !empty($purchased_classes_timestamp)) ? $purchased_classes_timestamp : array();
				$purchased_classes_timestamp[$current_timestamp][] = $class_id;
				update_user_meta($user_id, 'purchased_classes_timestamp', $purchased_classes_timestamp);


				// Start class for user
				dtlms_start_class_initialize($class_id, $user_id, $author_id, false);


				if($author_id > 0) {

					$classes_subscribed = get_user_meta($author_id, 'classes-subscribed', true);
					$classes_subscribed = (is_array($classes_subscribed) && !empty($classes_subscribed)) ? $classes_subscribed : array ();
					$classes_subscribed_timestamp = array_keys($classes_subscribed);

					$classes_subscribed[$current_timestamp][$class_id]['users'][] = $user_id;
					$classes_subscribed[$current_timestamp][$class_id]['status'] = 'unpaid';

					update_user_meta($author_id, 'classes-subscribed', $classes_subscribed);

				}

				// Notification & Mail
				do_action('dtlms_poc_class_subscribed', $class_id, $user_id);

			}

			if(in_array($post_type, array('dtlms_courses'))) {

				$course_id = $dtlms_item_id;
				$course_data = get_post($course_id);
				$author_id = $course_data->post_author;

				$purchased_users = get_post_meta($course_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				array_push($purchased_users, $user_id);
				update_post_meta($course_id, 'purchased_users', $purchased_users);

				$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();
				array_push($purchased_courses, $course_id);
				update_user_meta($user_id, 'purchased_courses', $purchased_courses);


				$current_timestamp = strtotime(current_time(get_option('date_format')));

				$purchased_users_timestamp = get_post_meta($course_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				$purchased_users_timestamp[$current_timestamp][] = $user_id;
				update_post_meta($course_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_courses_timestamp = get_user_meta($user_id, 'purchased_courses_timestamp', true);
				$purchased_courses_timestamp = (is_array($purchased_courses_timestamp) && !empty($purchased_courses_timestamp)) ? $purchased_courses_timestamp : array();
				$purchased_courses_timestamp[$current_timestamp][] = $course_id;
				update_user_meta($user_id, 'purchased_courses_timestamp', $purchased_courses_timestamp);


				if ( class_exists( 'BuddyPress' ) && function_exists('groups_join_group') ) {
					$course_group_id = get_post_meta( $course_id, 'dtlms-course-group-id', true );
					groups_join_group( $course_group_id, $user_id );
				}

				if($author_id > 0) {

					$courses_subscribed = get_user_meta($author_id, 'courses-subscribed', true);
					$courses_subscribed = (is_array($courses_subscribed) && !empty($courses_subscribed)) ? $courses_subscribed : array ();
					$courses_subscribed_timestamp = array_keys($courses_subscribed);

					$courses_subscribed[$current_timestamp][$course_id]['users'][] = $user_id;
					$courses_subscribed[$current_timestamp][$course_id]['status'] = 'unpaid';

					update_user_meta($author_id, 'courses-subscribed', $courses_subscribed);

				}

				// Notification & Mail
				do_action('dtlms_poc_course_subscribed', $course_id, $user_id);

			}

			if(in_array($post_type, array('dtlms_packages'))) {

				$package_id = $dtlms_item_id;

				$period = get_post_meta($package_id, 'period', true);
				$term = get_post_meta($package_id, 'term', true);
				$terms_list = array('D' => 'days', 'W' => 'weeks', 'M' => 'months', 'Y' => 'years', 'L' => 'lifetime');

				$current_timestamp = strtotime(current_time(get_option('date_format')));
				if($term != 'L' && $term != '') {
					$add_date = '+'.$period.' '.$terms_list[$term];
					$expiry_timestamp = strtotime($add_date, $current_timestamp);
				} else {
					$expiry_timestamp = 'NA';
				}
				

				$purchased_users = get_post_meta($package_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				$purchased_users[$user_id] = array (
												'purchased-date' => $current_timestamp,
												'expiry-date' => $expiry_timestamp,
											);
				//array_push($purchased_users, $user_id);
				update_post_meta($package_id, 'purchased_users', $purchased_users);

				$purchased_packages = get_user_meta($user_id, 'purchased_packages', true);
				$purchased_packages = (is_array($purchased_packages) && !empty($purchased_packages)) ? $purchased_packages : array();
				$purchased_packages[$package_id] = array (
												'purchased-date' => $current_timestamp,
												'expiry-date' => $expiry_timestamp,
											);	
				//array_push($purchased_packages, $package_id);
				update_user_meta($user_id, 'purchased_packages', $purchased_packages);
	

				$purchased_users_timestamp = get_post_meta($package_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				$purchased_users_timestamp[$current_timestamp][] = $user_id;
				update_post_meta($package_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_packages_timestamp = get_user_meta($user_id, 'purchased_packages_timestamp', true);
				$purchased_packages_timestamp = (is_array($purchased_packages_timestamp) && !empty($purchased_packages_timestamp)) ? $purchased_packages_timestamp : array();
				$purchased_packages_timestamp[$current_timestamp][] = $package_id;
				update_user_meta($user_id, 'purchased_packages_timestamp', $purchased_packages_timestamp);


				if($author_id > 0) {

					$packages_subscribed = get_user_meta($author_id, 'packages-subscribed', true);
					$packages_subscribed = (is_array($packages_subscribed) && !empty($packages_subscribed)) ? $packages_subscribed : array ();
					$packages_subscribed_timestamp = array_keys($packages_subscribed);

					$packages_subscribed[$current_timestamp][$package_id]['users'][] = $user_id;
					$packages_subscribed[$current_timestamp][$package_id]['status'] = 'unpaid';

					update_user_meta($author_id, 'packages-subscribed', $packages_subscribed);

				}

				// Start class for user
				$classes_included = get_post_meta($package_id, 'classes-included', true);
				if(is_array($classes_included) && !empty($classes_included)) {
					foreach($classes_included as $class_id) {
						dtlms_start_class_initialize($class_id, $user_id, $author_id, false);
					}
				}

				// Notification & Mail
				do_action('dtlms_poc_package_subscribed', $package_id, $user_id);				

			}

		}

		// Change the customer role to student
	    if ( $user_id > 0 ) {
	    	$user = new WP_User( $user_id );
	        $user->remove_role( 'customer' ); 
	        $user->remove_role( 'subscriber' );
	        $user->add_role( 'student' );
	    }

	}
	add_action('woocommerce_order_status_completed','dtlms_on_order_status_completion');
}

if(!function_exists('dtlms_on_order_status_cancellation')) {
	function dtlms_on_order_status_cancellation($order_id) {

		$order = new WC_Order( $order_id );
		$order_data = $order->get_data();

		$user_id = get_post_meta($order_id, '_customer_user', true);

		$items = $order->get_items();
		foreach ( $items as $item_id => $item ) {

			$dtlms_item_id = wc_get_order_item_meta($item_id, 'dtlms_item_id');
			$post_type = get_post_type($dtlms_item_id);

			if(in_array($post_type, array('dtlms_classes'))) {

				$class_id = $dtlms_item_id;				
				$class_data = get_post($class_id);
				$author_id = $class_data->post_author;				

				$purchased_users = get_post_meta($class_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				if(in_array($user_id, $purchased_users)) {
				    unset($purchased_users[array_search($user_id, $purchased_users)]);
				}
				update_post_meta($class_id, 'purchased_users', $purchased_users);

				$purchased_classes = get_user_meta($user_id, 'purchased_classes', true);
				$purchased_classes = (is_array($purchased_classes) && !empty($purchased_classes)) ? $purchased_classes : array();
				if(in_array($class_id, $purchased_classes)) {
				    unset($purchased_classes[array_search($class_id, $purchased_classes)]);
				}				
				update_user_meta($user_id, 'purchased_classes', $purchased_classes);


				$purchased_users_timestamp = get_post_meta($class_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(in_array($user_id, $purchased_users_timestamp_data)) {
					    unset($purchased_users_timestamp[$purchased_users_timestamp_key][array_search($user_id, $purchased_users_timestamp_data)]);
					}									
				}
				update_post_meta($class_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_classes_timestamp = get_user_meta($user_id, 'purchased_classes_timestamp', true);
				$purchased_classes_timestamp = (is_array($purchased_classes_timestamp) && !empty($purchased_classes_timestamp)) ? $purchased_classes_timestamp : array();
				foreach($purchased_classes_timestamp as $purchased_classes_timestamp_key => $purchased_classes_timestamp_data) {
					if(in_array($class_id, $purchased_classes_timestamp_data)) {
					    unset($purchased_classes_timestamp[$purchased_classes_timestamp_key][array_search($class_id, $purchased_classes_timestamp_data)]);
					}	
				}		
				update_user_meta($user_id, 'purchased_classes_timestamp', $purchased_classes_timestamp);

				if($author_id > 0) {

					$classes_subscribed = get_user_meta($author_id, 'classes-subscribed', true);
					$classes_subscribed = (is_array($classes_subscribed) && !empty($classes_subscribed)) ? $classes_subscribed : array ();

					$order_timestamp_completed = $order_data['date_completed']->date(get_option('date_format'));
					$order_timestamp_completed = strtotime($order_timestamp_completed);

					foreach($classes_subscribed[$order_timestamp_completed][$class_id]['users'] as $order_user_key => $order_user) {
						if($order_user == $user_id) {
							unset($classes_subscribed[$order_timestamp_completed][$class_id]['users'][$order_user_key]);	
						}
					}

					update_user_meta($author_id, 'classes-subscribed', $classes_subscribed);

				}

				// Notification & Mail
				do_action('dtlms_poc_class_subscription_cancellation', $class_id, $user_id);

			}

			if(in_array($post_type, array('dtlms_courses'))) {

				$course_id = $dtlms_item_id;				
				$course_data = get_post($course_id);
				$author_id = $course_data->post_author;				

				$purchased_users = get_post_meta($course_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				if(in_array($user_id, $purchased_users)) {
				    unset($purchased_users[array_search($user_id, $purchased_users)]);
				}
				update_post_meta($course_id, 'purchased_users', $purchased_users);

				$purchased_courses = get_user_meta($user_id, 'purchased_courses', true);
				$purchased_courses = (is_array($purchased_courses) && !empty($purchased_courses)) ? $purchased_courses : array();
				if(in_array($course_id, $purchased_courses)) {
				    unset($purchased_courses[array_search($course_id, $purchased_courses)]);
				}				
				update_user_meta($user_id, 'purchased_courses', $purchased_courses);


				$purchased_users_timestamp = get_post_meta($course_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(in_array($user_id, $purchased_users_timestamp_data)) {
					    unset($purchased_users_timestamp[$purchased_users_timestamp_key][array_search($user_id, $purchased_users_timestamp_data)]);
					}									
				}
				update_post_meta($course_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_courses_timestamp = get_user_meta($user_id, 'purchased_courses_timestamp', true);
				$purchased_courses_timestamp = (is_array($purchased_courses_timestamp) && !empty($purchased_courses_timestamp)) ? $purchased_courses_timestamp : array();
				foreach($purchased_courses_timestamp as $purchased_courses_timestamp_key => $purchased_courses_timestamp_data) {
					if(in_array($course_id, $purchased_courses_timestamp_data)) {
					    unset($purchased_courses_timestamp[$purchased_courses_timestamp_key][array_search($course_id, $purchased_courses_timestamp_data)]);
					}	
				}		
				update_user_meta($user_id, 'purchased_courses_timestamp', $purchased_courses_timestamp);

				if ( class_exists( 'BuddyPress' ) && function_exists('groups_join_group') ) {
					$course_group_id = get_post_meta( $course_id, 'dtlms-course-group-id', true );
					groups_remove_member( $user_id, $course_group_id );
				}

				if($author_id > 0) {

					$courses_subscribed = get_user_meta($author_id, 'courses-subscribed', true);
					$courses_subscribed = (is_array($courses_subscribed) && !empty($courses_subscribed)) ? $courses_subscribed : array ();

					$order_timestamp_completed = $order_data['date_completed']->date(get_option('date_format'));
					$order_timestamp_completed = strtotime($order_timestamp_completed);

					foreach($courses_subscribed[$order_timestamp_completed][$course_id]['users'] as $order_user_key => $order_user) {
						if($order_user == $user_id) {
							unset($courses_subscribed[$order_timestamp_completed][$course_id]['users'][$order_user_key]);
						}
					}

					update_user_meta($author_id, 'courses-subscribed', $courses_subscribed);

				}

				// Notification & Mail
				do_action('dtlms_poc_course_subscription_cancellation', $course_id, $user_id);

			}

			if(in_array($post_type, array('dtlms_packages'))) {

				$package_id = $dtlms_item_id;						

				$purchased_users = get_post_meta($package_id, 'purchased_users', true);
				$purchased_users = (is_array($purchased_users) && !empty($purchased_users)) ? $purchased_users : array();
				if(array_key_exists($user_id, $purchased_users)) {
				    unset($purchased_users[$user_id]);
				}
				update_post_meta($package_id, 'purchased_users', $purchased_users);

				$purchased_packages = get_user_meta($user_id, 'purchased_packages', true);
				$purchased_packages = (is_array($purchased_packages) && !empty($purchased_packages)) ? $purchased_packages : array();
				if(array_key_exists($package_id, $purchased_packages)) {
				    unset($purchased_packages[$package_id]);
				}				
				update_user_meta($user_id, 'purchased_packages', $purchased_packages);

				$purchased_users_timestamp = get_post_meta($package_id, 'purchased_users_timestamp', true);
				$purchased_users_timestamp = (is_array($purchased_users_timestamp) && !empty($purchased_users_timestamp)) ? $purchased_users_timestamp : array();
				foreach($purchased_users_timestamp as $purchased_users_timestamp_key => $purchased_users_timestamp_data) {
					if(in_array($user_id, $purchased_users_timestamp_data)) {
					    unset($purchased_users_timestamp[$purchased_users_timestamp_key][array_search($user_id, $purchased_users_timestamp_data)]);
					}									
				}
				update_post_meta($package_id, 'purchased_users_timestamp', $purchased_users_timestamp);

				$purchased_packages_timestamp = get_user_meta($user_id, 'purchased_packages_timestamp', true);
				$purchased_packages_timestamp = (is_array($purchased_packages_timestamp) && !empty($purchased_packages_timestamp)) ? $purchased_packages_timestamp : array();
				foreach($purchased_packages_timestamp as $purchased_packages_timestamp_key => $purchased_packages_timestamp_data) {
					if(in_array($package_id, $purchased_packages_timestamp_data)) {
					    unset($purchased_packages_timestamp[$purchased_packages_timestamp_key][array_search($package_id, $purchased_packages_timestamp_data)]);
					}	
				}		
				update_user_meta($user_id, 'purchased_packages_timestamp', $purchased_packages_timestamp);


				if($author_id > 0) {

					$packages_subscribed = get_user_meta($author_id, 'packages-subscribed', true);
					$packages_subscribed = (is_array($packages_subscribed) && !empty($packages_subscribed)) ? $packages_subscribed : array ();

					$order_timestamp_completed = $order_data['date_completed']->date(get_option('date_format'));
					$order_timestamp_completed = strtotime($order_timestamp_completed);

					foreach($packages_subscribed[$order_timestamp_completed][$package_id]['users'] as $order_user_key => $order_user) {
						if($order_user == $user_id) {
							unset($packages_subscribed[$order_timestamp_completed][$package_id]['users'][$order_user_key]);	
						}
					}

					update_user_meta($author_id, 'packages-subscribed', $packages_subscribed);

				}

				// Notification & Mail
				do_action('dtlms_poc_package_subscription_cancellation', $package_id, $user_id);				

			}

		}

	}
	add_action('woocommerce_order_status_cancelled','dtlms_on_order_status_cancellation');
	add_action('woocommerce_order_status_refunded','dtlms_on_order_status_cancellation');
}


// Prevent courses and classes from adding more than one item
add_action( 'woocommerce_add_to_cart', 'dtlms_update_quantity_on_addtocart', 10, 6 );
function dtlms_update_quantity_on_addtocart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

	foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
		$post_type = get_post_type($cart_item['product_id']);
		if($post_type == 'dtlms_classes' || $post_type == 'dtlms_courses') {
			if($cart_item['quantity'] > 1) {
				WC()->cart->set_quantity($cart_item_key, 1);
			}
		}
	}

}

// Prevent courses and classes from adding more than one item
add_filter( 'woocommerce_cart_item_quantity', 'dtlms_change_quantity_on_cartpage', 10, 3);
function dtlms_change_quantity_on_cartpage( $product_quantity, $cart_item_key, $cart_item ) {

    $product_id = $cart_item['product_id'];
    $post_type = get_post_type($product_id);

    if($post_type == 'dtlms_classes' || $post_type == 'dtlms_courses') {
    	return 1;	
    }

    return $product_quantity;

}

?>