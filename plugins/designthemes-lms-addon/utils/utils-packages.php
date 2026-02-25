<?php

/*
* Packages Listing
*/

function dtlms_packages_listing_content($packages_listing_options) {

	$output = '';

	$package_carousel_attributes = $package_listing_attributes = array ();
	$holder_class = $container_class = $package_carousel_attributes_string = $package_listing_attributes_string = '';

	if($packages_listing_options['enable-carousel'] == 'true') {

		array_push($package_carousel_attributes, 'data-enablecarousel="true"');
		array_push($package_carousel_attributes, 'data-carouseleffect="'.$packages_listing_options['carousel-effect'].'"');
		array_push($package_carousel_attributes, 'data-carouselautoplay="'.$packages_listing_options['carousel-autoplay'].'"');
		array_push($package_carousel_attributes, 'data-carouselslidesperview="'.$packages_listing_options['carousel-slidesperview'].'"');
		array_push($package_carousel_attributes, 'data-carouselloopmode="'.$packages_listing_options['carousel-loopmode'].'"');
		array_push($package_carousel_attributes, 'data-carouselmousewheelcontrol="'.$packages_listing_options['carousel-mousewheelcontrol'].'"');
		array_push($package_carousel_attributes, 'data-carouselbulletpagination="'.$packages_listing_options['carousel-bulletpagination'].'"');
		array_push($package_carousel_attributes, 'data-carouselarrowpagination="'.$packages_listing_options['carousel-arrowpagination'].'"');
		array_push($package_carousel_attributes, 'data-carouselspacebetween="'.$packages_listing_options['carousel-spacebetween'].'"');

		//$holder_class .= ' dtlms-packages-swiper-listing';
		$container_class .= ' swiper-wrapper';

	} else {

		array_push($package_listing_attributes, 'data-enablecarousel="false"');

		if($packages_listing_options['apply-isotope'] == 'true') {
			$container_class .= ' dtlms-apply-isotope';
		}

	}

	if(!empty($package_carousel_attributes)) {
		$package_carousel_attributes_string = implode(' ', $package_carousel_attributes);
	}

	array_push($package_listing_attributes, 'data-postperpage="'.$packages_listing_options['post-per-page'].'"');
	array_push($package_listing_attributes, 'data-columns="'.$packages_listing_options['columns'].'"');
	array_push($package_listing_attributes, 'data-applyisotope="'.$packages_listing_options['apply-isotope'].'"');
	array_push($package_listing_attributes, 'data-type="'.$packages_listing_options['type'].'"');
	array_push($package_listing_attributes, 'data-packageitemids="'.$packages_listing_options['package-item-ids'].'"');


	$display_type = 'grid';
	if($packages_listing_options['display-type']) {
		$display_type = $packages_listing_options['display-type'];
	}
	array_push($package_listing_attributes, 'data-displaytype="'.$display_type.'"');

	if(!empty($package_listing_attributes)) {
		$package_listing_attributes_string = implode(' ', $package_listing_attributes);
	}


	$output .= '<div class="dtlms-packages-listing-holder '.$display_type.' '.$holder_class.'" '.$package_listing_attributes_string.' '.$package_carousel_attributes_string.'>';

		    if($packages_listing_options['enable-carousel'] == 'true') {
		    	$output .= '<div class="dtlms-packages-swiper-listing" '.$package_carousel_attributes_string.'>';
		    }

		    $output .= '<div class="dtlms-packages-listing-containers '.$container_class.' '.$display_type.'"></div>';

			if($packages_listing_options['enable-carousel'] == 'true') {

				if($packages_listing_options['carousel-bulletpagination'] == 'true' || $packages_listing_options['carousel-arrowpagination'] == 'true') {
					$output .= '<div class="dtlms-swiper-pagination-holder">';
						if($packages_listing_options['carousel-bulletpagination'] == 'true') {
							$output .= '<div class="dtlms-swiper-bullet-pagination"></div>';	
						}
						if($packages_listing_options['carousel-arrowpagination'] == 'true') {				
							$output .= '<div class="dtlms-swiper-arrow-pagination">';
								$output .= '<a href="#" class="dtlms-swiper-arrow-prev">'.esc_html__('Prev', 'dtlms').'</a>';
								$output .= '<a href="#" class="dtlms-swiper-arrow-next">'.esc_html__('Next', 'dtlms').'</a>';
							$output .= '</div>';
						}						
					$output .= '</div>';
				}

				$output .= '</div>';

			}

			//$output .= dtlms_generate_loader_html(true);

	$output .= '</div>';


    return $output;

}


