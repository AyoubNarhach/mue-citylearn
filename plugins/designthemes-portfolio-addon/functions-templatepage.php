<?php

// Portfolio shortcode page details
function dtportfolio_shortcode_page_details($portfolio_shortcode_pageid) {

	$tpl_default_settings = get_post_meta($portfolio_shortcode_pageid, '_tpl_default_settings', TRUE );
	$tpl_default_settings = is_array( $tpl_default_settings ) ? $tpl_default_settings  : array();
	$page_layout  = array_key_exists( "layout", $tpl_default_settings ) ? $tpl_default_settings['layout'] : "content-full-width";
	$show_sidebar = false;

	if( $page_layout == "content-full-width" ) {
		$show_sidebar = false;
	} else {
		$show_sidebar = true;
	}

	return $show_sidebar;

}

function dtportfolio_get_portfolio_layout_class($show_sidebar, $post_style, $tpl_default_settings) {

    $post_layout = isset( $tpl_default_settings['portfolio-post-layout'] ) ? $tpl_default_settings['portfolio-post-layout'] : "dtportfolio-one-fourth-column";

	$post_class = $columns = '';

	if($post_style == 'dtportfolio-fullpage') {

		$post_class = ' section dtportfolio-item dtportfolio-column dtportfolio-one-column ';

	} else if($post_style == 'dtportfolio-multiscroll') {

		$post_class = ' ms-section dtportfolio-item ';	

	} else if(array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel') {

		$post_class = 'dtportfolio-item';
		$columns = -1;

	} else {

		switch( $post_layout ):
			case 'dtportfolio-one-tenth-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-tenth with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-tenth";
				$columns = 10;
			break;

			case 'dtportfolio-one-nineth-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-nineth with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-nineth";
				$columns = 9;
			break;

			case 'dtportfolio-one-eight-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-eight with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-eight";
				$columns = 8;
			break;

			case 'dtportfolio-one-seventh-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-seventh with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-seventh";
				$columns = 7;
			break;

			case 'dtportfolio-one-sixth-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-sixth with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-sixth";
				$columns = 6;
			break;

			case 'dtportfolio-one-fifth-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-fifth with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-fifth";
				$columns = 5;
			break;

			case 'dtportfolio-one-fourth-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-fourth with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-fourth";
				$columns = 4;
			break;

			case 'dtportfolio-one-third-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-third with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-third";
				$columns = 3;
			break;
			
			case 'dtportfolio-one-half-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-half with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-half";
				$columns = 2;
			break;

			default:
			case 'dtportfolio-one-column':
				$post_class = $show_sidebar ? " dtportfolio-item dtportfolio-column dtportfolio-one-column with-sidebar" : " dtportfolio-item dtportfolio-column dtportfolio-one-column";
				$columns = 1;
			break;
		endswitch;

	}

	$layout_class_output = $post_class.'|'.$columns;

	return $layout_class_output;

}

function dtportfolio_get_portfolio_category_filter($categories, $post_style, $filterdesign_type) {

	if(empty($categories)):
		$categories = get_categories('taxonomy=portfolio_entries&hide_empty=1');
	else:
		$c = array('taxonomy'=>'portfolio_entries','hide_empty'=>1,'include'=>$categories);
		$categories = get_categories($c);
	endif;
	
	$out = '';

	if( sizeof($categories) > 1 ) :
        $out .= '<div class="dtportfolio-sorting '.esc_attr($post_style).' '.esc_attr($filterdesign_type).'">
        			<a href="#" class="active-sort" title="" data-filter=".all-sort">'.esc_html__('All','dtportfolio').'</a>';
            		foreach( $categories as $category ):
                		 $out .= '<a href="#" data-filter=".'.esc_attr($category->category_nicename).'-sort">'.esc_html($category->cat_name).'</a>';
                	endforeach;
        $out .= '</div>';
	endif;

	return $out;

}

function dtportfolio_get_portfolio_images($portfolio_id, $post_style, $related_portfolio_id, $columns) {

	$portfolio_item_meta = get_post_meta($portfolio_id, '_portfolio_settings', TRUE);
	$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();

	$portfolio_featured_video = get_post_meta($portfolio_id, '_portfolio_featured_video', TRUE);
	$portfolio_featured_video = is_array($portfolio_featured_video) ? $portfolio_featured_video  : array();

	$featured_video =  isset($portfolio_featured_video['featured_video']) ? $portfolio_featured_video['featured_video'] : '';
	
	if($featured_video != '') {

		$video = $popup = $featured_video;
		$image = '';

	} elseif( has_post_thumbnail($portfolio_id) ) {
		
		$image_size = 'full';
		if($post_style == "dtportfolio-striped") {
			$image_size = 'dtportfolio-635x1100';
		} else if($columns > 6) {
			$image_size = 'dtportfolio-750x650';
		}

		$image = wp_get_attachment_image_src(get_post_thumbnail_id($portfolio_id), $image_size, false);
		$image = $popup = $image[0];

		$video = '';

	} elseif( array_key_exists('items_name', $portfolio_item_meta) ) {

		$item =  $portfolio_item_meta['items_name'][0];
		$popup = $portfolio_item_meta['items'][0];

		if( 'video' === $item ) {
			$x = array_diff( $portfolio_item_meta['items_name'] , array("video") );
			if( !empty($x) ) {
				$image = $portfolio_item_meta['items'][key($x)];
        	} else {
        		$image = 'http://placehold.it/1170X902.jpg&text=Video';
        	}								
		} else {
			if( sizeof($portfolio_item_meta['items']) > 1 ){
				$popup = $portfolio_item_meta['items'][1];
			}
			$image = $portfolio_item_meta['items'][0];
			if($post_style == "dtportfolio-striped") {
				$image = explode('.', $image);
				$image = $image[0].'-270x690.'.$image[1];
			}
		}

		$video = '';

	} else {

		$image = $popup = 'http://place-hold.it/1170X902.jpg&text='.get_the_title($portfolio_id);
		$video = '';

	}

	$out = $image.'|'.$popup.'|'.$video;

	return $out;

}

