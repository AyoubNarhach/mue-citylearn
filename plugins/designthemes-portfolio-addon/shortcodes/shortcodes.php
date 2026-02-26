<?php
class DTPortfolioShortcodesDefinition {
	
	function __construct() {

		add_shortcode ( "dtportfolio_portfolios", array (
			$this,
			"dtportfolio_portfolios"
		) );	
		
		add_action( 'wp_ajax_dtportfolio_ajax_infinite_portfolios', array( $this, 'dtportfolio_ajax_infinite_portfolios' ) );
		add_action( 'wp_ajax_nopriv_dtportfolio_ajax_infinite_portfolios', array( $this, 'dtportfolio_ajax_infinite_portfolios' ) );

		
		add_shortcode ( "dtportfolio_onepage_navigation_title_holder", array (
			$this,
			"dtportfolio_onepage_navigation_title_holder"
		) );

		add_shortcode ( "dtportfolio_comment_form", array (
			$this,
			"dtportfolio_comment_form"
		) );

		add_shortcode ( "dtportfolio_comment_list", array (
			$this,
			"dtportfolio_comment_list"
		) );

		add_shortcode ( "dtportfolio_custom_details", array (
			$this,
			"dtportfolio_custom_details"
		) );

		add_shortcode ( "dtportfolio_feature_image", array (
			$this,
			"dtportfolio_feature_image"
		) );	

		add_shortcode ( "dtportfolio_feature_video", array (
			$this,
			"dtportfolio_feature_video"
		) );

		add_shortcode ( "dtportfolio_gallery_listing", array (
			$this,
			"dtportfolio_gallery_listing"
		) );	

		add_shortcode ( "dtportfolio_navigation_links", array (
			$this,
			"dtportfolio_navigation_links"
		) );

		add_shortcode ( "dtportfolio_related_portfolios", array (
			$this,
			"dtportfolio_related_portfolios"
		) );

		add_shortcode ( "dtportfolio_slider", array (
			$this,
			"dtportfolio_slider"
		) );

		add_shortcode ( "dtportfolio_terms", array (
			$this,
			"dtportfolio_terms"
		) );																						

		add_shortcode ( "dtportfolio_title", array (
			$this,
			"dtportfolio_title"
		) );

		add_shortcode ( "dtportfolio_widget", array (
			$this,
			"dtportfolio_widget"
		) );
		# dtportfolio_widget
		add_filter( 'vc_autocomplete_dtportfolio_widget__post_categories_callback', array( 
			$this,
			'vc_autocomplete_portfolio_post_categories_field_search'
		), 10, 1 );

		add_filter( 'vc_autocomplete_dtportfolio_widget__post_categories_render', array(
			$this,
			'vc_autocomplete_portfolio_post_categories_field_render'
		), 10, 1 );
	}

	function dtportfolio_shortcodeHelper($content = null) {
		$content = do_shortcode ( shortcode_unautop ( $content ) );
		$content = preg_replace ( '#^<\/p>|^<br \/>|<p>$#', '', $content );
		$content = preg_replace ( '#<br \/>#', '', $content );
		return trim ( $content );
	}