add_action( 'wp_ajax_dtlms_generate_packages_listing', 'dtlms_generate_packages_listing' );
add_action( 'wp_ajax_nopriv_dtlms_generate_packages_listing', 'dtlms_generate_packages_listing' );
function dtlms_generate_packages_listing() {

	$output = '';

	$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
	$current_page = isset($_REQUEST['current_page']) ? $_REQUEST['current_page'] : 1;
	
	$post_per_page = isset($_REQUEST['post_per_page']) ? $_REQUEST['post_per_page'] : -1;
	$columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : 2;
	$apply_isotope = isset($_REQUEST['apply_isotope']) ? $_REQUEST['apply_isotope'] : 'false';
	$display_type = isset($_REQUEST['display_type']) ? $_REQUEST['display_type'] : 'grid';
	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'type1';

	$enable_carousel = isset($_REQUEST['enable_carousel']) ? $_REQUEST['enable_carousel'] : 'false';
	$carousel_class = '';
	if($enable_carousel == 'true') {
		$carousel_class = 'swiper-slide';
	}
	
	$current_user = wp_get_current_user();
	$user_id = $current_user->ID;

	if($enable_carousel == 'true') {
		$column_class = '';
		$post_per_page = -1;
	} else {
		if($columns == 3) {
			$column_class = 'dtlms-column dtlms-one-third';
		} else if($columns == 2) {
			$column_class = 'dtlms-column dtlms-one-half';
		} else {
			$column_class = 'dtlms-column dtlms-one-column';
		}
		if($display_type == 'list') {
			$column_class = 'dtlms-column dtlms-one-column';
		}	
	}
	
	if($apply_isotope == 'true') {
		$output .= '<div class="dtlms-packages-listing-items">';
	}

		
	$data_listing_attributes = array ();
	$data_listing_attributes['column_class'] = $column_class;
	$data_listing_attributes['carousel_class'] = $carousel_class;
	$data_listing_attributes['display_type'] = $display_type;
	$data_listing_attributes['type'] = $type;


	$args = array ( 
				'offset' => $offset, 
				'paged' => $current_page ,
				'posts_per_page' => $post_per_page, 
				'post_type' => 'dtlms_packages',
				'post_status'=>'publish'
			);

	$package_item_ids = $_REQUEST['package_item_ids'];

	if($package_item_ids != '') {
		$package_item_ids_arr = explode(',', $package_item_ids);
		$args['post__in'] = $package_item_ids_arr;
	}


	$packages_query = new WP_Query( $args );

	if ( $packages_query->have_posts() ) :

		if($apply_isotope == 'true') {
			$output .= '<div class="grid-sizer '.$column_class.'"></div>';
		}
		
		$i = 1;
		while ( $packages_query->have_posts() ) : 
			$packages_query->the_post();

			if($enable_carousel == 'true') {
				$first_class = '';
			} else {
				if($i == 1) { $first_class = 'first';  } else { $first_class = ''; }
				if($i == $columns) { $i = 1; } else { $i = $i + 1; }				
			}

			$data_listing_attributes['first_class'] = $first_class;

			$output .= dtlms_package_data_listing($user_id, $data_listing_attributes);

		endwhile;
		wp_reset_postdata();

	else :

		$output .= esc_html__('No records found!', 'dtlms');

	endif;

	if($apply_isotope == 'true') {
		$output .= '</div>';
	}

	if($enable_carousel != 'true'):
		$output .= dtlms_package_listing_pagination($packages_query, $current_page);
	endif;	

	echo $output;

	die();

}