function dtportfolio_portfolio_image($portfolio_id, $portfolio_template_pageid, $image, $video, $tpl_default_settings) {

	$portfolio_title = get_the_title($portfolio_id);
	$portfolio_permalink = get_permalink($portfolio_id);
	
	$post_style = isset( $tpl_default_settings['portfolio-post-style'] ) ? $tpl_default_settings['portfolio-post-style'] : "type1";
	$portfolio_fullpage_type = isset( $tpl_default_settings['portfolio-fullpage-type'] ) ? $tpl_default_settings['portfolio-fullpage-type'] : '';

	$out = '';

	if($video != '') {

		$out .= '<video id="portfolio-featured-video" poster="'.plugins_url('designthemes-portfolio-addon').'/images/video-image.png" class="play">
					<source src="'.$video.'" type="video/mp4">
					<source src="'.$video.'" type="video/webm">
					<source src="'.$video.'" type="video/ogg">
				</video>';

	} else {

		//if((array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel') || in_array($post_style, array('dtportfolio-multiscroll', 'dtportfolio-fixed'))) {
		if((array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel')) {

			//$out .= '<img src="'.esc_url($image).'" alt="'.$portfolio_title.'" title="'.$portfolio_title.'" />';
			//$out .= '<div class="dtportfolio-image-holder" style="background-image:url('.esc_url($image).');"></div>';

			$carousel_effect = array_key_exists("portfolio-carousel-effect",$tpl_default_settings) ?  $tpl_default_settings['portfolio-carousel-effect'] : '';
			if($carousel_effect == 'multirows') {
				$out .= '<div class="dtportfolio-image-holder" style="background-image:url('.esc_url($image).');"></div>';
			} else {
				$out .= '<img src="'.esc_url($image).'" alt="'.$portfolio_title.'" title="'.$portfolio_title.'" />';
			}		

		} else if(in_array($post_style, array('dtportfolio-multiscroll', 'dtportfolio-fixed'))) {

			$out .= '<div class="dtportfolio-image-holder" style="background-image:url('.esc_url($image).');"></div>';

		} elseif(!in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-parallax', 'dtportfolio-multiscroll'))) {

			$out .= '<img src="'.esc_url($image).'" alt="'.$portfolio_title.'" title="'.$portfolio_title.'" />';

		} elseif(in_array($post_style, array('dtportfolio-fullpage'))) {

			$portfolio_disable_fullpage_js = isset( $tpl_default_settings['portfolio-disable-fullpage-js'] ) ? true : false;

			if($portfolio_disable_fullpage_js)	 {
				$out .= '<img src="'.esc_url($image).'" alt="'.$portfolio_title.'" title="'.$portfolio_title.'" />';
			} else {
				$portfolio_fullpage_type = isset( $tpl_default_settings['portfolio-fullpage-type'] ) ? $tpl_default_settings['portfolio-fullpage-type'] : '';	
				if($portfolio_fullpage_type == 'splitted-section') {
					$out .= '<div class="dtportfolio-image-holder" style="background-image:url('.esc_url($image).');"></div>';
				}
			}

		}

	}

	return $out;

}

function dtportfolio_generate_gallery_thumb($portfolio_id, $image, $video) {

	if($video != '') {
		$out = '<div class="swiper-slide" style="background-image:url('.plugins_url('designthemes-portfolio-addon').'/images/video-image.png)"></div>';
		//$out = '<div class="swiper-slide"><img src="'.plugins_url('designthemes-portfolio-addon').'/images/video-image.png" /></div>';
	} else {
		$out = '<div class="swiper-slide" style="background-image:url('.$image.')"></div>';	
		//$out = '<div class="swiper-slide"><img src="'.esc_url($image).'" /></div>';
	}

	return $out;

}

function dtportfolio_portfolio_overlay_content($portfolio_id, $portfolio_template_pageid, $popup, $tpl_default_settings) {
	
	$out = '';
	
	$portfolio_title = get_the_title($portfolio_id);
	$portfolio_permalink = get_permalink($portfolio_id);
	
	$post_style = isset( $tpl_default_settings['portfolio-post-style'] ) ? $tpl_default_settings['portfolio-post-style'] : "type1";

	$portfolio_settings = get_post_meta($portfolio_id,'_portfolio_settings',TRUE);
	$portfolio_settings = is_array($portfolio_settings) ? $portfolio_settings : array();

	$disable_item_options = array_key_exists('portfolio-disable-item-options', $tpl_default_settings) ? true : false;

	if($disable_item_options) {

		$hover_bgcolor = array_key_exists('hover-background-color', $tpl_default_settings) ? $tpl_default_settings['hover-background-color'] : '';
		$hover_contentcolor = array_key_exists('hover-content-color', $tpl_default_settings) ? $tpl_default_settings['hover-content-color'] : '';
		$hover_gradientcolor = array_key_exists('hover-gradient-color', $tpl_default_settings) ? $tpl_default_settings['hover-gradient-color'] : '';
		$hover_gradient_direction = array_key_exists('hover-gradient-direction', $tpl_default_settings) ? $tpl_default_settings['hover-gradient-direction'] : 'lefttoright';

	} else {

		$hover_bgcolor = array_key_exists('hover-background-color', $portfolio_settings) ? $portfolio_settings['hover-background-color'] : '';
		$hover_contentcolor = array_key_exists('hover-content-color', $portfolio_settings) ? $portfolio_settings['hover-content-color'] : '';
		$hover_gradientcolor = array_key_exists('hover-gradient-color', $portfolio_settings) ? $portfolio_settings['hover-gradient-color'] : '';
		$hover_gradient_direction = array_key_exists('hover-gradient-direction', $portfolio_settings) ? $portfolio_settings['hover-gradient-direction'] : 'lefttoright';

	}

	$hover_style_portfolio = array_key_exists('hover-styles', $portfolio_settings) ? $portfolio_settings['hover-styles'] : '';
	if($disable_item_options || $hover_style_portfolio == '') {
		$hover_style = isset( $tpl_default_settings['portfolio-hover-style'] ) ? $tpl_default_settings['portfolio-hover-style'] : '';
	} else {
		$hover_style = $hover_style_portfolio;
	}

	$style = '';
	if($hover_bgcolor != '' && $hover_gradientcolor != '') {		

		if($hover_gradient_direction == 'diagonal') {
			$style = 'style="background:-webkit-linear-gradient(60deg, '.$hover_bgcolor.', '.$hover_gradientcolor.');background:linear-gradient(60deg, '.$hover_bgcolor.', '.$hover_gradientcolor.');filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$hover_bgcolor.', endColorstr='.$hover_gradientcolor.',GradientType=1) \0/IE9;"';
		} else if($hover_gradient_direction == 'toptobottom') {
			$style = 'style="background:linear-gradient(to bottom, '.$hover_bgcolor.', '.$hover_gradientcolor.'); background:-webkit-linear-gradient(top, '.$hover_bgcolor.', '.$hover_gradientcolor.'); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$hover_bgcolor.', endColorstr='.$hover_gradientcolor.',GradientType=0) \0/IE9;"';
		} else {
			$style = 'style="background: linear-gradient(to left, '.$hover_bgcolor.', '.$hover_gradientcolor.'); background:-webkit-linear-gradient(right, '.$hover_bgcolor.', '.$hover_gradientcolor.'); filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='.$hover_bgcolor.', endColorstr='.$hover_gradientcolor.',GradientType=1) \0/IE9;"';
		}

	} else if($hover_bgcolor != '') {

		if($hover_style == 'minimal-icons') {
			$css = '.dtportfolio-item.dtportfolio-hover-minimal-icons .dtportfolio-image-overlay:before, .dtportfolio-item.dtportfolio-hover-minimal-icons .dtportfolio-image-overlay:after, .dtportfolio-item.dtportfolio-hover-minimal-icons figure::before, .dtportfolio-item.dtportfolio-hover-minimal-icons figure:after, .dtportfolio-item.dtportfolio-hover-minimal-icons .dtportfolio-image-overlay h2, .dtportfolio-item.details-below-image.dtportfolio-hover-minimal-icons .details-holder h2 { background:'.$hover_bgcolor.'; }';
			wp_add_inline_style( 'dtportfolioaddon-css-custom',  $css );
		} else {
			$style = 'style="background:'.$hover_bgcolor.'"';
		}

	}


	if($hover_style == 'highlighter') {

		$out .= '<a class="dtportfolio-image-overlay '.$hover_contentcolor.'" title="'.$portfolio_title.'" href="'.$portfolio_permalink.'" '.$style.'></a>';

	} else if($hover_style != '') {

		$ilightbox_id_attr = $ilightbox_id = '';
		if($hover_style == 'with-gallery-thumb' || $hover_style == 'with-gallery-list') {
			$ilightbox_id = 'portfolio-ilightbox-'.$portfolio_id;
			$ilightbox_id_attr = 'data-ilightboxid="portfolio-ilightbox-'.$portfolio_id.'"';
		}

		$out .= '<div class="dtportfolio-image-overlay '.$hover_contentcolor.'" '.$style.' '.$ilightbox_id_attr.'>';

			if($hover_style == 'with-gallery-list') {

				$out .= '<div class="dtportfolio-image-overlay-container">';
					$out .= '<div class="links">
								<a title="'.$portfolio_title.'" href="'.$portfolio_permalink.'"> <span class="fas fa-link"> </span> </a>
								<a title="'.$portfolio_title.'" data-gal="prettyPhoto[gallery]" href="'.esc_url($popup).'"> <span class="fas fa-search"> </span> </a>
							</div>
							<div class="dtportfolio-image-overlay-details">'
								.get_the_term_list($portfolio_id, 'portfolio_entries', '<p class="categories">', ', ', '</p>').'
								<h2><a href="'.$portfolio_permalink.'" title="'.$portfolio_title.'">'.$portfolio_title.'</a></h2>
							</div>';

			} else if($hover_style == 'with-intro') {

				$out .= '<div class="dtportfolio-image-overlay-details">';
					$out .= do_shortcode(get_the_excerpt($portfolio_id));
				$out .= '</div>';

			} else {

				$out .= '<div class="links">
							<a title="'.$portfolio_title.'" href="'.$portfolio_permalink.'"> <span class="fas fa-link"> </span> </a>
							<a title="'.$portfolio_title.'" data-gal="prettyPhoto[gallery]" href="'.esc_url($popup).'"> <span class="fas fa-search"> </span> </a>
						</div>
						<div class="dtportfolio-image-overlay-details">
							<h2><a href="'.$portfolio_permalink.'" title="'.$portfolio_title.'">'.$portfolio_title.'</a></h2>'
							.get_the_term_list($portfolio_id, 'portfolio_entries', '<p class="categories">', ', ', '</p>');

							if($hover_style == 'with-details') {
								$out .= '<p class="dtportfolio-image-overlay-description">'.get_the_excerpt($portfolio_id).'</p>';
								$out .= '<a title="'.$portfolio_title.'" href="'.$portfolio_permalink.'" class="dtportfolio-gallery-link">'.esc_html__('View Gallery', 'dtportfolio').'<span class="fa fa-angle-double-right"></span></a>';
							}
							
				$out .= '</div>';	

			}



			if($hover_style == 'with-gallery-list') {

				$out .= '<p class="dtportfolio-gallery-list-description">'.get_the_excerpt($portfolio_id).'</p>';

				$out .= '<a title="'.$portfolio_title.'" href="'.$portfolio_permalink.'" class="dtportfolio-gallery-link">'.esc_html__('View Gallery', 'dtportfolio').'<span class="fa fa-angle-double-right"></span></a>';

			}

			if($hover_style == 'with-gallery-thumb' || $hover_style == 'with-gallery-list') {

				$portfolio_settings = get_post_meta ( $portfolio_id, '_portfolio_settings', true );
				$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();

				$out .= '<ul>';

							if( array_key_exists('portfolio-gallery', $portfolio_settings) ) {

								$i = 0;
								
								$items = explode(',', $portfolio_settings['portfolio-gallery']);
								$items = array_slice($items, 0, 3);
								foreach( $items as $item ) {

									if(($hover_style == 'with-gallery-thumb' && $i < 3) || ($hover_style == 'with-gallery-list' && $i < 6)) {

				    					$image_src = wp_get_attachment_image_src($item, 'dtportfolio-450x450', false);
										$image_src = $image_src[0];	
										
										$image_src_full = wp_get_attachment_image_src($item, 'full', false);
										$image_src_full = $image_src_full[0];

										if($image_src != '') {	
											$out .= '<li><img src="'.esc_url($image_src).'"  data-ilightboximg="'.esc_url($image_src_full).'" alt="portfolio-image" title="portfolio-image" class="'.$ilightbox_id.'" /></li>';
											$i++;
										}	

									}																

								}

							}

				$out .= '</ul>';

			}

			if($hover_style == 'with-gallery-list') {
				$out .= '</div>';
			}				

		$out .= '</div>';

	}
			
	return $out;
								
}