	function dtportfolio_portfolios( $attrs, $content = null ){

		$attrs = shortcode_atts ( array (

					'portfolioid' => '',
					'portfolio-post-per-page' => '-1',
					'portfolio-post-style' => 'default',
					'portfolio-details-below-image' => '',
					'portfolio-multiscroll-enable-arrows' => '',
					'portfolio-fullpage-navigation' => '',
					'portfolio-fullpage-type' => '',
					'portfolio-disable-auto-scrolling' => '',
					'portfolio-fullpage-splittedsections' => 'leftside-image',
					'portfolio-parallax-effects' => '',
					'portfolio-post-layout' => 'dtportfolio-one-fourth-column',
					'portfolio-grid-space' => '',
					'filter' => '',
					'portfolio-filterdesign-type' => '',
					'portfolio-categories' => '',					
					'portfolio-pagination-type' => '',
					'class' => '',
					'enable-fullwidth' => '',
					'paged' => '1',
					'ajax-call' => '0',
					
					
					'portfolio-disable-item-options' => '',
					'portfolio-hover-style' => 'modern-title',
					'portfolio-cursor-hover-style' => '',	
					'animationeffect' => '',
					'animationdelay' => '',
					'repeat-animation' => '',
					'hover-background-color' => '',
					'hover-content-color' => '',
					'hover-gradient-color' => '',
					'hover-gradient-direction' => 'lefttoright',
					'hover-state' => '',

					
					'portfolio-displaystyle' => '',
					'portfolio-carousel-effect' => '',
					'portfolio-carousel-numberofrows' => '',
					'portfolio-carousel-autoplay' => '',
					'portfolio-carousel-slidesperview' => 1,
					'portfolio-carousel-loopmode' => '',
					'portfolio-carousel-mousewheelcontrol' => '',
					'portfolio-carousel-centermode' => '',
					'portfolio-carousel-verticaldirection' => '',
					'portfolio-carousel-paginationtype' => '',
					'portfolio-carousel-thumbnailpagination' => '',
					'portfolio-carousel-arrowpagination' => '',
					'portfolio-carousel-arrowpagination_type' => '',
					'portfolio-carousel-scrollbar' => '',
					'portfolio-carousel-arrowformousepointer' => '',
					'portfolio-carousel-paginationcolorscheme' => '',
					'portfolio-carousel-playpausebutton' => '',
					'portfolio-carousel-spacebetween' => '',
					'portfolio-carousel-pagination_designtype' => '',


					'related-portfolio-id' => '',


				), $attrs, 'dtportfolio_portfolios' );

		$attrs = (is_array($attrs) && !empty($attrs)) ?array_filter($attrs) : array();

		$tpl_default_settings = array_filter($attrs);
		$tpl_default_settings['portfolio-carousel-content-over-slider'] = $content;

		global $post;
		$portfolio_shortcode_pageid = $post->ID;

		$show_sidebar = dtportfolio_shortcode_page_details($portfolio_shortcode_pageid);

		wp_enqueue_style ( 'dtportfolioaddon-css-custom', plugins_url('designthemes-portfolio-addon') . '/css/custom.css', array (), false, 'all' );

		return dtportfolio_portfolio_lists($portfolio_shortcode_pageid, $tpl_default_settings, $show_sidebar);

	}

	function dtportfolio_ajax_infinite_portfolios(){

		$completedata_array = json_decode(html_entity_decode(stripslashes($_REQUEST['completedata_json'])), true);

		$sc = '[dtportfolio_portfolios ajax-call="1"';
		foreach($completedata_array as $cda_key => $cda_val) {
			if($cda_key != 'ajax-call') {
				if($cda_key == 'paged') {
					$sc .= ' '.$cda_key.'="'.($cda_val+1).'"';
				} else if($cda_key == 'dtportfolio-categories') {
					if(is_array($cda_val)) {
						$sc .= ' '.$cda_key.'="'.implode(',',$cda_val).'"';
					} else {
						$sc .= ' '.$cda_key.'="'.$cda_val.'"';
					}				
				} else {
					$sc .= ' '.$cda_key.'="'.$cda_val.'"';
				}
			}
			
		}
		$sc .= ' /]';

		echo do_shortcode($sc);

		die(1);

	}
	
	function dtportfolio_onepage_navigation_title_holder($attrs, $content = null) {

		$attrs = shortcode_atts ( array (

					'navigation_ids' => '',
					'navigation_titles' => '',
					'type' => 'default',
					'position' => 'left',

				), $attrs, 'dtportfolio_onepage_navigation_title_holder' );


		$navigation_ids = explode(',', $attrs['navigation_ids']);
		$navigation_titles = explode(',', $attrs['navigation_titles']);

		$output = '';

		$output .= '<ul class="dtportfolio-onepage-navigation-title-holder '.$attrs['type'].' '.$attrs['position'].'">';
					if(is_array($navigation_ids) && !empty($navigation_ids)) {
						$i = 1;
						foreach($navigation_ids as $navigation_id) {
							$class = '';
							if($i == 1) {
								$class = 'class="active"';
							}
							$output .= '<li>
											<a href="#'.$navigation_id.'" onclick="return false" '.$class.'>
												<span>'.$i.'</span>
												<span>'.$navigation_titles[$i-1].'</span>
											</a>
										</li>';
							$i++;
						}
					}
		$output .= '</ul>';

		return $output;

	}
	