function dtlms_package_data_listing($user_id, $data_listing_attributes) {

	$output = '';

	$package_id = get_the_ID();
	$package_title = get_the_title();
	$package_permalink = get_permalink();

	extract($data_listing_attributes);

	$display_type = $display_type.'-item';

	$item_classes = array ('dtlms-packagelist-item-wrapper');
	array_push($item_classes, $column_class, $carousel_class, $display_type, $type);
	if($first_class != '') {
		array_push($item_classes, $first_class);
	}

	$class_plural_label = apply_filters( 'class_label', 'plural' );

	$product = dtlms_get_product_object($package_id);				
	$woo_price = dtlms_get_item_price_html($product);

	$purchased_package = false;
	if(dtlms_check_user_package_is_active($user_id, $package_id)) {
		$purchased_package = true;
	}


	$output .= '<div class="'.implode(' ', get_post_class($item_classes, $package_id)).'">';

		if($type == 'type1') {

			$output .= '<div class="dtlms-packagelist-thumb">';
				$output .= dtlms_packages_listing_thumb($package_id, $package_title, $package_permalink, $display_type);
				$output .= dtlms_packages_listing_purchase_status($purchased_package);
			$output .= '</div>';
			$output .= '<div class="dtlms-packagelist-details">';
				$output .= dtlms_packages_listing_title($package_id, $package_title, $package_permalink);
				$output .= dtlms_packages_listing_inclusion($package_id);
				$output .= dtlms_packages_listing_description($package_id);
				$output .= '<div class="dtlms-packagelist-details-inner">';
					$output .= dtlms_packages_listing_single_price($woo_price, $package_id);
					$output .= dtlms_packages_listing_single_addtocart($purchased_package, $package_id, $user_id, $product, $woo_price);
				$output .= '</div>';
			$output .= '</div>';

		} else if($type == 'type2') {

			$output .= '<div class="dtlms-packagelist-thumb">';
				$output .= dtlms_packages_listing_thumb($package_id, $package_title, $package_permalink, $display_type);
				$output .= dtlms_packages_listing_purchase_status($purchased_package);
			$output .= '</div>';
			$output .= '<div class="dtlms-packagelist-details">';
				$output .= dtlms_packages_listing_title($package_id, $package_title, $package_permalink);
				$output .= dtlms_packages_listing_subtitle($package_id);
				$output .= dtlms_packages_listing_single_price($woo_price, $package_id);
				$output .= dtlms_packages_listing_inclusion($package_id);
				$output .= dtlms_packages_listing_single_addtocart($purchased_package, $package_id, $user_id, $product, $woo_price);
			$output .= '</div>';

		} else if($type == 'type3') {

			$output .= '<div class="dtlms-packagelist-thumb">';
				$output .= dtlms_packages_listing_thumb($package_id, $package_title, $package_permalink, $display_type);
				$output .= dtlms_packages_listing_purchase_status($purchased_package);
			$output .= '</div>';
			$output .= '<div class="dtlms-packagelist-details">';
				$output .= dtlms_packages_listing_title($package_id, $package_title, $package_permalink);
				$output .= dtlms_packages_listing_inclusion($package_id);
				$output .= dtlms_packages_listing_description($package_id);
				$output .= '<div class="dtlms-packagelist-details-inner">';
					$output .= dtlms_packages_listing_single_price($woo_price, $package_id);
					$output .= dtlms_packages_listing_single_addtocart($purchased_package, $package_id, $user_id, $product, $woo_price);
				$output .= '</div>';
			$output .= '</div>';

		}

	$output .= '</div>';


	return $output;

}

function dtlms_package_listing_pagination($dtlms_wpquery, $current_page) {

	$output = '';
	$total_posts = $dtlms_wpquery->found_posts;

	if($dtlms_wpquery->max_num_pages > 1) {

		$pages = ($dtlms_wpquery->max_num_pages) ? $dtlms_wpquery->max_num_pages : 1;
		
		$output .= '<div class="dtlms-pagination dtlms-ajax-pagination">';
				
			if($current_page > 1) {
				$output .= '<div class="prev-post"><a href="#" data-currentpage="'.$current_page.'"><span class="fa fa-caret-left"></span>&nbsp;'.esc_html__('Prev', 'dtlms').'</a></div>';
			}

			$output .= paginate_links ( array (
						  'base' 		 => '#',
						  'format' 		 => '',
						  'current' 	 => $current_page,
						  'type'     	 => 'list',
						  'end_size'     => 2,
						  'mid_size'     => 3,
						  'prev_next'    => false,
						  'total' 		 => $dtlms_wpquery->max_num_pages 
					  ) );

			if ($current_page < $pages) {
				$output .= '<div class="next-post"><a href="#" data-currentpage="'.$current_page.'">'.esc_html__('Next', 'dtlms').'&nbsp;<span class="fa fa-caret-right"></span></a></div>';
			}

		$output .= '</div>';

    }

    return $output;

}

?>