function dtportfolio_portfolio_template_datas($datatype, $columns, $tpl_default_settings) {
	
	$post_style = isset( $tpl_default_settings['portfolio-post-style'] ) ? $tpl_default_settings['portfolio-post-style'] : '';
	$cursor_hover_style = isset( $tpl_default_settings['portfolio-cursor-hover-style'] ) ? $tpl_default_settings['portfolio-cursor-hover-style'] : '';
	$post_per_page = isset( $tpl_default_settings['portfolio-post-per-page'] ) ? $tpl_default_settings['portfolio-post-per-page'] : -1;

			
	$portfolio_container_classes = $portfolio_container_attributes = $portfolio_item_classes = $portfolio_item_attributes = array();

	array_push($portfolio_item_classes, $post_style);
	array_push($portfolio_item_classes, $cursor_hover_style);

	if($columns != '' && $columns != '-1') {
		array_push($portfolio_container_attributes, 'data-column="'.$columns.'"');	
	}

	if($post_per_page != '') {
		array_push($portfolio_container_attributes, 'data-postperpage="'.$post_per_page.'"');
	}

	if($post_style == 'dtportfolio-fullpage') {

		array_push($portfolio_container_classes, 'dtportfolio-container-fullpage');
		$portfolio_fullpage_type = isset( $tpl_default_settings['portfolio-fullpage-type'] ) ? $tpl_default_settings['portfolio-fullpage-type'] : '';
		if($portfolio_fullpage_type != '') {
			array_push($portfolio_container_classes, 'fullpage-'.$portfolio_fullpage_type);
		} else {
			array_push($portfolio_container_classes, 'fullpage-default');
		}

		$portfolio_disable_fullpage_js = isset( $tpl_default_settings['portfolio-disable-fullpage-js'] ) ? $tpl_default_settings['portfolio-disable-fullpage-js'] : '';
		if($portfolio_disable_fullpage_js) {
			array_push($portfolio_container_classes, 'disable-fullpage-jquery');
		}

		if($portfolio_fullpage_type == 'splitted-section') {
			$portfolio_fullpage_splittedsections = isset( $tpl_default_settings['portfolio-fullpage-splittedsections'] ) ? $tpl_default_settings['portfolio-fullpage-splittedsections'] : 'leftside-image';
			array_push($portfolio_container_classes, $portfolio_fullpage_splittedsections);
		}

		$fullpage_navigation  = array_key_exists( 'portfolio-fullpage-navigation', $tpl_default_settings ) ? $tpl_default_settings['portfolio-fullpage-navigation'] : "";
		$fullpage_navigation_attribute = 'data-fullpagenavigation="'.$fullpage_navigation.'"';
		array_push($portfolio_container_attributes, $fullpage_navigation_attribute);


		$disable_autoscrolling  = (array_key_exists( 'portfolio-disable-auto-scrolling', $tpl_default_settings ) && $tpl_default_settings['portfolio-disable-auto-scrolling'] == 'true') ? false : true;
		$fullpage_disableautoscrolling_attribute = 'data-disableautoscrolling="'.$disable_autoscrolling.'"';
		array_push($portfolio_container_attributes, $fullpage_disableautoscrolling_attribute);

		$portfolio_fullpage_type = array_key_exists('portfolio-fullpage-type',$tpl_default_settings) ?  $tpl_default_settings['portfolio-fullpage-type'] : ''; 
		if($portfolio_fullpage_type != '') {
			$portfolio_fullpage_type = '-'.$portfolio_fullpage_type;
		} else {
			$portfolio_fullpage_type = '-default';
		}
		$item_class = 'dtportfolio-fullpage'.$portfolio_fullpage_type;
		array_push($portfolio_item_classes, $item_class);	

		array_push($portfolio_item_classes, 'no-space');

	} else if($post_style == 'dtportfolio-parallax') {

		array_push($portfolio_container_classes, 'dtportfolio-container-parallax');

		$allow_space = ( isset( $tpl_default_settings['portfolio-grid-space'] ) && $tpl_default_settings['portfolio-grid-space'] == 'true' ) ? 'with-space' : 'no-space';
		array_push($portfolio_container_classes, $allow_space);
		array_push($portfolio_item_classes, $allow_space);

	} else if($post_style == 'dtportfolio-striped') {

		array_push($portfolio_container_classes, 'dtportfolio-container');
		array_push($portfolio_container_classes, 'dtportfolio-container-striped');
		array_push($portfolio_item_classes, 'no-space');

	} else if($post_style == 'dtportfolio-fixed') {

		array_push($portfolio_container_classes, 'dtportfolio-container');
		array_push($portfolio_container_classes, 'dtportfolio-container-fixed');

		$allow_space = ( isset( $tpl_default_settings['portfolio-grid-space'] ) && $tpl_default_settings['portfolio-grid-space'] == 'true' ) ? 'with-space' : 'no-space';

		array_push($portfolio_container_classes, $allow_space);
		array_push($portfolio_item_classes, $allow_space);
		
	} else if($post_style == 'dtportfolio-framed') {

		array_push($portfolio_container_classes, 'dtportfolio-container');
		array_push($portfolio_container_classes, 'dtportfolio-container-framed');

		$allow_space = ( isset( $tpl_default_settings['portfolio-grid-space'] ) && $tpl_default_settings['portfolio-grid-space'] == 'true' ) ? 'with-space' : 'no-space';

		array_push($portfolio_container_classes, $allow_space);
		array_push($portfolio_item_classes, $allow_space);

	} else if($post_style == 'dtportfolio-multiscroll') {

		array_push($portfolio_container_classes, 'dtportfolio-multiscroll-container');

	} else {

		array_push($portfolio_container_classes, 'dtportfolio-container');
		$allow_space = ( isset( $tpl_default_settings['portfolio-grid-space'] ) && $tpl_default_settings['portfolio-grid-space'] == 'true' ) ? 'with-space' : 'no-space';

		array_push($portfolio_container_classes, $allow_space);
		array_push($portfolio_item_classes, $allow_space);

	}

	array_push($portfolio_container_classes, 'dtportfolio-container-resetter');

	if($post_per_page == '1') {
		array_push($portfolio_container_classes, 'remove-default-height');
	}	


	if(array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel') {

		array_push($portfolio_container_classes, 'swiper-wrapper');
		array_push($portfolio_item_classes, 'swiper-slide');

		$portfolio_carousel_effect  = array_key_exists( 'portfolio-carousel-effect', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-effect'] : '';
		$portfolio_carousel_numberofrows  = array_key_exists( 'portfolio-carousel-numberofrows', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-numberofrows'] : 1;		
		$portfolio_carousel_autoplay  = array_key_exists( 'portfolio-carousel-autoplay', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-autoplay'] : '';
		$portfolio_carousel_slidesperview  = array_key_exists( 'portfolio-carousel-slidesperview', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-slidesperview'] : '';
		$portfolio_carousel_loopmode  = array_key_exists( 'portfolio-carousel-loopmode', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-loopmode'] : '';
		$portfolio_carousel_mousewheelcontrol  = array_key_exists( 'portfolio-carousel-mousewheelcontrol', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-mousewheelcontrol'] : '';
		$portfolio_carousel_centermode  = array_key_exists( 'portfolio-carousel-centermode', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-centermode'] : '';
		$portfolio_carousel_verticaldirection  = array_key_exists( 'portfolio-carousel-verticaldirection', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-verticaldirection'] : '';
		$portfolio_carousel_paginationtype  = array_key_exists( 'portfolio-carousel-paginationtype', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-paginationtype'] : '';
		$portfolio_carousel_thumbnailpagination  = array_key_exists( 'portfolio-carousel-thumbnailpagination', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-thumbnailpagination'] : '';
		$portfolio_carousel_arrowpagination  = array_key_exists( 'portfolio-carousel-arrowpagination', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-arrowpagination'] : '';
		$portfolio_carousel_arrowpagination_type  = array_key_exists( 'portfolio-carousel-arrowpagination_type', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-arrowpagination_type'] : '';
		$portfolio_carousel_scrollbar  = array_key_exists( 'portfolio-carousel-scrollbar', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-scrollbar'] : '';
		$portfolio_carousel_arrowformousepointer  = array_key_exists( 'portfolio-carousel-arrowformousepointer', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-arrowformousepointer'] : '';
		$portfolio_carousel_paginationcolorscheme  = array_key_exists( 'portfolio-carousel-paginationcolorscheme', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-paginationcolorscheme'] : '';
		$portfolio_carousel_playpausebutton  = array_key_exists( 'portfolio-carousel-playpausebutton', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-playpausebutton'] : '';
		$portfolio_carousel_spacebetween  = array_key_exists( 'portfolio-carousel-spacebetween', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-spacebetween'] : '';
		$portfolio_carousel_pagination_designtype  = array_key_exists( 'portfolio-carousel-pagination_designtype', $tpl_default_settings ) ? $tpl_default_settings['portfolio-carousel-pagination_designtype'] : '';

		// Attributes
		array_push($portfolio_container_attributes, 'data-carouseleffect="'.$portfolio_carousel_effect.'"');
		array_push($portfolio_container_attributes, 'data-carouselnumberofrows="'.$portfolio_carousel_numberofrows.'"');
		array_push($portfolio_container_attributes, 'data-carouselautoplay="'.$portfolio_carousel_autoplay.'"');
		array_push($portfolio_container_attributes, 'data-carouselslidesperview="'.$portfolio_carousel_slidesperview.'"');
		array_push($portfolio_container_attributes, 'data-carouselloopmode="'.$portfolio_carousel_loopmode.'"');
		array_push($portfolio_container_attributes, 'data-carouselmousewheelcontrol="'.$portfolio_carousel_mousewheelcontrol.'"');
		array_push($portfolio_container_attributes, 'data-carouselcentermode="'.$portfolio_carousel_centermode.'"');
		array_push($portfolio_container_attributes, 'data-carouselverticaldirection="'.$portfolio_carousel_verticaldirection.'"');
		array_push($portfolio_container_attributes, 'data-carouselpaginationtype="'.$portfolio_carousel_paginationtype.'"');
		array_push($portfolio_container_attributes, 'data-carouselthumbnailpagination="'.$portfolio_carousel_thumbnailpagination.'"');
		array_push($portfolio_container_attributes, 'data-carouselarrowpagination="'.$portfolio_carousel_arrowpagination.'"');
		array_push($portfolio_container_attributes, 'data-carouselarrowpaginationtype="'.$portfolio_carousel_arrowpagination_type.'"');
		array_push($portfolio_container_attributes, 'data-carouselscrollbar="'.$portfolio_carousel_scrollbar.'"');
		array_push($portfolio_container_attributes, 'data-carouselarrowformousepointer="'.$portfolio_carousel_arrowformousepointer.'"');
		array_push($portfolio_container_attributes, 'data-carouselpaginationcolorscheme="'.$portfolio_carousel_paginationcolorscheme.'"');
		array_push($portfolio_container_attributes, 'data-carouselplaypausebutton="'.$portfolio_carousel_playpausebutton.'"');
		array_push($portfolio_container_attributes, 'data-carouselspacebetween="'.$portfolio_carousel_spacebetween.'"');
		array_push($portfolio_container_attributes, 'data-carouselpaginationdesigntype="'.$portfolio_carousel_pagination_designtype.'"');

	}

	$repeat_animation = isset( $tpl_default_settings['repeat-animation'] ) ? $tpl_default_settings['repeat-animation'] : '';
	if($repeat_animation == 'true') {
		array_push($portfolio_container_classes, 'repeat-animation');
	}	

	$details_below_image = array_key_exists('portfolio-details-below-image', $tpl_default_settings) ? $tpl_default_settings['portfolio-details-below-image'] : '';
	if($details_below_image != '') {
		array_push($portfolio_container_classes, $details_below_image);
	}


	if($datatype == 'portfolio-container-classes') {
		return $portfolio_container_classes;
	} else if($datatype == 'portfolio-container-attributes') {
		return $portfolio_container_attributes;
	} else if($datatype == 'portfolio-item-classes') {
		return $portfolio_item_classes;
	} else if($datatype == 'portfolio-item-attributes') {
		return $portfolio_item_attributes;
	}
								
}

function dtportfolio_portfolio_item_datas($portfolio_template_pageid, $portfolio_id, $datatype, $image, $tpl_default_settings, $columns) {
	
	$post_style = isset( $tpl_default_settings['portfolio-post-style'] ) ? $tpl_default_settings['portfolio-post-style'] : '';

	$related_portfolio_id = isset( $tpl_default_settings['related-portfolio-id'] ) ? $tpl_default_settings['related-portfolio-id'] : '';	

	$portfolio_item_meta = get_post_meta($portfolio_id,'_portfolio_settings',TRUE);
	$portfolio_item_meta = is_array($portfolio_item_meta) ? $portfolio_item_meta  : array();

	$disable_item_options = array_key_exists('portfolio-disable-item-options', $tpl_default_settings) ? true : false;

	// Classes

	if($datatype == 'portfolio-individual-item-classes') {

		$portfolio_individual_item_classes = array();

		if($related_portfolio_id == '') {
			$masonry = array_key_exists("masonry-size", $portfolio_item_meta) ? $portfolio_item_meta['masonry-size'] : '';
			array_push($portfolio_individual_item_classes, $masonry);
		}

		if(!in_array($post_style, array('dtportfolio-fullpage'))) {

			if( array_key_exists('filter', $tpl_default_settings) && $tpl_default_settings['filter'] == 'true' ) {
				$item_categories = get_the_terms( $portfolio_id, 'portfolio_entries' );
				if(is_object($item_categories) || is_array($item_categories)) {
					foreach ($item_categories as $category) {
						array_push($portfolio_individual_item_classes, $category->slug.'-sort');
					}
				}
			}

		}

		$hover_state = array_key_exists('hover-state', $tpl_default_settings) ? 'hover-state' : '';
		if($hover_state != '') {
			array_push($portfolio_individual_item_classes, $hover_state);
		} else {
			if(!$disable_item_options) {
				$hover_state = (array_key_exists('hover-state', $portfolio_item_meta) && $portfolio_item_meta['hover-state'] != '') ? 'hover-state' : '';
				if($hover_state != '') {
					array_push($portfolio_individual_item_classes, $hover_state);
				}	
			}
		}

		if(!in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-parallax', 'dtportfolio-multiscroll')) && (!array_key_exists('portfolio-displaystyle', $tpl_default_settings))) {

			$animationeffect = array_key_exists('animationeffect', $tpl_default_settings) ? $tpl_default_settings['animationeffect'] :'';
			if($animationeffect != '') {
				array_push($portfolio_individual_item_classes, 'animate');

			} else {
				if(!$disable_item_options) {
					$animationeffect = array_key_exists('animationeffect', $portfolio_item_meta) ? $portfolio_item_meta['animationeffect'] :'';
					if($animationeffect != '') {
						array_push($portfolio_individual_item_classes, 'animate');
					}
				}			
			}

		}

		$details_below_image = array_key_exists('portfolio-details-below-image', $tpl_default_settings) ? $tpl_default_settings['portfolio-details-below-image'] : '';
		if($details_below_image != '') {
			array_push($portfolio_individual_item_classes, $details_below_image);
		}		

		$hover_style_portfolio = array_key_exists('hover-styles', $portfolio_item_meta) ? $portfolio_item_meta['hover-styles'] : '';
		if($disable_item_options || $hover_style_portfolio == '') {
			$hover_style = isset( $tpl_default_settings['portfolio-hover-style'] ) ? $tpl_default_settings['portfolio-hover-style'] : '';
		} else {
			$hover_style = $hover_style_portfolio;
		}


		if(array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel') {

			$slidesperview = array_key_exists('portfolio-carousel-slidesperview', $tpl_default_settings) ?  $tpl_default_settings['portfolio-carousel-slidesperview'] : ''; 
			if($slidesperview > 6 && $hover_style != 'art' && $hover_style != 'highlighter') {
				array_push($portfolio_individual_item_classes, 'dtportfolio-hover-overlay');
			} else {
				if($hover_style != '') {
					array_push($portfolio_individual_item_classes, 'dtportfolio-hover-'.$hover_style);
				}
			}

		} else {

			if($columns > 4 && $hover_style != 'art' && $hover_style != 'highlighter') {
				array_push($portfolio_individual_item_classes, 'dtportfolio-hover-overlay');
			} else {
				if($hover_style != '') {
					array_push($portfolio_individual_item_classes, 'dtportfolio-hover-'.$hover_style);
				}				
			}

		}

		return array_filter($portfolio_individual_item_classes);

	}

	// Attributes

	if($datatype == 'portfolio-individual-item-attributes') {

		$portfolio_individual_item_attributes = array();

		if($post_style == 'dtportfolio-parallax') {
			array_push($portfolio_individual_item_attributes, 'style="background-image:url('.esc_url($image).');"');

			$parallax_speed = isset( $tpl_default_settings['portfolio-parallax-effects'] ) ? $tpl_default_settings['portfolio-parallax-effects'] : '';
			if($parallax_speed == 'different-parallax-speed') {
				$different_parallax_speed_values = (isset( $portfolio_item_meta['different-parallax-speed-values'] ) && $portfolio_item_meta['different-parallax-speed-values'] != '') ? $portfolio_item_meta['different-parallax-speed-values'] : '';
				if($different_parallax_speed_values == '') {
					$speed_values = array('0.05', '0.1', '0.15', '0.2', '0.25', '0.3', '0.35', '0.4', '0.45', '0.5', '0.55', '0.6', '0.65', '0.7', '0.75', '0.8', '0.85', '0.9', '0.95', '1', '0.05', '0.1', '0.15', '0.2', '0.25', '0.3', '0.35', '0.4', '0.45', '0.5', '0.55', '0.6', '0.65', '0.7', '0.75', '0.8', '0.85', '0.9', '0.95', '1');
					$speed = array_rand($speed_values, 1);
					$speed = $speed_values[$speed];					
				} else {
					$speed = $different_parallax_speed_values;
				}
			} else if($parallax_speed == 'random-parallax-effect') {
				$random_parallax_speed_values = (isset( $portfolio_item_meta['random-parallax-speed-values'] ) && $portfolio_item_meta['random-parallax-speed-values'] != '') ? $portfolio_item_meta['random-parallax-speed-values'] : '';
				if($random_parallax_speed_values == '') {				
					$speed_values = array('0', '0.5', '0', '0', '0.5', '0', '0', '0', '0.5', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0.5', '0', '0', '0', '0.5', '0', '0', '0.5', '0', '0', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0', '0.5', '0', '0.5', '0', '0', '0', '0', '0.5', '0', '0', '0.5', '0', '0', '0', '0', '0.5');
					$speed = array_rand($speed_values, 1);
					$speed = $speed_values[$speed];
				} else {
					$speed = $random_parallax_speed_values;
				}					
			} else {
				$speed = 0.5;
			}

			if($speed > 0) {
				array_push($portfolio_individual_item_attributes, 'data-jarallax=\'{"speed": '.$speed.'}\'');
			}
			
		}

		if($post_style == 'dtportfolio-fullpage') {
			$portfolio_disable_fullpage_js = isset( $tpl_default_settings['portfolio-disable-fullpage-js'] ) ? true : false;

			if(!$portfolio_disable_fullpage_js) {

				$portfolio_fullpage_type = isset( $tpl_default_settings['portfolio-fullpage-type'] ) ? $tpl_default_settings['portfolio-fullpage-type'] : '';
				if($portfolio_fullpage_type == '' && $image != '') {
					array_push($portfolio_individual_item_attributes, 'style="background-image:url('.esc_url($image).');"');
				}

			}
		}

		if(!in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-parallax', 'dtportfolio-multiscroll')) && (!array_key_exists('portfolio-displaystyle', $tpl_default_settings))) {

			$animationeffect = array_key_exists('animationeffect', $tpl_default_settings) ? $tpl_default_settings['animationeffect'] :'';
			$animationdelay = array_key_exists('animationdelay', $tpl_default_settings) ? $tpl_default_settings['animationdelay'] :'';
			if($animationeffect != '') {
				array_push($portfolio_individual_item_attributes, 'data-animationeffect="'.$animationeffect.'"');
				array_push($portfolio_individual_item_attributes, 'data-animationdelay="'.$animationdelay.'"');
			} else {
				if(!$disable_item_options) {
					$animationeffect = array_key_exists('animationeffect', $portfolio_item_meta) ? $portfolio_item_meta['animationeffect'] :'';
					$animationdelay = array_key_exists('animationdelay', $portfolio_item_meta) ? $portfolio_item_meta['animationdelay'] :'';
					if($animationeffect != '') {
						array_push($portfolio_individual_item_attributes, 'data-animationeffect="'.$animationeffect.'"');
						array_push($portfolio_individual_item_attributes, 'data-animationdelay="'.$animationdelay.'"');
					}	
				}	
			}

		}

		return array_filter($portfolio_individual_item_attributes);

	}
								
}

function dtportfolio_portfolio_lists($portfolio_template_pageid, $tpl_default_settings, $show_sidebar) {

	$output = '';

	if(!is_array($tpl_default_settings) && empty($tpl_default_settings)) {
		$tpl_default_settings = array();
	}

	$portfolioid = isset( $tpl_default_settings['portfolioid'] ) ? $tpl_default_settings['portfolioid'] : '';
	$portfolioid = ($portfolioid != '') ? array_filter( explode(",",$portfolioid) ) : '';

	$related_portfolio_id = isset( $tpl_default_settings['related-portfolio-id'] ) ? $tpl_default_settings['related-portfolio-id'] : '';

	if( isset( $tpl_default_settings['from'] ) && $tpl_default_settings['from'] == 'related-portfolio' ) {
		if($related_portfolio_id != '' && $related_portfolio_id > 0) {

		} else {
			global $post;
			$related_portfolio_id = $post->ID;		
		}
	}

	$post_style = isset( $tpl_default_settings['portfolio-post-style'] ) ? $tpl_default_settings['portfolio-post-style'] : '';
	$post_per_page = isset( $tpl_default_settings['portfolio-post-per-page'] ) ? $tpl_default_settings['portfolio-post-per-page'] : -1;	
	$categories = isset($tpl_default_settings['portfolio-categories']) ? $tpl_default_settings['portfolio-categories'] : array();
	if((is_string($categories) || is_integer($categories)) && !empty($categories)) {
		if(strpos($categories, ',') !== false) {
			$categories = array_filter( explode(',', $categories) );
		}
	} else {
		$categories = array_filter($categories);
	}

	$pagination_type = isset($tpl_default_settings['portfolio-pagination-type']) ? $tpl_default_settings['portfolio-pagination-type'] : '';
	$paged = isset($tpl_default_settings['paged']) ? $tpl_default_settings['paged'] : 1;
	$ajax_call = isset($tpl_default_settings['ajax-call']) ? $tpl_default_settings['ajax-call'] : 0;	

	$portfolio_fullpage_type = isset( $tpl_default_settings['portfolio-fullpage-type'] ) ? $tpl_default_settings['portfolio-fullpage-type'] : '';
	$portfolio_fullpage_splittedsections = isset( $tpl_default_settings['portfolio-fullpage-splittedsections'] ) ? $tpl_default_settings['portfolio-fullpage-splittedsections'] : 'leftside-image';

    $portfolio_layout_class = dtportfolio_get_portfolio_layout_class($show_sidebar, $post_style, $tpl_default_settings);
    $portfolio_layout_class = explode('|', $portfolio_layout_class);
	$post_class = $portfolio_layout_class[0];
	$columns = $portfolio_layout_class[1];

	$portfolio_container_classes = dtportfolio_portfolio_template_datas('portfolio-container-classes', $columns, $tpl_default_settings);
	$portfolio_container_attributes = dtportfolio_portfolio_template_datas('portfolio-container-attributes', $columns, $tpl_default_settings);
	$portfolio_item_classes = dtportfolio_portfolio_template_datas('portfolio-item-classes', $columns, $tpl_default_settings);
	$portfolio_item_attributes = dtportfolio_portfolio_template_datas('portfolio-item-attributes', $columns, $tpl_default_settings);

	array_push($portfolio_item_classes, $post_class);

	$details_below_image = array_key_exists('portfolio-details-below-image', $tpl_default_settings) ? $tpl_default_settings['portfolio-details-below-image'] : '';


	$calculate_first_class = true;

	#Pagination
	if($pagination_type == 'numbered-pagination') {

		$paged = 1;
		if ( get_query_var('paged') ) { 
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}

	}


	#Query arg
	$args = array();
	if( !empty($portfolioid) ):
		$args = array( 'paged' => $paged ,'posts_per_page' => $post_per_page, 'post__in' => $portfolioid, 'post_type' => 'dt_portfolios');
	elseif( empty($categories) ):
		$args = array( 'paged' => $paged ,'posts_per_page' => $post_per_page, 'post_type' => 'dt_portfolios');
	else:
		$args = array(
			'paged' => $paged,
			'posts_per_page' => $post_per_page,
			'post_type' => 'dt_portfolios',
			'orderby' => 'ID',
			'order' => 'ASC',
			'tax_query' => array( 
				array(
					'taxonomy' => 'portfolio_entries',
					'field' => 'id',
					'operator' => 'IN',
					'terms' => $categories
				)
			)
		);
	endif;
	
	if($related_portfolio_id != ''):

		$related_portfolio_terms = wp_get_object_terms( $related_portfolio_id, 'portfolio_entries', array('fields' => 'ids') );
		$args['post__not_in'] = array($related_portfolio_id);
		$args['tax_query'][] = array('taxonomy' => 'portfolio_entries',
									'field' => 'id',
									'terms' => $related_portfolio_terms ,
									'operator' => 'IN'
									);

	endif;


	if(!in_array($pagination_type, array('load-more', 'lazy-load'))):
		$fullwidth_class = '';
		if(isset($tpl_default_settings['enable-fullwidth']) && $tpl_default_settings['enable-fullwidth'] == 'true'):
			$fullwidth_class = 'dtportfolio-fullwidth-wrapper';
		else:
			$fullwidth_class = 'dtportfolio-without-fullwidth-wrapper';
		endif;
		if(isset($tpl_default_settings['class'])) {
			$output .= '<div class="dtportfolio-container-wrapper '.esc_attr($fullwidth_class).' '.esc_attr($tpl_default_settings['class']).'">';
		} else {
			$output .= '<div class="dtportfolio-container-wrapper '.esc_attr($fullwidth_class).'">';
		}
	endif;

		#Filter Option
		if($ajax_call == '0'):
			if(array_key_exists('filter', $tpl_default_settings) && $tpl_default_settings['filter'] == 'true' && !in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-striped', 'dtportfolio-parallax', 'dtportfolio-multiscroll'))) :
				array_push($portfolio_item_classes, 'all-sort');
				$filterdesign_type = isset($tpl_default_settings['portfolio-filterdesign-type']) ? $tpl_default_settings['portfolio-filterdesign-type'] : '';
				$output .= dtportfolio_get_portfolio_category_filter($categories, $post_style, $filterdesign_type);
			endif;
		endif;

		$portfolio_query = new WP_Query($args);
		$portfolio_cnt = $portfolio_query->post_count;
		$isodd = ($portfolio_cnt%2);
		if($isodd == 1) {
			$portfolio_cnt = $portfolio_cnt-1;
		}
		$portfolio_mid_val = ($portfolio_cnt/2)-1;
		if($portfolio_query->have_posts()):		

			$i = 1; $oddeven = 0;

			if($ajax_call == '0'):

				if(in_array($pagination_type, array('load-more', 'lazy-load')) &&  !in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-multiscroll'))):

					$output .= '<div class="dtportfolio-infinite-portfolio-wrapper">';
						$output .= '<div class="message hidden">'.esc_html__('No more records to load!', 'dtportfolio').'</div>';

					array_push($portfolio_container_classes, 'dtportfolio-infinite-portfolio-container');

				endif;

				if(array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel'):
					$output .= '<div class="dtportfolio-swiper-container swiper-container">';
					$calculate_first_class = false;
				endif;

		    	$output .= '<div class="'.implode(' ', array_filter($portfolio_container_classes)).'" '.implode(' ', $portfolio_container_attributes).'>';

		    	if(!array_key_exists('portfolio-displaystyle', $tpl_default_settings)  && !in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-multiscroll'))) {

			    	$grid_sizer_class = str_replace('dtportfolio-item', '', $post_class);
			    	$grid_sizer_class = str_replace('dtportfolio-column', ' ', $grid_sizer_class);
			    	$grid_sizer_class = str_replace('with-sidebar', '', $grid_sizer_class);
			    	$output .= '<div class="dtportfolio-grid-sizer '.$grid_sizer_class.'"></div>';

			    }

	    	endif;

	    	$gallery_thumb = array();

			while( $portfolio_query->have_posts() ):

				$portfolio_query->the_post();
				$portfolio_id = get_the_ID();
				$portfolio_title = get_the_title($portfolio_id);
				$portfolio_permalink = get_permalink($portfolio_id);

				$portfolio_item_first_class = array();
				if($calculate_first_class):
					if($i == 1):
						array_push($portfolio_item_first_class, 'first');
					endif;
					if($i == $columns):
						$i = 1; 
					else:
						$i = $i + 1;
					endif;
				endif;

				#images
				$portfolio_items = dtportfolio_get_portfolio_images($portfolio_id, $post_style, $related_portfolio_id, $columns);
				$portfolio_items = explode('|', $portfolio_items);
				$image = $portfolio_items[0];
				$popup = $portfolio_items[1];
				$video = $portfolio_items[2];

				$portfolio_individual_item_classes = dtportfolio_portfolio_item_datas($portfolio_template_pageid, $portfolio_id, 'portfolio-individual-item-classes', $image, $tpl_default_settings, $columns);

				$portfolio_individual_item_attributes = dtportfolio_portfolio_item_datas($portfolio_template_pageid, $portfolio_id, 'portfolio-individual-item-attributes', $image, $tpl_default_settings, $columns);

				$portfolioitemclasses = array_merge($portfolio_item_classes, $portfolio_individual_item_classes);
				$portfolioitemclasses = array_merge($portfolioitemclasses, $portfolio_item_first_class);
				$portfolioitemattributes = array_merge($portfolio_item_attributes, $portfolio_individual_item_attributes);


				if($post_style == 'dtportfolio-multiscroll'):

					if($oddeven == 0):
						$output .= '<div class="ms-left">';	
					endif;

		                $output .= '<div class="'.implode(' ', array_filter($portfolioitemclasses)).'" '.implode(' ', $portfolioitemattributes).'>';

		        			$output .= '<figure>';

        						$output .= dtportfolio_portfolio_image($portfolio_id, $portfolio_template_pageid, $image, $video, $tpl_default_settings);
	                			$output .= dtportfolio_portfolio_overlay_content($portfolio_id, $portfolio_template_pageid, $popup, $tpl_default_settings);

		                	$output .= '</figure>';

			            $output .= '</div>';

			             if($oddeven == $portfolio_mid_val) {
			             	$output .= '</div>';
			             	$output .= '<div class="ms-right">';	
			             }

			        if($oddeven == ($portfolio_cnt-1)):
			        	$output .= '</div>';
			        endif;

				else:

	                $output .= '<div class="'.implode(' ', array_filter($portfolioitemclasses)).'" '.implode(' ', $portfolioitemattributes).'>';

	            		if($post_style == 'dtportfolio-framed'):
	            			$output .= '<div class="dtportfolio-framed-holder">';
	            		endif;

	        			$output .= '<figure>';

	        					if(($post_style == 'dtportfolio-fullpage' && $portfolio_fullpage_type == 'splitted-section'&& $portfolio_fullpage_splittedsections == 'leftside-image') || ($post_style == 'dtportfolio-fullpage' && $portfolio_fullpage_type == 'splitted-section'&& $portfolio_fullpage_splittedsections == 'alternate-content' && $oddeven%2 == 0)):

	        						$output .= '<div class="dtportfolio-column dtportfolio-one-half no-space first">';
	        							$output .= dtportfolio_portfolio_image($portfolio_id, $portfolio_template_pageid, $image, $video, $tpl_default_settings);
	        						$output .= '</div>';
	        						$output .= '<div class="dtportfolio-column dtportfolio-one-half no-space">';
			                			$output .= dtportfolio_portfolio_overlay_content($portfolio_id, $portfolio_template_pageid, $popup, $tpl_default_settings);
		                			$output .= '</div>';

		                		elseif(($post_style == 'dtportfolio-fullpage' && $portfolio_fullpage_type == 'splitted-section'&& $portfolio_fullpage_splittedsections == 'rightside-image') || ($post_style == 'dtportfolio-fullpage' && $portfolio_fullpage_type == 'splitted-section'&& $portfolio_fullpage_splittedsections == 'alternate-content' && $oddeven%2 != 0)):

	        						$output .= '<div class="dtportfolio-column dtportfolio-one-half no-space first">';
			                			$output .= dtportfolio_portfolio_overlay_content($portfolio_id, $portfolio_template_pageid, $popup, $tpl_default_settings);
		                			$output .= '</div>';
	        						$output .= '<div class="dtportfolio-column dtportfolio-one-half no-space">';
	        							$output .= dtportfolio_portfolio_image($portfolio_id, $portfolio_template_pageid, $image, $video, $tpl_default_settings);
	        						$output .= '</div>';

	        					else:

	        						$output .= dtportfolio_portfolio_image($portfolio_id, $portfolio_template_pageid, $image, $video, $tpl_default_settings);
		                			$output .= dtportfolio_portfolio_overlay_content($portfolio_id, $portfolio_template_pageid, $popup, $tpl_default_settings);	                			
		                		endif;

	                	$output .= '</figure>';

            			if($details_below_image != ''):
    						$output .= '<div class="details-holder">';
    							$output .= '<h2><a href="'.$portfolio_permalink.'" title="'.$portfolio_title.'">'.$portfolio_title.'</a></h2>'.get_the_term_list($portfolio_id, 'portfolio_entries', '<p class="categories">', ', ', '</p>');
    						$output .= '</div>';	
						endif;	

	            		if($post_style == 'dtportfolio-framed'):
	            			$output .= '</div>';
	            		endif;    								                	

		            $output .= '</div>';

		            array_push($gallery_thumb, dtportfolio_generate_gallery_thumb($portfolio_id, $image, $video));

				endif;

				$oddeven++;
				
			endwhile;

			if(in_array($pagination_type, array('load-more', 'lazy-load')) &&  !in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-multiscroll'))):
				if($ajax_call == '0'):
					$output .= '</div>';
				endif;
			endif;

			wp_reset_postdata();			

	        $output .= '</div>';

	        if($post_style == 'dtportfolio-multiscroll' && array_key_exists('portfolio-multiscroll-enable-arrows', $tpl_default_settings) && $tpl_default_settings['portfolio-multiscroll-enable-arrows'] == 'true'):

	        	$output .= '<div class="multiscroll-button-holder">';
		        	$output .= '<div class="multiscroll-button down"></div>';
		        	$output .= '<div class="multiscroll-button up"></div>';
		        $output .= '</div>';

	        endif;			        

			if(array_key_exists('portfolio-displaystyle', $tpl_default_settings) && $tpl_default_settings['portfolio-displaystyle'] == 'carousel'):

					if(array_key_exists( 'portfolio-carousel-content-over-slider', $tpl_default_settings ) && $tpl_default_settings['portfolio-carousel-content-over-slider'] != '') {
			        	$output .= '<div class="dtportfolio-content-over-slider">';
				        	$output .= do_shortcode($tpl_default_settings['portfolio-carousel-content-over-slider']);
				        $output .= '</div>';						
					}

					$pcp_design_type = isset($tpl_default_settings['portfolio-carousel-pagination_designtype']) ? $tpl_default_settings['portfolio-carousel-pagination_designtype'] : '';
					$pcp_paginationcolorscheme = isset($tpl_default_settings['portfolio-carousel-paginationcolorscheme']) ? $tpl_default_settings['portfolio-carousel-paginationcolorscheme'] : '';

					$output .= '<div class="dtportfolio-swiper-pagination-holder '.$pcp_paginationcolorscheme.' '.$pcp_design_type.'">';

						if($tpl_default_settings['portfolio-carousel-arrowformousepointer'] == 'true') {

							$output .= '<div class="dtportfolio-swiper-arrow-mouse-pointer">
											<div class="dtportfolio-swiper-arrow-left">
												<div class="dtportfolio-swiper-arrow-click left">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
											<div class="dtportfolio-swiper-arrow-middle">
												<div class="dtportfolio-swiper-arrow-click middle">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
											<div class="dtportfolio-swiper-arrow-right">
												<div class="dtportfolio-swiper-arrow-click right">
													<div class="dtportfolio-swiper-arrow"></div>
												</div>
											</div>
										</div>';

						}

						if(isset($tpl_default_settings['portfolio-carousel-paginationtype']) && $tpl_default_settings['portfolio-carousel-paginationtype'] == 'bullets') {
							$output .= '<div class="dtportfolio-swiper-bullet-pagination"></div>';	
						}

						if(isset($tpl_default_settings['portfolio-carousel-paginationtype']) && $tpl_default_settings['portfolio-carousel-paginationtype'] == 'progressbar') {
							$output .= '<div class="dtportfolio-swiper-progress-pagination"></div>';	
						}	

						if(isset($tpl_default_settings['portfolio-carousel-scrollbar']) && $tpl_default_settings['portfolio-carousel-scrollbar'] == 'true') {
							$output .= '<div class="dtportfolio-swiper-scrollbar"></div>';	
						}						

						if(isset($tpl_default_settings['portfolio-carousel-pagination_designtype']) && in_array($tpl_default_settings['portfolio-carousel-pagination_designtype'], array ('type2', 'type3'))) {
							$output .= '<div class="dtportfolio-swiper-pagination-wrapper">';
						}

							if(isset($tpl_default_settings['portfolio-carousel-paginationtype']) && $tpl_default_settings['portfolio-carousel-paginationtype'] == 'fraction') {
								$output .= '<div class="dtportfolio-swiper-fraction-pagination"></div>';
							}							

							if(isset($tpl_default_settings['portfolio-carousel-arrowpagination']) && $tpl_default_settings['portfolio-carousel-arrowpagination'] == 'true') {
								$pcp_arrowpagination_type = isset($tpl_default_settings['portfolio-carousel-arrowpagination_type']) ? $tpl_default_settings['portfolio-carousel-arrowpagination_type'] : '';
								$output .= '<div class="dtportfolio-swiper-arrow-pagination '.$pcp_arrowpagination_type.'">';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-prev">'.esc_html__('Prev', 'dtportfolio').'</a>';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-next">'.esc_html__('Next', 'dtportfolio').'</a>';
								$output .= '</div>';
							}

							if(isset($tpl_default_settings['portfolio-carousel-playpausebutton']) && $tpl_default_settings['portfolio-carousel-playpausebutton'] == 'true') {
								if(isset($tpl_default_settings['portfolio-carousel-autoplay']) && $tpl_default_settings['portfolio-carousel-autoplay'] > 0) {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause pause"><span class="fa fa-pause"></span></a>';
								} else {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause play"><span class="fa fa-play"></span></a>';
								}
							}

						if(isset($tpl_default_settings['portfolio-carousel-pagination_designtype']) && in_array($tpl_default_settings['portfolio-carousel-pagination_designtype'], array ('type2', 'type3'))) {
							$output .= '</div>';
						}
					
					$output .= '</div>';

				$output .= '</div>';

				if(array_key_exists( 'portfolio-carousel-thumbnailpagination', $tpl_default_settings ) && $tpl_default_settings['portfolio-carousel-thumbnailpagination'] == 'true') {
					$output .= '<div class="dtportfolio-swiper-thumbnail-container swiper-container">';
						$output .= '<div class="dtportfolio-swiper-thumbnail swiper-wrapper">';
							$output .= implode('', $gallery_thumb);
						$output .= '</div>';
					$output .= '</div>';	
				}				

			endif;	

		endif;

	if(!in_array($pagination_type, array('load-more', 'lazy-load'))):	
		$output .= '</div>';
		$output .= '<div class="dtportfolio-fullwidth-wrapper-fix"></div>';
	endif;


	if($pagination_type == 'numbered-pagination'):
		if(!in_array($post_style, array('dtportfolio-fullpage'))):
        	$output .= '<div class="dtportfolio-pagination">';

    		$output .= paginate_links( array(
							  'current' 	 => $paged,
							  'type'     	 => 'list',
							  'end_size'     => 2,
							  'mid_size'     => 3,
							  'prev_next'    => true,
							  'prev_text'    => '<i class="fa fa-caret-left"></i>&nbsp;'.esc_html__('Prev', 'priority'),
							  'next_text'    => esc_html__('Next', 'priority').'&nbsp;<i class="fa fa-caret-right"></i>',
							  'total' 		 => $portfolio_query->max_num_pages 
							  ) );

        	$output .= '</div>';
	    endif;
    endif;

    if(in_array($pagination_type, array('load-more', 'lazy-load')) &&  !in_array($post_style, array('dtportfolio-fullpage', 'dtportfolio-multiscroll'))):
    	if($ajax_call == '0'):

    		$completedata_str = '';
    		if(is_array($tpl_default_settings) && !empty($tpl_default_settings)):
    			unset($tpl_default_settings['portfolio-carousel-content-over-slider']);
    			$completedata_str = json_encode($tpl_default_settings);
    		endif;

    		if($pagination_type == 'load-more') {
    			$label = esc_html__('Load More', 'dtportfolio' );
    		} else if ($pagination_type == 'lazy-load') {
    			$label = esc_html__('Scroll To Load More','dtportfolio');
    		}

		    $output .= '<a href="javascript:void(0)" class="dtportfolio-infinite-portfolio-load-more '.$pagination_type.' aligncenter" data-complete-data='.esc_js($completedata_str).'><span>'.$label.'</span></a>';

		endif;
	endif;

	return $output;
}?>