	function dtportfolio_comment_form( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (

					'form_title' => '',
					'form_comment_field_placeholder' => '',
					'form_submit_button_label' => '',
					'class' => '',

				), $attrs, 'dtportfolio_comment_form' );

		$output = '';

		if ( post_password_required() ) {			
			return;
		}

		$output .= '<div class="dtportfolio-comment-form-holder '.esc_attr($attrs['class']).'">';

	    	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
	    		$output .= '<p class="no-comments">'.esc_html__( 'Comments are closed.', 'dtportfolio').'</p>';
	    	}

	    	if($attrs['form_title'] != '') {
	    		$form_title = $attrs['form_title'];
	    	} else {
	    		$form_title = esc_html__( 'Leave a Comment', 'dtportfolio' );
	    	}

	    	if($attrs['form_comment_field_placeholder'] != '') {
	    		$form_comment_field_placeholder = $attrs['form_comment_field_placeholder'];
	    	} else {
	    		$form_comment_field_placeholder = esc_html__( 'Comment', 'dtportfolio' );
	    	}

	    	if($attrs['form_submit_button_label'] != '') {
	    		$form_submit_button_label = $attrs['form_submit_button_label'];
	    	} else {
	    		$form_submit_button_label = esc_html__( 'Comment', 'dtportfolio' );
	    	}

			ob_start();

			$comments_args = array (
								'title_reply' 			=> 	$form_title,
								'comment_field'			=>	'<div class="dtportfolio-column dtportfolio-one-column first">
																<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true" placeholder="'.esc_attr($form_comment_field_placeholder).'"></textarea>
															</div>',
								'label_submit'			=>	$form_submit_button_label,
								'class_form'			=>  'comment-form'
							);

			comment_form($comments_args);

			$comment_form = ob_get_contents();
			ob_end_clean();

			$output .= $comment_form;

		$output .= '</div>';


		return $output;

	}	

	function dtportfolio_comment_list( $attrs, $content = null ) {

		$attrs = shortcode_atts ( array (
			
					'class' => '',

				), $attrs, 'dtportfolio_comment_list' );


		$output = '';
	
		ob_start();

		comments_template();
		$comment_list_template = ob_get_contents();
		
		ob_end_clean();

		$output .= '<div class="dtportfolio-comment-list-holder '.esc_attr($attrs['class']).'">';
			$output .= $comment_list_template;
		$output .= '</div>';


		return $output;

	}

	function dtportfolio_custom_details($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'type' => '',
					'class' => '',

				), $attrs, 'dtportfolio_custom_details' );


		$output = '';

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$portfolio_settings = get_post_meta ( $attrs['portfolio_id'], '_portfolio_settings', TRUE );
			$portfolio_settings = is_array ( $portfolio_settings ) ? $portfolio_settings : array ();	

		    if(array_key_exists('portfolio_opt_flds', $portfolio_settings)) {

				$output .= '<ul class="dtportfolio-project-details '.esc_attr($attrs['type']).' '.esc_attr($attrs['class']).'">';

					for( $i = 1; $i <= sizeof($portfolio_settings['portfolio_opt_flds']) / 2; $i++ ):

						$label = $portfolio_settings['portfolio_opt_flds']["portfolio_opt_flds_title_{$i}"];
        				$value = $portfolio_settings['portfolio_opt_flds']["portfolio_opt_flds_value_{$i}"];

        				if(filter_var($value ,FILTER_VALIDATE_URL)) {
        					$value = '<a href="'.esc_url($value).'">'.esc_html($value).'</a>';
        				} elseif(is_email($value)) {
        					$email = sanitize_email($value);
        					$value = '<a href="mailto:'.antispambot($email,1).'">'.antispambot($value).'</a>';
        				}

	    				if($value != '') {
	            			$output .= '<li><span>'.esc_html($label).' : </span> '.$value.' </li>';
	            		}

        			endfor;

		        $output .= '</ul>';

		    }		

		}

		return $output;

	}

	function dtportfolio_feature_image($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'class' => '',

				), $attrs, 'dtportfolio_feature_image' );

		$output = '';

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$featured_image_id = get_post_thumbnail_id($attrs['portfolio_id']);

			if($featured_image_id) {

				$image_details = wp_get_attachment_image_src($featured_image_id, 'full');

				$output .= '<div class="dtportfolio-featured-image-holder '.esc_attr($attrs['class']).'">';
					$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" all="'.esc_html__('Featured Image', 'dtportfolio').'" />';
				$output .= '</div>';
				
			}

		}

		return $output;

	}	

	function dtportfolio_feature_video($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'class' => '',

				), $attrs, 'dtportfolio_feature_video' );

		$output = '';				

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$portfolio_featured_video = get_post_meta($attrs['portfolio_id'], '_portfolio_featured_video', TRUE);
			$portfolio_featured_video = is_array($portfolio_featured_video) ? $portfolio_featured_video  : array();

			$featured_video =  isset($portfolio_featured_video['featured_video']) ? $portfolio_featured_video['featured_video'] : '';

			if($featured_video != '') {
				$output .= '<div class="dtportfolio-featured-video-holder '.esc_attr($attrs['class']).'">';
					$output .= '<video id="portfolio-featured-video" poster="'.plugins_url('designthemes-portfolio-addon').'/images/video-image.png" class="play">
									<source src="'.esc_attr($featured_video).'" type="video/mp4">
									<source src="'.esc_attr($featured_video).'" type="video/webm">
									<source src="'.esc_attr($featured_video).'" type="video/ogg">
								</video>';	
				$output .= '</div>';

			}

		}

		return $output;

	}	

	function dtportfolio_gallery_listing($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'gallery_ids' => '',
					'portfolio-post-layout' => 'dtportfolio-one-half-column',
					'portfolio-grid-space' => '',
					'portfolio-hover-design' => '',
					'animationeffect' => '',
					'animationdelay' => '',
					'repeat-animation' => '',
					'class' => '',

				), $attrs, 'dtportfolio_gallery_listing' );

		$output = '';				

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$gallery_settings = is_array($attrs) ? array_filter($attrs) : array();

			$output .= '<div class="dtportfolio-gallery-listing-holder '.esc_attr($attrs['class']).'">';
				$output .= dtportfolio_get_portfolio_gallery_listing($gallery_settings);
			$output .= '</div>';

		}

		return $output;

	}	

	function dtportfolio_navigation_links($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'type' => '',
					'class' => '',

				), $attrs, 'dtportfolio_navigation_links' );

		$output = '';				

		if(is_singular('dt_portfolios')) {

			$output .= '<div class="dtportfolio-navigation-links-holder '.esc_attr($attrs['class']).'">';

				if($attrs['type'] == 'type4') {

					$output .= '<div class="post-nav-container '.esc_attr($attrs['type']).'">';
						$output .= '<div class="post-prev-link">';
							$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
						$output .= '</div>';		
						$output .= '<div class="post-next-link">';
							$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				} else if($attrs['type'] == 'type3') {

					$prev_post = get_previous_post();
					$next_post = get_next_post();

					$output .= '<div class="post-nav-container '.esc_attr($attrs['type']).'">';
						$output .= '<div class="post-prev-link">';
							if(isset($prev_post) && !empty($prev_post)) {
								$output .= '<i class="fas fa-chevron-left"> </i>';
								$output .= get_previous_post_link('%link', esc_html__('Prev Entry','dtportfolio') );
								$output .= '<a href="'.get_permalink($prev_post->ID).'">'.$prev_post->post_title.'</a>';
							}
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';	
						$output .= '<div class="post-next-link">';
							if(isset($next_post) && !empty($next_post)) {
								$output .= '<i class="fas fa-chevron-right"> </i>';
								$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio'));
								$output .= '<a href="'.get_permalink($next_post->ID).'">'.$next_post->post_title.'</a>';
							}
						$output .= '</div>';
					$output .= '</div>';

				} else if($attrs['type'] == 'type2') {

					$output .= '<div class="post-nav-container '.esc_attr($attrs['type']).'">';
						$output .= '<div class="post-prev-link">';
								$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>' );
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';
						$output .= '<div class="post-next-link">';
								$output .= get_next_post_link('%link', '<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				} else {

					$output .= '<div class="post-nav-container type1">';
						$output .= '<div class="post-prev-link">';
								$output .= get_previous_post_link('%link', '<i class="fas fa-chevron-left"> </i>'.esc_html__('Prev Entry','dtportfolio') );
						$output .= '</div>';
						$output .= '<div class="post-archive-link-wrapper">';
								$output .= '<a class="post-archive-link" href="'.get_post_type_archive_link('dt_portfolios').'"></a>';
						$output .= '</div>';			
						$output .= '<div class="post-next-link">';
								$output .= get_next_post_link('%link', esc_html__('Next Entry','dtportfolio').'<i class="fas fa-chevron-right"> </i>');
						$output .= '</div>';
					$output .= '</div>';

				}

			$output .= '</div>';

		}

		return $output;

	}

	function dtportfolio_related_portfolios( $attrs, $content = null ){

		$attrs = shortcode_atts ( array (

					//'portfolioid' => '',
					'portfolio-post-per-page' => '-1',
					'portfolio-post-style' => 'default',
					'portfolio-details-below-image' => '',
					'portfolio-multiscroll-enable-arrows' => '',
					'portfolio-fullpage-navigation' => '',
					'portfolio-fullpage-type' => '',
					'portfolio-disable-auto-scrolling' => '',
					'portfolio-fullpage-splittedsections' => 'leftside-image',
					'portfolio-parallax-effects' => '',
					'portfolio-post-layout' => 'dtportfolio-one-fourth-column',
					'portfolio-grid-space' => '',
					'filter' => '',
					'portfolio-filterdesign-type' => '',
					'dtportfolio-categories' => '',					
					'portfolio-pagination-type' => '',
					'class' => '',
					'enable-fullwidth' => '',
					'paged' => '1',
					'ajax-call' => '0',
					
					
					'portfolio-disable-item-options' => '',
					'portfolio-hover-style' => 'modern-title',
					'portfolio-cursor-hover-style' => '',	
					'animationeffect' => '',
					'animationdelay' => '',
					'repeat-animation' => '',
					'hover-background-color' => '',
					'hover-content-color' => '',
					'hover-gradient-color' => '',
					'hover-gradient-direction' => 'lefttoright',
					'hover-state' => '',

					
					'portfolio-displaystyle' => '',
					'portfolio-carousel-effect' => '',
					'portfolio-carousel-numberofrows' => '',
					'portfolio-carousel-autoplay' => '',
					'portfolio-carousel-slidesperview' => 1,
					'portfolio-carousel-loopmode' => '',
					'portfolio-carousel-mousewheelcontrol' => '',
					'portfolio-carousel-centermode' => '',
					'portfolio-carousel-verticaldirection' => '',
					'portfolio-carousel-paginationtype' => '',
					'portfolio-carousel-thumbnailpagination' => '',
					'portfolio-carousel-arrowpagination' => '',
					'portfolio-carousel-arrowpagination_type' => '',
					'portfolio-carousel-scrollbar' => '',
					'portfolio-carousel-arrowformousepointer' => '',
					'portfolio-carousel-paginationcolorscheme' => '',
					'portfolio-carousel-playpausebutton' => '',
					'portfolio-carousel-spacebetween' => '',
					'portfolio-carousel-pagination_designtype' => '',


					'related-portfolio-id' => '',


				), $attrs, 'dtportfolio_related_portfolios' );

		$attrs = (is_array($attrs) && !empty($attrs)) ?array_filter($attrs) : array();

		$tpl_default_settings = array_filter($attrs);
		$tpl_default_settings['from'] = 'related-portfolio';
		$tpl_default_settings['portfolio-carousel-content-over-slider'] = $content;

		global $post;
		$portfolio_shortcode_pageid = $post->ID;

		$show_sidebar = dtportfolio_shortcode_page_details($portfolio_shortcode_pageid);

		wp_enqueue_style ( 'dtportfolioaddon-css-custom', plugins_url('designthemes-portfolio-addon') . '/css/custom.css', array (), false, 'all' );

		return dtportfolio_portfolio_lists($portfolio_shortcode_pageid, $tpl_default_settings, $show_sidebar);

	}	

	function dtportfolio_slider($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'gallery_ids' => '',
					'include_featured_image' => 'false',
					'include_featured_video' => 'false',
					'background_image' => '',
					'class' => '',
					
					'carousel_effect' => '',
					'carousel_autoplay' => '',
					'carousel_slidesperview' => 1,
					'carousel_loopmode' => '',
					'carousel_mousewheelcontrol' => '',
					'carousel_verticaldirection' => '',
					'carousel_paginationtype' => '',
					'carousel_thumbnailpagination' => '',
					'carousel_arrowpagination' => '',
					'carousel_arrowpagination_type' => '',
					'carousel_scrollbar' => '',
					'carousel_arrowformousepointer' => '',
					'carousel_paginationcolorscheme' => '',
					'carousel_playpausebutton' => '',
					'carousel_spacebetween' => '',
					'carousel_pagination_designtype' => '',

				), $attrs, 'dtportfolio_slider' );

		$output = '';
	
		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$media_carousel_attributes = array ();

			array_push($media_carousel_attributes, 'data-enablecarousel="true"');
			array_push($media_carousel_attributes, 'data-carouseleffect="'.esc_attr($attrs['carousel_effect']).'"');
			array_push($media_carousel_attributes, 'data-carouselautoplay="'.esc_attr($attrs['carousel_autoplay']).'"');
			array_push($media_carousel_attributes, 'data-carouselslidesperview="'.esc_attr($attrs['carousel_slidesperview']).'"');
			array_push($media_carousel_attributes, 'data-carouselloopmode="'.esc_attr($attrs['carousel_loopmode']).'"');
			array_push($media_carousel_attributes, 'data-carouselmousewheelcontrol="'.esc_attr($attrs['carousel_mousewheelcontrol']).'"');
			array_push($media_carousel_attributes, 'data-carouselverticaldirection="'.esc_attr($attrs['carousel_verticaldirection']).'"');
			array_push($media_carousel_attributes, 'data-carouselpaginationtype="'.esc_attr($attrs['carousel_paginationtype']).'"');
			array_push($media_carousel_attributes, 'data-carouselthumbnailpagination="'.esc_attr($attrs['carousel_thumbnailpagination']).'"');
			array_push($media_carousel_attributes, 'data-carouselarrowpagination="'.esc_attr($attrs['carousel_arrowpagination']).'"');
			array_push($media_carousel_attributes, 'data-carouselscrollbar="'.esc_attr($attrs['carousel_scrollbar']).'"');
			array_push($media_carousel_attributes, 'data-carouselarrowformousepointer="'.esc_attr($attrs['carousel_arrowformousepointer']).'"');
			array_push($media_carousel_attributes, 'data-carouselplaypausebutton="'.esc_attr($attrs['carousel_playpausebutton']).'"');
			array_push($media_carousel_attributes, 'data-carouselspacebetween="'.esc_attr($attrs['carousel_spacebetween']).'"');

			if(!empty($media_carousel_attributes)) {
				$media_carousel_attributes_string = implode(' ', $media_carousel_attributes);
			}

			$portfolio_settings = get_post_meta($attrs['portfolio_id'], '_portfolio_settings', true);
			$portfolio_settings = is_array($portfolio_settings) ? $portfolio_settings : array ();

			$portfolio_galleries = explode(',', $portfolio_settings['portfolio-gallery']);

			if(isset($attrs['gallery_ids']) && $attrs['gallery_ids'] != '') {
				$filter_gallery = true;
				$gallery_ids = explode(',', $attrs['gallery_ids']);
			} else {
				$filter_gallery = false;
			}


		    $featured_image_id = get_post_thumbnail_id($attrs['portfolio_id']);	
		    $featured_image_id = ($featured_image_id != '') ? $featured_image_id : -1;

			$output .= '<div class="dtportfolio-image-gallery-holder '.$attrs['class'].'">';

				// Gallery Images
				$output .= '<div class="dtportfolio-image-gallery-container swiper-container">';
				    $output .= '<div class="dtportfolio-image-gallery swiper-wrapper" '.$media_carousel_attributes_string.'>';

				    				$gallery_thumb = array ();

				    				if($attrs['include_featured_image'] == 'true') {

										$image_details = wp_get_attachment_image_src($featured_image_id, 'full');
										
			                           	$output .= '<div class="swiper-slide">';

				                           	if($attrs['background_image'] == 'true') {
				                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
				                           	} else {
				                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
				                           	}

			                           	$output .= '</div>';

										array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

									}

				                    if(is_array($portfolio_galleries) && !empty($portfolio_galleries) && !empty($portfolio_galleries[0])) {
				                        foreach($portfolio_galleries as $portfolio_gallery) {

											if($filter_gallery) {

												if(in_array($portfolio_gallery, $gallery_ids)) {

					                                $image_details = wp_get_attachment_image_src($portfolio_gallery, 'full');
					                               	$output .= '<div class="swiper-slide">';
						                           		if($attrs['background_image'] == 'true') {
							                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
							                           	} else {
							                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
							                           	}
					                               	$output .= '</div>';

					                               	array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

												}

											} else {

					                            $image_details = wp_get_attachment_image_src($portfolio_gallery, 'full');
					                           	$output .= '<div class="swiper-slide">';
					                           		if($attrs['background_image'] == 'true') {
						                           		$output .= '<div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div>';
						                           	} else {
						                           		$output .= '<img src="'.esc_url($image_details[0]).'" title="'.esc_html__('Featured Image', 'dtportfolio').'" alt="'.esc_html__('Featured Image', 'dtportfolio').'" />';
						                           	}
					                           	$output .= '</div>';

					                           	array_push($gallery_thumb, '<div class="swiper-slide"><div class="dtportfolio-single-image-holder" style="background-image:url('.esc_url($image_details[0]).');"></div></div>');

							    			}

				                        }
				                    }

		    		$output .= '</div>';

					$output .= '<div class="dtportfolio-swiper-pagination-holder '.$attrs['carousel_paginationcolorscheme'].' '.$attrs['carousel_pagination_designtype'].'">';

						if($attrs['carousel_arrowformousepointer'] == 'true') {

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

						if($attrs['carousel_paginationtype'] == 'bullets') {
							$output .= '<div class="dtportfolio-swiper-bullet-pagination"></div>';	
						}

						if($attrs['carousel_paginationtype'] == 'progressbar') {
							$output .= '<div class="dtportfolio-swiper-progress-pagination"></div>';	
						}	

						if($attrs['carousel_scrollbar'] == 'true') {
							$output .= '<div class="dtportfolio-swiper-scrollbar"></div>';	
						}						

						if(in_array($attrs['carousel_pagination_designtype'], array ('type2', 'type3'))) {
							$output .= '<div class="dtportfolio-swiper-pagination-wrapper">';
						}

							if($attrs['carousel_paginationtype'] == 'fraction') {
								$output .= '<div class="dtportfolio-swiper-fraction-pagination"></div>';
							}							

							if($attrs['carousel_arrowpagination'] == 'true') {
								$output .= '<div class="dtportfolio-swiper-arrow-pagination '.$attrs['carousel_arrowpagination_type'].'">';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-prev">'.esc_html__('Prev', 'dtportfolio').'</a>';
									$output .= '<a href="#" class="dtportfolio-swiper-arrow-next">'.esc_html__('Next', 'dtportfolio').'</a>';
								$output .= '</div>';
							}

							if($attrs['carousel_playpausebutton'] == 'true') {
								if($attrs['carousel_autoplay'] != '' && $attrs['carousel_autoplay'] > 0) {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause pause"><span class="fa fa-pause"></span></a>';
								} else {
									$output .= '<a href="#" class="dtportfolio-swiper-playpause play"><span class="fa fa-play"></span></a>';
								}
							}

						if(in_array($attrs['carousel_pagination_designtype'], array ('type2', 'type3'))) {
							$output .= '</div>';
						}
					
					$output .= '</div>';

		   		$output .= '</div>';

		   		if($attrs['carousel_thumbnailpagination'] == 'true') {

			   		// Gallery Thumb
					$output .= '<div class="dtportfolio-image-gallery-thumb-container swiper-container">';
					    $output .= '<div class="dtportfolio-image-gallery-thumb swiper-wrapper">';

					    	$output .= implode('', $gallery_thumb);

			    		$output .= '</div>';
			    	$output .= '</div>';

			    }

		   	$output .= '</div>';
			
		}

		return $output;

	}

	function dtportfolio_terms($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'with_icon' => '',
					'class' => '',

				), $attrs, 'dtportfolio_terms' );

		$output = '';				

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$output .= '<div class="dtportfolio-terms-holder '.esc_attr($attrs['class']).'">';

				if($attrs['with_icon']) {
					$output .= get_the_term_list($attrs['portfolio_id'], 'portfolio_entries', '<p class="dtportfolio-categories"><i class="fas fa-tags"></i>', ', ', '</p> ');
				} else {
					$output .= get_the_term_list($attrs['portfolio_id'], 'portfolio_entries', '<p class="dtportfolio-categories">', ', ', '</p>');
				}

			$output .= '</div>';

		}

		return $output;

	}		

	function dtportfolio_title($attrs, $content = null) {

		$attrs = shortcode_atts ( array (
			
					'portfolio_id' => '',
					'class' => '',

				), $attrs, 'dtportfolio_title' );

		$output = '';				

		if($attrs['portfolio_id'] == '' && is_singular('dt_portfolios')) {
			global $post;
			$attrs['portfolio_id'] = $post->ID;
		}

		if($attrs['portfolio_id'] != '') {

			$output .= '<div class="dtportfolio-title-holder '.esc_attr($attrs['class']).'">';
				$output .= '<h2>'.get_the_title($attrs['portfolio_id']).'</h2>';
			$output .= '</div>';

		}

		return $output;

	}

	function dtportfolio_widget( $attrs, $content = null ) {
		extract ( shortcode_atts ( array (
			'el_id'            => '',
			'title'            => '',
			'_post_categories' => '',
			'_post_count'      => ''
		), $attrs ) );

		if($el_id != '') {
			$el_id = 'dt-'.$el_id;
        }

        ob_start();
        echo '<div id="'.esc_attr( $el_id ).'" class="dt-widget-portfolio-wrapper">';

        	global $wp_widget_factory;
        	$args = array();
        	$type = 'DTPortfolio_Widget';

        	if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
        		the_widget( $type,  $attrs, $args );
            }

        echo '</div>';
        $output = ob_get_clean();

        return $output;
	}

	/*
	 * Shortcode : dt_sc_portfolio_widget
	 */
	function vc_autocomplete_portfolio_post_categories_field_search( $search_string ) {
		$vc_taxonomies = get_terms( 'portfolio_entries', array(
			'hide_empty' => false,
			'search' => $search_string,
		) );

		if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
			foreach ( $vc_taxonomies as $t ) {
				if ( is_object( $t ) ) {
					$data[] = vc_get_term_object( $t );
				}
			}
		}
		
		return $data;
	}

	/*
	 * Shortcode : dt_sc_portfolio_widget
	 */
	function vc_autocomplete_portfolio_post_categories_field_render( $term ) {

		$terms = get_terms( 'portfolio_entries', array(
		 	'include' => array( $term['value'] ),
			'hide_empty' => false,
		) );
		
		$data = false;
		if ( is_array( $terms ) && 1 === count( $terms ) ) {
			$term = $terms[0];
			$data = vc_get_term_object( $term );
		}
		
		return $data;
	}
}?>